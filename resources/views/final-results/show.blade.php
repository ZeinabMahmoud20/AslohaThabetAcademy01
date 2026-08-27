@extends('layouts.academy')

@section('title', 'النتيجة النهائية — ' . $finalResult->levelAttempt->student->full_name)

@section('content')
    <div class="page-header">
        <h1>{{ $finalResult->levelAttempt->student->full_name }} — {{ $finalResult->levelAttempt->level->name }}</h1>
        <a href="{{ route('final-results.index', ['level_id' => $finalResult->levelAttempt->level_id]) }}" class="btn btn-outline">⟵ رجوع للقائمة</a>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">

        {{-- Score breakdown --}}
        <div class="card" style="text-align:center;">
            <div style="font-size: 42px; font-weight: 800; color: var(--color-green-dark);">
                {{ $finalResult->final_score }} / 100
            </div>
            <div style="font-size: 16px; font-weight: 700; margin-top: 4px;">
                {{ $finalResult->grade_label }} —
                <span style="color: {{ $finalResult->pass_fail === 'passed' ? 'var(--color-green-dark)' : '#a5311f' }};">
                    {{ $finalResult->pass_fail === 'passed' ? 'ناجح/ة' : 'راسب/ة' }}
                </span>
            </div>

            <table style="margin-top: 20px; text-align:right;">
                <tr>
                    <td style="font-weight:700;">التقييمات الأسبوعية (70%)</td>
                    <td>{{ $finalResult->weekly_component }} / 70</td>
                </tr>
                <tr>
                    <td style="font-weight:700;">الامتحانات (30%)</td>
                    <td>{{ $finalResult->exam_component }} / 30</td>
                </tr>
            </table>
        </div>

        {{-- Approval workflow --}}
        <div class="card">
            <h3 style="color: var(--color-green-dark); margin-top:0;">دورة الاعتماد</h3>

            <div style="margin-bottom: 16px;">
                <div style="display:flex; align-items:center; gap:8px; margin-bottom:10px;">
                    <span style="font-size:18px;">{{ $finalResult->teacher_approved_at ? '✅' : '⬜' }}</span>
                    <span>
                        موافقة المعلم/ة
                        @if ($finalResult->teacher_approved_at)
                            <br><small style="color: var(--color-muted);">{{ $finalResult->teacher_approved_at->format('Y-m-d H:i') }}</small>
                        @endif
                    </span>
                </div>
                <div style="display:flex; align-items:center; gap:8px;">
                    <span style="font-size:18px;">{{ $finalResult->admin_approved_at ? '✅' : '⬜' }}</span>
                    <span>
                        موافقة الإدارة (نهائية)
                        @if ($finalResult->admin_approved_at)
                            <br><small style="color: var(--color-muted);">{{ $finalResult->admin_approved_at->format('Y-m-d H:i') }}</small>
                        @endif
                    </span>
                </div>
            </div>

            @if ($finalResult->status === 'calculated')
             @can('teacherApprove', $finalResult)
                <form method="POST" action="{{ route('final-results.teacher-approve', $finalResult) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-primary" style="width:100%;">✅ موافقة المعلم/ة</button>
                </form>
                @endcan

            @elseif ($finalResult->status === 'teacher_approved')
            @can('adminApprove', $finalResult)
                <form method="POST" action="{{ route('final-results.admin-approve', $finalResult) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-gold" style="width:100%;">
                        🏁 الموافقة النهائية (سيتم تحديث حالة الطالب/ة تلقائياً)
                    </button>
                </form>
                @endcan
            @elseif ($finalResult->status === 'final')
                <div class="alert alert-success" style="margin:0;">
                    تم اعتماد النتيجة نهائياً. تم تحديث حالة الطالب/ة تلقائياً
                    ({{ $finalResult->pass_fail === 'passed' ? 'تمت الترقية للمستوى التالي' : 'تمت إعادة نفس المستوى' }}).
                </div>
            @endif
        </div>
    </div>
@endsection