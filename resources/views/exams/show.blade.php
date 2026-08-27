@extends('layouts.academy')

@section('title', 'إدارة الامتحان — ' . $exam->title)

@section('content')
    <div class="page-header">
        <h1>{{ $exam->title }}</h1>
        <a href="{{ route('exams.index', ['level_id' => $exam->level_id]) }}" class="btn btn-outline">⟵ رجوع للقائمة</a>
    </div>

    @can('update', $exam)
        @if ($exam->status === 'draft')
            <div class="alert alert-error" style="display:flex; align-items:center; justify-content:space-between;">
                <span>هذا الامتحان لا يزال مسودة ولن يظهر للطلاب حتى يتم نشره.</span>
                <form method="POST" action="{{ route('exams.publish', $exam) }}">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-gold">نشر الامتحان الآن</button>
                </form>
            </div>
        @else
            <div class="alert alert-success" style="display:flex; align-items:center; justify-content:space-between;">
                <span>
                    هذا الامتحان منشور. الدرجة الكلية: {{ $exam->max_score }} —
                    متاح من {{ $exam->start_at->format('Y-m-d H:i') }} إلى {{ $exam->end_at->format('Y-m-d H:i') }}.
                </span>
                @if ($exam->attempts()->count() === 0)
                    <form method="POST" action="{{ route('exams.unpublish', $exam) }}">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline">إلغاء النشر للتعديل</button>
                    </form>
                @endif
            </div>
        @endif
    @else
        @if ($exam->status === 'published')
            <div class="alert alert-success">
                هذا الامتحان منشور. الدرجة الكلية: {{ $exam->max_score }} —
                متاح من {{ $exam->start_at->format('Y-m-d H:i') }} إلى {{ $exam->end_at->format('Y-m-d H:i') }}.
            </div>
        @else
            <div class="alert alert-error">هذا الامتحان لا يزال مسودة.</div>
        @endif
    @endcan

    {{-- Existing questions --}}
    <div class="card" style="margin-bottom: 20px;">
        <h3 style="color: var(--color-green-dark); margin-top:0;">الأسئلة ({{ $exam->questions->count() }})</h3>

        @forelse ($exam->questions as $question)
            <div style="border: 1px solid var(--color-border); border-radius: 10px; padding: 14px 16px; margin-bottom: 12px;">
                <div style="display:flex; align-items:flex-start; justify-content:space-between;">
                    <div>
                        <strong>{{ $question->display_order }}. {{ $question->question_text }}</strong>
                        <div style="font-size: 12px; color: var(--color-muted); margin-top: 2px;">
                            {{ $question->score }} درجة — {{ $question->question_type === 'true_false' ? 'صح/خطأ' : 'اختيار من متعدد' }}
                        </div>
                    </div>
                    @can('update', $exam)
                        @if ($exam->status === 'draft')
                            <form method="POST" action="{{ route('exams.questions.remove', [$exam, $question]) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 4px 10px; font-size:12px;">حذف</button>
                            </form>
                        @endif
                    @endcan
                </div>
                <ul style="margin: 10px 0 0; padding-right: 20px; font-size: 13px;">
                    @foreach ($question->options as $option)
                        <li style="{{ $option->is_correct ? 'color: var(--color-green-dark); font-weight:700;' : '' }}">
                            {{ $option->option_text }} {{ $option->is_correct ? '✅' : '' }}
                        </li>
                    @endforeach
                </ul>
            </div>
        @empty
            <p style="color: var(--color-muted); text-align:center; padding: 20px;">لم تتم إضافة أسئلة بعد.</p>
        @endforelse
    </div>

    {{-- Add question form -- only for users who can edit this exam, and only while it's still a draft --}}
    @can('update', $exam)
        @if ($exam->status === 'draft')
            <div class="card">
                <h3 style="color: var(--color-green-dark); margin-top:0;">إضافة سؤال جديد</h3>

                <form method="POST" action="{{ route('exams.questions.add', $exam) }}">
                    @csrf

                    <div class="form-group">
                        <label for="question_text">نص السؤال</label>
                        <textarea name="question_text" id="question_text" rows="2" required>{{ old('question_text') }}</textarea>
                    </div>

                    <div style="display:grid; grid-template-columns:1fr 1fr; gap:12px;">
                        <div class="form-group">
                            <label for="question_type">نوع السؤال</label>
                            <select name="question_type" id="question_type" onchange="toggleOptionType(this.value)">
                                <option value="multiple_choice">اختيار من متعدد</option>
                                <option value="true_false">صح / خطأ</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="score">الدرجة</label>
                            <input type="number" step="0.01" min="0.01" name="score" id="score" required value="{{ old('score', 5) }}">
                        </div>
                    </div>

                    <label style="display:block; font-weight:700; font-size:13px; margin-bottom:8px; color: var(--color-green-dark);">
                        حدد الإجابة الصحيحة
                    </label>

                    {{-- Multiple choice: 4 free-text options --}}
                    <div id="mc_options">
                        @for ($i = 0; $i < 4; $i++)
                            <div style="display:flex; align-items:center; gap:10px; margin-bottom: 8px;">
                                <input type="radio" name="correct_option" value="{{ $i }}" {{ $i === 0 ? 'checked' : '' }}>
                                <input type="text" name="options[{{ $i }}][option_text]" placeholder="الخيار {{ $i + 1 }}"
                                       style="flex:1; padding: 8px 10px; border-radius:8px; border:1.5px solid var(--color-border);"
                                       {{ $i < 2 ? 'required' : '' }}>
                            </div>
                        @endfor
                    </div>

                    {{-- True / False: 2 fixed options, disabled by default (only enabled when selected,
                         so disabled inputs are excluded from submission automatically). --}}
                    <div id="tf_options" style="display:none;">
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom: 8px;">
                            <input type="radio" name="correct_option" value="0" disabled>
                            <input type="hidden" name="options[0][option_text]" value="صح" disabled>
                            <span style="flex:1; padding: 8px 10px;">صح</span>
                        </div>
                        <div style="display:flex; align-items:center; gap:10px; margin-bottom: 8px;">
                            <input type="radio" name="correct_option" value="1" disabled>
                            <input type="hidden" name="options[1][option_text]" value="خطأ" disabled>
                            <span style="flex:1; padding: 8px 10px;">خطأ</span>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary" style="margin-top: 10px;">إضافة السؤال</button>
                </form>
            </div>

            <script>
                function toggleOptionType(type) {
                    const mc = document.getElementById('mc_options');
                    const tf = document.getElementById('tf_options');

                    const setEnabled = (container, enabled) => {
                        container.querySelectorAll('input').forEach(input => {
                            input.disabled = !enabled;
                        });
                    };

                    if (type === 'true_false') {
                        mc.style.display = 'none';
                        tf.style.display = 'block';
                        setEnabled(mc, false);
                        setEnabled(tf, true);
                    } else {
                        mc.style.display = 'block';
                        tf.style.display = 'none';
                        setEnabled(mc, true);
                        setEnabled(tf, false);
                    }
                }
            </script>
        @endif
    @endcan
@endsection