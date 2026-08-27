<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Homework extends Model
{
    use HasFactory;

        protected $table = 'homeworks';

    protected $fillable = ['weekly_plan_id', 'title', 'description', 'instructions', 'due_date'];

    protected $casts = ['due_date' => 'date'];

    public function weeklyPlan(): BelongsTo
    {
        return $this->belongsTo(WeeklyPlan::class);
    }
}
