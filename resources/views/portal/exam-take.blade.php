<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->title }} — امتحان</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-gold: #F1C40F; --color-gold-dark: #d4ac0d;
            --color-green: #1E8449; --color-green-dark: #166638;
            --color-white: #FFFFFF; --color-border: #e7e2d0; --color-bg: #f7f6f1;
        }
        * { box-sizing: border-box; }
        body { margin: 0; font-family: 'Cairo', system-ui, sans-serif; background: var(--color-bg); }
        .topbar {
            background: linear-gradient(180deg, var(--color-gold) 0%, var(--color-gold-dark) 100%);
            color: var(--color-green-dark); padding: 14px 24px;
            display: flex; align-items: center; justify-content: space-between;
            position: sticky; top: 0; z-index: 10;
        }
        #timer { font-weight: 800; font-size: 16px; font-family: monospace; }
        .content { max-width: 680px; margin: 0 auto; padding: 24px 20px; }
        .question-card {
            background: var(--color-white); border: 1px solid var(--color-border);
            border-radius: 14px; padding: 18px 20px; margin-bottom: 16px;
            box-shadow: 0 2px 8px rgba(30,132,73,0.06);
        }
        .question-card .q-title { font-weight: 800; color: var(--color-green-dark); margin-bottom: 12px; font-size: 15px; }
        .option-row {
            display: flex; align-items: center; gap: 10px; padding: 10px 12px;
            border: 1.5px solid var(--color-border); border-radius: 8px; margin-bottom: 8px; cursor: pointer;
        }
        .option-row:hover { border-color: var(--color-gold); }
        .btn-submit {
            width: 100%; padding: 14px; border: none; border-radius: 10px;
            background: var(--color-green); color: var(--color-white);
            font-weight: 800; font-size: 16px; cursor: pointer; font-family: inherit; margin-top: 10px;
        }
        .btn-submit:hover { background: var(--color-green-dark); }
    </style>
</head>
<body>
    <header class="topbar">
        <span style="font-weight:800;">{{ $exam->title }}</span>
        <span id="timer">--:--</span>
    </header>

    <div class="content">
        <form method="POST" action="{{ route('portal.exams.submit', [$student, $exam]) }}" id="examForm">
            @csrf

            @foreach ($exam->questions as $index => $question)
                <div class="question-card">
                    <div class="q-title">{{ $index + 1 }}. {{ $question->question_text }}</div>
                    @foreach ($question->options as $option)
                        <label class="option-row">
                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $option->id }}" required>
                            {{ $option->option_text }}
                        </label>
                    @endforeach
                </div>
            @endforeach

            <button type="submit" class="btn-submit">تسليم الامتحان</button>
        </form>
    </div>

    <script>
        // Client-side countdown is a VISUAL aid only -- the server enforces the
        // real deadline independently (see ExamGradingService::submit and
        // effectiveDeadline()). If the student's clock is wrong or they close
        // the tab, the server-side check still applies when they come back.
        const deadline = new Date({{ $examAttempt->effectiveDeadline()->timestamp * 1000 }});
        const timerEl = document.getElementById('timer');
        const formEl = document.getElementById('examForm');

        function tick() {
            const remainingMs = deadline - new Date();
            if (remainingMs <= 0) {
                timerEl.textContent = '00:00';
                formEl.submit(); // time's up -- auto-submit whatever is answered
                return;
            }
            const totalSeconds = Math.floor(remainingMs / 1000);
            const minutes = String(Math.floor(totalSeconds / 60)).padStart(2, '0');
            const seconds = String(totalSeconds % 60).padStart(2, '0');
            timerEl.textContent = `${minutes}:${seconds}`;
        }

        tick();
        setInterval(tick, 1000);
    </script>
</body>
</html>