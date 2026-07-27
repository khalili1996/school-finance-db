@extends('layouts.admin')

@section('title', 'پیش‌پرداخت‌ها (مساعده)')

@section('content')
<div class="container-fluid p-0">
    <div class="sticky-toolbar bg-white shadow-sm border-bottom px-3 py-2 mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-hand-holding-usd fa-lg text-warning ms-2"></i>
                <h5 class="fw-bold mb-0 text-dark">پیش‌پرداخت‌ها (مساعده)</h5>
            </div>
            <div class="btn-group">
                <a href="{{ route('school.employee-advances.create') }}" class="btn btn-success rounded-pill px-3 py-2">
                    <i class="fas fa-plus-circle ms-1"></i> ثبت پیش‌پرداخت
                </a>
            </div>
        </div>
    </div>

    <div class="px-3">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-3">
                <label class="form-label">کارمند</label>
                <select name="employee_id" class="form-select">
                    <option value="">همه</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->first_name }} {{ $emp->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-primary">اعمال</button>
                <a href="{{ route('school.employee-advances.index') }}" class="btn btn-secondary">حذف فیلتر</a>
            </div>
        </form>

        <div class="card shadow">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>کارمند</th>
                            <th>ماه</th>
                            <th>مبلغ (افغانی)</th>
                            <th>تاریخ</th>
                            <th>توضیحات</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advances as $advance)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $advance->employee->first_name ?? '' }} {{ $advance->employee->last_name ?? '' }}</td>
                            <td>{{ $advance->month->name ?? '—' }}</td>
                            <td>{{ number_format($advance->amount) }}</td>
                            <td>{{ \App\Helpers\JalaliHelper::toJalali($advance->advance_date) }}</td>
                            <td>{{ $advance->notes ?? '—' }}</td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('school.employee-advances.receipt', $advance) }}"
                                       target="_blank" class="btn btn-outline-info" title="رسید">
                                        <i class="fa fa-receipt"></i>
                                    </a>
                                    <a href="{{ route('school.employee-advances.edit', $advance) }}"
                                       class="btn btn-outline-warning" title="ویرایش">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form action="{{ route('school.employee-advances.destroy', $advance) }}"
                                          method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="حذف"
                                           onclick="return confirm('آیا مطمئن هستید؟')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">هیچ پیش‌پرداختی ثبت نشده است.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">{{ $advances->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .sticky-toolbar { position: sticky; top: 0; z-index: 1020; background: #fff; }
</style>
@endpush
