@extends('layouts.admin')

@section('title', 'گزارش درآمدها')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-arrow-down ms-2"></i> گزارش درآمدها</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    {{-- ★ هدر یکسان (لوگو + نام مکتب + سال مالی) --}}
    @include('partials.report-header', ['title' => 'گزارش درآمدها', 'subtitle' => ''])

    {{-- فیلترها --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-2">
            <input type="text" name="from_date" class="form-control form-control-sm" placeholder="از تاریخ (شمسی)" value="{{ $fromDate }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="to_date" class="form-control form-control-sm" placeholder="تا تاریخ (شمسی)" value="{{ $toDate }}">
        </div>
        <div class="col-md-2">
            <select name="class_filter" class="form-select form-select-sm">
                <option value="">همه صنف‌ها</option>
                @foreach($classes as $cls)
                    <option value="{{ $cls }}" {{ request('class_filter') == $cls ? 'selected' : '' }}>{{ $cls }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="month_filter" class="form-select form-select-sm">
                <option value="">همه ماه‌ها</option>
                @foreach($months as $m)
                    <option value="{{ $m->id }}" {{ request('month_filter') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="category_filter" class="form-select form-select-sm">
                <option value="">همه دسته‌بندی‌ها</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_filter') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
            <a href="{{ route('school.reports.financial.incomes') }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    {{-- ============= جدول اول: شهریه‌های پرداختی ============= --}}
    <div class="card shadow mb-5">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-1"></i> شهریه‌های پرداختی – تفکیک صنف</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>صنف</th>
                        <th>تعداد دانش‌آموز</th>
                        <th>مجموع شهریه</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($feePaymentsByClass as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item['class'] }}</td>
                            <td>{{ $item['count'] }}</td>
                            <td>{{ number_format($item['total']) }} ؋</td>
                            <td>—</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">پرداخت شهریه‌ای با این فیلترها یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td colspan="3" class="text-end">جمع کل شهریه‌ها:</td>
                        <td colspan="2">{{ number_format($totalFeeIncome) }} ؋</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ============= جدول دوم: سایر درآمدها ============= --}}
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-coins me-1"></i> سایر درآمدها (غیر از شهریه)</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>دسته‌بندی</th>
                        <th>شرح</th>
                        <th>مبلغ (افغانی)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($otherIncomes as $income)
                        <tr>
                            <td>{{ $otherIncomes->firstItem() + $loop->index }}</td>
                            <td>{{ \App\Helpers\JalaliHelper::toJalali($income->income_date) }}</td>
                            <td>{{ $income->incomeCategory->name ?? '—' }}</td>
                            <td>{{ $income->description ?? '—' }}</td>
                            <td>{{ number_format($income->received_amount ?? 0) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">درآمد دیگری با این فیلترها یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td colspan="4" class="text-end">جمع کل سایر درآمدها:</td>
                        <td>{{ number_format($totalOtherIncomes) }} ؋</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $otherIncomes->links() }}
        </div>
    </div>

    {{-- جمع کل نهایی --}}
    <div class="row mt-4">
        <div class="col-md-6 offset-md-6">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-info">مجموع کل درآمدها (شهریه + سایر)</h5>
                    <h3>{{ number_format($grandTotalIncome) }} ؋</h3>
                </div>
            </div>
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
        .table { font-size: 12px; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>
@endpush
