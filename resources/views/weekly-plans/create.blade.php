@extends('layouts.academy')

@section('title', 'إنشاء خطة أسبوعية')

@section('content')
    <div class="page-header">
        <h1>إنشاء خطة أسبوعية جديدة</h1>
        <a href="{{ route('weekly-plans.index', ['level_id' => $levelId]) }}" class="btn btn-outline">⟵ رجوع للقائمة</a>
    </div>

    <div class="card" style="max-width: 560px;">
        <form method="POST" action="{{ route('weekly-plans.store') }}">
            @csrf

            <div class="form-group">
                <label for="level_id">المستوى</label>
                <select name="level_id" id="level_id" required>
                    @foreach ($levels as $level)
                        <option value="{{ $level->id }}" {{ old('level_id', $levelId) == $level->id ? 'selected' : '' }}>
                            {{ $level->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label for="week_number">رقم الأسبوع</label>
                <input type="number" name="week_number" id="week_number" min="1" required
                       value="{{ old('week_number', $nextWeekNumber) }}">
            </div>

            <div class="form-group">
                <label for="title">عنوان الخطة</label>
                <input type="text" name="title" id="title" required value="{{ old('title') }}"
                       placeholder="مثال: سورة الفاتحة وآداب الاستماع">
            </div>

            <div class="form-group">
                <label for="description">الوصف (اختياري)</label>
                <textarea name="description" id="description" rows="3">{{ old('description') }}</textarea>
            </div>

            <div class="row-2" style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="form-group">
                    <label for="start_date">تاريخ البداية (اختياري)</label>
                    <input type="date" name="start_date" id="start_date" value="{{ old('start_date') }}">
                </div>
                <div class="form-group">
                    <label for="end_date">تاريخ النهاية (اختياري)</label>
                    <input type="date" name="end_date" id="end_date" value="{{ old('end_date') }}">
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">إنشاء الخطة</button>
        </form>
    </div>
@endsection