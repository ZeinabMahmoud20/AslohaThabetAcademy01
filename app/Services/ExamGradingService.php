<?php

namespace App\Services;

use App\Models\Exam;
use App\Models\ExamStudentAnswer;
use App\Models\ExamStudentAttempt;
use App\Models\LevelAttempt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Implements UC-08/UC-09 (Take Exam + Auto-Grade). Every read/write here is
 * scoped to a single level_attempt_id (BR-011/012/049) -- an exam attempt
 * belongs to a specific attempt at a level, never to the student directly,
 * so a repeated level never mixes exam history with the new attempt.
 */
class ExamGradingService
{
    /**
     * Starts (or resumes) a student's attempt at an exam. The server clock is
     * authoritative for timing (Section P.7) -- the client never dictates
     * how much time is left.
     */
    public function startOrResume(Exam $exam, LevelAttempt $levelAttempt): ExamStudentAttempt
    {
        if ($exam->status !== 'published') {
            throw ValidationException::withMessages(['exam' => 'هذا الامتحان غير متاح حالياً.']);
        }

        if (! now()->between($exam->start_at, $exam->end_at)) {
            throw ValidationException::withMessages(['exam' => 'هذا الامتحان ليس متاحاً في هذا التوقيت.']);
        }

        if ($exam->level_id !== $levelAttempt->level_id) {
            throw ValidationException::withMessages(['exam' => 'هذا الامتحان لا يخص مستوى الطالب/ة الحالي.']);
        }

        $attempt = ExamStudentAttempt::firstOrCreate(
            ['exam_id' => $exam->id, 'level_attempt_id' => $levelAttempt->id],
            ['status' => 'not_started']
        );

        if ($attempt->status === 'submitted' || $attempt->status === 'graded') {
            throw ValidationException::withMessages(['exam' => 'تم تسليم هذا الامتحان بالفعل ولا يمكن إعادة الدخول إليه.']);
        }

        if ($attempt->status === 'not_started') {
            $attempt->update(['status' => 'in_progress', 'started_at' => now()]);
        }

        // If the server-side deadline has already passed (student closed the
        // tab and came back late), auto-submit whatever was saved so far
        // instead of letting them keep answering.
        if (now()->greaterThan($attempt->effectiveDeadline())) {
            return $this->submit($attempt, []);
        }

        return $attempt->fresh();
    }

    /**
     * Saves the student's selected options and grades the attempt immediately
     * (UC-09: auto-grading is instant for multiple_choice/true_false, both of
     * which are graded the same way -- compare selected_option_id to the
     * question's correct option).
     *
     * @param  array<int,int>  $selectedOptionsByQuestionId  [question_id => selected_option_id]
     */
    public function submit(ExamStudentAttempt $attempt, array $selectedOptionsByQuestionId): ExamStudentAttempt
    {
        if (in_array($attempt->status, ['submitted', 'graded'], true)) {
            throw ValidationException::withMessages(['attempt' => 'تم تسليم هذا الامتحان بالفعل.']);
        }

        return DB::transaction(function () use ($attempt, $selectedOptionsByQuestionId) {
            $exam = $attempt->exam()->with('questions.options')->first();
            $totalScore = 0.0;

            foreach ($exam->questions as $question) {
                $selectedOptionId = $selectedOptionsByQuestionId[$question->id] ?? null;

                $correctOption = $question->options->firstWhere('is_correct', true);
                $isCorrect = $selectedOptionId !== null && $correctOption && (int) $selectedOptionId === $correctOption->id;
                $scoreEarned = $isCorrect ? $question->score : 0.0;
                $totalScore += $scoreEarned;

                ExamStudentAnswer::updateOrCreate(
                    ['exam_student_attempt_id' => $attempt->id, 'exam_question_id' => $question->id],
                    [
                        'selected_option_id' => $selectedOptionId,
                        'is_correct' => $isCorrect,
                        'score_earned' => $scoreEarned,
                    ]
                );
            }

            $percentage = $exam->max_score > 0 ? round(($totalScore / $exam->max_score) * 100, 2) : 0.0;

            $attempt->update([
                'submitted_at' => now(),
                'score' => $totalScore,
                'percentage' => $percentage,
                'status' => 'graded', // auto-graded immediately -- no manual step needed
            ]);

            return $attempt->fresh();
        });
    }
}