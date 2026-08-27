<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'guardian_id', 'student_full_name', 'student_dob', 'country', 'governorate',
        'school', 'grade_level', 'submitted_at', 'computed_age_years',
        'is_age_exception', 'exception_reason', 'exception_approved_by', 'exception_approved_at',
        'status', 'reviewed_by', 'reviewed_at', 'rejection_reason', 'student_id',
    ];

    protected $casts = [
        'student_dob' => 'date',
        'submitted_at' => 'datetime',
        'is_age_exception' => 'boolean',
        'exception_approved_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }
}
