@extends('layouts.academy')

@section('title', 'إضافة معلم/ة أو مشرف/ة')

@section('content')
    <div class="page-header">
        <h1>إضافة {{ $role === 'supervisor' ? 'مشرف/ة' : 'معلم/ة' }} جديد/ة</h1>
        <a href="{{ route('staff.index', ['role' => $role]) }}" class="btn btn-outline">⟵ رجوع للقائمة</a>
    </div>

    <div class="card" style="max-width: 520px;">
        <form method="POST" action="{{ route('staff.store') }}">
            @csrf

            <div class="form-group">
                <label for="full_name">الاسم الكامل</label>
                <input type="text" name="full_name" id="full_name" required value="{{ old('full_name') }}">
            </div>

            <div class="form-group">
                <label for="email">البريد الإلكتروني</label>
                <input type="email" name="email" id="email" required value="{{ old('email') }}" dir="ltr">
            </div>

            <div class="form-group">
                <label for="phone_number">رقم الهاتف (اختياري)</label>
                <input type="text" name="phone_number" id="phone_number" value="{{ old('phone_number') }}" dir="ltr">
            </div>

            <div class="form-group">
                <label for="role_type">الدور</label>
                <select name="role_type" id="role_type" required>
                    <option value="teacher" {{ old('role_type', $role) === 'teacher' ? 'selected' : '' }}>معلم/ة</option>
                    <option value="supervisor" {{ old('role_type', $role) === 'supervisor' ? 'selected' : '' }}>مشرف/ة</option>
                </select>
            </div>

            <div class="form-group">
                <label for="password">كلمة المرور المبدئية</label>
                <input type="password" name="password" id="password" required minlength="8">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">حفظ</button>
        </form>
    </div>
@endsection