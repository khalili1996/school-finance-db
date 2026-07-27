@extends('layouts.admin')

@section('title', 'گزارش دفتر کل')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-book ms-2"></i> گزارش دفتر کل</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    {{-- ★ هدر یکسان (لوگو + نام مکتب + سال مالی) --}}
    @include('partials.report-header', ['title' => 'دفتر کل', 'subtitle' => ''])

    {{-- فیلتر تاریخ --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-2">
            <input type="text" name="from_date" class="form-control form-control-sm datepicker" placeholder="از تاریخ (شمسی)" value="{{ $fromDate }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="to_date" class="form-control form-control-sm datepicker" placeholder="تا تاریخ (شمسی)" value="{{ $toDate }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال فیلتر</button>
            <a href="{{ route('school.reports.financial.ledger') }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    {{-- کارت‌های خلاصه --}}
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <h6>جمع کل درآمد</h6>
                    <h4 class="fw-bold text-success">{{ number_format($totalIncome) }} ؋</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger shadow-sm">
                <div class="card-body text-center">
                    <h6>جمع کل مصرف</h6>
                    <h4 class="fw-bold text-danger">{{ number_format($totalExpense) }} ؋</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center">
                    <h6>تراز</h6>
                    <h4 class="fw-bold text-info">{{ number_format($balance) }} ؋</h4>
                </div>
            </div>
        </div>
    </div>

    {{-- ============= جدول درآمدها ============= --}}
    <div class="card shadow mb-5">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-arrow-down me-1"></i> لیست درآمدها</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>شرح</th>
                        <th>مبلغ (افغانی)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($incomes as $income)
                    <tr>
                        <td>{{ $incomes->firstItem() + $loop->index }}</td>
                        <td>{{ \App\Helpers\JalaliHelper::toJalali($income->entry_date) }}</td>
                        <td>{{ $income->description }}</td>
                        <td>{{ number_format($income->debit) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">درآمدی با این فیلترها یافت نشد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $incomes->appends(request()->except('income_page'))->links() }}
        </div>
    </div>

    {{-- ============= جدول مصارف ============= --}}
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-arrow-up me-1"></i> لیست مصارف</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>شرح</th>
                        <th>مبلغ (افغانی)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                    <tr>
                        <td>{{ $expenses->firstItem() + $loop->index }}</td>
                        <td>{{ \App\Helpers\JalaliHelper::toJalali($expense->entry_date) }}</td>
                        <td>{{ $expense->description }}</td>
                        <td>{{ number_format($expense->credit) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">مصرفی با این فیلترها یافت نشد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $expenses->appends(request()->except('expense_page'))->links() }}
        </div>
    </div>

</div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, form, .breadcrumb, .card-footer, #sidebar-wrapper, header {
            display: none !important;
        }
        #page-content-wrapper {
            padding: 0 !important;
        }
        .table {
            font-size: 12px;
        }
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
    }
</style>
@endpush
