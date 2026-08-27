<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>التقديم لأكاديمية العلوم الشرعية</title>
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
            max-width: 620px;
            border-radius: 18px;
            overflow: hidden;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25);
        }
        .panel-header {
            background: linear-gradient(180deg, var(--color-gold) 0%, #d4ac0d 100%);
            padding: 26px 30px;
            text-align: center;
        }
        .panel-header h1 { margin: 0; font-size: 22px; color: var(--color-green-dark); font-weight: 800; }
        .panel-header p { margin: 6px 0 0; color: var(--color-green-dark); font-size: 14px; opacity: 0.85; }
        .panel-body { padding: 28px 30px; }
        fieldset { border: 1px solid var(--color-border); border-radius: 12px; margin-bottom: 18px; padding: 16px 18px; }
        legend { color: var(--color-green-dark); font-weight: 700; padding: 0 8px; font-size: 14px; }
        .form-group { margin-bottom: 14px; }
        .form-group label { display: block; font-weight: 700; font-size: 13px; margin-bottom: 6px; color: var(--color-green-dark); }
        .form-group input {
            width: 100%; padding: 10px 12px; border-radius: 8px;
            border: 1.5px solid var(--color-border); font-family: inherit; font-size: 14px;
        }
        .form-group input:focus { outline: none; border-color: var(--color-gold); }
        .row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .btn-submit {
            width: 100%; padding: 13px; border: none; border-radius: 10px;
            background: var(--color-green); color: var(--color-white);
            font-weight: 800; font-size: 15px; cursor: pointer; font-family: inherit;
        }
        .btn-submit:hover { background: var(--color-green-dark); }
        .alert-error { background: #fbe4e1; color: #a5311f; padding: 12px 16px; border-radius: 8px; margin-bottom: 16px; font-size: 13px; }
    </style>
</head>
<body>
    <div class="panel">
        <div class="panel-header">
            <h1>📖 أكاديمية أصلها ثابت للعلوم الشرعية للأطفال</h1>
            <p>نموذج تقديم طلب التحاق</p>
        </div>
        <div class="panel-body">

            @if ($errors->any())
                <div class="alert-error">
                    @foreach ($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form method="POST" action="{{ route('applications.store') }}">
                @csrf

                <fieldset>
                    <legend>بيانات ولي الأمر</legend>
                    <div class="form-group">
                        <label for="guardian_full_name">الاسم الكامل</label>
                        <input type="text" name="guardian[full_name]" id="guardian_full_name" required value="{{ old('guardian.full_name') }}">
                    </div>
                    <div class="row-2">
                        <div class="form-group">
                            <label for="guardian_phone_number">رقم الهاتف</label>
                            <input type="text" name="guardian[phone_number]" id="guardian_phone_number" required value="{{ old('guardian.phone_number') }}">
                        </div>
                        <div class="form-group">
                            <label for="guardian_relationship">صلة القرابة</label>
                            <input type="text" name="guardian[relationship_to_student]" id="guardian_relationship" placeholder="أب / أم / ولي أمر" required value="{{ old('guardian.relationship_to_student') }}">
                        </div>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>بيانات الطالب</legend>
                    <div class="form-group">
                        <label for="student_full_name">الاسم الكامل للطالب</label>
                        <input type="text" name="student_full_name" id="student_full_name" required value="{{ old('student_full_name') }}">
                    </div>
                    <div class="row-2">
                        <div class="form-group">
                            <label for="student_dob">تاريخ الميلاد</label>
                            <input type="date" name="student_dob" id="student_dob" required value="{{ old('student_dob') }}">
                        </div>
                        <div class="form-group">
                            <label for="country">الدولة</label>
                            <input type="text" name="country" id="country" value="{{ old('country') }}">
                        </div>
                    </div>
                    <div class="row-2">
                        <div class="form-group">
                            <label for="school">المدرسة</label>
                            <input type="text" name="school" id="school" value="{{ old('school') }}">
                        </div>
                        <div class="form-group">
                            <label for="grade_level">الصف الدراسي</label>
                            <input type="text" name="grade_level" id="grade_level" value="{{ old('grade_level') }}">
                        </div>
                    </div>
                </fieldset>

                <button type="submit" class="btn-submit">إرسال الطلب</button>
            </form>
        </div>
    </div>
</body>
</html>
