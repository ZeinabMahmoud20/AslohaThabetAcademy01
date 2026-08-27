@extends('layouts.academy')

@section('title', 'طلبات الالتحاق')

@section('content')
    <div class="page-header">
        <h1>طلبات الالتحاق</h1>
    </div>

    <div class="card" style="margin-bottom: 18px; display:flex; gap:10px;">
        <a href="{{ route('applications.index') }}"
           class="btn {{ request('status') ? 'btn-outline' : 'btn-gold' }}">الكل</a>
        <a href="{{ route('applications.index', ['status' => 'pending_review']) }}"
           class="btn {{ request('status') === 'pending_review' ? 'btn-gold' : 'btn-outline' }}">قيد المراجعة</a>
        <a href="{{ route('applications.index', ['status' => 'accepted']) }}"
           class="btn {{ request('status') === 'accepted' ? 'btn-gold' : 'btn-outline' }}">مقبول</a>
        <a href="{{ route('applications.index', ['status' => 'rejected']) }}"
           class="btn {{ request('status') === 'rejected' ? 'btn-gold' : 'btn-outline' }}">مرفوض</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>اسم الطالب</th>
                    <th>ولي الأمر</th>
                    <th>العمر</th>
                    <th>تاريخ التقديم</th>
                    <th>الحالة</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($applications as $application)
                    <tr>
                        <td>
                            {{ $application->student_full_name }}
                            @if ($application->is_age_exception)
                                <span class="badge-status badge-pending" title="استثناء عمر">⚠️ استثناء</span>
                            @endif
                        </td>
                        <td>{{ $application->guardian->full_name }}</td>
                        <td>{{ $application->computed_age_years }} سنة</td>
                        <td>{{ $application->submitted_at->format('Y-m-d') }}</td>
                        <td>
                            @php
                                $statusMap = [
                                    'pending_review' => ['قيد المراجعة', 'badge-pending'],
                                    'admin_review' => ['مراجعة إدارية', 'badge-pending'],
                                    'accepted' => ['مقبول', 'badge-accepted'],
                                    'rejected' => ['مرفوض', 'badge-rejected'],
                                ];
                                [$label, $class] = $statusMap[$application->status] ?? [$application->status, 'badge-pending'];
                            @endphp
                            <span class="badge-status {{ $class }}">{{ $label }}</span>
                        </td>
                        <td>
                            <a href="{{ route('applications.show', $application) }}" class="btn btn-outline">مراجعة</a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color: var(--color-muted); padding: 30px;">
                            لا توجد طلبات مطابقة.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 18px;">
        {{ $applications->links() }}
    </div>
@endsection
