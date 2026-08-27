<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\ClassRoom;
use App\Models\Level;
use App\Models\LevelAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Implements UC-03 (Assign Student to Class). Operates on LevelAttempt, not
 * Student directly, because the class assignment belongs to a specific attempt
 * (Section K.10/L) -- a student who repeats or is promoted gets a fresh
 * assignment against their NEW level_attempt, never a mutation of the old one.
 */
class StudentAssignmentController extends Controller
{
    // GET /admin/students?level_id=..&status=unassigned|assigned
    public function index(Request $request)
    {
        $this->authorize('viewAny', LevelAttempt::class);

        $levels = Level::where('is_active', true)->orderBy('order_index')->get();

        $attempts = LevelAttempt::query()
            ->where('is_active', true) // only the student's current, in-progress attempt
            ->with(['student', 'level', 'activeClassAssignment.classRoom'])
            ->when($request->level_id, fn ($q, $levelId) => $q->where('level_id', $levelId))
            ->when($request->status === 'unassigned', fn ($q) => $q->whereDoesntHave('activeClassAssignment'))
            ->when($request->status === 'assigned', fn ($q) => $q->whereHas('activeClassAssignment'))
            ->join('students', 'students.id', '=', 'level_attempts.student_id')
            ->orderBy('students.full_name')
            ->select('level_attempts.*')
            ->paginate(20)
            ->withQueryString();

        // Classes available for the assignment dropdown, grouped by level_id for JS-free filtering.
        $classesByLevel = ClassRoom::where('is_active', true)
            ->orderBy('name')
            ->get()
            ->groupBy('level_id');

        return view('students.index', compact('attempts', 'levels', 'classesByLevel'));
    }

    // POST /admin/students/{levelAttempt}/assign-class
    // Also used for REassignment (moving a student to a different class within
    // the same level) -- ends the current assignment and starts a new one,
    // same history-preserving pattern as class_teacher_assignments.
    public function assignClass(Request $request, LevelAttempt $levelAttempt)
    {
        $this->authorize('update', $levelAttempt);

        $data = $request->validate(['class_id' => 'required|exists:classes,id']);

        $class = ClassRoom::findOrFail($data['class_id']);

        if ($class->level_id !== $levelAttempt->level_id) {
            throw ValidationException::withMessages([
                'class_id' => 'لا يمكن تعيين فصل من مستوى مختلف عن مستوى الطالب الحالي.',
            ]);
        }

        DB::transaction(function () use ($request, $levelAttempt, $data) {
            $current = $levelAttempt->activeClassAssignment()->first();

            if ($current) {
                $current->update(['is_active' => false, 'end_date' => now()]);
            }

            $levelAttempt->classAssignments()->create([
                'class_id' => $data['class_id'],
                'start_date' => now(),
                'is_active' => true,
                'assigned_by' => $request->user()->id,
            ]);

            AuditLog::record(
                userId: $request->user()->id,
                action: $current ? 'STUDENT_CLASS_REASSIGNED' : 'STUDENT_CLASS_ASSIGNED',
                entityType: 'level_attempts',
                entityId: $levelAttempt->id,
                old: $current ? ['class_id' => $current->class_id] : null,
                new: ['class_id' => $data['class_id']],
                ip: $request->ip(),
            );
        });

        return back()->with('success', 'تم تعيين الطالب/ة للفصل بنجاح.');
    }
}