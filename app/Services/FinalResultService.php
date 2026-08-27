<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\ClassRoom;
use App\Models\FinalResult;
use App\Models\GradeScale;
use App\Models\LevelAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Implements Section O (Evaluation Engine) + BR-024..BR-031 + the state machine in Q.4
 * + the promotion/repetition workflow (J.11/J.12, UC-11).
 */
class FinalResultService
{
    /**
     * BR-024/BR-025/BR-026: compute Weekly Component (70) + Exam Component (30) = Final Score.
     * Never mixes data across level_attempts (BR-011/012/049) -- every query below is
     * scoped by level_attempt_id.
     */
    public function calculate(LevelAttempt $attempt): FinalResult
    {
        // --- Weekly Component ---------------------------------------------------
        $earnedWeekly = (float) $attempt->weeklyEvaluations()->sum('total_score');
        $maxWeekly = (float) $attempt->weeklyEvaluations()->sum('max_score');

        $weeklyComponent = $maxWeekly > 0
            ? round(($earnedWeekly / $maxWeekly) * 70, 2)
            : 0.0;

        // --- Exam Component -------------------------------------------------------
        $earnedExam = (float) $attempt->examAttempts()
            ->where('status', 'graded')
            ->sum('score');

        $maxExam = (float) DB::table('exams')
            ->where('level_id', $attempt->level_id)
            ->sum('max_score');

        $examComponent = $maxExam > 0
            ? round(($earnedExam / $maxExam) * 30, 2)
            : 0.0;

        // --- Final Score, Pass/Fail, Grade Label ----------------------------------
        $finalScore = round($weeklyComponent + $examComponent, 2);
        $passFail = $finalScore >= 50 ? 'passed' : 'failed'; // BR-027, fixed threshold

        $gradeScale = GradeScale::resolveLabel($finalScore);

        return DB::transaction(function () use (
            $attempt, $weeklyComponent, $examComponent, $finalScore, $passFail, $gradeScale
        ) {
            return FinalResult::updateOrCreate(
                ['level_attempt_id' => $attempt->id],
                [
                    'weekly_component' => $weeklyComponent,
                    'exam_component' => $examComponent,
                    'final_score' => $finalScore,
                    'pass_fail' => $passFail,
                    // grade_label is a point-in-time SNAPSHOT (Section N.4) -- future
                    // changes to grade_scales must never rewrite a student's history.
                    'grade_label' => $gradeScale->label,
                    'grade_scale_id' => $gradeScale->id,
                    'status' => 'calculated',
                    'calculated_at' => now(),
                ]
            );
        });
    }

    /** Step 1 of the dual approval: Teacher (BR-029). */
    public function teacherApprove(FinalResult $result, int $teacherId): void
    {
        if ($result->status !== 'calculated') {
            throw ValidationException::withMessages([
                'status' => 'Only a Calculated result can receive Teacher approval.',
            ]);
        }

        $result->update([
            'status' => 'teacher_approved',
            'teacher_approved_by' => $teacherId,
            'teacher_approved_at' => now(),
        ]);
    }

    /**
     * Step 2 of the dual approval: Administration (BR-029). On success the result becomes
     * Final and the promotion/repetition workflow fires automatically (BR-030, UC-11).
     */
    public function adminApprove(FinalResult $result, int $adminId): void
    {
        if ($result->status !== 'teacher_approved') {
            throw ValidationException::withMessages([
                'status' => 'Administration can only approve a Teacher-approved result.',
            ]);
        }

        DB::transaction(function () use ($result, $adminId) {
            $result->update([
                'status' => 'final',
                'admin_approved_by' => $adminId,
                'admin_approved_at' => now(),
            ]);

            AuditLog::record(
                userId: $adminId,
                action: 'RESULT_APPROVED',
                entityType: 'final_results',
                entityId: $result->id,
                new: ['status' => 'final', 'pass_fail' => $result->pass_fail],
            );

            $this->applyPromotionOrRepetition($result, $adminId);
        });
    }

    /**
     * UC-11 / J.11 / J.12: never callable directly -- only from adminApprove() once
     * final_results.status has just become 'final' (BR-030).
     */
    protected function applyPromotionOrRepetition(FinalResult $result, int $actingUserId): void
    {
        $attempt = $result->levelAttempt()->with('student')->first();

        if ($result->pass_fail === 'passed') {
            $attempt->update(['status' => 'completed_passed', 'is_active' => false, 'ended_at' => now()]);

            $nextLevel = $attempt->level->next();

            if (! $nextLevel) {
                // Z.2 #3 (Open Question): Level 6 graduate handling. For now we simply
                // stop -- no new attempt is created. Revisit once the PO confirms the
                // "Graduate" status requirement.
                return;
            }

            $newAttempt = LevelAttempt::create([
                'student_id' => $attempt->student_id,
                'level_id' => $nextLevel->id,
                'attempt_number' => 1,
                'started_at' => now(),
                'status' => 'active',
                'is_active' => true,
            ]);

            // Default: same class cohort/teacher unless Administration overrides later
            // via a separate assignment call (Z.1 assumption -- see the docx Section Z.1).
            $currentClass = $attempt->activeClassAssignment?->classRoom;
            $matchingClass = $currentClass
                ? ClassRoom::where('level_id', $nextLevel->id)->where('name', $currentClass->name)->first()
                : null;

            if ($matchingClass) {
                $newAttempt->classAssignments()->create([
                    'class_id' => $matchingClass->id,
                    'start_date' => now(),
                    'is_active' => true,
                    'assigned_by' => $actingUserId,
                ]);
            }

            AuditLog::record($actingUserId, 'STUDENT_PROMOTED', 'level_attempts', $newAttempt->id);
        } else {
            $attempt->update(['status' => 'completed_failed', 'is_active' => false, 'ended_at' => now()]);

            // Failed: new attempt at the SAME level; Administration must assign a class
            // manually (BR-008) -- no class assignment is created here.
            $newAttempt = LevelAttempt::create([
                'student_id' => $attempt->student_id,
                'level_id' => $attempt->level_id,
                'attempt_number' => $attempt->attempt_number + 1,
                'started_at' => now(),
                'status' => 'active',
                'is_active' => true,
            ]);

            AuditLog::record($actingUserId, 'STUDENT_FAILED', 'level_attempts', $newAttempt->id);
        }
    }
}
