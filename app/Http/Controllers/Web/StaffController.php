<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

/**
 * Manages "staff" users -- teachers and supervisors (role_type = teacher | supervisor).
 * Admin/General Supervisor only (Section V). This is a prerequisite for class
 * assignment (BR-009): you cannot assign a teacher to a class until the teacher
 * exists as a User record.
 */
class StaffController extends Controller
{
    // GET /admin/staff?role=teacher|supervisor
    public function index(Request $request)
    {
        $this->authorize('viewAny', User::class);

        $role = $request->query('role', 'teacher');

        $staff = User::query()
            ->whereIn('role_type', ['teacher', 'supervisor'])
            ->when($role !== 'all', fn ($q) => $q->where('role_type', $role))
            ->orderBy('full_name')
            ->paginate(15)
            ->withQueryString();

        return view('staff.index', compact('staff', 'role'));
    }

    // GET /admin/staff/create?role=teacher|supervisor
    public function create(Request $request)
    {
        $this->authorize('create', User::class);

        $role = $request->query('role', 'teacher');

        return view('staff.create', compact('role'));
    }

    // POST /admin/staff
    public function store(Request $request)
    {
        $this->authorize('create', User::class);

        $data = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'phone_number' => 'nullable|string|max:30',
            'role_type' => ['required', Rule::in(['teacher', 'supervisor'])],
            'password' => 'required|string|min:8',
        ]);

        $user = User::create([
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'] ?? null,
            'role_type' => $data['role_type'],
            'password' => Hash::make($data['password']),
            'is_active' => true,
        ]);

        AuditLog::record(
            userId: $request->user()->id,
            action: 'STAFF_CREATED',
            entityType: 'users',
            entityId: $user->id,
            new: ['full_name' => $user->full_name, 'role_type' => $user->role_type],
            ip: $request->ip(),
        );

        return redirect()
            ->route('staff.index', ['role' => $data['role_type']])
            ->with('success', 'تم إضافة ' . ($data['role_type'] === 'teacher' ? 'المعلم/ة' : 'المشرف/ة') . ' بنجاح.');
    }

    // PATCH /admin/staff/{user}/toggle-active
    public function toggleActive(Request $request, User $user)
    {
        $this->authorize('update', $user);

        $user->update(['is_active' => ! $user->is_active]);

        AuditLog::record(
            userId: $request->user()->id,
            action: $user->is_active ? 'STAFF_ACTIVATED' : 'STAFF_DEACTIVATED',
            entityType: 'users',
            entityId: $user->id,
            new: ['is_active' => $user->is_active],
            ip: $request->ip(),
        );

        return back()->with('success', 'تم تحديث حالة الحساب.');
    }
}