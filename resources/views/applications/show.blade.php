@extends('layouts.academy')

@section('title', 'مراجعة طلب — ' . $application->student_full_name)

@section('content')
<div class="page-header">
    <h1>مراجعة طلب: {{ $application->student_full_name }}</h1>
    <a href="{{ route('applications.index') }}" class="btn btn-outline">⟵ رجوع للقائمة</a>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">

    {{-- Left column: application details --}}
    <div class="card">
        <h3 style="color: var(--color-green-dark); margin-top:0;">بيانات الطالب</h3>
        <table>
            <tr>
                <td style="width:180px; font-weight:700;">الاسم الكامل</td>
                <td>{{ $application->student_full_name }}</td>
            </tr>
            <tr>
                <td style="font-weight:700;">تاريخ الميلاد</td>
                <td>{{ $application->student_dob->format('Y-m-d') }}</td>
            </tr>
            <tr>
                <td style="font-weight:700;">العمر عند التقديم</td>
                <td>{{ $application->computed_age_years }} سنة</td>
            </tr>
            <tr>
                <td style="font-weight:700;">الدولة / المحافظة</td>
                <td>{{ $application->country }} — {{ $application->governorate }}</td>
            </tr>
            <tr>
                <td style="font-weight:700;">المدرسة / الصف</td>
                <td>{{ $application->school }} — {{ $application->grade_level }}</td>
            </tr>
            <tr>
                <td style="font-weight:700;">تاريخ التقديم</td>
                <td>{{ $application->submitted_at->format('Y-m-d H:i') }}</td>
            </tr>
        </table>

        <h3 style="color: var(--color-green-dark);">بيانات ولي الأمر</h3>
        <table>
            <tr>
                <td style="width:180px; font-weight:700;">الاسم</td>
                <td>{{ $application->guardian->full_name }}</td>
            </tr>
            <tr>
                <td style="font-weight:700;">رقم الهاتف</td>
                <td dir="ltr" style="text-align:right;">{{ $application->guardian->phone_number }}</td>
            </tr>
            <tr>
                <td style="font-weight:700;">صلة القرابة</td>
                <td>{{ $application->guardian->relationship_to_student }}</td>
            </tr>
        </table>

        @if ($application->is_age_exception)
        <div class="alert alert-error" style="margin-top: 16px;">
            ⚠️ هذا الطلب يمثل استثناءً في السن (خارج نطاق 7–10 سنوات) —
            @if ($application->exception_approved_at)
            تمت الموافقة على الاستثناء بتاريخ {{ $application->exception_approved_at->format('Y-m-d') }}.
            @else
            <strong>لم تتم الموافقة على الاستثناء بعد.</strong> يجب اعتماده قبل قبول الطلب (BR-002).
            @endif
        </div>
        @endif
    </div>

    {{-- Right column: actions --}}
    <div class="card">
        <h3 style="color: var(--color-green-dark); margin-top:0;">إجراء المراجعة</h3>

        @if ($application->status === 'pending_review' || $application->status === 'admin_review')

        @if ($application->is_age_exception && ! $application->exception_approved_at)
        <form method="POST" action="{{ route('applications.approve-exception', $application) }}" style="margin-bottom: 20px;">
            @csrf
            @method('PATCH') @csrf
            <button type="submit" class="btn btn-gold" style="width:100%;">
                اعتماد استثناء السن
            </button>
        </form>
        @endif

        <form method="POST" action="{{ route('applications.accept', $application) }}" style="margin-bottom: 14px;">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label for="initial_level_id">المستوى المبدئي</label>
                <select name="initial_level_id" id="initial_level_id" required>
                    @foreach ($levels as $level)
                    <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary" style="width:100%;"
                {{ $application->is_age_exception && ! $application->exception_approved_at ? 'disabled' : '' }}>
                ✅ قبول الطلب
            </button>
        </form>

        <hr style="border: none; border-top: 1px solid var(--color-border); margin: 18px 0;">

        <form method="POST" action="{{ route('applications.reject', $application) }}">
            @csrf
            @method('PATCH')
            <div class="form-group">
                <label for="rejection_reason">سبب الرفض</label>
                <textarea name="rejection_reason" id="rejection_reason" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-danger" style="width:100%;">❌ رفض الطلب</button>
        </form>

        @elseif ($application->status === 'accepted')
        <div class="alert alert-success">
            تم القبول بتاريخ {{ $application->reviewed_at->format('Y-m-d') }}
            @if ($application->student)
            <br>كود الطالب: <strong>{{ $application->student->academy_student_code ?? '—' }}</strong>
            @endif
        </div>

        <div class="card" style="margin-top: 16px; background: #fbfaf3;">
            <h3 style="color: var(--color-green-dark); margin-top:0; font-size: 15px;">
                🔑 دخول ولي الأمر لبوابة المتابعة
            </h3>

            <table style="margin-bottom: 14px;">
                <tr>
                    <td style="width:140px; font-weight:700;">رقم هاتف الدخول</td>
                    <td dir="ltr" style="text-align:right;">{{ $application->guardian->phone_number }}</td>
                </tr>
                <tr>
                    <td style="font-weight:700;">كود الدخول الحالي</td>
                    <td>
                        @if ($application->guardian->access_code)
                        <span style="font-family: monospace; font-size: 18px; font-weight:800; color: var(--color-green-dark); letter-spacing: 2px;">
                            {{ $application->guardian->access_code }}
                        </span>
                        <span style="color: var(--color-muted); font-size: 12px;">
                            (تم توليده بتاريخ {{ $application->guardian->access_code_generated_at?->format('Y-m-d') }})
                        </span>
                        @else
                        <span style="color: var(--color-muted);">لم يتم توليد كود بعد</span>
                        @endif
                    </td>
                </tr>
            </table>

            <form method="POST" action="{{ route('applications.generate-guardian-code', $application) }}">
                @csrf
                <button type="submit" class="btn btn-gold">
                    {{ $application->guardian->access_code ? '🔄 توليد كود جديد' : '➕ توليد كود دخول' }}
                </button>
            </form>
        </div>
        @else
        <div class="alert alert-error">
            تم الرفض بتاريخ {{ $application->reviewed_at->format('Y-m-d') }}<br>
            السبب: {{ $application->rejection_reason }}
        </div>
        @endif
    </div>
</div>
@endsection