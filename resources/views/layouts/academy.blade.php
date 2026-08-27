<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'أكاديمية العلوم الشرعية') — لوحة الإدارة</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --color-gold: #F1C40F;
            --color-gold-dark: #d4ac0d;
            --color-green: #1E8449;
            --color-green-dark: #166638;
            --color-white: #FFFFFF;
            --color-ink: #1f2a24;
            --color-muted: #6b7a72;
            --color-bg: #f7f6f1;
            --color-border: #e7e2d0;
            --radius: 12px;
            --shadow: 0 2px 10px rgba(30, 132, 73, 0.08);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: 'Cairo', system-ui, sans-serif;
            background: var(--color-bg);
            color: var(--color-ink);
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        /* ---------- Top bar: golden ---------- */
        .topbar {
            background: linear-gradient(180deg, var(--color-gold) 0%, var(--color-gold-dark) 100%);
            color: var(--color-green-dark);
            padding: 14px 28px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .topbar .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 800;
            font-size: 19px;
        }

        .topbar .brand .badge {
            width: 34px;
            height: 34px;
            border-radius: 50%;
            background: var(--color-green);
            color: var(--color-white);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
            box-shadow: inset 0 0 0 3px rgba(255, 255, 255, 0.5);
        }

        .topbar .user-chip {
            background: rgba(255, 255, 255, 0.35);
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 600;
            font-size: 14px;
        }

        /* ---------- Shell: sidebar (dark green) + content (white) ---------- */
        .shell {
            display: flex;
            min-height: calc(100vh - 62px);
        }

        .sidebar {
            width: 240px;
            background: var(--color-green);
            color: var(--color-white);
            padding: 22px 14px;
            flex-shrink: 0;
        }

        .sidebar nav a {
            display: block;
            padding: 11px 14px;
            border-radius: var(--radius);
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 4px;
            color: rgba(255, 255, 255, 0.88);
            transition: background 0.15s ease;
        }

        .sidebar nav a:hover {
            background: var(--color-green-dark);
            color: var(--color-white);
        }

        .sidebar nav a.active {
            background: var(--color-gold);
            color: var(--color-green-dark);
        }

        .sidebar .section-label {
            font-size: 12px;
            color: rgba(255, 255, 255, 0.55);
            margin: 18px 10px 8px;
            font-weight: 700;
            letter-spacing: 0.03em;
        }

        .content {
            flex: 1;
            padding: 28px 32px;
            background: var(--color-white);
        }

        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 22px;
            padding-bottom: 16px;
            border-bottom: 2px solid var(--color-gold);
        }

        .page-header h1 {
            font-size: 22px;
            font-weight: 800;
            color: var(--color-green-dark);
            margin: 0;
        }

        /* ---------- Reusable pieces ---------- */
        .card {
            background: var(--color-white);
            border: 1px solid var(--color-border);
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            padding: 20px;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 9px 18px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--color-green);
            color: var(--color-white);
        }

        .btn-primary:hover {
            background: var(--color-green-dark);
        }

        .btn-gold {
            background: var(--color-gold);
            color: var(--color-green-dark);
        }

        .btn-gold:hover {
            background: var(--color-gold-dark);
        }

        .btn-danger {
            background: #c0392b;
            color: var(--color-white);
        }

        .btn-danger:hover {
            background: #a5311f;
        }

        .btn-outline {
            background: transparent;
            color: var(--color-green);
            border: 1.5px solid var(--color-green);
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            text-align: right;
            background: #eef7f0;
            color: var(--color-green-dark);
            font-size: 13px;
            padding: 12px 14px;
            border-bottom: 2px solid var(--color-gold);
        }

        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--color-border);
            font-size: 14px;
        }

        tr:hover td {
            background: #fbfaf3;
        }

        .badge-status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .badge-pending {
            background: #fdf3d0;
            color: #a9820b;
        }

        .badge-accepted {
            background: #e3f5ea;
            color: var(--color-green-dark);
        }

        .badge-rejected {
            background: #fbe4e1;
            color: #a5311f;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
            font-weight: 600;
            font-size: 14px;
        }

        .alert-success {
            background: #e3f5ea;
            color: var(--color-green-dark);
            border: 1px solid var(--color-green);
        }

        .alert-error {
            background: #fbe4e1;
            color: #a5311f;
            border: 1px solid #c0392b;
        }

        .form-group {
            margin-bottom: 16px;
        }

        .form-group label {
            display: block;
            font-weight: 700;
            font-size: 13px;
            margin-bottom: 6px;
            color: var(--color-green-dark);
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border-radius: 8px;
            border: 1.5px solid var(--color-border);
            font-family: inherit;
            font-size: 14px;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: var(--color-gold);
        }
    </style>

    @stack('styles')
</head>

<body>

    <header class="topbar">
        <div class="brand">
            <span class="badge">📖</span>
            <span>أكاديمية العلوم الشرعية للأطفال</span>
        </div>
<div style="display:flex; align-items:center; gap:12px;">
    <div class="user-chip">{{ auth()->user()->full_name ?? 'زائر' }}</div>
    <form method="POST" action="{{ route('logout') }}">
        @csrf
        <button type="submit" style="
            background: rgba(255,255,255,0.35);
            border: none;
            padding: 6px 14px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 13px;
            font-family: inherit;
            color: var(--color-green-dark);
            cursor: pointer;
        ">
            🚪 تسجيل الخروج
        </button>
    </form>
</div>
    </header>

    <div class="shell">
        <aside class="sidebar">
<nav>
    @php $role = auth()->user()->role_type; @endphp

    @if (in_array($role, ['admin', 'general_supervisor']))
        <a href="{{ route('applications.index') }}"
            class="{{ request()->routeIs('applications.*') ? 'active' : '' }}">
            📝 طلبات الالتحاق
        </a>
        <a href="{{ route('students.index') }}"
            class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
            🎓 تعيين الطلاب للفصول
        </a>
        <div class="section-label">إدارة أكاديمية</div>
        <a href="{{ route('staff.index', ['role' => 'teacher']) }}"
            class="{{ request()->routeIs('staff.*') ? 'active' : '' }}">
            👩‍🏫 المعلمات والمشرفات
        </a>
    @endif

    @if (in_array($role, ['admin', 'general_supervisor', 'teacher', 'supervisor']))
        <a href="{{ route('classes.index') }}"
            class="{{ request()->routeIs('classes.*') ? 'active' : '' }}">
            🏫 الفصول والمستويات
        </a>
    @endif

    @if (in_array($role, ['admin', 'general_supervisor', 'teacher']))
        <a href="{{ route('weekly-plans.index') }}"
            class="{{ request()->routeIs('weekly-plans.*') ? 'active' : '' }}">
            🗓️ الخطط الأسبوعية
        </a>
        <a href="{{ route('weekly-evaluations.index') }}"
            class="{{ request()->routeIs('weekly-evaluations.*') ? 'active' : '' }}">
            ✅ التقييم الأسبوعي
        </a>
    @endif

    @if (in_array($role, ['admin', 'general_supervisor', 'teacher', 'supervisor']))
        <a href="{{ route('exams.index') }}"
            class="{{ request()->routeIs('exams.*') ? 'active' : '' }}">
            📋 الامتحانات
        </a>
    @endif

    @if (in_array($role, ['admin', 'general_supervisor', 'teacher']))
        <a href="{{ route('final-results.index') }}"
            class="{{ request()->routeIs('final-results.*') ? 'active' : '' }}">
            🧾 نتائج الطلاب
        </a>
    @endif

    <a href="#">🔔 الإشعارات</a>
</nav>
        </aside>

        <main class="content">
            @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if (session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
            @endif
            @if ($errors->any())
            <div class="alert alert-error">
                @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
                @endforeach
            </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>

</html>