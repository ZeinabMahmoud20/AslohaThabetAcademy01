@extends('layouts.academy')

@section('title', 'التقييم الأسبوعي')

@section('content')
    <div class="page-header">
        <h1>التقييم الأسبوعي — اختر الفصل والأسبوع</h1>
    </div>

    <div class="card">
        @forelse ($classes as $class)
            @php
                $weeksForThisLevel = $publishedPlansByLevel->get($class->level_id, collect());
            @endphp
            <div style="border-bottom: 1px solid var(--color-border); padding: 16px 0;">
                <div style="display:flex; align-items:center; justify-content:space-between; margin-bottom: 10px;">
                    <div>
                        <strong>{{ $class->name }}</strong>
                        <span style="color: var(--color-muted); font-size: 13px;"> — {{ $class->level->name }}</span>
                        @if ($class->activeTeacherAssignment)
                            <span style="color: var(--color-muted); font-size: 13px;">
                                (المعلم/ة: {{ $class->activeTeacherAssignment->teacher->full_name }})
                            </span>
                        @endif
                    </div>
                </div>

                @if ($weeksForThisLevel->isEmpty())
                    <p style="color: var(--color-muted); font-size: 13px;">لا توجد أسابيع منشورة لهذا المستوى بعد.</p>
                @else
                    <div style="display:flex; gap:8px; flex-wrap: wrap;">
                        @foreach ($weeksForThisLevel as $plan)
                            <a href="{{ route('weekly-evaluations.grade', ['class_id' => $class->id, 'weekly_plan_id' => $plan->id]) }}"
                               class="btn btn-outline">
                                الأسبوع {{ $plan->week_number }}: {{ $plan->title }}
                            </a>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <p style="text-align:center; color: var(--color-muted); padding: 30px;">
                لا توجد فصول متاحة لك للتصحيح حالياً.
            </p>
        @endforelse
    </div>
@endsection