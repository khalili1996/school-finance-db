@extends('layouts.admin')

@section('title', 'معاشات کارمندان')

@section('content')
<div class="container-fluid p-0">
    {{-- نوار ابزار چسبان --}}
    <div class="sticky-toolbar bg-white shadow-sm border-bottom px-3 py-2 mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-money-check-alt fa-lg text-success ms-2"></i>
                <h5 class="fw-bold mb-0 text-dark">معاشات کارمندان (نمای ماهانه)</h5>
            </div>
            <div class="btn-group">
                <a href="{{ route('school.salaries.create') }}" class="btn btn-success rounded-pill px-3 py-2">
                    <i class="fas fa-plus-circle ms-1"></i> ثبت معاش
                </a>
            </div>
            <div class="btn-group">
                <a href="{{ route('school.salaries.print-report', request()->query()) }}"
                   target="_blank"
                   class="btn btn-outline-info rounded-pill px-3 py-2">
                    <i class="fas fa-print ms-1"></i> چاپ گزارش
                </a>
            </div>
        </div>
    </div>

    <div class="px-3">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

        {{-- فیلترها --}}
        <form method="GET" action="{{ route('school.salaries.index') }}" class="row g-2 mb-4">
            <div class="col-md-2">
                <label class="form-label">کارمند</label>
                <select name="employee_id" class="form-select">
                    <option value="">همه</option>
                    @foreach($allEmployees as $emp)
                        <option value="{{ $emp->id }}" {{ request('employee_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->first_name }} {{ $emp->last_name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">از ماه</label>
                <select name="month_from" class="form-select">
                    <option value="">انتخاب</option>
                    @foreach($allMonths as $m)
                        <option value="{{ $m->id }}" {{ request('month_from') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">تا ماه</label>
                <select name="month_to" class="form-select">
                    <option value="">انتخاب</option>
                    @foreach($allMonths as $m)
                        <option value="{{ $m->id }}" {{ request('month_to') == $m->id ? 'selected' : '' }}>{{ $m->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">وضعیت پرداخت</label>
                <select name="payment_status" class="form-select">
                    <option value="">همه</option>
                    <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>پرداخت نشده</option>
                    <option value="paid"   {{ request('payment_status') == 'paid' ? 'selected' : '' }}>پرداخت کامل</option>
                </select>
            </div>
            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-primary">اعمال</button>
                <a href="{{ route('school.salaries.index') }}" class="btn btn-secondary">حذف فیلتر</a>
            </div>
        </form>

        {{-- جدول ماتریسی --}}
        @if(empty($matrix) || count($matrix) == 0)
            <div class="alert alert-info text-center py-5">
                <h4>هیچ معاشی برای نمایش وجود ندارد</h4>
                <p>لطفاً ابتدا از دکمه «ثبت معاش» برای ثبت معاش یک کارمند استفاده کنید.</p>
                <a href="{{ route('school.salaries.create') }}" class="btn btn-success mt-2">
                    <i class="fas fa-plus-circle"></i> ثبت اولین معاش
                </a>
            </div>
        @else
            <div class="card shadow">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0" style="font-size: 12px;">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>نام کارمند</th>
                                <th>سمت</th>
                                @foreach($months as $month)
                                    <th class="text-center">{{ $month->name }}</th>
                                @endforeach
                                <th>جمع کل</th>
                                <th>پرداخت شده</th>
                                <th>باقی‌مانده</th>
                                <th>پرینت</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($matrix as $row)
                                @php $emp = $row['employee']; @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $emp->first_name }} {{ $emp->last_name }}</td>
                                    <td>{{ $emp->position->name ?? '—' }}</td>
                                    @foreach($months as $month)
                                        @php $cell = $row['months'][$month->id] ?? null; @endphp
                                        <td class="text-center {{ $cell['isPaid'] ?? false ? 'bg-success-light' : ($cell['amount'] > 0 ? 'bg-warning-light' : '') }}">
                                            @if($cell && $cell['amount'] > 0)
                                                <div>{{ number_format($cell['amount']) }}</div>
                                                @if($cell['isPaid'])
                                                    <span class="badge bg-success">پرداخت</span>
                                                @else
                                                    <span class="text-danger">{{ number_format($cell['remaining']) }}</span>
                                                    <button class="btn btn-sm btn-outline-success mt-1 pay-salary-btn"
                                                        data-employee-id="{{ $emp->id }}"
                                                        data-month-id="{{ $month->id }}"
                                                        data-remaining="{{ $cell['remaining'] }}">
                                                        پرداخت
                                                    </button>
                                                @endif
                                                {{-- دکمه‌های ویرایش و حذف --}}
                                                <div class="mt-1">
                                                    <a href="{{ route('school.salaries.edit', $cell['salary']->id) }}"
                                                       class="btn btn-sm btn-outline-warning px-1" title="ویرایش">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('school.salaries.destroy', $cell['salary']->id) }}"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('آیا از حذف این معاش اطمینان دارید؟')">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger px-1" title="حذف">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    @endforeach
                                    <td class="fw-bold">{{ number_format($row['totalAmount']) }}</td>
                                    <td class="text-success fw-bold">{{ number_format($row['totalPaid']) }}</td>
                                    <td class="text-danger fw-bold">{{ number_format($row['totalRemaining']) }}</td>
                                    <td class="text-center">
                                        <a href="{{ route('school.salaries.preview', ['employee' => $emp->id, 'month_from' => request('month_from'), 'month_to' => request('month_to')]) }}"
                                           target="_blank" class="btn btn-sm btn-outline-secondary" title="پیش‌نمایش چاپ">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Modal پرداخت سریع --}}
<div class="modal fade" id="quickPayModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-money-bill-wave me-1"></i> پرداخت سریع معاش</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickPayForm">
                    @csrf
                    <input type="hidden" id="quick_employee_id" name="employee_id">
                    <input type="hidden" id="quick_month_id" name="month_id">
                    <div class="mb-3">
                        <label class="form-label">مبلغ (افغانی)</label>
                        <input type="number" id="quick_amount" name="amount" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">صندوق</label>
                        <select id="quick_cashbox" name="cashbox_id" class="form-select" required>
                            <option value="">-- انتخاب --</option>
                            @foreach($cashboxes as $cb)
                                <option value="{{ $cb->id }}">{{ $cb->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاریخ</label>
                        <input type="text" id="quick_date" name="payment_date" class="form-control"
                               value="{{ \App\Helpers\JalaliHelper::todayJalali() }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">شماره رسید</label>
                        <input type="text" id="quick_receipt" name="receipt_number" class="form-control">
                    </div>
                    <button type="button" id="submitQuickPay" class="btn btn-success w-100">ثبت پرداخت</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .sticky-toolbar { position: sticky; top: 0; z-index: 1020; background: #fff; }
    .bg-success-light { background-color: #d4edda !important; }
    .bg-warning-light { background-color: #fff3cd !important; }
</style>
@endpush

@push('scripts')
<script>
document.querySelectorAll('.pay-salary-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('quick_employee_id').value = this.dataset.employeeId;
        document.getElementById('quick_month_id').value = this.dataset.monthId;
        document.getElementById('quick_amount').value = this.dataset.remaining;
        new bootstrap.Modal(document.getElementById('quickPayModal')).show();
    });
});

document.getElementById('submitQuickPay').addEventListener('click', function() {
    const data = {
        employee_id: document.getElementById('quick_employee_id').value,
        month_id: document.getElementById('quick_month_id').value,
        amount: document.getElementById('quick_amount').value,
        cashbox_id: document.getElementById('quick_cashbox').value,
        payment_date: document.getElementById('quick_date').value,
        receipt_number: document.getElementById('quick_receipt').value,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    fetch('{{ route('school.salary-payments.quick-store') }}', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            alert('پرداخت با موفقیت ثبت شد.');
            location.reload();
        } else {
            alert('خطا: ' + (response.message || 'مشکلی رخ داد'));
        }
    })
    .catch(() => alert('خطا در ارتباط با سرور'));
});
</script>
@endpush
