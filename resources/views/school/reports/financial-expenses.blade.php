@extends('layouts.admin')

@section('title', 'گزارش مصارف')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-arrow-up ms-2"></i> گزارش مصارف</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    {{-- ★ هدر یکسان (لوگو + نام مکتب + سال مالی) --}}
    @include('partials.report-header', ['title' => 'گزارش مصارف', 'subtitle' => ''])

    {{-- فیلترها --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-2">
            <input type="text" name="from_date" class="form-control form-control-sm" placeholder="از تاریخ (شمسی)" value="{{ $fromDate }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="to_date" class="form-control form-control-sm" placeholder="تا تاریخ (شمسی)" value="{{ $toDate }}">
        </div>
        <div class="col-md-2">
            <select name="expense_category_filter" class="form-select form-select-sm">
                <option value="">همه دسته‌بندی‌ها (مصارف)</option>
                @foreach($expenseCategories as $cat)
                    <option value="{{ $cat->id }}" {{ request('expense_category_filter') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="month_filter" class="form-select form-select-sm">
                <option value="">همه ماه‌ها (معاشات)</option>
                @foreach($months as $m)
                    <option value="{{ $m->id }}" {{ request('month_filter') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
            <a href="{{ route('school.reports.financial.expenses') }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    {{-- ============= جدول اول: مصارف روزمره ============= --}}
    <div class="card shadow mb-5">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-file-invoice me-1"></i> مصارف روزمره</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>گروه هزینه</th>
                        <th>ش فاکتور</th>
                        <th>تاریخ</th>
                        <th>شرح</th>
                        <th>تعداد-مقدار</th>
                        <th>مبلغ</th>
                        <th>مجموع</th>
                        <th>جمع گروه</th>
                    </tr>
                </thead>
                <tbody>
                    @php $runningTotal = 0; @endphp
                    @forelse($expensesGrouped as $group)
                        @foreach($group['items'] as $index => $expense)
                            <tr>
                                @if($index === 0)
                                    <td rowspan="{{ count($group['items']) }}">{{ $group['category'] }}</td>
                                @endif
                                <td>{{ $expense->id }}</td>
                                <td>{{ \App\Helpers\JalaliHelper::toJalali($expense->expense_date) }}</td>
                                <td>{{ $expense->description ?? $expense->title ?? '—' }}</td>
                                <td>{{ $expense->quantity ?? '—' }}</td>
                                <td>{{ number_format($expense->total_amount) }}</td>
                                @if($index === 0)
                                    @php $groupTotal = $group['total']; $runningTotal += $groupTotal; @endphp
                                    <td>{{ number_format($runningTotal) }}</td>
                                    <td rowspan="{{ count($group['items']) }}">{{ number_format($groupTotal) }}</td>
                                @else
                                    <td>{{ number_format($runningTotal) }}</td>
                                @endif
                            </tr>
                        @endforeach
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">مصرفی با این فیلترها یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td colspan="5" class="text-end">جمع کل:</td>
                        <td>{{ number_format($totalExpenses) }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- ============= جدول دوم: حقوق کارمندان ============= --}}
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-money-check-alt me-1"></i> حقوق کارمندان</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ش فاکتور</th>
                        <th>نام و نام خانوادگی</th>
                        <th>سمت</th>
                        <th>وضعیت</th>
                        <th>آی‌دی کارمند</th>
                        <th>حقوق پایه</th>
                        <th>امتیاز سمت</th>
                        <th>امتیاز سابقه</th>
                        <th>امتیاز تحصیل</th>
                        <th>پاداش</th>
                        <th>اضافه‌کاری</th>
                        <th>کسورات</th>
                        <th>مالیات</th>
                        <th>خالص</th>
                        <th>پرداختی</th>
                        <th>مجموع</th>
                    </tr>
                </thead>
                <tbody>
                    @php $salaryRunningTotal = 0; @endphp
                    @forelse($salariesQuery as $salary)
                        @php
                            $deduction = ($salary->deduction_amount ?? 0) + ($salary->guarantee_amount ?? 0);
                            $net = $salary->total_amount ?? 0;
                            $paid = $salary->paid_amount ?? 0;
                            $salaryRunningTotal += $paid;
                        @endphp
                        <tr>
                            <td>{{ $salary->id }}</td>
                            <td>{{ $salary->employee->first_name ?? '' }} {{ $salary->employee->last_name ?? '' }}</td>
                            <td>{{ $salary->employee->position->name ?? $salary->employee->position ?? '—' }}</td>
                            <td>{{ $salary->employee->status ?? '—' }}</td>
                            <td>{{ $salary->employee->employee_code ?? '—' }}</td>
                            <td>{{ number_format($salary->base_salary) }}</td>
                            <td>{{ $salary->employee->position_points ?? '—' }}</td>
                            <td>{{ $salary->employee->experience_points ?? '—' }}</td>
                            <td>{{ $salary->employee->education_points ?? '—' }}</td>
                            <td>{{ number_format($salary->bonus_amount ?? 0) }}</td>
                            <td>{{ number_format($salary->overtime_amount ?? 0) }}</td>
                            <td>{{ number_format($deduction) }}</td>
                            <td>{{ number_format($salary->tax_amount ?? 0) }}</td>
                            <td>{{ number_format($net) }}</td>
                            <td>{{ number_format($paid) }}</td>
                            <td>{{ number_format($salaryRunningTotal) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="16" class="text-center text-muted py-4">حقوق پرداختی با این فیلترها یافت نشد.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td colspan="5" class="text-end">جمع کل:</td>
                        <td>{{ number_format($salariesQuery->sum('base_salary')) }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td>{{ number_format($salariesQuery->sum('bonus_amount')) }}</td>
                        <td>{{ number_format($salariesQuery->sum('overtime_amount')) }}</td>
                        <td>{{ number_format($salariesQuery->sum('deduction_amount') + $salariesQuery->sum('guarantee_amount')) }}</td>
                        <td>{{ number_format($salariesQuery->sum('tax_amount')) }}</td>
                        <td>{{ number_format($salariesQuery->sum('total_amount')) }}</td>
                        <td>{{ number_format($totalSalaries) }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
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
