<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name', 'email', 'phone_number', 'password', 'role_type', 'is_active',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
        'password' => 'hashed',
    ];

    // ---- Role helpers -------------------------------------------------

    public function isAdmin(): bool
    {
        return $this->role_type === 'admin';
    }

    public function isTeacher(): bool
    {
        return $this->role_type === 'teacher';
    }

    public function isSupervisor(): bool
    {
        return $this->role_type === 'supervisor';
    }

    // ---- Relationships --------------------------------------------------

    public function studentProfile(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    public function telegramAccount(): HasOne
    {
        return $this->hasOne(TelegramAccount::class);
    }

    public function classTeacherAssignments(): HasMany
    {
        return $this->hasMany(ClassTeacherAssignment::class, 'teacher_id');
    }

    public function classSupervisorAssignments(): HasMany
    {
        return $this->hasMany(ClassSupervisorAssignment::class, 'supervisor_id');
    }

    public function appNotifications(): HasMany
    {
        return $this->hasMany(AppNotification::class, 'recipient_user_id');
    }

    public function auditLogs(): HasMany
    {
        return $this->hasMany(AuditLog::class);
    }

    // Scope: only the classes this teacher is CURRENTLY, ACTIVELY assigned to.
    // This is what BR-032 ("scope-based access") is built on.
    public function activeClassesAsTeacher()
    {
        return ClassRoom::whereHas('activeTeacherAssignment', function ($q) {
            $q->where('teacher_id', $this->id);
        });
    }
}
