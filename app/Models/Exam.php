<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Exam extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_id', 'title', 'description', 'start_at', 'end_at', 'duration_minutes',
        'max_score', 'status', 'created_by', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('display_order');
    }

    public function attempts(): HasMany
    {
        return $this->hasMany(ExamStudentAttempt::class);
    }

    public function isOpenNow(): bool
    {
        return now()->between($this->start_at, $this->end_at) && $this->status === 'published';
    }
}
