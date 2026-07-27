@extends('layouts.admin')

@section('title', 'لیست پرداخت‌ها')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-hand-holding-usd ms-2"></i> مدیریت پرداخت‌ها</h1>

        <a href="{{ route('school.payments.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> ثبت پرداخت جدید
        </a>
        <form action="{{ route('school.payments.sync-to-ledger') }}" method="POST" class="d-inline">
    @csrf
    <button type="submit" class="btn btn-outline-warning btn-sm" title="همگام‌سازی پرداخت‌های قدیمی با دفتر کل">
        <i class="fas fa-sync-alt me-1"></i> همگام‌سازی دفتر کل
    </button>
</form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- فیلتر و جستجو --}}
    <form method="GET" action="{{ route('school.payments.index') }}" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="جستجوی نام، کد دانش‌آموز یا شماره رسید..." value="{{ request('search') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> جستجو</button>
            @if(request('search'))
                <a href="{{ route('school.payments.index') }}" class="btn btn-secondary">حذف جستجو</a>
            @endif
        </div>
    </form>

    {{-- جدول پرداخت‌ها --}}
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>شماره رسید</th>
                            <th>دانش‌آموز</th>
                            <th>مبلغ</th>
                            <th>تاریخ</th>
                            <th>روش پرداخت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>{{ $payment->receipt_number ?? $payment->id }}</td>
                                <td>{{ $payment->student->first_name ?? '—' }} {{ $payment->student->last_name ?? '' }}</td>
                                <td>{{ number_format($payment->amount) }} ؋</td>
                                {{-- 📅 نمایش تاریخ شمسی --}}
                                <td>{{ \App\Helpers\JalaliHelper::toJalali($payment->payment_date) }}</td>
                                <td>
                                    @switch($payment->payment_method)
                                        @case('cash') <span class="badge bg-success">نقدی</span> @break
                                        @case('bank') <span class="badge bg-info">بانکی</span> @break
                                        @default <span class="badge bg-secondary">سایر</span>
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('school.payments.receipt', $payment->id) }}" class="btn btn-outline-secondary" title="رسید چاپی">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="{{ route('school.payments.edit', $payment->id) }}" class="btn btn-outline-warning" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('school.payments.destroy', $payment->id) }}" method="POST" class="d-inline"
                                              onsubmit="return confirm('آیا از حذف این پرداخت اطمینان دارید؟')">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    هیچ پرداختی ثبت نشده است.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($payments->hasPages())
            <div class="card-footer">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
