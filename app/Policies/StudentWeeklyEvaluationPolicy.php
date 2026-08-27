<?php

namespace App\Policies;

use App\Models\ClassRoom;
use App\Models\StudentWeeklyEvaluation;
use App\Models\User;

/**
 * Implements Section V's scope-based access (BR-032): a Teacher may only grade
 * students in a class she is CURRENTLY, ACTIVELY assigned to -- checked live
 * against class_teacher_assignments, never a cached role check.
 */
class StudentWeeklyEvaluationPolicy
{
    public function gradeClass(User $user, ClassRoom $class): bool
    {
        if ($user->isAdmin() || $user->role_type === 'general_supervisor') {
            return true;
        }

        if ($user->isTeacher()) {
            return $class->activeTeacherAssignment?->teacher_id === $user->id;
        }

        return false;
    }

    public function update(User $user, StudentWeeklyEvaluation $evaluation): bool
    {
        return $this->gradeClass($user, $evaluation->classRoom);
    }

    // Reopening an Approved evaluation is a more sensitive action (BR-019) --
    // restricted to Admin/General Supervisor only, not the class's own teacher.
    public function reopen(User $user, StudentWeeklyEvaluation $evaluation): bool
    {
        return $user->isAdmin() || $user->role_type === 'general_supervisor';
    }
}