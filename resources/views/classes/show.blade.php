@extends('layouts.academy')

@section('title', 'إدارة الفصل — ' . $class->name)

@section('content')
    <div class="page-header">
        <h1>{{ $class->name }} — {{ $class->level->name }}</h1>
        <a href="{{ route('classes.index') }}" class="btn btn-outline">⟵ رجوع لكل الفصول</a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">

        {{-- Teacher assignment --}}
        <div class="card">
            <h3 style="color: var(--color-green-dark); margin-top:0;">المعلم/ة الحالي/ة</h3>

            @if ($class->activeTeacherAssignment)
                <div class="alert alert-success" style="margin-bottom: 16px;">
                    {{ $class->activeTeacherAssignment->teacher->full_name }}
                    <br><small>معيّن/ة منذ {{ $class->activeTeacherAssignment->start_date->format('Y-m-d') }}</small>
                </div>
            @else
                <div class="alert alert-error" style="margin-bottom: 16px;">لا يوجد معلم/ة معيّن/ة حالياً.</div>
            @endif
@can('update', $class)
            <form method="POST" action="{{ route('classes.assign-teacher', $class) }}">
                @csrf
                <div class="form-group">
                    <label for="teacher_id">
                        {{ $class->activeTeacherAssignment ? 'تبديل المعلم/ة' : 'تعيين معلم/ة' }}
                    </label>
                    <select name="teacher_id" id="teacher_id" required>
                        <option value="" disabled selected>اختر معلم/ة</option>
                        @foreach ($availableTeachers as $teacher)
                            <option value="{{ $teacher->id }}">{{ $teacher->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label for="reason">سبب التبديل (اختياري)</label>
                    <textarea name="reason" id="reason" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">
                    {{ $class->activeTeacherAssignment ? 'تبديل المعلم/ة' : 'تعيين' }}
                </button>
            </form>
            @endcan

            @if ($class->teacherAssignments->count() > 1)
                <h4 style="color: var(--color-green-dark); margin-top: 20px; font-size: 14px;">سجل المعلمين السابقين</h4>
                <table>
                    <tbody>
                        @foreach ($class->teacherAssignments as $ta)
                            @if (! $ta->is_active)
                                <tr>
                                    <td>{{ $ta->teacher->full_name }}</td>
                                    <td style="font-size: 12px; color: var(--color-muted);">
                                        {{ $ta->start_date->format('Y-m-d') }} → {{ $ta->end_date?->format('Y-m-d') }}
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        {{-- Supervisor assignments --}}
        <div class="card">
            <h3 style="color: var(--color-green-dark); margin-top:0;">المشرفون النشطون</h3>

            @forelse ($class->activeSupervisorAssignments as $sa)
                <div style="display:flex; align-items:center; justify-content:space-between; padding: 8px 0; border-bottom: 1px solid var(--color-border);">
                    <span>{{ $sa->supervisor->full_name }}</span>
                    @can('update', $class)
                    <form method="POST" action="{{ route('classes.supervisors.end', [$class, $sa->id]) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-danger" style="padding: 5px 12px; font-size:12px;">إنهاء</button>
                    </form>
                    @endcan
                </div>
            @empty
                <div class="alert alert-error" style="margin-bottom: 16px;">لا يوجد مشرفون معيّنون حالياً.</div>
            @endforelse
            @can('update', $class)
            <form method="POST" action="{{ route('classes.assign-supervisor', $class) }}" style="margin-top: 16px;">
                @csrf
                <div class="form-group">
                    <label for="supervisor_id">إضافة مشرف/ة</label>
                    <select name="supervisor_id" id="supervisor_id" required>
                        <option value="" disabled selected>اختر مشرف/ة</option>
                        @foreach ($availableSupervisors as $supervisor)
                            <option value="{{ $supervisor->id }}">{{ $supervisor->full_name }}</option>
                        @endforeach
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%;">إضافة</button>
            </form>
            @endcan
        </div>
    </div>
@endsection