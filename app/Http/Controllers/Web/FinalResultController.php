<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\FinalResult;
use App\Models\Level;
use App\Models\LevelAttempt;
use App\Services\FinalResultService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Implements UC-10 (Calculate & Approve Final Result) using FinalResultService
 * for the 70/30 calculation and the dual-approval state machine (BR-024..030).
 * Promotion/repetition (UC-11) fires automatically inside the service once
 * Administration approval completes it -- this controller never touches that
 * logic directly.
 */
class FinalResultController extends Controller
{
    public function __construct(protected FinalResultService $service)
    {
    }

    // GET /admin/final-results?level_id=..
    // Lists active level_attempts (students currently in progress) so the
    // Admin/Teacher can see who's ready to have a result calculated.
    public function index(Request $request)
    {
        $this->authorize('viewAny', FinalResult::class);

        $user = $request->user();
        $levels = Level::where('is_active', true)->orderBy('order_index')->get();
        $levelId = $request->level_id ?? $levels->first()?->id;

        $attempts = LevelAttempt::where('is_active', true)
            ->when($levelId, fn ($q) => $q->where('level_id', $levelId))
            ->when(
                $user->isTeacher(),
                fn ($q) => $q->whereHas(
                    'activeClassAssignment.classRoom.activeTeacherAssignment',
                    fn ($q2) => $q2->where('teacher_id', $user->id)
                )
            )
            ->with(['student', 'level', 'finalResult', 'activeClassAssignment.classRoom'])
            ->join('students', 'students.id', '=', 'level_attempts.student_id')
            ->orderBy('students.full_name')
            ->select('level_attempts.*')
            ->get();

        return view('final-results.index', compact('attempts', 'levels', 'levelId'));
    }

    // POST /admin/final-results/{levelAttempt}/calculate
    public function calculate(Request $request, LevelAttempt $levelAttempt)
    {
        $this->authorize('viewAny', FinalResult::class);

        $result = $this->service->calculate($levelAttempt);

        return redirect()->route('final-results.show', $result)
            ->with('success', 'تم حساب النتيجة النهائية.');
    }

    // GET /admin/final-results/{finalResult}
    public function show(FinalResult $finalResult)
    {
        $this->authorize('view', $finalResult);

        $finalResult->load([
            'levelAttempt.student',
            'levelAttempt.level',
            'levelAttempt.activeClassAssignment.classRoom',
            'gradeScale',
        ]);

        return view('final-results.show', compact('finalResult'));
    }

    // PATCH /admin/final-results/{finalResult}/teacher-approve
    public function teacherApprove(Request $request, FinalResult $finalResult)
    {
        $this->authorize('teacherApprove', $finalResult);

        try {
            $this->service->teacherApprove($finalResult, $request->user()->id);
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first();
            return back()->with('error', $message);
        }

        return back()->with('success', 'تمت موافقة المعلم/ة على النتيجة.');
    }

    // PATCH /admin/final-results/{finalResult}/admin-approve
    // Triggers promotion/repetition automatically (UC-11) via the service.
    public function adminApprove(Request $request, FinalResult $finalResult)
    {
        $this->authorize('adminApprove', $finalResult);

        try {
            $this->service->adminApprove($finalResult, $request->user()->id);
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first();
            return back()->with('error', $message);
        }

        return back()->with('success', 'تمت الموافقة النهائية. تم تحديث حالة الطالب/ة تلقائياً (ترقية/إعادة).');
    }
}