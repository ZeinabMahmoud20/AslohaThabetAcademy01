@extends('layouts.academy')

@section('title', 'إنشاء امتحان')

@section('content')
    <div class="page-header">
        <h1>إنشاء امتحان جديد</h1>
        <a href="{{ route('exams.index', ['level_id' => $levelId]) }}" class="btn btn-outline">⟵ رجوع للقائمة</a>
    </div>

    <div class="card" style="max-width: 560px;">
        <form method="POST" action="{{ route('exams.store') }}">
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
                <label for="title">عنوان الامتحان</label>
                <input type="text" name="title" id="title" required value="{{ old('title') }}">
            </div>

            <div class="form-group">
                <label for="description">الوصف (اختياري)</label>
                <textarea name="description" id="description" rows="2">{{ old('description') }}</textarea>
            </div>

            <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                <div class="form-group">
                    <label for="start_at">يبدأ في</label>
                    <input type="datetime-local" name="start_at" id="start_at" required value="{{ old('start_at') }}">
                </div>
                <div class="form-group">
                    <label for="end_at">ينتهي في</label>
                    <input type="datetime-local" name="end_at" id="end_at" required value="{{ old('end_at') }}">
                </div>
            </div>

            <div class="form-group">
                <label for="duration_minutes">مدة الامتحان (بالدقائق)</label>
                <input type="number" name="duration_minutes" id="duration_minutes" min="1" max="600" required
                       value="{{ old('duration_minutes', 30) }}">
                <small style="color: var(--color-muted);">
                    لو الطالب دخل الامتحان متأخراً، الوقت المتاح هو الأقل بين هذه المدة ووقت نهاية الامتحان.
                </small>
            </div>

            <button type="submit" class="btn btn-primary" style="width:100%;">إنشاء الامتحان</button>
        </form>
    </div>
@endsection