<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة أولياء الأمور</title>
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
        .topbar .brand { font-weight: 800; font-size: 17px; }
        .topbar form button {
            background: rgba(255,255,255,0.35); border: none; padding: 7px 16px;
            border-radius: 18px; font-weight: 700; font-size: 13px; cursor: pointer; color: var(--color-green-dark);
            font-family: inherit;
        }
        .content { max-width: 720px; margin: 0 auto; padding: 28px 20px; }
        .content h2 { color: var(--color-green-dark); font-size: 19px; margin-bottom: 18px; }
        .student-card {
            background: var(--color-white); border: 1px solid var(--color-border);
            border-radius: 14px; padding: 18px 20px; margin-bottom: 14px;
            display: flex; align-items: center; justify-content: space-between;
            box-shadow: 0 2px 8px rgba(30,132,73,0.06);
        }
        .student-card .name { font-weight: 800; font-size: 16px; color: var(--color-green-dark); }
        .student-card .meta { font-size: 13px; color: #6b7a72; margin-top: 4px; }
        .btn-view {
            background: var(--color-green); color: var(--color-white);
            padding: 9px 18px; border-radius: 8px; font-weight: 700; font-size: 13px;
        }
        .empty { text-align:center; color: #999; padding: 40px 0; }
    </style>
</head>
<body>
    <header class="topbar">
        <span class="brand">📖 أهلاً، {{ $guardian->full_name }}</span>
        <form method="POST" action="{{ route('portal.logout') }}">
            @csrf
            <button type="submit">تسجيل الخروج</button>
        </form>
    </header>

    <div class="content">
        <h2>أبناؤك المسجّلون</h2>

        @forelse ($students as $student)
            <div class="student-card">
                <div>
                    <div class="name">{{ $student->full_name }}</div>
                    <div class="meta">
                        @if ($student->activeLevelAttempt)
                            {{ $student->activeLevelAttempt->level->name }}
                            @if ($student->activeLevelAttempt->activeClassAssignment)
                                — {{ $student->activeLevelAttempt->activeClassAssignment->classRoom->name }}
                            @endif
                        @else
                            لا يوجد مستوى نشط حالياً
                        @endif
                    </div>
                </div>
                <a href="{{ route('portal.students.show', $student) }}" class="btn-view">عرض التفاصيل</a>
            </div>
        @empty
            <p class="empty">لا يوجد أبناء مسجّلون على هذا الحساب حالياً.</p>
        @endforelse
    </div>
</body>
</html>