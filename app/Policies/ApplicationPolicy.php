<?php

namespace App\Policies;

use App\Models\Application;
use App\Models\User;

/**
 * Implements Section V (Authorization Matrix): only Admin and General Supervisor
 * may view/review admission applications.
 */
class ApplicationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }

    public function review(User $user, Application $application): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }
}