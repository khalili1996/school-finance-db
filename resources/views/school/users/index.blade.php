@extends('layouts.admin')

@section('title', 'مدیریت کاربران')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-users-cog ms-2"></i> کاربران این مکتب</h1>
        <a href="{{ route('school.users.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> ایجاد کاربر جدید
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>نام</th>
                        <th>ایمیل</th>
                        <th>تلفن</th>
                        <th>نقش</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->phone ?? '—' }}</td>
                        <td>
                            @foreach($user->roles as $role)
                                <span class="badge bg-info">{{ $role->name }}</span>
                            @endforeach
                        </td>
                        <td>
                            <span class="badge bg-{{ $user->is_active ? 'success' : 'danger' }}">
                                {{ $user->is_active ? 'فعال' : 'غیرفعال' }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('school.users.edit', $user->id) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('school.users.destroy', $user->id) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('آیا از حذف این کاربر اطمینان دارید؟')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-danger">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center">هیچ کاربری ثبت نشده است.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
