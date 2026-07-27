@extends('layouts.admin')

@section('title', 'سطل زباله کارمندان')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.employees.index') }}">کارمندان</a></li>
            <li class="breadcrumb-item active">سطل زباله</li>
        </ol>
    </nav>

    <h1><i class="fas fa-trash ms-2"></i> کارمندان حذف‌شده</h1>

    <div class="card shadow mt-3">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>کد</th>
                        <th>نام</th>
                        <th>سمت</th>
                        <th>تاریخ حذف</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr>
                            <td>{{ $emp->employee_code }}</td>
                            <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                            <td>{{ $emp->employeeRole->name ?? '—' }}</td>
                            <td>{{ $emp->deleted_at->format('Y/m/d H:i') }}</td>
                            <td>
                                {{-- بازیابی --}}
                                <form action="{{ route('school.employees.restore', $emp->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="بازیابی">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                {{-- حذف دائمی --}}
                                <form action="{{ route('school.employees.force-delete', $emp->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('آیا از حذف دائمی این کارمند اطمینان دارید؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="حذف دائمی">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">سطل زباله خالی است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $employees->links() }}</div>
    </div>
</div>
@endsection
