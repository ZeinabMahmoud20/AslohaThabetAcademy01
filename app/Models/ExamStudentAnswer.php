<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExamStudentAnswer extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_student_attempt_id', 'exam_question_id', 'selected_option_id',
        'is_correct', 'score_earned',
    ];

    protected $casts = ['is_correct' => 'boolean'];

    public function attempt(): BelongsTo
    {
        return $this->belongsTo(ExamStudentAttempt::class, 'exam_student_attempt_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(ExamQuestion::class, 'exam_question_id');
    }

    public function selectedOption(): BelongsTo
    {
        return $this->belongsTo(ExamQuestionOption::class, 'selected_option_id');
    }
}
