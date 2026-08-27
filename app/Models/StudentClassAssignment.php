<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentClassAssignment extends Model
{
    use HasFactory;

    protected $fillable = [
        'level_attempt_id', 'class_id', 'start_date', 'end_date', 'is_active', 'assigned_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function levelAttempt(): BelongsTo
    {
        return $this->belongsTo(LevelAttempt::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}
