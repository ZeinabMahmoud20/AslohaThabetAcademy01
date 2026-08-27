<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WeeklyPlanEvaluationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'weekly_plan_id', 'evaluation_item_id', 'max_score', 'display_order', 'is_required',
    ];

    protected $casts = ['is_required' => 'boolean'];

    public function weeklyPlan(): BelongsTo
    {
        return $this->belongsTo(WeeklyPlan::class);
    }

    public function evaluationItem(): BelongsTo
    {
        return $this->belongsTo(EvaluationItem::class);
    }

    public function scores(): HasMany
    {
        return $this->hasMany(StudentEvaluationItemScore::class);
    }
}
