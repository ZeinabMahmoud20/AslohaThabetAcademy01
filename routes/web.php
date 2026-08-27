<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Web\ApplicationController;
use App\Http\Controllers\Web\ClassController;
use App\Http\Controllers\Web\StaffController;
use App\Http\Controllers\Web\StudentAssignmentController;
use App\Http\Controllers\Web\WeeklyEvaluationController;
use App\Http\Controllers\Web\WeeklyPlanController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Portal\AuthController as PortalAuthController;
use App\Http\Controllers\Portal\DashboardController as PortalDashboardController;
use App\Http\Controllers\Web\ExamController;
use App\Http\Controllers\Portal\ExamController as PortalExamController;
use App\Http\Controllers\Web\FinalResultController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('landing');
});

Route::get('/dashboard', [\App\Http\Controllers\Web\DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

// ---- Breeze's own profile routes (كانت موجودة أصلاً قبل الدمج) ----
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});



// ---- Our routes: applications workflow ----

// Public -- no auth (FR-001): guardian-facing application form.
Route::get('/apply', [ApplicationController::class, 'create'])->name('applications.create');
Route::post('/apply', [ApplicationController::class, 'store'])->name('applications.store');
Route::get('/apply/thank-you', [ApplicationController::class, 'thankYou'])->name('applications.thank-you');

// Admin panel -- requires login (Section U, V).
Route::middleware(['auth'])->prefix('admin')->name('applications.')->group(function () {
    Route::get('/applications', [ApplicationController::class, 'index'])->name('index');
    Route::get('/applications/{application}', [ApplicationController::class, 'show'])->name('show');
    Route::patch('/applications/{application}/approve-exception', [ApplicationController::class, 'approveException'])->name('approve-exception');
    Route::patch('/applications/{application}/accept', [ApplicationController::class, 'accept'])->name('accept');
    Route::patch('/applications/{application}/reject', [ApplicationController::class, 'reject'])->name('reject');
    Route::post('/applications/{application}/generate-guardian-code', [ApplicationController::class, 'generateGuardianCode'])->name('generate-guardian-code');



    });

// Staff management (teachers / supervisors) -- prerequisite for class assignment (BR-009).
Route::middleware(['auth'])->prefix('admin')->name('staff.')->group(function () {
    Route::get('/staff', [StaffController::class, 'index'])->name('index');
    Route::get('/staff/create', [StaffController::class, 'create'])->name('create');
    Route::post('/staff', [StaffController::class, 'store'])->name('store');
    Route::patch('/staff/{user}/toggle-active', [StaffController::class, 'toggleActive'])->name('toggle-active');
});

// Class management + teacher/supervisor assignment (Section K.8/K.9, BR-009).
Route::middleware(['auth'])->prefix('admin')->name('classes.')->group(function () {
    Route::get('/classes', [ClassController::class, 'index'])->name('index');
    Route::get('/classes/create', [ClassController::class, 'create'])->name('create');
    Route::post('/classes', [ClassController::class, 'store'])->name('store');
    Route::get('/classes/{class}', [ClassController::class, 'show'])->name('show');
    Route::post('/classes/{class}/assign-teacher', [ClassController::class, 'assignTeacher'])->name('assign-teacher');
    Route::post('/classes/{class}/assign-supervisor', [ClassController::class, 'assignSupervisor'])->name('assign-supervisor');
    Route::patch('/classes/{class}/supervisors/{assignment}/end', [ClassController::class, 'endSupervisor'])->name('supervisors.end');
});

// Student -> Class assignment (UC-03). Operates on LevelAttempt (see controller docblock).
Route::middleware(['auth'])->prefix('admin')->name('students.')->group(function () {
    Route::get('/students', [StudentAssignmentController::class, 'index'])->name('index');
    Route::post('/students/{levelAttempt}/assign-class', [StudentAssignmentController::class, 'assignClass'])->name('assign-class');
});


// Weekly plans + dynamic evaluation-item configuration (UC-05, Section O).
Route::middleware(['auth'])->prefix('admin')->name('weekly-plans.')->group(function () {
    Route::get('/weekly-plans', [WeeklyPlanController::class, 'index'])->name('index');
    Route::get('/weekly-plans/create', [WeeklyPlanController::class, 'create'])->name('create');
    Route::post('/weekly-plans', [WeeklyPlanController::class, 'store'])->name('store');
    Route::get('/weekly-plans/{weeklyPlan}', [WeeklyPlanController::class, 'show'])->name('show');
    Route::post('/weekly-plans/{weeklyPlan}/video', [WeeklyPlanController::class, 'saveVideo'])->name('video.save');
    Route::post('/weekly-plans/{weeklyPlan}/homework', [WeeklyPlanController::class, 'saveHomework'])->name('homework.save');
    Route::post('/weekly-plans/{weeklyPlan}/evaluation-items', [WeeklyPlanController::class, 'addEvaluationItem'])->name('evaluation-items.add');
    Route::delete('/weekly-plans/{weeklyPlan}/evaluation-items/{item}', [WeeklyPlanController::class, 'removeEvaluationItem'])->name('evaluation-items.remove');
    Route::patch('/weekly-plans/{weeklyPlan}/publish', [WeeklyPlanController::class, 'publish'])->name('publish');
});

// Weekly evaluation entry (UC-06) -- uses WeeklyEvaluationService for every write.
Route::middleware(['auth'])->prefix('admin')->name('weekly-evaluations.')->group(function () {
    Route::get('/weekly-evaluations', [WeeklyEvaluationController::class, 'index'])->name('index');
    Route::get('/weekly-evaluations/grade', [WeeklyEvaluationController::class, 'grade'])->name('grade');
    Route::post('/weekly-evaluations/grade', [WeeklyEvaluationController::class, 'saveScores'])->name('save-scores');
    Route::patch('/weekly-evaluations/{evaluation}/approve', [WeeklyEvaluationController::class, 'approve'])->name('approve');
    Route::patch('/weekly-evaluations/{evaluation}/reopen', [WeeklyEvaluationController::class, 'reopen'])->name('reopen');
});

// Exam authoring (UC-07). Publishing freezes the question set.
Route::middleware(['auth'])->prefix('admin')->name('exams.')->group(function () {
    Route::get('/exams', [ExamController::class, 'index'])->name('index');
    Route::get('/exams/create', [ExamController::class, 'create'])->name('create');
    Route::post('/exams', [ExamController::class, 'store'])->name('store');
    Route::get('/exams/{exam}', [ExamController::class, 'show'])->name('show');
    Route::post('/exams/{exam}/questions', [ExamController::class, 'addQuestion'])->name('questions.add');
    Route::delete('/exams/{exam}/questions/{question}', [ExamController::class, 'removeQuestion'])->name('questions.remove');
Route::patch('/exams/{exam}/publish', [ExamController::class, 'publish'])->name('publish');
Route::patch('/exams/{exam}/unpublish', [ExamController::class, 'unpublish'])->name('unpublish');    Route::get('/exams/{exam}/results', [ExamController::class, 'results'])->name('results');
});


// Guardian portal -- fully separate auth guard ('guardian'), read-only views
// except for the exam-taking flow below.
Route::prefix('portal')->name('portal.')->group(function () {
    Route::get('/login', [PortalAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [PortalAuthController::class, 'login'])->name('login.attempt');
    Route::post('/logout', [PortalAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth:guardian')->group(function () {
        Route::get('/', [PortalDashboardController::class, 'index'])->name('dashboard');
        Route::get('/students/{student}', [PortalDashboardController::class, 'show'])->name('students.show');

        // Exam-taking flow (UC-08/UC-09) -- scoped to a specific student + exam.
        Route::post('/students/{student}/exams/{exam}/start', [PortalExamController::class, 'start'])->name('exams.start');
        Route::get('/students/{student}/exams/{exam}/take', [PortalExamController::class, 'take'])->name('exams.take');
        Route::post('/students/{student}/exams/{exam}/submit', [PortalExamController::class, 'submit'])->name('exams.submit');
        Route::get('/students/{student}/exams/{exam}/result', [PortalExamController::class, 'result'])->name('exams.result');
    });


});

    // Final result calculation + dual approval (UC-10/UC-11).
Route::middleware(['auth'])->prefix('admin')->name('final-results.')->group(function () {
    Route::get('/final-results', [FinalResultController::class, 'index'])->name('index');
    Route::post('/final-results/{levelAttempt}/calculate', [FinalResultController::class, 'calculate'])->name('calculate');
    Route::get('/final-results/{finalResult}', [FinalResultController::class, 'show'])->name('show');
    Route::patch('/final-results/{finalResult}/teacher-approve', [FinalResultController::class, 'teacherApprove'])->name('teacher-approve');
    Route::patch('/final-results/{finalResult}/admin-approve', [FinalResultController::class, 'adminApprove'])->name('admin-approve');
});

require __DIR__.'/auth.php';
