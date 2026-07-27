@extends('layouts.admin')

@section('title', 'انواع هزینه‌ها')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tags ms-2"></i> انواع هزینه‌ها</h1>
        <a href="{{ route('school.fee-types.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> ثبت نوع هزینه جدید
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>نام</th>
                        <th>دسته‌بندی</th>
                        <th>اختیاری</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feeTypes as $feeType)
                        <tr>
                            <td>{{ $feeType->name }}</td>
                            <td>
                                @switch($feeType->category)
                                    @case('tuition') <span class="badge bg-primary">شهریه</span> @break
                                    @case('one_time') <span class="badge bg-info">یک‌باره</span> @break
                                    @case('other') <span class="badge bg-secondary">سایر</span> @break
                                @endswitch
                            </td>
                            <td>{{ $feeType->is_optional ? 'بله' : 'خیر' }}</td>
                            <td>
                                <span class="badge bg-{{ $feeType->is_active ? 'success' : 'danger' }}">
                                    {{ $feeType->is_active ? 'فعال' : 'غیرفعال' }}
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('school.fee-types.edit', $feeType->id) }}" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('school.fee-types.destroy', $feeType->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('آیا از حذف این نوع هزینه اطمینان دارید؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">هیچ نوع هزینه‌ای ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
