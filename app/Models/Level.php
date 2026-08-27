<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Level extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'order_index', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function classes(): HasMany
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function weeklyPlans(): HasMany
    {
        return $this->hasMany(WeeklyPlan::class);
    }

    public function exams(): HasMany
    {
        return $this->hasMany(Exam::class);
    }

    public function levelAttempts(): HasMany
    {
        return $this->hasMany(LevelAttempt::class);
    }

    // Used by the promotion workflow (BR-006/FR-047) to find "the next level".
    public function next(): ?Level
    {
        return static::where('order_index', $this->order_index + 1)
            ->where('is_active', true)
            ->first();
    }
}
