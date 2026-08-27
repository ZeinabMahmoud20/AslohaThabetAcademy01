@extends('layouts.academy')

@section('title', 'تعيين الطلاب للفصول')

@section('content')
    <div class="page-header">
        <h1>تعيين الطلاب للفصول</h1>
    </div>

    <div class="card" style="margin-bottom: 18px; display:flex; gap:10px; flex-wrap: wrap;">
        <a href="{{ route('students.index') }}"
           class="btn {{ ! request('status') ? 'btn-gold' : 'btn-outline' }}">الكل</a>
        <a href="{{ route('students.index', ['status' => 'unassigned']) }}"
           class="btn {{ request('status') === 'unassigned' ? 'btn-gold' : 'btn-outline' }}">بدون فصل</a>
        <a href="{{ route('students.index', ['status' => 'assigned']) }}"
           class="btn {{ request('status') === 'assigned' ? 'btn-gold' : 'btn-outline' }}">معيّن/ة بالفعل</a>

        <span style="width:1px; background: var(--color-border); margin: 0 6px;"></span>

        <a href="{{ route('students.index', request()->except('level_id')) }}"
           class="btn {{ ! request('level_id') ? 'btn-gold' : 'btn-outline' }}">كل المستويات</a>
        @foreach ($levels as $level)
            <a href="{{ route('students.index', array_merge(request()->except('level_id'), ['level_id' => $level->id])) }}"
               class="btn {{ request('level_id') == $level->id ? 'btn-gold' : 'btn-outline' }}">{{ $level->name }}</a>
        @endforeach
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>الطالب/ة</th>
                    <th>المستوى</th>
                    <th>الفصل الحالي</th>
                    <th>تعيين / تبديل فصل</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($attempts as $attempt)
                    <tr>
                        <td>{{ $attempt->student->full_name }}</td>
                        <td>{{ $attempt->level->name }}</td>
                        <td>
                            @if ($attempt->activeClassAssignment)
                                <span class="badge-status badge-accepted">
                                    {{ $attempt->activeClassAssignment->classRoom->name }}
                                </span>
                            @else
                                <span class="badge-status badge-pending">بدون فصل</span>
                            @endif
                        </td>
                        <td>
                            <form method="POST" action="{{ route('students.assign-class', $attempt) }}"
                                  style="display:flex; gap:8px;">
                                @csrf
                                <select name="class_id" required style="width: auto; min-width: 160px;">
                                    <option value="" disabled selected>اختر فصل</option>
                                    @foreach ($classesByLevel->get($attempt->level_id, collect()) as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                <button type="submit" class="btn btn-primary">
                                    {{ $attempt->activeClassAssignment ? 'تبديل' : 'تعيين' }}
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color: var(--color-muted); padding: 30px;">
                            لا يوجد طلاب مطابقون.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 18px;">
        {{ $attempts->links() }}
    </div>
@endsection