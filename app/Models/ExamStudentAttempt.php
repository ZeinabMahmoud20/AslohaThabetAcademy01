<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ExamStudentAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'exam_id', 'level_attempt_id', 'started_at', 'submitted_at',
        'score', 'percentage', 'status',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
    ];

    public function exam(): BelongsTo
    {
        return $this->belongsTo(Exam::class);
    }

    public function levelAttempt(): BelongsTo
    {
        return $this->belongsTo(LevelAttempt::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(ExamStudentAnswer::class);
    }

    // Server is authoritative: cap the effective deadline (Recommendation, Section P.7).
    public function effectiveDeadline(): \Carbon\Carbon
    {
        $byDuration = $this->started_at->copy()->addMinutes($this->exam->duration_minutes);
        return $byDuration->min($this->exam->end_at);
    }
}
