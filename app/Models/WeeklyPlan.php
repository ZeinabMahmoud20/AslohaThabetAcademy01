<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class WeeklyPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id', 'week_number', 'title', 'description', 'start_date', 'end_date',
        'status', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function video(): HasOne
    {
        return $this->hasOne(Video::class);
    }

    public function homework(): HasOne
    {
        return $this->hasOne(Homework::class);
    }

    public function evaluationItems(): HasMany
    {
        return $this->hasMany(WeeklyPlanEvaluationItem::class);
    }

    public function studentEvaluations(): HasMany
    {
        return $this->hasMany(StudentWeeklyEvaluation::class);
    }

    // FR-024: the max possible score for the week = sum of configured items' max scores.
    public function computeMaxScore(): float
    {
        return (float) $this->evaluationItems()->sum('max_score');
    }
}
