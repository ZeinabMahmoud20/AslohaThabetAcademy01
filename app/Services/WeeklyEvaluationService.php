<?php

namespace App\Services;

use App\Models\AuditLog;
use App\Models\StudentEvaluationItemScore;
use App\Models\StudentWeeklyEvaluation;
use App\Models\WeeklyPlanEvaluationItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Implements Section O.1 and the Draft -> Approved -> Reopened state machine (Q.3),
 * plus FR-025..FR-030 and BR-018/019.
 */
class WeeklyEvaluationService
{
    /**
     * Set/update a single item's score for a student's weekly evaluation (FR-025).
     * Called from PATCH /weekly-evaluations/{id}/items/{itemId}.
     */
    public function setItemScore(
        StudentWeeklyEvaluation $evaluation,
        WeeklyPlanEvaluationItem $planItem,
        ?float $score,
        int $actingUserId,
        ?string $attendanceStatus = null,
        ?string $excuseNote = null
    ): StudentEvaluationItemScore {
        if ($evaluation->status === 'approved') {
            // FR-029: an Approved evaluation is locked until an explicit Reopen.
            throw ValidationException::withMessages([
                'status' => 'Cannot edit an approved evaluation. Reopen it first.',
            ]);
        }

        // An evaluation-item score cannot exceed its configured max (Section V).
        if ($score !== null && $score > $planItem->max_score) {
            throw ValidationException::withMessages([
                'score' => "Score cannot exceed the configured max ({$planItem->max_score}).",
            ]);
        }

        return DB::transaction(function () use (
            $evaluation, $planItem, $score, $actingUserId, $attendanceStatus, $excuseNote
        ) {
            $itemScore = StudentEvaluationItemScore::updateOrCreate(
                [
                    'student_weekly_evaluation_id' => $evaluation->id,
                    'weekly_plan_evaluation_item_id' => $planItem->id,
                ],
                [
                    'score' => $score,
                    'attendance_status' => $attendanceStatus,
                    'excuse_note' => $excuseNote,
                ]
            );

            // FR-030: every edit to an evaluation score is audit-logged.
            AuditLog::record(
                userId: $actingUserId,
                action: 'GRADE_EDITED',
                entityType: 'student_evaluation_item_scores',
                entityId: $itemScore->id,
                new: ['score' => $score, 'attendance_status' => $attendanceStatus],
            );

            $this->recalculateHeader($evaluation);

            return $itemScore;
        });
    }

    /**
     * Section N.4 / O.1: total_score and max_score are cached rollups.
     * Recalculate them whenever an item score changes.
     */
    public function recalculateHeader(StudentWeeklyEvaluation $evaluation): void
    {
        $totalScore = $evaluation->itemScores()->sum('score');

        $maxScore = WeeklyPlanEvaluationItem::where('weekly_plan_id', $evaluation->weekly_plan_id)
            ->sum('max_score');

        $evaluation->update([
            'total_score' => $totalScore,
            'max_score' => $maxScore,
        ]);
    }

    /**
     * Teacher approves Draft -> Approved (BR-018). Blocks if a required item is unscored (FR-029).
     */
    public function approve(StudentWeeklyEvaluation $evaluation, int $teacherId): void
    {
        $requiredItemIds = WeeklyPlanEvaluationItem::where('weekly_plan_id', $evaluation->weekly_plan_id)
            ->where('is_required', true)
            ->pluck('id');

        $scoredItemIds = $evaluation->itemScores()
            ->whereNotNull('score')
            ->pluck('weekly_plan_evaluation_item_id');

        $missing = $requiredItemIds->diff($scoredItemIds);

        if ($missing->isNotEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Cannot approve: required items are still unscored.',
            ]);
        }

        $evaluation->update([
            'status' => 'approved',
            'approved_by' => $teacherId,
            'approved_at' => now(),
        ]);
    }

    /**
     * Approved -> Reopened (BR-019). Only a user with reopen permission may call this
     * (enforced in the controller/policy layer, not here).
     */
    public function reopen(StudentWeeklyEvaluation $evaluation, int $actingUserId): void
    {
        $evaluation->update(['status' => 'reopened']);

        AuditLog::record(
            userId: $actingUserId,
            action: 'EVALUATION_REOPENED',
            entityType: 'student_weekly_evaluations',
            entityId: $evaluation->id,
        );
    }
}
