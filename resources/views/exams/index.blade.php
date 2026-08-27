@extends('layouts.academy')

@section('title', 'الامتحانات')

@section('content')
    <div class="page-header">
        <h1>الامتحانات</h1>
        @can('create', \App\Models\Exam::class)
            <a href="{{ route('exams.create', ['level_id' => $levelId]) }}" class="btn btn-primary">+ امتحان جديد</a>
        @endcan
    </div>

    <div class="card" style="margin-bottom: 18px; display:flex; gap:10px; flex-wrap: wrap;">
        @foreach ($levels as $level)
            <a href="{{ route('exams.index', ['level_id' => $level->id]) }}"
               class="btn {{ $levelId == $level->id ? 'btn-gold' : 'btn-outline' }}">{{ $level->name }}</a>
        @endforeach
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>العنوان</th>
                    <th>يبدأ</th>
                    <th>ينتهي</th>
                    <th>عدد الأسئلة</th>
                    <th>الدرجة الكلية</th>
                    <th>المحاولات</th>
                    <th>الحالة</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($exams as $exam)
                    <tr>
                        <td>{{ $exam->title }}</td>
                        <td>{{ $exam->start_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $exam->end_at->format('Y-m-d H:i') }}</td>
                        <td>{{ $exam->questions_count }}</td>
                        <td>{{ $exam->max_score }}</td>
                        <td>{{ $exam->attempts_count }}</td>
                        <td>
                            @php
                                $statusMap = [
                                    'draft' => ['مسودة', 'badge-pending'],
                                    'reviewed' => ['تمت المراجعة', 'badge-pending'],
                                    'published' => ['منشور', 'badge-accepted'],
                                    'closed' => ['مغلق', 'badge-rejected'],
                                ];
                                [$label, $class] = $statusMap[$exam->status] ?? [$exam->status, 'badge-pending'];
                            @endphp
                            <span class="badge-status {{ $class }}">{{ $label }}</span>
                        </td>
                        <td style="display:flex; gap:6px;">
                            <a href="{{ route('exams.show', $exam) }}" class="btn btn-outline">
                                @can('update', $exam) إدارة @else عرض @endcan
                            </a>
                            @can('view', $exam)
                                @if ($exam->attempts_count > 0)
                                    <a href="{{ route('exams.results', $exam) }}" class="btn btn-outline">النتائج</a>
                                @endif
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; color: var(--color-muted); padding: 30px;">
                            لا توجد امتحانات لهذا المستوى بعد.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection