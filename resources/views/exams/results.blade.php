@extends('layouts.academy')

@section('title', 'نتائج امتحان — ' . $exam->title)

@section('content')
    <div class="page-header">
        <h1>نتائج: {{ $exam->title }}</h1>
        <a href="{{ route('exams.show', $exam) }}" class="btn btn-outline">⟵ رجوع لإدارة الامتحان</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>الطالب/ة</th>
                    <th>الدرجة</th>
                    <th>النسبة</th>
                    <th>وقت التسليم</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attempts as $attempt)
                    <tr>
                        <td>{{ $attempt->levelAttempt->student->full_name }}</td>
                        <td>{{ $attempt->score }} / {{ $exam->max_score }}</td>
                        <td>
                            <span class="badge-status {{ $attempt->percentage >= 50 ? 'badge-accepted' : 'badge-rejected' }}">
                                {{ $attempt->percentage }}%
                            </span>
                        </td>
                        <td>{{ $attempt->submitted_at?->format('Y-m-d H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color: var(--color-muted); padding: 30px;">
                            لا توجد محاولات مصححة بعد.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection