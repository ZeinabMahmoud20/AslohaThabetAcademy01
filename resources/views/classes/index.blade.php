@extends('layouts.academy')

@section('title', 'الفصول')

@section('content')
<div class="page-header">
    <h1>الفصول</h1>
    @can('create', \App\Models\ClassRoom::class)
        <a href="{{ route('classes.create') }}" class="btn btn-primary">+ إنشاء فصل جديد</a>
    @endcan
</div>

    <div class="card" style="margin-bottom: 18px; display:flex; gap:10px; flex-wrap: wrap;">
        <a href="{{ route('classes.index') }}"
           class="btn {{ ! request('level_id') ? 'btn-gold' : 'btn-outline' }}">كل المستويات</a>
        @foreach ($levels as $level)
            <a href="{{ route('classes.index', ['level_id' => $level->id]) }}"
               class="btn {{ request('level_id') == $level->id ? 'btn-gold' : 'btn-outline' }}">{{ $level->name }}</a>
        @endforeach
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>اسم الفصل</th>
                    <th>المستوى</th>
                    <th>المعلم/ة الحالي/ة</th>
                    <th>المشرفون النشطون</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($classes as $class)
                    <tr>
                        <td>{{ $class->name }}</td>
                        <td>{{ $class->level->name }}</td>
                        <td>
                            @if ($class->activeTeacherAssignment)
                                {{ $class->activeTeacherAssignment->teacher->full_name }}
                            @else
                                <span class="badge-status badge-pending">لا يوجد معلم/ة</span>
                            @endif
                        </td>
                        <td>
                            @forelse ($class->activeSupervisorAssignments as $sa)
                                <span class="badge-status badge-accepted">{{ $sa->supervisor->full_name }}</span>
                            @empty
                                <span style="color: var(--color-muted);">—</span>
                            @endforelse
                        </td>
                        <td>
                            <a href="{{ route('classes.show', $class) }}" class="btn btn-outline">إدارة الفصل</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align:center; color: var(--color-muted); padding: 30px;">
                            لا توجد فصول مطابقة.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection