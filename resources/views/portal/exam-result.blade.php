<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>نتيجة {{ $exam->title }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-gold: #F1C40F; --color-green: #1E8449; --color-green-dark: #166638;
            --color-white: #FFFFFF; --color-border: #e7e2d0; --color-bg: #f7f6f1;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Cairo', system-ui, sans-serif; background: var(--color-bg); }
        a { text-decoration: none; }
        .topbar {
            background: linear-gradient(180deg, var(--color-gold) 0%, #d4ac0d 100%);
            color: var(--color-green-dark); padding: 14px 24px;
        }
        .content { max-width: 680px; margin: 0 auto; padding: 24px 20px; }
        .result-card {
            background: var(--color-white); border: 2px solid var(--color-gold);
            border-radius: 16px; padding: 26px; text-align: center; margin-bottom: 20px;
        }
        .result-card .score { font-size: 36px; font-weight: 800; color: var(--color-green-dark); }
        .result-card .pct { font-size: 15px; color: #6b7a72; margin-top: 4px; }
        .q-card {
            background: var(--color-white); border: 1px solid var(--color-border);
            border-radius: 12px; padding: 14px 16px; margin-bottom: 10px;
        }
        .q-card .q-text { font-weight: 700; font-size: 14px; margin-bottom: 6px; }
        .badge { display:inline-block; padding: 2px 10px; border-radius: 12px; font-size: 12px; font-weight:700; }
        .badge-correct { background: #e3f5ea; color: var(--color-green-dark); }
        .badge-wrong { background: #fbe4e1; color: #a5311f; }
        .btn-back {
            display:inline-block; margin-top: 16px; background: var(--color-green); color: var(--color-white);
            padding: 10px 22px; border-radius: 8px; font-weight: 700; font-size: 14px;
        }
    </style>
</head>
<body>
    <header class="topbar"><strong>📖 نتيجة الامتحان</strong></header>

    <div class="content">
        <div class="result-card">
            <div class="score">{{ $examAttempt->score }} / {{ $exam->max_score }}</div>
            <div class="pct">النسبة: {{ $examAttempt->percentage }}%</div>
        </div>

        <h3 style="color: var(--color-green-dark); margin-bottom: 12px;">تفاصيل الإجابات</h3>

        @foreach ($examAttempt->answers as $answer)
            <div class="q-card">
                <div class="q-text">{{ $answer->question->question_text }}</div>
                <span class="badge {{ $answer->is_correct ? 'badge-correct' : 'badge-wrong' }}">
                    {{ $answer->is_correct ? 'إجابة صحيحة ✅' : 'إجابة غير صحيحة ❌' }}
                </span>
            </div>
        @endforeach

        <div style="text-align:center;">
            <a href="{{ route('portal.students.show', $student) }}" class="btn-back">⟵ رجوع لصفحة الطالب/ة</a>
        </div>
    </div>
</body>
</html>