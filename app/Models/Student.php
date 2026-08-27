<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'guardian_id', 'application_id', 'full_name', 'dob', 'country',
        'governorate', 'school', 'grade_level', 'academy_student_code', 'status',
    ];

    protected $casts = [
        'dob' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function guardian(): BelongsTo
    {
        return $this->belongsTo(Guardian::class);
    }

    public function application(): BelongsTo
    {
        return $this->belongsTo(Application::class);
    }

    public function levelAttempts(): HasMany
    {
        return $this->hasMany(LevelAttempt::class);
    }

    // The single active level_attempt, enforced at the DB level (see migration).
    public function activeLevelAttempt(): HasOne
    {
        return $this->hasOne(LevelAttempt::class)->where('is_active', true);
    }
}
