@extends('layouts.academy')

@section('title', 'لوحة المعلومات')

@section('content')
    <div class="page-header">
        <h1>مرحباً، {{ auth()->user()->full_name }} 👋</h1>
    </div>

    {{-- Stats cards --}}
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 24px;">

        <a href="{{ route('applications.index', ['status' => 'pending_review']) }}" class="card" style="text-decoration:none; display:block;">
            <div style="font-size: 28px; font-weight: 800; color: var(--color-green-dark);">{{ $stats['pending_applications'] }}</div>
            <div style="font-size: 13px; color: var(--color-muted); margin-top: 4px;">📝 طلبات قيد المراجعة</div>
        </a>

        <a href="{{ route('students.index', ['status' => 'unassigned']) }}" class="card" style="text-decoration:none; display:block;">
            <div style="font-size: 28px; font-weight: 800; color: var(--color-green-dark);">{{ $stats['unassigned_students'] }}</div>
            <div style="font-size: 13px; color: var(--color-muted); margin-top: 4px;">🎓 طلاب بدون فصل</div>
        </a>

        <a href="{{ route('students.index') }}" class="card" style="text-decoration:none; display:block;">
            <div style="font-size: 28px; font-weight: 800; color: var(--color-green-dark);">{{ $stats['active_students'] }}</div>
            <div style="font-size: 13px; color: var(--color-muted); margin-top: 4px;">👦 إجمالي الطلاب النشطين</div>
        </a>

        <a href="{{ route('classes.index') }}" class="card" style="text-decoration:none; display:block;">
            <div style="font-size: 28px; font-weight: 800; color: var(--color-green-dark);">{{ $stats['active_classes'] }}</div>
            <div style="font-size: 13px; color: var(--color-muted); margin-top: 4px;">🏫 الفصول النشطة</div>
        </a>

        <a href="{{ route('staff.index', ['role' => 'all']) }}" class="card" style="text-decoration:none; display:block;">
            <div style="font-size: 28px; font-weight: 800; color: var(--color-green-dark);">{{ $stats['staff_count'] }}</div>
            <div style="font-size: 13px; color: var(--color-muted); margin-top: 4px;">👩‍🏫 المعلمات والمشرفات</div>
        </a>

        <a href="{{ route('exams.index') }}" class="card" style="text-decoration:none; display:block;">
            <div style="font-size: 28px; font-weight: 800; color: var(--color-green-dark);">{{ $stats['published_exams'] }}</div>
            <div style="font-size: 13px; color: var(--color-muted); margin-top: 4px;">📋 امتحانات منشورة حالياً</div>
        </a>
    </div>

    {{-- Recent pending applications --}}
    <div class="card">
        <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 14px;">
            <h3 style="color: var(--color-green-dark); margin:0;">أحدث طلبات الالتحاق المعلّقة</h3>
            <a href="{{ route('applications.index') }}" class="btn btn-outline">عرض الكل</a>
        </div>

        <table>
            <thead>
                <tr>
                    <th>اسم الطالب/ة</th>
                    <th>ولي الأمر</th>
                    <th>تاريخ التقديم</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($recentApplications as $application)
                    <tr>
                        <td>{{ $application->student_full_name }}</td>
                        <td>{{ $application->guardian->full_name }}</td>
                        <td>{{ $application->submitted_at->format('Y-m-d') }}</td>
                        <td>
                            <a href="{{ route('applications.show', $application) }}" class="btn btn-outline">مراجعة</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color: var(--color-muted); padding: 24px;">
                            لا توجد طلبات معلّقة حالياً 🎉
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection