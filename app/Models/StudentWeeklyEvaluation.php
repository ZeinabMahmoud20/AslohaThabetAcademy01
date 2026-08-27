<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StudentWeeklyEvaluation extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_attempt_id', 'weekly_plan_id', 'class_id', 'total_score', 'max_score',
        'notes', 'status', 'approved_by', 'approved_at',
    ];

    protected $casts = ['approved_at' => 'datetime'];

    public function levelAttempt(): BelongsTo
    {
        return $this->belongsTo(LevelAttempt::class);
    }

    public function weeklyPlan(): BelongsTo
    {
        return $this->belongsTo(WeeklyPlan::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function itemScores(): HasMany
    {
        return $this->hasMany(StudentEvaluationItemScore::class);
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }
}
