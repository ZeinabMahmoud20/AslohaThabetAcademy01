<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بوابة أولياء الأمور — أكاديمية العلوم الشرعية</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --color-gold: #F1C40F;
            --color-green: #1E8449;
            --color-green-dark: #166638;
            --color-white: #FFFFFF;
            --color-border: #e7e2d0;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: 'Cairo', system-ui, sans-serif;
            background: linear-gradient(180deg, var(--color-green) 0%, var(--color-green-dark) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 14px;
        }
        .panel {
            background: var(--color-white);
            width: 100%;
            max-width: 420px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
        }
        .panel-header {
            background: linear-gradient(180deg, var(--color-gold) 0%, #d4ac0d 100%);
            padding: 26px 30px;
            text-align: center;
        }
        .panel-header h1 { margin: 0; font-size: 20px; color: var(--color-green-dark); font-weight: 800; }
        .panel-header p { margin: 6px 0 0; color: var(--color-green-dark); font-size: 13px; opacity: 0.85; }
        .panel-body { padding: 28px 30px; }
        .form-group { margin-bottom: 16px; }
        .form-group label { display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px; color: var(--color-green-dark); }
        .form-group input {
            width: 100%; padding: 11px 12px; border-radius: 8px;
            border: 1.5px solid var(--color-border); font-family: inherit; font-size: 15px;
        }
        .form-group input:focus { outline: none; border-color: var(--color-gold); }
        .btn-submit {
            width: 100%; padding: 13px; border: none; border-radius: 10px;
            background: var(--color-green); color: var(--color-white);
            font-weight: 800; font-size: 15px; cursor: pointer; font-family: inherit;
        }
        .btn-submit:hover { background: var(--color-green-dark); }
        .alert-error { background: #fbe4e1; color: #a5311f; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
        .hint { text-align:center; color: #888; font-size: 12px; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="panel">
        <div class="panel-header">
            <h1>📖 بوابة أولياء الأمور</h1>
            <p>تابع درجات ومواد أبنائك</p>
        </div>
        <div class="panel-body">

            @if ($errors->any())
                <div class="alert-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('portal.login.attempt') }}">
                @csrf
                <div class="form-group">
                    <label for="phone_number">رقم الهاتف المسجَّل</label>
                    <input type="text" name="phone_number" id="phone_number" required dir="ltr"
                           value="{{ old('phone_number') }}" autofocus>
                </div>
                <div class="form-group">
                    <label for="access_code">كود الدخول</label>
                    <input type="text" name="access_code" id="access_code" required dir="ltr" inputmode="numeric">
                </div>
                <button type="submit" class="btn-submit">دخول</button>
            </form>

            <p class="hint">الكود يتم تسليمه من إدارة الأكاديمية مباشرة.</p>
        </div>
    </div>
</body>
</html>