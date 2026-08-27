<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\ClassRoom;
use App\Models\Exam;
use App\Models\FinalResult;
use App\Models\LevelAttempt;
use App\Models\User;
use App\Models\WeeklyPlan;
use Illuminate\Support\Facades\Auth;

/**
 * Admin landing screen after login. Content is role-scoped (Section V):
 * Admin/General Supervisor get an organization-wide snapshot; a Teacher only
 * sees her own active classes; a class Supervisor only sees classes she
 * currently, actively supervises. Never the same dashboard for every role.
 */
class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if ($user->isAdmin() || $user->role_type === 'general_supervisor') {
            return $this->adminDashboard();
        }

        if ($user->isTeacher()) {
            return $this->teacherDashboard($user);
        }

        if ($user->isSupervisor()) {
            return $this->supervisorDashboard($user);
        }

        // Fallback for any role without a dedicated dashboard yet.
        return view('dashboard-empty');
    }

    protected function adminDashboard()
    {
        $stats = [
            'pending_applications' => Application::where('status', 'pending_review')->count(),
            'active_students' => LevelAttempt::where('is_active', true)->count(),
            'unassigned_students' => LevelAttempt::where('is_active', true)
                ->whereDoesntHave('activeClassAssignment')->count(),
            'active_classes' => ClassRoom::where('is_active', true)->count(),
            'staff_count' => User::whereIn('role_type', ['teacher', 'supervisor'])
                ->where('is_active', true)->count(),
            'published_exams' => Exam::where('status', 'published')
                ->where('end_at', '>=', now())->count(),
        ];

        $recentApplications = Application::where('status', 'pending_review')
            ->with('guardian')
            ->latest('submitted_at')
            ->limit(5)
            ->get();

        return view('dashboard-admin', compact('stats', 'recentApplications'));
    }

    protected function teacherDashboard(User $user)
    {
        $classes = ClassRoom::where('is_active', true)
            ->whereHas('activeTeacherAssignment', fn ($q) => $q->where('teacher_id', $user->id))
            ->with('level')
            ->withCount(['studentClassAssignments as active_students_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        // For each class, find the latest published week for its level -- the
        // one the teacher most likely needs to grade next.
        foreach ($classes as $class) {
            $class->latestPublishedPlan = WeeklyPlan::where('level_id', $class->level_id)
                ->where('status', 'published')
                ->orderByDesc('week_number')
                ->first();
        }

        $pendingApprovals = FinalResult::where('status', 'calculated')
            ->whereHas(
                'levelAttempt.activeClassAssignment.classRoom.activeTeacherAssignment',
                fn ($q) => $q->where('teacher_id', $user->id)
            )
            ->with('levelAttempt.student')
            ->get();

        return view('dashboard-teacher', compact('classes', 'pendingApprovals'));
    }

    protected function supervisorDashboard(User $user)
    {
        $classes = ClassRoom::where('is_active', true)
            ->whereHas('activeSupervisorAssignments', fn ($q) => $q->where('supervisor_id', $user->id))
            ->with('level', 'activeTeacherAssignment.teacher')
            ->withCount(['studentClassAssignments as active_students_count' => fn ($q) => $q->where('is_active', true)])
            ->orderBy('name')
            ->get();

        return view('dashboard-supervisor', compact('classes'));
    }
}