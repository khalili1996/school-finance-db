@extends('layouts.admin')

@section('title', 'جزئیات صندوق: ' . $cashbox->name)

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">📋 جزئیات صندوق</h4>
        <div>
            <a href="{{ route('school.cashboxes.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست
            </a>
        </div>
    </div>

    {{-- انتخاب سال مالی --}}
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                <option value="">همه سال‌ها</option>
                @foreach($academicYears as $yr)
                    <option value="{{ $yr->id }}" {{ request('academic_year_id') == $yr->id ? 'selected' : '' }}>
                        {{ $yr->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </form>

    {{-- کارت اطلاعات صندوق --}}
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-end">
                    <h5 class="card-title">{{ $cashbox->name }}</h5>
                    <p class="mb-2">
                        <span class="badge bg-{{ $cashbox->type === 'bank' ? 'info' : 'success' }}">
                            {{ $cashbox->type === 'bank' ? 'بانکی' : 'نقدی' }}
                        </span>
                        @if($cashbox->is_active)
                            <span class="badge bg-success">فعال</span>
                        @else
                            <span class="badge bg-secondary">غیرفعال</span>
                        @endif
                    </p>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <span class="text-muted small">موجودی فعلی</span><br>
                            <strong class="fs-4 text-{{ $cashbox->current_balance >= 0 ? 'success' : 'danger' }}">
                                {{ number_format($cashbox->current_balance, 0) }}
                            </strong><br>
                            <small>افغانی</small>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small">موجودی اولیه</span><br>
                            <strong class="fs-4 text-primary">
                                {{ number_format($cashbox->initial_balance, 0) }}
                            </strong><br>
                            <small>افغانی</small>
                        </div>
                    </div>
                    @if($cashbox->notes)
                    <hr>
                    <p class="text-muted small mb-0"><strong>توضیحات:</strong> {{ $cashbox->notes }}</p>
                    @endif
                </div>
            </div>
        </div>

        {{-- خلاصه تراکنش‌ها (بدون فیلتر سال) --}}
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-end">
                    <h6 class="card-title">خلاصه تراکنش‌ها (کل)</h6>
                    @php
                        $totalDeposit    = $cashbox->transactions()->where('type', 'deposit')->sum('amount');
                        $totalWithdrawal = $cashbox->transactions()->where('type', 'withdrawal')->sum('amount');
                    @endphp
                    <div class="d-flex justify-content-around text-center">
                        <div>
                            <span class="text-success small">واریز کل</span><br>
                            <strong>{{ number_format($totalDeposit, 0) }}</strong>
                        </div>
                        <div>
                            <span class="text-danger small">برداشت کل</span><br>
                            <strong>{{ number_format($totalWithdrawal, 0) }}</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- دکمه‌های عملیات --}}
    <div class="btn-group mb-4">
        <form action="{{ route('school.cashboxes.sync-old') }}" method="POST" class="d-inline">
            @csrf
            <input type="hidden" name="cashbox_id" value="{{ $cashbox->id }}">
            <button type="submit" class="btn btn-sm btn-outline-warning">
                <i class="fas fa-sync-alt me-1"></i> همگام‌سازی قدیمی
            </button>
        </form>

        <form action="{{ route('school.cashboxes.clean-orphan') }}" method="POST" class="d-inline ms-2">
            @csrf
            <input type="hidden" name="cashbox_id" value="{{ $cashbox->id }}">
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-broom me-1"></i> پاک‌سازی یتیم‌ها
            </button>
        </form>
    </div>

    {{-- لیست تراکنش‌ها --}}
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title mb-3">
                تاریخچه تراکنش‌ها
                @if(request('academic_year_id'))
                    <span class="badge bg-info ms-2">{{ \App\Models\AcademicYear::find(request('academic_year_id'))->name ?? '' }}</span>
                @endif
            </h5>
            @if($transactions->isEmpty())
                <div class="alert alert-info text-center">هنوز هیچ تراکنشی برای این صندوق ثبت نشده است.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>تاریخ</th>
                                <th>نوع</th>
                                <th>مبلغ (افغانی)</th>
                                <th>شرح</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($transactions as $trx)
                            <tr>
                                <td>{{ \App\Helpers\JalaliHelper::toJalali($trx->transaction_date) }}</td>
                                <td>
                                    @if($trx->type === 'deposit')
                                        <span class="badge bg-success">واریز</span>
                                    @elseif($trx->type === 'withdrawal')
                                        <span class="badge bg-danger">برداشت</span>
                                    @endif
                                </td>
                                <td class="{{ $trx->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($trx->amount, 0) }}
                                </td>
                                <td>{{ $trx->description ?: '—' }}</td>
                                <td>
                                    <form action="{{ route('school.cashbox-transactions.destroy', $trx->id) }}" method="POST"
                                          onsubmit="return confirm('آیا از حذف این تراکنش اطمینان دارید؟ (رکورد اصلی نیز حذف خواهد شد)')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
