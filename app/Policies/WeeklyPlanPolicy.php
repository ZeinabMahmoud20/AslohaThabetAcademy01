<?php

namespace App\Policies;

use App\Models\User;
use App\Models\WeeklyPlan;

/**
 * Implements Section V: Admin/General Supervisor create and edit weekly plans.
 * Teachers/Supervisors can VIEW plans (they need to see the week's content and
 * required items to grade against) but never edit them.
 */
class WeeklyPlanPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // every authenticated staff role needs to browse plans
    }

    public function view(User $user, WeeklyPlan $weeklyPlan): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }

    public function update(User $user, WeeklyPlan $weeklyPlan): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }
}