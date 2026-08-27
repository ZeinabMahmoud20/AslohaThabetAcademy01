<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Application;
use App\Models\AuditLog;
use App\Models\Guardian;
use App\Models\Level;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Implements UC-01 (Submit Application) and UC-02 (Review & Accept/Reject) as
 * server-rendered Blade views instead of a JSON API.
 */
class ApplicationController extends Controller
{
    // GET /applications/new -- public form (no auth), FR-001.
    public function create()
    {
        return view('applications.create');
    }

    // POST /applications -- public, no auth (FR-001).
    public function store(Request $request)
    {
        $data = $request->validate([
            'guardian.full_name' => 'required|string|max:150',
            'guardian.phone_number' => 'required|string|max:30',
            'guardian.relationship_to_student' => 'required|string|max:50',
            'guardian.country' => 'nullable|string|max:80',
            'student_full_name' => 'required|string|max:150',
            'student_dob' => 'required|date|before:today',
            'country' => 'nullable|string|max:80',
            'governorate' => 'nullable|string|max:80',
            'school' => 'nullable|string|max:150',
            'grade_level' => 'nullable|string|max:50',
        ]);

        DB::transaction(function () use ($data) {
            $guardian = Guardian::firstOrCreate(
                ['phone_number' => $data['guardian']['phone_number']],
                $data['guardian']
            );

            $submittedAt = now();
            $age = $submittedAt->diffInYears($data['student_dob']); // FR-003

            Application::create([
                'guardian_id' => $guardian->id,
                'student_full_name' => $data['student_full_name'],
                'student_dob' => $data['student_dob'],
                'country' => $data['country'] ?? null,
                'governorate' => $data['governorate'] ?? null,
                'school' => $data['school'] ?? null,
                'grade_level' => $data['grade_level'] ?? null,
                'submitted_at' => $submittedAt,
                'computed_age_years' => $age,
                'is_age_exception' => $age < 7 || $age > 10, // FR-004
                'status' => 'pending_review',
            ]);

            // TODO: dispatch a Notification to the Administration queue (Section T / J.1).
        });

        return redirect()->route('applications.thank-you');
    }

    public function thankYou()
    {
        return view('applications.thank-you');
    }

    // GET /admin/applications -- Admin only, listing + status filter tabs.
    public function index(Request $request)
    {
        $this->authorize('viewAny', Application::class); // see README step 8 (Policies)

        $applications = Application::query()
            ->when($request->status, fn ($q, $status) => $q->where('status', $status))
            ->with('guardian')
            ->latest('submitted_at')
            ->paginate(15)
            ->withQueryString();

        return view('applications.index', compact('applications'));
    }

    // GET /admin/applications/{application} -- single application review screen.
    public function show(Application $application)
    {
        $this->authorize('review', $application);

        $levels = Level::where('is_active', true)->orderBy('order_index')->get();

        return view('applications.show', compact('application', 'levels'));
    }

    // PATCH /admin/applications/{application}/approve-exception -- BR-002.
    public function approveException(Request $request, Application $application)
    {
        $this->authorize('review', $application);

        $application->update([
            'exception_approved_by' => $request->user()->id,
            'exception_approved_at' => now(),
        ]);

        return back()->with('success', 'تم اعتماد استثناء السن. يمكنك الآن قبول الطلب.');
    }

    // PATCH /admin/applications/{application}/accept -- Admin only (UC-02).
    public function accept(Request $request, Application $application)
    {
        $this->authorize('review', $application);

        $request->validate(['initial_level_id' => 'required|exists:levels,id']);

        if ($application->is_age_exception && ! $application->exception_approved_at) {
            return back()->with('error', 'يجب اعتماد استثناء السن أولاً قبل القبول (BR-002).');
        }

        DB::transaction(function () use ($request, $application) {
            $application->update([
                'status' => 'accepted',
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);

            // FR-008: creates a Student record AND an initial Level Attempt, atomically.
            $student = $application->guardian->students()->create([
                'application_id' => $application->id,
                'full_name' => $application->student_full_name,
                'dob' => $application->student_dob,
                'country' => $application->country,
                'governorate' => $application->governorate,
                'school' => $application->school,
                'grade_level' => $application->grade_level,
                'status' => 'active',
            ]);

            $application->update(['student_id' => $student->id]);

            $student->levelAttempts()->create([
                'level_id' => $request->input('initial_level_id'),
                'attempt_number' => 1,
                'started_at' => now(),
                'status' => 'active',
                'is_active' => true,
            ]);

            AuditLog::record(
                userId: $request->user()->id,
                action: 'APPLICATION_ACCEPTED',
                entityType: 'applications',
                entityId: $application->id,
                new: ['status' => 'accepted'],
                ip: $request->ip(),
            );

            // TODO: notify the Guardian's contact channel (FR-007).
        });

        return redirect()->route('applications.show', $application)
            ->with('success', 'تم قبول الطلب وإنشاء ملف الطالب بنجاح.');
    }

    // PATCH /admin/applications/{application}/reject -- Admin only.
// PATCH /admin/applications/{application}/reject -- Admin only.
    public function reject(Request $request, Application $application)
    {
        $this->authorize('review', $application);

        $request->validate(['rejection_reason' => 'required|string|max:500']);

        $application->update([
            'status' => 'rejected',
            'reviewed_by' => $request->user()->id,
            'reviewed_at' => now(),
            'rejection_reason' => $request->rejection_reason,
        ]);

        return redirect()->route('applications.show', $application)
            ->with('success', 'تم رفض الطلب.');
    }

    // POST /admin/applications/{application}/generate-guardian-code
    // Generates (or regenerates) the guardian's portal access code. Stored as
    // plaintext (by design, per Admin's request) so it stays visible on the
    // application screen for the Admin to re-share at any time -- not hidden
    // or one-time like a hashed password would be.
    public function generateGuardianCode(Request $request, Application $application)
    {
        $this->authorize('review', $application);

        $code = (string) random_int(100000, 999999); // 6-digit code

        $application->guardian->update([
            'access_code' => $code,
            'access_code_generated_at' => now(),
        ]);

        AuditLog::record(
            userId: $request->user()->id,
            action: 'GUARDIAN_ACCESS_CODE_GENERATED',
            entityType: 'guardians',
            entityId: $application->guardian_id,
            ip: $request->ip(),
        );

        return back()->with('success', 'تم توليد كود الدخول بنجاح.');
    }


}
