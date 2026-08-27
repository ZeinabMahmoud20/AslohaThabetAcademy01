<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentEvaluationItemScore extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_weekly_evaluation_id', 'weekly_plan_evaluation_item_id',
        'score', 'attendance_status', 'excuse_note',
    ];

    public function studentWeeklyEvaluation(): BelongsTo
    {
        return $this->belongsTo(StudentWeeklyEvaluation::class);
    }

    public function weeklyPlanEvaluationItem(): BelongsTo
    {
        return $this->belongsTo(WeeklyPlanEvaluationItem::class);
    }
}
