<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\EvaluationItem;
use App\Models\Level;
use App\Models\WeeklyPlan;
use App\Models\WeeklyPlanEvaluationItem;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Implements UC-05 (Create Weekly Plan) and the dynamic evaluation-item
 * configuration that makes Section O possible: a weekly_plan does NOT carry
 * fixed quran_score/hadith_score-style columns -- instead each week explicitly
 * picks which evaluation_items apply and what their max_score is THIS week.
 */
class WeeklyPlanController extends Controller
{
    // GET /admin/weekly-plans?level_id=..
    public function index(Request $request)
    {
        $this->authorize('viewAny', WeeklyPlan::class);

        $levels = Level::where('is_active', true)->orderBy('order_index')->get();
        $levelId = $request->level_id ?? $levels->first()?->id;

        $plans = WeeklyPlan::query()
            ->when($levelId, fn ($q) => $q->where('level_id', $levelId))
            ->withCount('evaluationItems')
            ->with(['video', 'homework'])
            ->orderBy('week_number')
            ->get();

        return view('weekly-plans.index', compact('plans', 'levels', 'levelId'));
    }

    // GET /admin/weekly-plans/create?level_id=..
    public function create(Request $request)
    {
        $this->authorize('create', WeeklyPlan::class);

        $levels = Level::where('is_active', true)->orderBy('order_index')->get();
        $levelId = $request->level_id ?? $levels->first()?->id;

        $nextWeekNumber = WeeklyPlan::where('level_id', $levelId)->max('week_number') + 1;

        return view('weekly-plans.create', compact('levels', 'levelId', 'nextWeekNumber'));
    }

    // POST /admin/weekly-plans
    public function store(Request $request)
    {
        $this->authorize('create', WeeklyPlan::class);

        $data = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'week_number' => 'required|integer|min:1',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $plan = WeeklyPlan::create($data + [
            'status' => 'draft',
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('weekly-plans.show', $plan)
            ->with('success', 'تم إنشاء الخطة الأسبوعية. أضف الفيديو والواجب وعناصر التقييم قبل النشر.');
    }

    // GET /admin/weekly-plans/{weeklyPlan}
    public function show(WeeklyPlan $weeklyPlan)
    {
        $this->authorize('view', $weeklyPlan);

        $weeklyPlan->load(['video', 'homework', 'evaluationItems.evaluationItem']);

        $availableItems = EvaluationItem::where('is_active', true)
            ->whereNotIn('id', $weeklyPlan->evaluationItems->pluck('evaluation_item_id'))
            ->orderBy('default_order')
            ->get();

        return view('weekly-plans.show', compact('weeklyPlan', 'availableItems'));
    }

    // POST /admin/weekly-plans/{weeklyPlan}/video
    public function saveVideo(Request $request, WeeklyPlan $weeklyPlan)
    {
        $this->authorize('update', $weeklyPlan);

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'video_url' => 'nullable|url|max:500',
            'publish_date' => 'nullable|date',
        ]);

        $weeklyPlan->video()->updateOrCreate([], $data + ['status' => 'draft']);

        return back()->with('success', 'تم حفظ الفيديو.');
    }

    // POST /admin/weekly-plans/{weeklyPlan}/homework
    public function saveHomework(Request $request, WeeklyPlan $weeklyPlan)
    {
        $this->authorize('update', $weeklyPlan);

        $data = $request->validate([
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'instructions' => 'nullable|string',
            'due_date' => 'nullable|date',
        ]);

        $weeklyPlan->homework()->updateOrCreate([], $data);

        return back()->with('success', 'تم حفظ الواجب.');
    }

    // POST /admin/weekly-plans/{weeklyPlan}/evaluation-items
    // THE key action: attach an evaluation_item to THIS week with THIS week's max_score.
    public function addEvaluationItem(Request $request, WeeklyPlan $weeklyPlan)
    {
        $this->authorize('update', $weeklyPlan);

        $data = $request->validate([
            'evaluation_item_id' => 'required|exists:evaluation_items,id',
            'max_score' => 'required|numeric|min:0.01|max:999.99',
            'is_required' => 'sometimes|boolean',
        ]);

        WeeklyPlanEvaluationItem::create([
            'weekly_plan_id' => $weeklyPlan->id,
            'evaluation_item_id' => $data['evaluation_item_id'],
            'max_score' => $data['max_score'],
            'display_order' => $weeklyPlan->evaluationItems()->count() + 1,
            'is_required' => $request->boolean('is_required', true),
        ]);

        return back()->with('success', 'تمت إضافة عنصر التقييم للخطة.');
    }

    // DELETE /admin/weekly-plans/{weeklyPlan}/evaluation-items/{item}
    public function removeEvaluationItem(WeeklyPlan $weeklyPlan, WeeklyPlanEvaluationItem $item)
    {
        $this->authorize('update', $weeklyPlan);

        if ($weeklyPlan->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف عناصر تقييم من خطة منشورة.');
        }

        $item->delete();

        return back()->with('success', 'تم حذف عنصر التقييم.');
    }

    // PATCH /admin/weekly-plans/{weeklyPlan}/publish
    public function publish(Request $request, WeeklyPlan $weeklyPlan)
    {
        $this->authorize('update', $weeklyPlan);

        if ($weeklyPlan->evaluationItems()->count() === 0) {
            throw ValidationException::withMessages([
                'items' => 'لا يمكن نشر خطة أسبوعية بدون عناصر تقييم واحدة على الأقل.',
            ]);
        }

        $weeklyPlan->update(['status' => 'published']);

        return back()->with('success', 'تم نشر الخطة الأسبوعية.');
    }
}