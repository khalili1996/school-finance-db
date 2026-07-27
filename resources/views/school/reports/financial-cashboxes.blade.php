@extends('layouts.admin')

@section('title', 'گزارش صندوق')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-cash-register ms-2"></i> گزارش صندوق</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    {{-- ★ هدر یکسان (لوگو + نام مکتب + سال مالی) --}}
    @include('partials.report-header', ['title' => 'گزارش صندوق', 'subtitle' => ''])

    {{-- خلاصه وضعیت صندوق‌ها --}}
    <div class="row mb-4">
        @foreach($cashboxes as $box)
        <div class="col-md-3 mb-3">
            <div class="card border-{{ $box->type == 'bank' ? 'primary' : 'success' }} shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-{{ $box->type == 'bank' ? 'university' : 'money-bill-wave' }} fa-2x text-{{ $box->type == 'bank' ? 'primary' : 'success' }} mb-2"></i>
                    <h6>{{ $box->name }}</h6>
                    <h4 class="fw-bold">{{ number_format($box->current_balance) }} ؋</h4>
                    <small class="text-muted">{{ $box->type == 'bank' ? 'بانکی' : 'نقدی' }}</small>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    {{-- فیلتر تراکنش‌ها --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-2">
            <input type="text" name="from_date" class="form-control form-control-sm datepicker" placeholder="از تاریخ (شمسی)" value="{{ $fromDate }}">
        </div>
        <div class="col-md-2">
            <input type="text" name="to_date" class="form-control form-control-sm datepicker" placeholder="تا تاریخ (شمسی)" value="{{ $toDate }}">
        </div>
        <div class="col-md-2">
            <select name="type_filter" class="form-select form-select-sm">
                <option value="">همه انواع</option>
                <option value="deposit" {{ request('type_filter') == 'deposit' ? 'selected' : '' }}>واریز</option>
                <option value="withdrawal" {{ request('type_filter') == 'withdrawal' ? 'selected' : '' }}>برداشت</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال فیلتر</button>
            <a href="{{ route('school.reports.financial.cashboxes') }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    {{-- جدول تراکنش‌ها --}}
    <div class="card shadow">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-exchange-alt me-1"></i> تراکنش‌های صندوق</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>صندوق</th>
                        <th>نوع</th>
                        <th>مبلغ (افغانی)</th>
                        <th>شرح</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $tr)
                    <tr>
                        <td>{{ $transactions->firstItem() + $loop->index }}</td>
                        <td>{{ \App\Helpers\JalaliHelper::toJalali($tr->transaction_date) }}</td>
                        <td>{{ $tr->cashbox->name ?? '—' }}</td>
                        <td>
                            @if($tr->type == 'deposit')
                                <span class="badge bg-success">واریز</span>
                            @else
                                <span class="badge bg-danger">برداشت</span>
                            @endif
                        </td>
                        <td>{{ number_format($tr->amount) }}</td>
                        <td>{{ $tr->description ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">تراکنشی با این فیلترها یافت نشد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $transactions->links() }}
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
