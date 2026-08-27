<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $student->full_name }} — بوابة أولياء الأمور</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-gold: #F1C40F;
            --color-gold-dark: #d4ac0d;
            --color-green: #1E8449;
            --color-green-dark: #166638;
            --color-white: #FFFFFF;
            --color-border: #e7e2d0;
            --color-bg: #f7f6f1;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Cairo', system-ui, sans-serif; background: var(--color-bg); }
        a { text-decoration: none; color: inherit; }
        .topbar {
            background: linear-gradient(180deg, var(--color-gold) 0%, var(--color-gold-dark) 100%);
            color: var(--color-green-dark);
            padding: 14px 24px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .topbar .back { font-weight: 700; font-size: 13px; }
        .content { max-width: 720px; margin: 0 auto; padding: 24px 20px; }
        .header-card {
            background: var(--color-green); color: var(--color-white);
            border-radius: 14px; padding: 20px; margin-bottom: 20px;
        }
        .header-card h2 { margin: 0 0 4px; font-size: 20px; }
        .header-card .meta { font-size: 13px; opacity: 0.85; }

        .final-result-card {
            background: var(--color-white); border: 2px solid var(--color-gold);
            border-radius: 14px; padding: 18px 20px; margin-bottom: 20px; text-align: center;
        }
        .final-result-card .score { font-size: 32px; font-weight: 800; color: var(--color-green-dark); }
        .final-result-card .grade { font-size: 15px; font-weight: 700; margin-top: 4px; }

        .week-card {
            background: var(--color-white); border: 1px solid var(--color-border);
            border-radius: 14px; padding: 18px 20px; margin-bottom: 14px;
            box-shadow: 0 2px 8px rgba(30,132,73,0.06);
        }
        .week-card .week-title {
            display:flex; align-items:center; justify-content:space-between;
            font-weight: 800; color: var(--color-green-dark); font-size: 15px; margin-bottom: 10px;
        }
        .week-score { background: #eef7f0; color: var(--color-green-dark); padding: 3px 12px; border-radius: 14px; font-size: 13px; font-weight: 700; }
        .item-row { display:flex; justify-content:space-between; padding: 6px 0; border-bottom: 1px dashed var(--color-border); font-size: 13px; }
        .item-row:last-of-type { border-bottom: none; }
        .resource-links { margin-top: 12px; display:flex; gap: 10px; flex-wrap: wrap; }
        .resource-links a {
            background: var(--color-gold); color: var(--color-green-dark);
            padding: 6px 14px; border-radius: 8px; font-size: 12px; font-weight: 700;
        }
        .empty { text-align:center; color: #999; padding: 40px 0; }
    </style>
</head>
<body>
    <header class="topbar">
        <a href="{{ route('portal.dashboard') }}" class="back">⟵ رجوع لكل الأبناء</a>
        <span>📖 {{ $student->full_name }}</span>
    </header>

    <div class="content">
        <div class="header-card">
            <h2>{{ $student->full_name }}</h2>
            <div class="meta">
                @if ($activeAttempt)
                    {{ $activeAttempt->level->name }}
                @else
                    لا يوجد مستوى نشط حالياً
                @endif
            </div>
        </div>

        @if ($finalResult)
            <div class="final-result-card">
                <div class="score">{{ $finalResult->final_score }} / 100</div>
                <div class="grade">{{ $finalResult->grade_label }} — {{ $finalResult->pass_fail === 'passed' ? 'ناجح/ة' : 'يحتاج إعادة' }}</div>
            </div>
        @endif

        <h3 style="color: var(--color-green-dark); margin-bottom: 12px;">الامتحانات المتاحة</h3>

@forelse ($availableExams as $exam)
    @php $examAttempt = $examAttemptsByExamId->get($exam->id); @endphp
    <div class="week-card" style="display:flex; align-items:center; justify-content:space-between;">
        <div>
            <strong style="color: var(--color-green-dark);">{{ $exam->title }}</strong>
            <div style="font-size: 12px; color: #6b7a72; margin-top: 4px;">
                متاح حتى {{ $exam->end_at->format('Y-m-d H:i') }} — المدة {{ $exam->duration_minutes }} دقيقة
            </div>
        </div>

        @if (! $examAttempt || $examAttempt->status === 'not_started')
            <form method="POST" action="{{ route('portal.exams.start', [$student, $exam]) }}">
                @csrf
                <button type="submit" class="btn-view" style="border:none; cursor:pointer; font-family:inherit;">ابدأ الامتحان</button>
            </form>
        @elseif ($examAttempt->status === 'in_progress')
            <a href="{{ route('portal.exams.take', [$student, $exam]) }}" class="btn-view">متابعة الامتحان</a>
        @else
            <a href="{{ route('portal.exams.result', [$student, $exam]) }}" class="week-score" style="text-decoration:none;">
                {{ $examAttempt->score }} / {{ $exam->max_score }}
            </a>
        @endif
    </div>
@empty
    <p class="empty" style="padding: 16px 0;">لا توجد امتحانات متاحة حالياً.</p>
@endforelse

        <h3 style="color: var(--color-green-dark); margin-bottom: 12px;">الأسابيع والدرجات</h3>

        @forelse ($weeklyEvaluations as $evaluation)
            <div class="week-card">
                <div class="week-title">
                    <span>الأسبوع {{ $evaluation->weeklyPlan->week_number }}: {{ $evaluation->weeklyPlan->title }}</span>
                    <span class="week-score">{{ $evaluation->total_score }} / {{ $evaluation->max_score }}</span>
                </div>

                @foreach ($evaluation->itemScores as $itemScore)
                    <div class="item-row">
                        <span>{{ $itemScore->weeklyPlanEvaluationItem->evaluationItem->name }}</span>
                        <span>{{ $itemScore->score ?? '—' }} / {{ $itemScore->weeklyPlanEvaluationItem->max_score }}</span>
                    </div>
                @endforeach

                @if ($evaluation->weeklyPlan->video || $evaluation->weeklyPlan->homework)
                    <div class="resource-links">
                        @if ($evaluation->weeklyPlan->video?->video_url)
                            <a href="{{ $evaluation->weeklyPlan->video->video_url }}" target="_blank">🎬 مشاهدة فيديو الأسبوع</a>
                        @endif
                        @if ($evaluation->weeklyPlan->homework)
                            <a href="#" onclick="alert('{{ addslashes($evaluation->weeklyPlan->homework->instructions ?? $evaluation->weeklyPlan->homework->title) }}'); return false;">📝 عرض الواجب</a>
                        @endif
                    </div>
                @endif
            </div>
        @empty
            <p class="empty">لا توجد تقييمات معتمدة بعد لهذا الأسبوع.</p>
        @endforelse
    </div>
</body>
</html>