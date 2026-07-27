@extends('layouts.admin')

@section('title', 'قرض‌الحسنه')

@section('content')
<div class="container-fluid p-0">
    {{-- نوار ابزار چسبان --}}
    <div class="sticky-toolbar bg-white shadow-sm border-bottom px-3 py-2 mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-hand-holding-heart fa-lg text-danger ms-2"></i>
                <h5 class="fw-bold mb-0 text-dark">مدیریت قرض‌الحسنه</h5>
            </div>
            <div class="btn-group">
                <a href="{{ route('school.loans.create') }}" class="btn btn-success rounded-pill px-3 py-2">
                    <i class="fas fa-plus-circle ms-1"></i> ثبت قرض‌الحسنه جدید
                </a>
            </div>
        </div>
    </div>

    <div class="px-3">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

        <div class="card shadow">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>قرض‌گیرنده</th>
                            <th>مبلغ (افغانی)</th>
                            <th>اقساط</th>
                            <th>تاریخ شروع</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($loans as $loan)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>
                                @if($loan->employee)
                                    {{ $loan->employee->first_name }} {{ $loan->employee->last_name }}
                                @else
                                    {{ $loan->borrower_name }} {{ $loan->borrower_last_name }}
                                @endif
                            </td>
                            <td>{{ number_format($loan->amount) }}</td>
                            <td>{{ $loan->installments->where('status', 'paid')->count() }}/{{ $loan->duration_months }}</td>
                            <td>{{ \App\Helpers\JalaliHelper::toJalali($loan->start_date) }}</td>
                            <td>
                                @if($loan->status == 'completed')
                                    <span class="badge bg-success">تسویه شده</span>
                                @else
                                    <span class="badge bg-warning text-dark">فعال</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    {{-- 🆕 دکمه اقساط --}}
                                    <a href="{{ route('school.loans.installments', $loan) }}" class="btn btn-outline-success" title="اقساط">
                                        <i class="fa fa-calendar-alt"></i> اقساط
                                    </a>
                                    {{-- پیش‌نمایش چاپ --}}
                                    <a href="{{ route('school.loans.show', $loan) }}" target="_blank" class="btn btn-outline-info" title="پیش‌نمایش"><i class="fa fa-print"></i></a>
                                    {{-- ویرایش --}}
                                    <a href="{{ route('school.loans.edit', $loan) }}" class="btn btn-outline-warning" title="ویرایش"><i class="fa fa-pencil"></i></a>
                                    {{-- حذف --}}
                                    <form action="{{ route('school.loans.destroy', $loan) }}" method="POST" class="d-inline">@csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="حذف" onclick="return confirm('آیا مطمئن هستید؟')"><i class="fa fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="7" class="text-center text-muted py-3">هیچ قرض‌الحسنه‌ای ثبت نشده است.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">{{ $loans->links() }}</div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .sticky-toolbar { position: sticky; top: 0; z-index: 1020; background: #fff; }
</style>
@endpush
