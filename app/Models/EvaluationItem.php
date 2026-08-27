<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'default_max_score', 'default_order', 'is_attendance_type', 'is_active',
    ];

    protected $casts = [
        'is_attendance_type' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function weeklyPlanUsages(): HasMany
    {
        return $this->hasMany(WeeklyPlanEvaluationItem::class);
    }
}
