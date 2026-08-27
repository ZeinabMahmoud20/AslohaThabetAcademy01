<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class LevelAttempt extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id', 'level_id', 'attempt_number', 'started_at', 'ended_at',
        'status', 'is_active',
    ];

    protected $casts = [
        'started_at' => 'date',
        'ended_at' => 'date',
        'is_active' => 'boolean',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function level(): BelongsTo
    {
        return $this->belongsTo(Level::class);
    }

    public function classAssignments(): HasMany
    {
        return $this->hasMany(StudentClassAssignment::class);
    }

    public function activeClassAssignment(): HasOne
    {
        return $this->hasOne(StudentClassAssignment::class)->where('is_active', true);
    }

    public function weeklyEvaluations(): HasMany
    {
        return $this->hasMany(StudentWeeklyEvaluation::class);
    }

    public function examAttempts(): HasMany
    {
        return $this->hasMany(ExamStudentAttempt::class);
    }

    public function finalResult(): HasOne
    {
        return $this->hasOne(FinalResult::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(StudentNote::class);
    }
}
