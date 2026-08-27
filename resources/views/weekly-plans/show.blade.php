@extends('layouts.academy')

@section('title', 'إدارة الخطة الأسبوعية — ' . $weeklyPlan->title)

@section('content')
    <div class="page-header">
        <h1>الأسبوع {{ $weeklyPlan->week_number }}: {{ $weeklyPlan->title }}</h1>
        <a href="{{ route('weekly-plans.index', ['level_id' => $weeklyPlan->level_id]) }}" class="btn btn-outline">⟵ رجوع للقائمة</a>
    </div>

    @can('update', $weeklyPlan)
        @if ($weeklyPlan->status === 'draft')
            <div class="alert alert-error" style="display:flex; align-items:center; justify-content:space-between;">
                <span>هذه الخطة لا تزال مسودة ولن تظهر للمعلمين حتى يتم نشرها.</span>
                <form method="POST" action="{{ route('weekly-plans.publish', $weeklyPlan) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-gold">نشر الخطة الآن</button>
                </form>
            </div>
        @else
            <div class="alert alert-success">هذه الخطة منشورة ومتاحة للمعلمين.</div>
        @endif
    @else
        @if ($weeklyPlan->status === 'draft')
            <div class="alert alert-error">هذه الخطة لا تزال مسودة.</div>
        @else
            <div class="alert alert-success">هذه الخطة منشورة ومتاحة للمعلمين.</div>
        @endif
    @endcan

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">

        {{-- Video --}}
        <div class="card">
            <h3 style="color: var(--color-green-dark); margin-top:0;">🎬 الفيديو</h3>

            @can('update', $weeklyPlan)
                <form method="POST" action="{{ route('weekly-plans.video.save', $weeklyPlan) }}">
                    @csrf
                    <div class="form-group">
                        <label for="video_title">العنوان</label>
                        <input type="text" name="title" id="video_title" required
                               value="{{ old('title', $weeklyPlan->video?->title) }}">
                    </div>
                    <div class="form-group">
                        <label for="video_url">رابط الفيديو</label>
                        <input type="url" name="video_url" id="video_url" dir="ltr"
                               value="{{ old('video_url', $weeklyPlan->video?->video_url) }}"
                               placeholder="https://...">
                    </div>
                    <div class="form-group">
                        <label for="video_description">وصف (اختياري)</label>
                        <textarea name="description" id="video_description" rows="2">{{ old('description', $weeklyPlan->video?->description) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        {{ $weeklyPlan->video ? 'تحديث الفيديو' : 'حفظ الفيديو' }}
                    </button>
                </form>
            @else
                @if ($weeklyPlan->video)
                    <p><strong>{{ $weeklyPlan->video->title }}</strong></p>
                    @if ($weeklyPlan->video->description)
                        <p style="font-size: 13px; color: var(--color-muted);">{{ $weeklyPlan->video->description }}</p>
                    @endif
                    @if ($weeklyPlan->video->video_url)
                        <a href="{{ $weeklyPlan->video->video_url }}" target="_blank" class="btn btn-outline">فتح الفيديو</a>
                    @endif
                @else
                    <p style="color: var(--color-muted);">لم تتم إضافة فيديو لهذا الأسبوع بعد.</p>
                @endif
            @endcan
        </div>

        {{-- Homework --}}
        <div class="card">
            <h3 style="color: var(--color-green-dark); margin-top:0;">📝 الواجب</h3>

            @can('update', $weeklyPlan)
                <form method="POST" action="{{ route('weekly-plans.homework.save', $weeklyPlan) }}">
                    @csrf
                    <div class="form-group">
                        <label for="hw_title">العنوان</label>
                        <input type="text" name="title" id="hw_title" required
                               value="{{ old('title', $weeklyPlan->homework?->title) }}">
                    </div>
                    <div class="form-group">
                        <label for="hw_instructions">التعليمات</label>
                        <textarea name="instructions" id="hw_instructions" rows="3">{{ old('instructions', $weeklyPlan->homework?->instructions) }}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="due_date">تاريخ التسليم (اختياري)</label>
                        <input type="date" name="due_date" id="due_date"
                               value="{{ old('due_date', $weeklyPlan->homework?->due_date?->format('Y-m-d')) }}">
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%;">
                        {{ $weeklyPlan->homework ? 'تحديث الواجب' : 'حفظ الواجب' }}
                    </button>
                </form>
            @else
                @if ($weeklyPlan->homework)
                    <p><strong>{{ $weeklyPlan->homework->title }}</strong></p>
                    @if ($weeklyPlan->homework->instructions)
                        <p style="font-size: 13px; color: var(--color-muted);">{{ $weeklyPlan->homework->instructions }}</p>
                    @endif
                    @if ($weeklyPlan->homework->due_date)
                        <p style="font-size: 12px; color: var(--color-muted);">تاريخ التسليم: {{ $weeklyPlan->homework->due_date->format('Y-m-d') }}</p>
                    @endif
                @else
                    <p style="color: var(--color-muted);">لم تتم إضافة واجب لهذا الأسبوع بعد.</p>
                @endif
            @endcan
        </div>
    </div>

    {{-- Evaluation items -- the dynamic engine --}}
    <div class="card">
        <h3 style="color: var(--color-green-dark); margin-top:0;">📊 عناصر التقييم لهذا الأسبوع</h3>
        <p style="color: var(--color-muted); font-size: 13px; margin-top:-6px;">
            هذه العناصر ودرجاتها القصوى خاصة بهذا الأسبوع فقط — كل أسبوع يمكن أن يختلف عن غيره.
        </p>

        <table style="margin-bottom: 18px;">
            <thead>
                <tr>
                    <th>العنصر</th>
                    <th>الدرجة القصوى</th>
                    <th>إجباري؟</th>
                    @can('update', $weeklyPlan)
                        <th></th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @forelse ($weeklyPlan->evaluationItems as $item)
                    <tr>
                        <td>{{ $item->evaluationItem->name }}</td>
                        <td>{{ $item->max_score }}</td>
                        <td>{{ $item->is_required ? 'نعم' : 'لا' }}</td>
                        @can('update', $weeklyPlan)
                            <td>
                                @if ($weeklyPlan->status === 'draft')
                                    <form method="POST" action="{{ route('weekly-plans.evaluation-items.remove', [$weeklyPlan, $item]) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 5px 12px; font-size:12px;">حذف</button>
                                    </form>
                                @endif
                            </td>
                        @endcan
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color: var(--color-muted); padding: 20px;">
                            لم تتم إضافة أي عناصر تقييم بعد.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @can('update', $weeklyPlan)
            @if ($weeklyPlan->status === 'draft' && $availableItems->isNotEmpty())
                <form method="POST" action="{{ route('weekly-plans.evaluation-items.add', $weeklyPlan) }}"
                      style="display:flex; gap:10px; align-items:flex-end; flex-wrap: wrap;">
                    @csrf
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="evaluation_item_id">إضافة عنصر</label>
                        <select name="evaluation_item_id" id="evaluation_item_id" required>
                            @foreach ($availableItems as $item)
                                <option value="{{ $item->id }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="max_score">الدرجة القصوى</label>
                        <input type="number" step="0.01" min="0.01" name="max_score" id="max_score" required
                               style="width: 100px;" placeholder="10">
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label for="is_required">إجباري؟</label>
                        <select name="is_required" id="is_required">
                            <option value="1">نعم</option>
                            <option value="0">لا</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">إضافة</button>
                </form>
            @elseif ($weeklyPlan->status !== 'draft')
                <p style="color: var(--color-muted); font-size: 13px;">لا يمكن تعديل العناصر بعد نشر الخطة.</p>
            @endif
        @endcan
    </div>
@endsection