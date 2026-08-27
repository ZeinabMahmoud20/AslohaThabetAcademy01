<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Guardian-facing views: their children, each child's approved weekly
 * evaluations + attached video/homework, and final results. Read-only --
 * guardians never write anything back into the system from here.
 */
class DashboardController extends Controller
{
    // GET /portal -- list of the guardian's own children.
    public function index()
    {
        $guardian = Auth::guard('guardian')->user();

        $students = $guardian->students()
            ->with('activeLevelAttempt.level', 'activeLevelAttempt.activeClassAssignment.classRoom')
            ->get();

        return view('portal.dashboard', compact('guardian', 'students'));
    }

    // GET /portal/students/{student} -- one child's detail.
// GET /portal/students/{student} -- one child's detail.
    public function show(Student $student)
    {
        $guardian = Auth::guard('guardian')->user();

        // Scope check: a guardian may only view THEIR OWN children.
        if ($student->guardian_id !== $guardian->id) {
            throw new NotFoundHttpException();
        }

        $student->load('activeLevelAttempt.level');

        $activeAttempt = $student->activeLevelAttempt;

        $weeklyEvaluations = collect();
        $availableExams = collect();
        $examAttemptsByExamId = collect();

        if ($activeAttempt) {
            $weeklyEvaluations = $activeAttempt->weeklyEvaluations()
                ->where('status', 'approved') // only approved grades are shown to guardians
                ->with(['weeklyPlan.video', 'weeklyPlan.homework', 'itemScores.weeklyPlanEvaluationItem.evaluationItem'])
                ->get()
                ->sortBy(fn ($e) => $e->weeklyPlan->week_number);

            // Exams for this student's current level that are published and
            // within their time window -- regardless of whether already taken,
            // so the student sees "submitted" state instead of the exam vanishing.
            $availableExams = \App\Models\Exam::where('level_id', $activeAttempt->level_id)
                ->where('status', 'published')
                ->where('end_at', '>=', now())
                ->orderBy('start_at')
                ->get();

            $examAttemptsByExamId = $activeAttempt->examAttempts()
                ->whereIn('exam_id', $availableExams->pluck('id'))
                ->get()
                ->keyBy('exam_id');
        }

        $finalResult = $activeAttempt?->finalResult;

        return view('portal.student', compact(
            'student', 'activeAttempt', 'weeklyEvaluations', 'finalResult',
            'availableExams', 'examAttemptsByExamId'
        ));
    }
}