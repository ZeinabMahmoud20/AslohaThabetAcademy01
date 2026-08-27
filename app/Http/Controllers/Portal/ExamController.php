<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\ExamStudentAttempt;
use App\Models\Student;
use App\Services\ExamGradingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Guardian-portal side of UC-08/UC-09: the guardian's session takes the exam
 * on behalf of the student they're logged in for. Every action re-checks that
 * the student actually belongs to the logged-in guardian (BR-032-style scope
 * check, just for guardians instead of staff).
 */
class ExamController extends Controller
{
    public function __construct(protected ExamGradingService $service)
    {
    }

    protected function authorizeStudent(Student $student): void
    {
        $guardian = Auth::guard('guardian')->user();

        if ($student->guardian_id !== $guardian->id) {
            throw new NotFoundHttpException();
        }
    }

    // POST /portal/students/{student}/exams/{exam}/start
    public function start(Student $student, Exam $exam)
    {
        $this->authorizeStudent($student);

        $attempt = $student->activeLevelAttempt;

        if (! $attempt) {
            return back()->with('error', 'لا يوجد مستوى نشط لهذا الطالب/ة حالياً.');
        }

        try {
            $this->service->startOrResume($exam, $attempt);
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first();
            return back()->with('error', $message);
        }

        return redirect()->route('portal.exams.take', [$student, $exam]);
    }

    // GET /portal/students/{student}/exams/{exam}/take
    public function take(Student $student, Exam $exam)
    {
        $this->authorizeStudent($student);

        $attempt = $student->activeLevelAttempt;
        $examAttempt = ExamStudentAttempt::where('exam_id', $exam->id)
            ->where('level_attempt_id', $attempt?->id)
            ->first();

        if (! $examAttempt || $examAttempt->status === 'not_started') {
            return redirect()->route('portal.students.show', $student)
                ->with('error', 'يجب بدء الامتحان أولاً.');
        }

        if (in_array($examAttempt->status, ['submitted', 'graded'], true)) {
            return redirect()->route('portal.exams.result', [$student, $exam]);
        }

        $exam->load('questions.options');

        return view('portal.exam-take', compact('student', 'exam', 'examAttempt'));
    }

    // POST /portal/students/{student}/exams/{exam}/submit
    public function submit(Request $request, Student $student, Exam $exam)
    {
        $this->authorizeStudent($student);

        $attempt = $student->activeLevelAttempt;
        $examAttempt = ExamStudentAttempt::where('exam_id', $exam->id)
            ->where('level_attempt_id', $attempt?->id)
            ->firstOrFail();

        // Input shape: answers[{question_id}] = {selected_option_id}
        $answers = array_map('intval', $request->input('answers', []));

        try {
            $this->service->submit($examAttempt, $answers);
        } catch (ValidationException $e) {
            $message = collect($e->errors())->flatten()->first();
            return back()->with('error', $message);
        }

        return redirect()->route('portal.exams.result', [$student, $exam]);
    }

    // GET /portal/students/{student}/exams/{exam}/result
    public function result(Student $student, Exam $exam)
    {
        $this->authorizeStudent($student);

        $attempt = $student->activeLevelAttempt;
        $examAttempt = ExamStudentAttempt::where('exam_id', $exam->id)
            ->where('level_attempt_id', $attempt?->id)
            ->with('answers.question', 'answers.selectedOption')
            ->firstOrFail();

        return view('portal.exam-result', compact('student', 'exam', 'examAttempt'));
    }
}