@extends('layouts.academy')

@section('title', 'الخطط الأسبوعية')

@section('content')
    <div class="page-header">
    <h1>الخطط الأسبوعية</h1>
    @can('create', \App\Models\WeeklyPlan::class)
        <a href="{{ route('weekly-plans.create', ['level_id' => $levelId]) }}" class="btn btn-primary">+ خطة أسبوعية جديدة</a>
    @endcan
</div>

    <div class="card" style="margin-bottom: 18px; display:flex; gap:10px; flex-wrap: wrap;">
        @foreach ($levels as $level)
            <a href="{{ route('weekly-plans.index', ['level_id' => $level->id]) }}"
               class="btn {{ $levelId == $level->id ? 'btn-gold' : 'btn-outline' }}">{{ $level->name }}</a>
        @endforeach
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>الأسبوع</th>
                    <th>العنوان</th>
                    <th>الفيديو</th>
                    <th>الواجب</th>
                    <th>عناصر التقييم</th>
                    <th>الحالة</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($plans as $plan)
                    <tr>
                        <td>{{ $plan->week_number }}</td>
                        <td>{{ $plan->title }}</td>
                        <td>{{ $plan->video ? '✅' : '—' }}</td>
                        <td>{{ $plan->homework ? '✅' : '—' }}</td>
                        <td>{{ $plan->evaluation_items_count }} عنصر</td>
                        <td>
                            @php
                                $statusMap = [
                                    'draft' => ['مسودة', 'badge-pending'],
                                    'published' => ['منشورة', 'badge-accepted'],
                                    'closed' => ['مغلقة', 'badge-rejected'],
                                ];
                                [$label, $class] = $statusMap[$plan->status] ?? [$plan->status, 'badge-pending'];
                            @endphp
                            <span class="badge-status {{ $class }}">{{ $label }}</span>
                        </td>
                        <td>
                            <a href="{{ route('weekly-plans.show', $plan) }}" class="btn btn-outline">إدارة</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; color: var(--color-muted); padding: 30px;">
                            لا توجد خطط أسبوعية لهذا المستوى بعد.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection