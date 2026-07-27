@extends('layouts.admin')

@section('title', 'اقساط قرض‌الحسنه – ' . $loan->borrower_name)

@section('content')
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.loans.index') }}">قرض‌الحسنه</a></li>
            <li class="breadcrumb-item active">اقساط</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-calendar-check ms-2"></i> اقساط وام: {{ $loan->borrower_name }} {{ $loan->borrower_last_name }}</h5>
            <a href="{{ route('school.loans.show', $loan) }}" target="_blank" class="btn btn-light btn-sm">
                <i class="fas fa-print"></i> پیش‌نمایش چاپ
            </a>
        </div>
        <div class="card-body">
            @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>مبلغ قسط</th>
                        <th>سررسید</th>
                        <th>پرداخت شده</th>
                        <th>تاریخ پرداخت</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loan->installments as $installment)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ number_format($installment->amount) }} ؋</td>
                        <td>{{ \App\Helpers\JalaliHelper::toJalali($installment->due_date) }}</td>
                        <td>{{ $installment->paid_amount ? number_format($installment->paid_amount) : '—' }}</td>
                        <td>{{ $installment->paid_date ? \App\Helpers\JalaliHelper::toJalali($installment->paid_date) : '—' }}</td>
                        <td>
                            @if($installment->status == 'paid')
                                <span class="badge bg-success">پرداخت</span>
                            @else
                                <span class="badge bg-danger">معوق</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                @if($installment->status == 'pending')
                                    {{-- دکمه پرداخت --}}
                                    <button class="btn btn-outline-success pay-installment-btn"
                                            data-installment-id="{{ $installment->id }}"
                                            data-amount="{{ $installment->amount }}">
                                        <i class="fa fa-money-bill-wave"></i> پرداخت
                                    </button>
                                @else
                                    {{-- رسید --}}
                                    <a href="{{ route('school.installments.receipt', $installment) }}"
                                       target="_blank" class="btn btn-outline-info" title="رسید چاپی">
                                        <i class="fa fa-receipt"></i>
                                    </a>
                                    {{-- ویرایش --}}
                                    <a href="{{ route('school.installments.edit', $installment) }}"
                                       class="btn btn-outline-warning" title="ویرایش پرداخت">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    {{-- حذف پرداخت --}}
                                    <form action="{{ route('school.installments.destroy', $installment) }}"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('با حذف این پرداخت، قسط به حالت معوق برمی‌گردد. ادامه؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger" title="حذف پرداخت">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center">هیچ قسطی وجود ندارد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Modal پرداخت سریع (بدون صندوق) --}}
<div class="modal fade" id="payInstallmentModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">پرداخت قسط</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="payForm">
                    @csrf
                    <input type="hidden" id="installment_id" name="installment_id">
                    <div class="mb-3">
                        <label class="form-label">مبلغ (افغانی)</label>
                        <input type="text" id="modal_amount" class="form-control" readonly>
                    </div>
                    {{-- ❌ فیلد صندوق حذف شد --}}
                    <div class="mb-3">
                        <label class="form-label">تاریخ پرداخت (شمسی)</label>
                        <input type="text" id="modal_paid_date" class="form-control" value="{{ \App\Helpers\JalaliHelper::todayJalali() }}">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">یادداشت</label>
                        <input type="text" id="modal_notes" class="form-control">
                    </div>
                    <button type="button" id="submitPayInstallment" class="btn btn-success w-100">ثبت پرداخت</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.querySelectorAll('.pay-installment-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('installment_id').value = this.dataset.installmentId;
        document.getElementById('modal_amount').value = this.dataset.amount;
        new bootstrap.Modal(document.getElementById('payInstallmentModal')).show();
    });
});

document.getElementById('submitPayInstallment').addEventListener('click', function() {
    const data = {
        installment_id: document.getElementById('installment_id').value,
        paid_date: document.getElementById('modal_paid_date').value,
        notes: document.getElementById('modal_notes').value,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    fetch('{{ route('school.installments.pay') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            alert('پرداخت ثبت شد.');
            location.reload();
        } else {
            alert('خطا: ' + (response.message || 'مشکلی رخ داد'));
        }
    })
    .catch(() => alert('خطا در ارتباط با سرور'));
});
</script>
@endpush
