@extends('layouts.admin')

@section('title', 'داشبورد ' . ($school->name ?? 'مکتب'))

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold"><i class="fas fa-tachometer-alt ms-2"></i> داشبورد {{ $school->name ?? 'مکتب' }}</h5>
        <div>
            @if(auth()->user()->hasRole('Super Admin') && session()->has('active_school_id'))
                <a href="{{ route('admin.exit-school') }}" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fas fa-arrow-right ms-1"></i> بازگشت به پنل مرکزی
                </a>
            @endif
            <form method="GET" class="d-inline">
                <select name="year_filter" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                    <option value="">همه سال‌ها</option>
                    @foreach($academicYears ?? [] as $year)
                        <option value="{{ $year->id }}" {{ request('year_filter') == $year->id ? 'selected' : '' }}>سال {{ $year->name }}</option>
                    @endforeach
                </select>
            </form>
        </div>
    </div>

    {{-- نوار ابزار آماری (ردیف اول: ۶ کارت) --}}
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-2 mb-2">
        <div class="col"><div class="stat-card bg-primary"><div class="info"><h6>دانش‌آموزان</h6><h3>{{ $totalStudents ?? 0 }}</h3></div><i class="fas fa-user-graduate"></i></div></div>
        <div class="col"><div class="stat-card bg-success"><div class="info"><h6>کارمندان</h6><h3>{{ $totalEmployees ?? 0 }}</h3></div><i class="fas fa-user-tie"></i></div></div>
        <div class="col"><div class="stat-card bg-info"><div class="info"><h6>درآمد امروز</h6><h3>{{ number_format($todayIncome ?? 0) }} ؋</h3></div><i class="fas fa-calendar-day"></i></div></div>
        <div class="col"><div class="stat-card bg-teal text-white"><div class="info"><h6>عواید این ماه</h6><h3>{{ number_format($monthIncome ?? 0) }} ؋</h3></div><i class="fas fa-chart-line"></i></div></div>
        <div class="col"><div class="stat-card bg-warning text-dark"><div class="info"><h6>مصارف این ماه</h6><h3>{{ number_format($monthExpenses ?? 0) }} ؋</h3></div><i class="fas fa-file-invoice"></i></div></div>
        <div class="col"><div class="stat-card bg-dark"><div class="info"><h6>موجودی صندوق</h6><h3>{{ number_format($cashboxBalance ?? 0) }} ؋</h3></div><i class="fas fa-cash-register"></i></div></div>
    </div>

    {{-- ردیف دوم: ۴ کارت --}}
    <div class="row row-cols-2 row-cols-md-4 g-2 mb-3">
        <div class="col"><div class="stat-card bg-secondary"><div class="info"><h6>عواید کل تا امروز</h6><h3>{{ number_format($totalIncomeAllTime ?? 0) }} ؋</h3></div><i class="fas fa-coins"></i></div></div>
        <div class="col"><div class="stat-card bg-danger"><div class="info"><h6>مصارف کل تا امروز</h6><h3>{{ number_format($totalExpensesAllTime ?? 0) }} ؋</h3></div><i class="fas fa-file-contract"></i></div></div>
        <div class="col"><div class="stat-card bg-danger bg-opacity-75"><div class="info"><h6>بدهی شاگردان</h6><h3>{{ number_format($totalDebt ?? 0) }} ؋</h3></div><i class="fas fa-hand-holding-usd"></i></div></div>
        <div class="col"><div class="stat-card bg-info bg-opacity-75"><div class="info"><h6>سود/زیان کل</h6><h3>{{ number_format(($totalIncomeAllTime ?? 0) - ($totalExpensesAllTime ?? 0)) }} ؋</h3></div><i class="fas fa-balance-scale"></i></div></div>
    </div>

    {{-- کارت‌های عملیاتی + هشدارها --}}
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger bg-opacity-10 text-danger fw-bold"><i class="fas fa-exclamation-circle ms-1"></i> شاگردان بدهکار ({{ ($debtorStudents ?? collect([]))->count() }})</div>
                <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                    @forelse($debtorStudents ?? [] as $student)
                        @php
                            $totalFee = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                            $balance = $totalFee - ($student->total_paid ?? 0);
                        @endphp
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
                            <span>{{ $student->first_name }} {{ $student->last_name }}</span>
                            <span class="text-danger">{{ number_format($balance) }} ؋</span>
                        </a>
                    @empty
                        <div class="text-muted p-2">شاگرد بدهکاری وجود ندارد</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success bg-opacity-10 text-success fw-bold"><i class="fas fa-receipt ms-1"></i> پرداخت‌های امروز</div>
                <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                    @forelse($todayPayments ?? [] as $payment)
                        <div class="list-group-item d-flex justify-content-between">
                            <span>{{ $payment->student->first_name ?? '—' }} {{ $payment->student->last_name ?? '' }}</span>
                            <span class="text-success">{{ number_format($payment->amount) }} ؋</span>
                        </div>
                    @empty
                        <div class="text-muted p-2">امروز پرداختی ثبت نشده</div>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning bg-opacity-10 text-warning fw-bold"><i class="fas fa-bell ms-1"></i> هشدارها</div>
                <div class="list-group list-group-flush">
                    @if(($unpaidSalaries ?? 0) > 0)
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
                            معاشات پرداخت‌نشده
                            <span class="badge bg-warning rounded-pill">{{ $unpaidSalaries }}</span>
                        </a>
                    @endif
                    @if(($negativeCashboxes ?? 0) > 0)
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
                            صندوق منفی
                            <span class="badge bg-dark rounded-pill">{{ $negativeCashboxes }}</span>
                        </a>
                    @endif
                    @if(($unpaidSalaries ?? 0) == 0 && ($negativeCashboxes ?? 0) == 0)
                        <div class="list-group-item text-muted">موردی برای هشدار وجود ندارد</div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- آخرین تراکنش‌ها --}}
    <div class="card shadow-sm">
        <div class="card-header bg-light"><h6><i class="fas fa-history ms-2"></i> آخرین تراکنش‌های مالی</h6></div>
        <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0">
                <tbody>
                    @forelse($recentTransactions ?? [] as $trx)
                        @if($trx instanceof \App\Models\Payment)
                            <tr>
                                <td><small>{{ $trx->created_at->format('Y/m/d H:i') }}</small></td>
                                <td>پرداخت شهریه</td>
                                <td>{{ $trx->student->first_name ?? '—' }} {{ $trx->student->last_name ?? '' }}</td>
                                <td class="text-success">{{ number_format($trx->amount) }} ؋</td>
                            </tr>
                        @else
                            <tr>
                                <td><small>{{ $trx->created_at->format('Y/m/d H:i') }}</small></td>
                                <td>دریافت {{ $trx->income->title ?? '—' }}</td>
                                <td></td>
                                <td class="text-success">{{ number_format($trx->amount) }} ؋</td>
                            </tr>
                        @endif
                    @empty
                        <tr><td colspan="4" class="text-muted text-center">تراکنشی یافت نشد</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .stat-card {
        border-radius: 6px;
        padding: 0.5rem 0.7rem;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }
    .stat-card i { font-size: 1.2rem; opacity: 0.7; }
    .stat-card .info h6 { margin: 0; font-size: 0.65rem; opacity: 0.9; white-space: nowrap; }
    .stat-card .info h3 { margin: 0; font-size: 1rem; font-weight: 700; white-space: nowrap; }
    .bg-teal { background-color: #20c997; }
</style>
@endsection
