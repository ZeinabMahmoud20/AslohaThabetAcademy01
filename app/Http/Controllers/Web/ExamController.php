<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamQuestion;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Implements UC-07 (Author Exam): create exam shell, add questions with
 * options, review, and publish. Never edits a question once the exam is
 * published (Section P) -- publishing freezes the content.
 */
class ExamController extends Controller
{
    // GET /admin/exams?level_id=..
    public function index(Request $request)
    {
        $this->authorize('viewAny', Exam::class);

        $levels = Level::where('is_active', true)->orderBy('order_index')->get();
        $levelId = $request->level_id ?? $levels->first()?->id;

        $exams = Exam::query()
            ->when($levelId, fn ($q) => $q->where('level_id', $levelId))
            ->withCount(['questions', 'attempts'])
            ->orderByDesc('start_at')
            ->get();

        return view('exams.index', compact('exams', 'levels', 'levelId'));
    }

    // GET /admin/exams/create?level_id=..
    public function create(Request $request)
    {
        $this->authorize('create', Exam::class);

        $levels = Level::where('is_active', true)->orderBy('order_index')->get();
        $levelId = $request->level_id ?? $levels->first()?->id;

        return view('exams.create', compact('levels', 'levelId'));
    }

    // POST /admin/exams
    public function store(Request $request)
    {
        $this->authorize('create', Exam::class);

        $data = $request->validate([
            'level_id' => 'required|exists:levels,id',
            'title' => 'required|string|max:200',
            'description' => 'nullable|string',
            'start_at' => 'required|date',
            'end_at' => 'required|date|after:start_at',
            'duration_minutes' => 'required|integer|min:1|max:600',
        ]);

        $exam = Exam::create($data + [
            'status' => 'draft',
            'max_score' => 0,
            'created_by' => $request->user()->id,
        ]);

        return redirect()->route('exams.show', $exam)
            ->with('success', 'تم إنشاء الامتحان. أضف الأسئلة قبل النشر.');
    }

    // GET /admin/exams/{exam}
    public function show(Exam $exam)
    {
        $this->authorize('view', $exam);

        $exam->load('questions.options');

        return view('exams.show', compact('exam'));
    }

    // POST /admin/exams/{exam}/questions
    // Accepts the question + its options (2-6) in a single submission, and each
    // option's "is_correct" checkbox -- multiple_choice can only have ONE
    // correct option, true_false always has exactly 2 options.
    public function addQuestion(Request $request, Exam $exam)
    {
        $this->authorize('update', $exam);

        if ($exam->status !== 'draft') {
            return back()->with('error', 'لا يمكن إضافة أسئلة لامتحان منشور.');
        }

        $data = $request->validate([
            'question_text' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false',
            'score' => 'required|numeric|min:0.01|max:999.99',
            'options' => 'required|array|min:2|max:6',
            'options.*.option_text' => 'required|string|max:500',
            'correct_option' => 'required|integer|min:0',
        ]);

        if ($data['correct_option'] >= count($data['options'])) {
            throw ValidationException::withMessages(['correct_option' => 'يجب اختيار إجابة صحيحة واحدة.']);
        }

        DB::transaction(function () use ($exam, $data) {
            $question = $exam->questions()->create([
                'question_text' => $data['question_text'],
                'question_type' => $data['question_type'],
                'score' => $data['score'],
                'display_order' => $exam->questions()->count() + 1,
            ]);

            foreach ($data['options'] as $index => $option) {
                $question->options()->create([
                    'option_text' => $option['option_text'],
                    'is_correct' => $index === (int) $data['correct_option'],
                    'display_order' => $index + 1,
                ]);
            }

            // Keep the exam's max_score in sync with the sum of its questions.
            $exam->update(['max_score' => $exam->questions()->sum('score')]);
        });

        return back()->with('success', 'تمت إضافة السؤال.');
    }

    // DELETE /admin/exams/{exam}/questions/{question}
    public function removeQuestion(Exam $exam, ExamQuestion $question)
    {
        $this->authorize('update', $exam);

        if ($exam->status !== 'draft') {
            return back()->with('error', 'لا يمكن حذف أسئلة من امتحان منشور.');
        }

        $question->delete();
        $exam->update(['max_score' => $exam->questions()->sum('score')]);

        return back()->with('success', 'تم حذف السؤال.');
    }

  // PATCH /admin/exams/{exam}/publish
    public function publish(Exam $exam)
    {
        $this->authorize('update', $exam);

        if ($exam->questions()->count() === 0) {
            throw ValidationException::withMessages(['questions' => 'لا يمكن نشر امتحان بدون أسئلة.']);
        }

        $exam->update(['status' => 'published']);

        return back()->with('success', 'تم نشر الامتحان. سيظهر للطلاب خلال الفترة المحددة.');
    }

    // PATCH /admin/exams/{exam}/unpublish
    // Reverts a published exam back to draft so the Admin can fix a mistake --
    // only allowed if NO student has started an attempt yet, otherwise a
    // student's already-graded answers could be silently invalidated.
    public function unpublish(Exam $exam)
    {
        $this->authorize('update', $exam);

        if ($exam->attempts()->count() > 0) {
            return back()->with('error', 'لا يمكن إلغاء نشر امتحان دخله طلاب بالفعل.');
        }

        $exam->update(['status' => 'draft']);

        return back()->with('success', 'تم إلغاء نشر الامتحان. يمكنك الآن تعديل الأسئلة.');
    }

    // GET /admin/exams/{exam}/results// GET /admin/exams/{exam}/results
    // Admin/General Supervisor see every student's result. A Teacher only sees
    // results for students CURRENTLY, ACTIVELY assigned to a class she teaches
    // (BR-032 scope) -- an exam is level-wide, so without this filter she'd see
    // every other teacher's students too.
    public function results(Request $request, Exam $exam)
    {
        $this->authorize('view', $exam);

        $user = $request->user();

        $attempts = $exam->attempts()
            ->with('levelAttempt.student', 'levelAttempt.activeClassAssignment.classRoom')
            ->where('status', 'graded')
            ->when(
                $user->isTeacher(),
                fn ($q) => $q->whereHas(
                    'levelAttempt.activeClassAssignment.classRoom.activeTeacherAssignment',
                    fn ($q2) => $q2->where('teacher_id', $user->id)
                )
            )
            ->orderByDesc('score')
            ->get();

        return view('exams.results', compact('exam', 'attempts'));
    }

}