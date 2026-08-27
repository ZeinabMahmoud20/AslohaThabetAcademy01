@extends('layouts.academy')

@section('title', 'لوحة المعلومات')

@section('content')
    <div class="page-header">
        <h1>مرحباً، {{ auth()->user()->full_name }} 👋</h1>
    </div>

    @if ($pendingApprovals->isNotEmpty())
        <div class="alert alert-error" style="display:flex; align-items:center; justify-content:space-between;">
            <span>لديك {{ $pendingApprovals->count() }} نتيجة نهائية بانتظار موافقتك.</span>
            <a href="{{ route('final-results.index') }}" class="btn btn-gold">مراجعة الآن</a>
        </div>
    @endif

    <h3 style="color: var(--color-green-dark); margin-bottom: 14px;">فصولك النشطة</h3>

    @forelse ($classes as $class)
        <div class="card" style="margin-bottom: 14px; display:flex; align-items:center; justify-content:space-between;">
            <div>
                <strong style="color: var(--color-green-dark); font-size: 16px;">{{ $class->name }}</strong>
                <span style="color: var(--color-muted); font-size: 13px;"> — {{ $class->level->name }}</span>
                <div style="font-size: 13px; color: var(--color-muted); margin-top: 4px;">
                    👦 {{ $class->active_students_count }} طالب/ة
                </div>
            </div>

            <div style="display:flex; gap:8px;">
                <a href="{{ route('classes.show', $class) }}" class="btn btn-outline">تفاصيل الفصل</a>

                @if ($class->latestPublishedPlan)
                    <a href="{{ route('weekly-evaluations.grade', ['class_id' => $class->id, 'weekly_plan_id' => $class->latestPublishedPlan->id]) }}"
                       class="btn btn-primary">
                        صحّح الأسبوع {{ $class->latestPublishedPlan->week_number }}
                    </a>
                @endif
            </div>
        </div>
    @empty
        <div class="card" style="text-align:center; color: var(--color-muted); padding: 40px;">
            لا توجد فصول معيّنة عليك حالياً.
        </div>
    @endforelse
@endsection