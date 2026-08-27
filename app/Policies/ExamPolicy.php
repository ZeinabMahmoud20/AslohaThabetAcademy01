<?php

namespace App\Policies;

use App\Models\Exam;
use App\Models\User;

/**
 * Implements Section V: Admin/General Supervisor author and publish exams.
 * Teachers/Supervisors can view (they need to know what's coming) but not edit.
 */
class ExamPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Exam $exam): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }

    public function update(User $user, Exam $exam): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }
}