<?php

namespace App\Policies;

use App\Models\LevelAttempt;
use App\Models\User;

/**
 * Implements Section V: only Admin/General Supervisor assign or reassign
 * a student's class (UC-03). Teachers/Supervisors never touch this --
 * it's outside their scope even for their own class's students.
 */
class LevelAttemptPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }

    public function update(User $user, LevelAttempt $levelAttempt): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }
}