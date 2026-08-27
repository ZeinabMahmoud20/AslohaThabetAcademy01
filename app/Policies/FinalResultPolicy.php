<?php

namespace App\Policies;

use App\Models\FinalResult;
use App\Models\User;

/**
 * Implements Section V + the dual-approval workflow (BR-029): a class's
 * ACTIVE teacher may give Teacher approval only for their own students;
 * Admin/General Supervisor may view everything and give Administration
 * approval (the second, final step).
 */
class FinalResultPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor' || $user->isTeacher();
    }

    public function view(User $user, FinalResult $finalResult): bool
    {
        if ($user->isAdmin() || $user->role_type === 'general_supervisor') {
            return true;
        }

        if ($user->isTeacher()) {
            return $this->isActiveTeacherOf($user, $finalResult);
        }

        return false;
    }

    public function teacherApprove(User $user, FinalResult $finalResult): bool
    {
        return $user->isTeacher() && $this->isActiveTeacherOf($user, $finalResult);
    }

    public function adminApprove(User $user, FinalResult $finalResult): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }

    protected function isActiveTeacherOf(User $user, FinalResult $finalResult): bool
    {
        $classAssignment = $finalResult->levelAttempt->activeClassAssignment
            ?? $finalResult->levelAttempt->classAssignments()->latest('start_date')->first();

        return $classAssignment?->classRoom?->activeTeacherAssignment?->teacher_id === $user->id;
    }
}