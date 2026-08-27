@extends('layouts.academy')

@section('title', 'النتائج النهائية')

@section('content')
    <div class="page-header">
        <h1>النتائج النهائية</h1>
    </div>

    <div class="card" style="margin-bottom: 18px; display:flex; gap:10px; flex-wrap: wrap;">
        @foreach ($levels as $level)
            <a href="{{ route('final-results.index', ['level_id' => $level->id]) }}"
               class="btn {{ $levelId == $level->id ? 'btn-gold' : 'btn-outline' }}">{{ $level->name }}</a>
        @endforeach
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>الطالب/ة</th>
                    <th>الفصل</th>
                    <th>الدرجة النهائية</th>
                    <th>الحالة</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attempts as $attempt)
                    <tr>
                        <td>{{ $attempt->student->full_name }}</td>
                        <td>{{ $attempt->activeClassAssignment?->classRoom?->name ?? '—' }}</td>
                        <td>
                            @if ($attempt->finalResult)
                                {{ $attempt->finalResult->final_score }} / 100
                            @else
                                <span style="color: var(--color-muted);">لم يتم الحساب بعد</span>
                            @endif
                        </td>
                        <td>
                            @if ($attempt->finalResult)
                                @php
                                    $statusMap = [
                                        'calculated' => ['محسوبة', 'badge-pending'],
                                        'teacher_approved' => ['موافقة المعلم/ة', 'badge-pending'],
                                        'admin_approved' => ['موافقة الإدارة', 'badge-accepted'],
                                        'final' => ['نهائية', 'badge-accepted'],
                                    ];
                                    [$label, $class] = $statusMap[$attempt->finalResult->status] ?? [$attempt->finalResult->status, 'badge-pending'];
                                @endphp
                                <span class="badge-status {{ $class }}">{{ $label }}</span>
                            @else
                                <span class="badge-status badge-pending">—</span>
                            @endif
                        </td>
                        <td>
                            @if ($attempt->finalResult)
                                <a href="{{ route('final-results.show', $attempt->finalResult) }}" class="btn btn-outline">عرض</a>
                            @else
                                <form method="POST" action="{{ route('final-results.calculate', $attempt) }}">
                                    @csrf
                                    <button type="submit" class="btn btn-primary">حساب النتيجة</button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; color: var(--color-muted); padding: 30px;">
                            لا يوجد طلاب نشطون في هذا المستوى.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection