@extends('layouts.academy')

@section('title', 'إدارة المعلمين والمشرفين')

@section('content')
    <div class="page-header">
        <h1>{{ $role === 'supervisor' ? 'المشرفات' : ($role === 'all' ? 'كل المعلمين والمشرفين' : 'المعلمات') }}</h1>
        @can('create', \App\Models\User::class)
        <a href="{{ route('staff.create', ['role' => $role === 'supervisor' ? 'supervisor' : 'teacher']) }}" class="btn btn-primary">
            + إضافة {{ $role === 'supervisor' ? 'مشرف/ة' : 'معلم/ة' }}
        </a>
        @endcan
    </div>

    <div class="card" style="margin-bottom: 18px; display:flex; gap:10px;">
        <a href="{{ route('staff.index', ['role' => 'teacher']) }}"
           class="btn {{ $role === 'teacher' ? 'btn-gold' : 'btn-outline' }}">المعلمات</a>
        <a href="{{ route('staff.index', ['role' => 'supervisor']) }}"
           class="btn {{ $role === 'supervisor' ? 'btn-gold' : 'btn-outline' }}">المشرفات</a>
        <a href="{{ route('staff.index', ['role' => 'all']) }}"
           class="btn {{ $role === 'all' ? 'btn-gold' : 'btn-outline' }}">الكل</a>
    </div>

    <div class="card">
        <table>
            <thead>
                <tr>
                    <th>الاسم</th>
                    <th>البريد الإلكتروني</th>
                    <th>الهاتف</th>
                    <th>الدور</th>
                    <th>الحالة</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse ($staff as $member)
                    <tr>
                        <td>{{ $member->full_name }}</td>
                        <td dir="ltr" style="text-align:right;">{{ $member->email }}</td>
                        <td dir="ltr" style="text-align:right;">{{ $member->phone_number ?? '—' }}</td>
                        <td>{{ $member->role_type === 'teacher' ? 'معلم/ة' : 'مشرف/ة' }}</td>
                        <td>
                            <span class="badge-status {{ $member->is_active ? 'badge-accepted' : 'badge-rejected' }}">
                                {{ $member->is_active ? 'نشط' : 'موقوف' }}
                            </span>
                        </td>
                        <td>
                            @can('update', $member)
                            <form method="POST" action="{{ route('staff.toggle-active', $member) }}">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline">
                                    {{ $member->is_active ? 'إيقاف' : 'تفعيل' }}
                                </button>
                            </form>
                            @endcan
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color: var(--color-muted); padding: 30px;">
                            لا يوجد أعضاء مطابقين.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 18px;">
        {{ $staff->links() }}
    </div>
@endsection