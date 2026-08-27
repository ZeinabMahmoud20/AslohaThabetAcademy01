<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FinalResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_attempt_id', 'weekly_component', 'exam_component', 'final_score',
        'pass_fail', 'grade_label', 'grade_scale_id', 'status',
        'teacher_approved_by', 'teacher_approved_at',
        'admin_approved_by', 'admin_approved_at', 'calculated_at',
    ];

    protected $casts = [
        'teacher_approved_at' => 'datetime',
        'admin_approved_at' => 'datetime',
        'calculated_at' => 'datetime',
    ];

    public function levelAttempt(): BelongsTo
    {
        return $this->belongsTo(LevelAttempt::class);
    }

    public function gradeScale(): BelongsTo
    {
        return $this->belongsTo(GradeScale::class);
    }
}
