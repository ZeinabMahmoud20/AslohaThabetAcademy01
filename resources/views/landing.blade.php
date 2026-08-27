<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>أكاديمية العلوم الشرعية للأطفال</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-gold: #F1C40F;
            --color-gold-dark: #d4ac0d;
            --color-green: #1E8449;
            --color-green-dark: #166638;
            --color-white: #FFFFFF;
            --color-bg: #f7f6f1;
            --color-border: #e7e2d0;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Cairo', system-ui, sans-serif;
            background: var(--color-bg);
            color: #1f2a24;
        }
        a { text-decoration: none; }

        .topbar {
            background: linear-gradient(180deg, var(--color-gold) 0%, var(--color-gold-dark) 100%);
            padding: 16px 28px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .topbar .brand { font-weight: 800; font-size: 18px; color: var(--color-green-dark); display:flex; align-items:center; gap:8px; }
        .topbar .btn-portal {
            background: var(--color-green); color: var(--color-white);
            padding: 9px 20px; border-radius: 8px; font-weight: 700; font-size: 14px;
        }

        .hero {
            background: linear-gradient(180deg, var(--color-green) 0%, var(--color-green-dark) 100%);
            color: var(--color-white);
            padding: 60px 24px;
            text-align: center;
        }
        .hero h1 { font-size: 30px; font-weight: 800; margin: 0 0 12px; }
        .hero p { font-size: 16px; opacity: 0.9; max-width: 560px; margin: 0 auto 28px; line-height: 1.9; }
        .hero .cta-group { display:flex; gap: 14px; justify-content: center; flex-wrap: wrap; }
        .btn-apply {
            background: var(--color-gold); color: var(--color-green-dark);
            padding: 14px 32px; border-radius: 10px; font-weight: 800; font-size: 16px;
        }
        .btn-apply:hover { background: var(--color-gold-dark); }
        .btn-outline-white {
            background: transparent; color: var(--color-white); border: 2px solid rgba(255,255,255,0.6);
            padding: 12px 30px; border-radius: 10px; font-weight: 700; font-size: 16px;
        }

        .section { max-width: 900px; margin: 0 auto; padding: 50px 24px; }
        .section h2 { color: var(--color-green-dark); font-size: 22px; text-align:center; margin-bottom: 30px; }

        .features { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 18px; }
        .feature-card {
            background: var(--color-white); border: 1px solid var(--color-border);
            border-radius: 14px; padding: 22px 18px; text-align: center;
            box-shadow: 0 2px 10px rgba(30,132,73,0.06);
        }
        .feature-card .icon { font-size: 30px; margin-bottom: 10px; }
        .feature-card h3 { font-size: 15px; color: var(--color-green-dark); margin: 0 0 6px; }
        .feature-card p { font-size: 13px; color: #6b7a72; margin: 0; line-height: 1.7; }

        .levels-strip {
            background: var(--color-white); border-radius: 14px; padding: 24px;
            display: flex; gap: 10px; justify-content: center; flex-wrap: wrap;
            box-shadow: 0 2px 10px rgba(30,132,73,0.06);
        }
        .level-pill {
            background: #eef7f0; color: var(--color-green-dark);
            padding: 8px 18px; border-radius: 20px; font-weight: 700; font-size: 13px;
        }

        footer {
            text-align: center; padding: 30px 20px; color: #9aa39c; font-size: 12px;
            border-top: 1px solid var(--color-border);
        }
        footer a { color: #9aa39c; text-decoration: underline; }
    </style>
</head>
<body>
    <header class="topbar">
        <div class="brand">📖 أكاديمية العلوم الشرعية للأطفال</div>
        <a href="{{ route('portal.login') }}" class="btn-portal">بوابة أولياء الأمور</a>
    </header>

    <section class="hero">
        <h1>رحلة تعليمية إيمانية لأبنائكم</h1>
        <p>
            نقدّم منهجاً متكاملاً في القرآن الكريم، والحديث الشريف، والعقيدة، والذِّكر،
            وبناء الشخصية، عبر ستة مستويات تعليمية متدرّجة، بمتابعة أسبوعية دقيقة
            ونتائج واضحة لأولياء الأمور.
        </p>
        <div class="cta-group">
            <a href="{{ route('applications.create') }}" class="btn-apply">قدّم الآن</a>
            <a href="{{ route('portal.login') }}" class="btn-outline-white">متابعة ابنك/ابنتك</a>
        </div>
    </section>

    <section class="section">
        <h2>ماذا نقدّم؟</h2>
        <div class="features">
            <div class="feature-card">
                <div class="icon">📗</div>
                <h3>القرآن الكريم</h3>
                <p>حفظ وتلاوة وتجويد بمتابعة أسبوعية</p>
            </div>
            <div class="feature-card">
                <div class="icon">📜</div>
                <h3>الحديث الشريف</h3>
                <p>فهم وحفظ أحاديث مختارة تناسب أعمارهم</p>
            </div>
            <div class="feature-card">
                <div class="icon">🕌</div>
                <h3>العقيدة</h3>
                <p>ترسيخ أصول الإيمان بأسلوب مبسّط</p>
            </div>
            <div class="feature-card">
                <div class="icon">🌱</div>
                <h3>بناء الشخصية</h3>
                <p>الذِّكر والالتزام والسلوك القويم</p>
            </div>
        </div>
    </section>

    <section class="section" style="padding-top: 0;">
        <h2>ستة مستويات تعليمية متدرّجة</h2>
        <div class="levels-strip">
            <span class="level-pill">المستوى الأول</span>
            <span class="level-pill">المستوى الثاني</span>
            <span class="level-pill">المستوى الثالث</span>
            <span class="level-pill">المستوى الرابع</span>
            <span class="level-pill">المستوى الخامس</span>
            <span class="level-pill">المستوى السادس</span>
        </div>
    </section>

    <footer>
        &copy; {{ date('Y') }} أكاديمية العلوم الشرعية للأطفال — جميع الحقوق محفوظة
        <br>
        <a href="{{ route('login') }}">دخول فريق العمل</a>
    </footer>
</body>
</html>