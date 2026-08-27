@extends('layouts.academy')

@section('title', 'تصحيح الأسبوع ' . $weeklyPlan->week_number . ' — ' . $class->name)

@section('content')
    <div class="page-header">
        <h1>{{ $class->name }} — الأسبوع {{ $weeklyPlan->week_number }}: {{ $weeklyPlan->title }}</h1>
        <a href="{{ route('weekly-evaluations.index') }}" class="btn btn-outline">⟵ رجوع لاختيار فصل آخر</a>
    </div>

    {{-- Student status + approve/reopen -- kept as separate small forms, outside
         the main grading form below, since HTML forms cannot be nested. --}}
    <div class="card" style="margin-bottom: 18px;">
        <h3 style="color: var(--color-green-dark); margin-top:0;">حالة اعتماد كل طالب/ة</h3>
        <table>
            <thead>
                <tr>
                    <th>الطالب/ة</th>
                    <th>الدرجة الحالية</th>
                    <th>الحالة</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($evaluations as $evaluation)
                    <tr>
                        <td>{{ $evaluation->levelAttempt->student->full_name }}</td>
                        <td>{{ $evaluation->total_score }} / {{ $evaluation->max_score }}</td>
                        <td>
                            @php
                                $statusMap = [
                                    'draft' => ['مسودة', 'badge-pending'],
                                    'approved' => ['معتمد', 'badge-accepted'],
                                    'reopened' => ['أعيد فتحه', 'badge-pending'],
                                ];
                                [$label, $class_] = $statusMap[$evaluation->status] ?? [$evaluation->status, 'badge-pending'];
                            @endphp
                            <span class="badge-status {{ $class_ }}">{{ $label }}</span>
                        </td>
                        <td>
                            @if ($evaluation->status !== 'approved')
                                <form method="POST" action="{{ route('weekly-evaluations.approve', $evaluation) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-primary" style="padding:5px 12px; font-size:12px;">اعتماد</button>
                                </form>
                            @else
                            @can('reopen', $evaluation)
                                <form method="POST" action="{{ route('weekly-evaluations.reopen', $evaluation) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline" style="padding:5px 12px; font-size:12px;">إعادة فتح</button>
                                </form>
                            @endcan
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{-- Main grading grid --}}
    <div class="card">
        <h3 style="color: var(--color-green-dark); margin-top:0;">جدول الدرجات</h3>
        <form method="POST" action="{{ route('weekly-evaluations.save-scores') }}">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class->id }}">
            <input type="hidden" name="weekly_plan_id" value="{{ $weeklyPlan->id }}">

            <div style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>الطالب/ة</th>
                            @foreach ($weeklyPlan->evaluationItems as $planItem)
                                <th>
                                    {{ $planItem->evaluationItem->name }}
                                    <br><small style="font-weight: 400;">من {{ $planItem->max_score }}</small>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($evaluations as $evaluation)
                            @php
                                $scoresByItem = $evaluation->itemScores->keyBy('weekly_plan_evaluation_item_id');
                                $isLocked = $evaluation->status === 'approved';
                            @endphp
                            <tr>
                                <td>{{ $evaluation->levelAttempt->student->full_name }}</td>
                                @foreach ($weeklyPlan->evaluationItems as $planItem)
                                    <td>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="{{ $planItem->max_score }}"
                                            name="scores[{{ $evaluation->level_attempt_id }}][{{ $planItem->id }}]"
                                            value="{{ $scoresByItem->get($planItem->id)?->score }}"
                                            style="width: 80px; padding: 6px;"
                                            {{ $isLocked ? 'disabled' : '' }}
                                        >
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <button type="submit" class="btn btn-primary" style="margin-top: 16px;">💾 حفظ الدرجات</button>
        </form>
    </div>
@endsection