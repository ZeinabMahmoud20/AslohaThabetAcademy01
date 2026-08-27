@extends('layouts.academy')

@section('title', 'لوحة المعلومات')

@section('content')
    <div class="page-header">
        <h1>مرحباً، {{ auth()->user()->full_name }} 👋</h1>
    </div>

    <h3 style="color: var(--color-green-dark); margin-bottom: 14px;">الفصول تحت إشرافك</h3>

    @forelse ($classes as $class)
        <div class="card" style="margin-bottom: 14px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <strong style="color: var(--color-green-dark); font-size: 16px;">{{ $class->name }}</strong>
                <span style="color: var(--color-muted); font-size: 13px;"> — {{ $class->level->name }}</span>
                <div style="font-size: 13px; color: var(--color-muted); margin-top: 4px;">
                    👦 {{ $class->active_students_count }} طالب/ة
                    @if ($class->activeTeacherAssignment)
                        — المعلم/ة: {{ $class->activeTeacherAssignment->teacher->full_name }}
                    @else
                        — <span style="color:#a5311f;">بدون معلم/ة حالياً</span>
                    @endif
                </div>
            </div>

            <a href="{{ route('classes.show', $class) }}" class="btn btn-outline">تفاصيل الفصل</a>
        </div>
    @empty
        <div class="card" style="text-align:center; color: var(--color-muted); padding: 40px;">
            لا توجد فصول تحت إشرافك حالياً.
        </div>
    @endforelse
@endsection