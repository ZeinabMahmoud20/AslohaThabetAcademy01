<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تم إرسال الطلب</title>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@600;800&display=swap" rel="stylesheet">
    <style>
        body {
            margin: 0; font-family: 'Cairo', sans-serif;
            background: linear-gradient(180deg, #1E8449 0%, #166638 100%);
            min-height: 100vh; display: flex; align-items: center; justify-content: center;
        }
        .box {
            background: #FFFFFF; border-radius: 18px; padding: 40px 44px; text-align: center;
            box-shadow: 0 12px 40px rgba(0,0,0,0.25); max-width: 460px;
        }
        .icon { font-size: 52px; margin-bottom: 10px; }
        h1 { color: #166638; font-size: 20px; margin: 8px 0; }
        p { color: #555; font-size: 14px; line-height: 1.8; }
        a {
            display: inline-block; margin-top: 18px; padding: 10px 26px; border-radius: 10px;
            background: #F1C40F; color: #166638; font-weight: 800; text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="box">
        <div class="icon">✅</div>
        <h1>تم إرسال طلبكم بنجاح</h1>
        <p>سيقوم فريق الأكاديمية بمراجعة الطلب والتواصل معكم عبر رقم الهاتف المسجَّل خلال أيام قليلة.</p>
        <a href="{{ route('applications.create') }}">تقديم طلب آخر</a>
    </div>
</body>
</html>
