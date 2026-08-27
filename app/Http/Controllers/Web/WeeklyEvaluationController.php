<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\ClassRoom;
use App\Models\StudentWeeklyEvaluation;
use App\Models\WeeklyPlan;
use App\Services\WeeklyEvaluationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\ValidationException;

/**
 * Implements UC-06 (Weekly Evaluation Entry) using WeeklyEvaluationService
 * for every score write, so the max-score validation, header rollup, and
 * audit logging (FR-025..030) all happen in exactly one place.
 */
class WeeklyEvaluationController extends Controller
{
    public function __construct(protected WeeklyEvaluationService $service)
    {
    }

    // GET /admin/weekly-evaluations -- pick a class + published week to grade.
    public function index(Request $request)
    {
        $user = $request->user();

        $classes = ClassRoom::where('is_active', true)
            ->with('level', 'activeTeacherAssignment.teacher')
            ->when(
                $user->isTeacher(),
                fn ($q) => $q->whereHas(
                    'activeTeacherAssignment',
                    fn ($q2) => $q2->where('teacher_id', $user->id)
                )
            )
            ->orderBy('name')
            ->get();

        $publishedPlansByLevel = WeeklyPlan::where('status', 'published')
            ->orderByDesc('week_number')
            ->get()
            ->groupBy('level_id');

        return view('weekly-evaluations.index', compact('classes', 'publishedPlansByLevel'));
    }

    // GET /admin/weekly-evaluations/grade?class_id=..&weekly_plan_id=..
    public function grade(Request $request)
    {
        $data = $request->validate([
            'class_id' => 'required|exists:classes,id',
            'weekly_plan_id' => 'required|exists:weekly_plans,id',
        ]);

        $class = ClassRoom::with('level')->findOrFail($data['class_id']);
        $weeklyPlan = WeeklyPlan::with('evaluationItems.evaluationItem')->findOrFail($data['weekly_plan_id']);

        Gate::authorize('gradeClass', [StudentWeeklyEvaluation::class, $class]);

        if ($weeklyPlan->status !== 'published') {
            return back()->with('error', 'لا يمكن التصحيح على خطة أسبوعية غير منشورة.');
        }

        if ($weeklyPlan->level_id !== $class->level_id) {
            return back()->with('error', 'هذه الخطة الأسبوعية لا تتبع مستوى هذا الفصل.');
        }

        // Every student CURRENTLY, ACTIVELY assigned to this class (Section K.10).
        $activeAttempts = $class->studentClassAssignments()
            ->where('is_active', true)
            ->with('levelAttempt.student')
            ->get()
            ->pluck('levelAttempt');

        // Ensure a draft header row exists for every student before rendering
        // the grid, so the teacher always has something to fill in (Section O.1).
        foreach ($activeAttempts as $attempt) {
            StudentWeeklyEvaluation::firstOrCreate(
                ['level_attempt_id' => $attempt->id, 'weekly_plan_id' => $weeklyPlan->id],
                ['class_id' => $class->id, 'status' => 'draft']
            );
        }

        $evaluations = StudentWeeklyEvaluation::where('weekly_plan_id', $weeklyPlan->id)
            ->whereIn('level_attempt_id', $activeAttempts->pluck('id'))
            ->with(['levelAttempt.student', 'itemScores'])
            ->get()
            ->keyBy('level_attempt_id')
            ->sortBy(fn ($e) => $e->levelAttempt->student->full_name);

        return view('weekly-evaluations.grade', compact('class', 'weeklyPlan', 'evaluations'));
    }

    // POST /admin/weekly-evaluations/grade -- batch-save the whole grid at once.
    // Expected input shape: scores[{level_attempt_id}][{weekly_plan_evaluation_item_id}] = value
    public function saveScores(Request $request)
    {
        $data = $request->validate([
            'weekly_plan_id' => 'required|exists:weekly_plans,id',
            'class_id' => 'required|exists:classes,id',
            'scores' => 'array',
        ]);

        $class = ClassRoom::findOrFail($data['class_id']);
        Gate::authorize('gradeClass', [StudentWeeklyEvaluation::class, $class]);

        $weeklyPlan = WeeklyPlan::with('evaluationItems')->findOrFail($data['weekly_plan_id']);
        $planItemsById = $weeklyPlan->evaluationItems->keyBy('id');

        $errors = [];

        foreach ($data['scores'] ?? [] as $levelAttemptId => $itemScores) {
            $evaluation = StudentWeeklyEvaluation::where('level_attempt_id', $levelAttemptId)
                ->where('weekly_plan_id', $weeklyPlan->id)
                ->first();

            if (! $evaluation) {
                continue;
            }

            foreach ($itemScores as $planItemId => $rawScore) {
                $planItem = $planItemsById->get($planItemId);

                if (! $planItem || $rawScore === '' || $rawScore === null) {
                    continue;
                }

                try {
                    $this->service->setItemScore(
                        evaluation: $evaluation,
                        planItem: $planItem,
                        score: (float) $rawScore,
                        actingUserId: $request->user()->id,
                    );
                } catch (ValidationException $e) {
                    $firstMessage = collect($e->errors())->flatten()->first();
                    $errors[] = $evaluation->levelAttempt->student->full_name . ': ' . $firstMessage;
                }
            }
        }

        if ($errors) {
            return back()->with('error', implode(' | ', array_unique($errors)));
        }

        return back()->with('success', 'تم حفظ الدرجات بنجاح.');
    }

    // PATCH /admin/weekly-evaluations/{evaluation}/approve
    public function approve(Request $request, StudentWeeklyEvaluation $evaluation)
    {
        $this->authorize('update', $evaluation);

        try {
            $this->service->approve($evaluation, $request->user()->id);
        } catch (ValidationException $e) {
            $firstMessage = collect($e->errors())->flatten()->first();
            return back()->with('error', $firstMessage);
        }

        return back()->with('success', 'تم اعتماد تقييم الطالب/ة.');
    }

    // PATCH /admin/weekly-evaluations/{evaluation}/reopen
    public function reopen(Request $request, StudentWeeklyEvaluation $evaluation)
    {
        $this->authorize('reopen', $evaluation);

        $this->service->reopen($evaluation, $request->user()->id);

        return back()->with('success', 'تم إعادة فتح التقييم للتعديل.');
    }
}