<?php

namespace App\Policies;

use App\Models\ClassRoom;
use App\Models\User;

/**
 * Implements Section V: Admin and General Supervisor manage classes and
 * assignments. Teachers/Supervisors can only VIEW classes they're currently,
 * actively assigned to (BR-032 scope-based access) -- see the `view` method.
 */
class ClassRoomPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor'
            || $user->isTeacher() || $user->isSupervisor();
    }

    public function view(User $user, ClassRoom $class): bool
    {
        if ($user->isAdmin() || $user->role_type === 'general_supervisor') {
            return true;
        }

        if ($user->isTeacher()) {
            return $class->activeTeacherAssignment?->teacher_id === $user->id;
        }

        if ($user->isSupervisor()) {
            return $class->activeSupervisorAssignments()
                ->where('supervisor_id', $user->id)->exists();
        }

        return false;
    }

    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }

    public function update(User $user, ClassRoom $class): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }
}