<?php

namespace App\Policies;

use App\Models\User;

/**
 * Implements Section V: only Admin may manage staff accounts (teachers/supervisors).
 * General Supervisor can view but not create/edit (adjust here if the PO decides
 * otherwise -- see the open questions in Section Z.2).
 */
class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }

    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    public function update(User $user, User $target): bool
    {
        return $user->isAdmin();
    }
}