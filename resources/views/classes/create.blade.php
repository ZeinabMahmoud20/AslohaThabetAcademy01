@extends('layouts.academy')

@section('title', 'إنشاء فصل جديد')

@section('content')
    <div class="page-header">
        <h1>إنشاء فصل جديد</h1>
        <a href="{{ route('classes.index') }}" class="btn btn-outline">⟵ رجوع للقائمة</a>
    </div>

    <div class="card" style="max-width: 480px;">
        <form method="POST" action="{{ route('classes.store') }}">
            @csrf

            <div class="form-group">
                <label for="level_id">المستوى</label>
                <select name="level_id" id="level_id" required>
                    <option value="" disabled selected>اختر المستوى</option>
                    @foreach ($levels as $level)
                        <option value="{{ $level->id }}" {{ old('level_id') == $level->id ? 'selected' : '' }}>
                            {{ $level->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="name">اسم الفصل</label>
                <input type="text" name="name" id="name" required value="{{ old('name') }}" placeholder="مثال: فصل أ">
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">إنشاء الفصل</button>
        </form>
    </div>
@endsection
