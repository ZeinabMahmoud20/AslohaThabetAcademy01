<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Implements class management + the history-preserving teacher/supervisor
 * assignment workflow (Section K.8/K.9, BR-009). Never writes teacher_id
 * directly on `classes` -- every change is a new row in the assignment tables.
 */
class ClassController extends Controller
{
// GET /admin/classes?level_id=..
    // Admin/General Supervisor see every class. A Teacher or Supervisor only
    // sees the classes she is CURRENTLY, ACTIVELY assigned to (BR-032 scope) --
    // never the full system-wide list, even though the route itself is
    // reachable by her role.
    public function index(Request $request)
    {
        $this->authorize('viewAny', ClassRoom::class);

        $user = $request->user();
        $levels = Level::where('is_active', true)->orderBy('order_index')->get();

        $classes = ClassRoom::query()
            ->with(['level', 'activeTeacherAssignment.teacher', 'activeSupervisorAssignments.supervisor'])
            ->when($request->level_id, fn ($q, $levelId) => $q->where('level_id', $levelId))
            ->when(
                $user->isTeacher(),
                fn ($q) => $q->whereHas(
                    'activeTeacherAssignment',
                    fn ($q2) => $q2->where('teacher_id', $user->id)
                )
            )
            ->when(
                $user->isSupervisor(),
                fn ($q) => $q->whereHas(
                    'activeSupervisorAssignments',
                    fn ($q2) => $q2->where('supervisor_id', $user->id)
                )
            )
            ->orderBy('level_id')
            ->orderBy('name')
            ->get();

        return view('classes.index', compact('classes', 'levels'));
    }

    // GET /admin/classes/create
    public function create()
    {
        $this->authorize('create', ClassRoom::class);

        $levels = Level::where('is_active', true)->orderBy('order_index')->get();

        return view('classes.create', compact('levels'));
    }

    // POST /admin/classes
    public function store(Request $request)
    {
        $this->authorize('create', ClassRoom::class);

        $data = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'name' => 'required|string|max:80',
        ]);

        $class = ClassRoom::create($data + ['is_active' => true]);

        return redirect()->route('classes.show', $class)
            ->with('success', 'تم إنشاء الفصل بنجاح. الخطوة التالية: عيّن معلم/ة.');
    }

    // GET /admin/classes/{class}
    public function show(ClassRoom $class)
    {
        $this->authorize('view', $class);

        $class->load([
            'level',
            'activeTeacherAssignment.teacher',
            'activeSupervisorAssignments.supervisor',
            'teacherAssignments' => fn ($q) => $q->with('teacher', 'assignedBy')->orderByDesc('start_date'),
            'supervisorAssignments' => fn ($q) => $q->with('supervisor')->orderByDesc('start_date'),
        ]);

        $availableTeachers = User::where('role_type', 'teacher')->where('is_active', true)
            ->orderBy('full_name')->get();

        $availableSupervisors = User::where('role_type', 'supervisor')->where('is_active', true)
            ->orderBy('full_name')->get();

        return view('classes.show', compact('class', 'availableTeachers', 'availableSupervisors'));
    }

    // POST /admin/classes/{class}/assign-teacher
    // Ends the currently active teacher assignment (if any) and starts a new one.
    // The "one active teacher per class" rule is ALSO enforced at the DB level
    // (generated column + unique index, see migration 000008) -- this transaction
    // is what keeps the two in sync, not what enforces the rule by itself.
    public function assignTeacher(Request $request, ClassRoom $class)
    {
        $this->authorize('update', $class);

        $data = $request->validate([
            'teacher_id' => 'required|exists:users,id',
            'reason' => 'nullable|string|max:300', // optional, per PO decision
        ]);

        DB::transaction(function () use ($request, $class, $data) {
            $current = $class->activeTeacherAssignment()->first();

            if ($current) {
                $current->update(['is_active' => false, 'end_date' => now()]);
            }

            $class->teacherAssignments()->create([
                'teacher_id' => $data['teacher_id'],
                'start_date' => now(),
                'is_active' => true,
                'reason' => $data['reason'] ?? null,
                'assigned_by' => $request->user()->id,
            ]);

            AuditLog::record(
                userId: $request->user()->id,
                action: 'TEACHER_ASSIGNED',
                entityType: 'classes',
                entityId: $class->id,
                old: $current ? ['teacher_id' => $current->teacher_id] : null,
                new: ['teacher_id' => $data['teacher_id']],
                ip: $request->ip(),
            );
        });

        return back()->with('success', 'تم تعيين المعلم/ة بنجاح.');
    }

    // POST /admin/classes/{class}/assign-supervisor
    // Supervisors are additive (a class may have more than one active supervisor);
    // this does NOT deactivate existing assignments.
    public function assignSupervisor(Request $request, ClassRoom $class)
    {
        $this->authorize('update', $class);

        $data = $request->validate([
            'supervisor_id' => 'required|exists:users,id',
        ]);

        $alreadyActive = $class->activeSupervisorAssignments()
            ->where('supervisor_id', $data['supervisor_id'])->exists();

        if ($alreadyActive) {
            return back()->with('error', 'هذا المشرف معيّن بالفعل على هذا الفصل.');
        }

        $class->supervisorAssignments()->create([
            'supervisor_id' => $data['supervisor_id'],
            'start_date' => now(),
            'is_active' => true,
            'assigned_by' => $request->user()->id,
        ]);

        AuditLog::record(
            userId: $request->user()->id,
            action: 'SUPERVISOR_ASSIGNED',
            entityType: 'classes',
            entityId: $class->id,
            new: ['supervisor_id' => $data['supervisor_id']],
            ip: $request->ip(),
        );

        return back()->with('success', 'تم تعيين المشرف/ة بنجاح.');
    }

    // PATCH /admin/classes/{class}/supervisors/{assignment}/end
    public function endSupervisor(Request $request, ClassRoom $class, int $assignment)
    {
        $this->authorize('update', $class);

        $supervisorAssignment = $class->supervisorAssignments()->findOrFail($assignment);
        $supervisorAssignment->update(['is_active' => false, 'end_date' => now()]);

        AuditLog::record(
            userId: $request->user()->id,
            action: 'SUPERVISOR_REMOVED',
            entityType: 'classes',
            entityId: $class->id,
            old: ['supervisor_id' => $supervisorAssignment->supervisor_id],
            ip: $request->ip(),
        );

        return back()->with('success', 'تم إنهاء إشراف هذا المشرف/ة على الفصل.');
    }
}