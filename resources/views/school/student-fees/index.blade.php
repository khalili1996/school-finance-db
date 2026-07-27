@extends('layouts.admin')

@section('title', 'شهریه دانش‌آموزان')

@section('content')
<div class="container-fluid p-0">
    {{-- نوار ابزار چسبان --}}
    <div class="sticky-toolbar bg-white shadow-sm border-bottom px-3 py-2 mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-file-invoice-dollar fa-lg text-primary ms-2"></i>
                <h5 class="fw-bold mb-0 text-dark">شهریه دانش‌آموزان</h5>
            </div>
            <div class="btn-group" role="group">
                <a href="{{ route('school.student-fees.create') }}"
                   class="btn btn-success rounded-pill px-3 py-2"
                   title="تعیین شهریه جدید">
                    <i class="fas fa-plus-circle ms-1"></i> تعیین شهریه
                </a>
                <button class="btn btn-primary rounded-pill px-3 py-2"
                        data-bs-toggle="modal" data-bs-target="#paymentModal"
                        title="ثبت پرداخت سریع شهریه">
                    <i class="fas fa-money-bill-wave ms-1"></i> دریافت شهریه
                </button>
            </div>
            <div class="btn-group" role="group">
    <a href="{{ route('school.payments.index') }}"
       class="btn btn-outline-primary rounded-pill px-3 py-2"
       title="مشاهده و مدیریت تمام پرداخت‌ها">
        <i class="fas fa-list-ul ms-1"></i> مدیریت پرداخت‌ها
    </a>
</div>
            <div class="btn-group" role="group">
                <a href="{{ route('school.student-fees.print', request()->query()) }}"
                   target="_blank"
                   class="btn btn-outline-info rounded-pill px-3 py-2"
                   title="چاپ گزارش جدول شهریه">
                    <i class="fas fa-print ms-1"></i> چاپ گزارش
                </a>
                <a href="{{ route('school.student-fees.notice-report', request()->query()) }}"
                   target="_blank"
                   class="btn btn-outline-danger rounded-pill px-3 py-2"
                   title="چاپ اطلاعیه‌های بدهکاری">
                    <i class="fas fa-exclamation-triangle ms-1"></i> چاپ اطلاعیه‌ها
                </a>
            </div>
        </div>
    </div>

    {{-- فیلترها و جدول --}}
    <div class="px-3">
        <form method="GET" action="{{ route('school.student-fees.index') }}" class="row g-2 mb-4">
            <div class="col-md-2">
                <label class="form-label">صنف</label>
                <select name="class_filter" class="form-select">
                    <option value="">همه</option>
                    @foreach($classes as $cls)
                        <option value="{{ $cls }}" {{ request('class_filter') == $cls ? 'selected' : '' }}>{{ $cls }}</option>
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
                    <option value="paid"   {{ request('payment_status') == 'paid' ? 'selected' : '' }}>پرداخت شده</option>
                </select>
            </div>
            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-primary">اعمال</button>
                <a href="{{ route('school.student-fees.index') }}" class="btn btn-secondary">حذف فیلتر</a>
            </div>
        </form>

        {{-- جدول ماتریسی --}}
        <div class="card shadow">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size: 12px;">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>کد</th>
                            <th>نام</th>
                            <th>نام پدر</th>
                            <th>صنف</th>
                            @foreach($months as $month)
                                <th class="text-center">{{ $month->name }}</th>
                            @endforeach
                            <th>تخفیف</th>
                            <th>کل رسید</th>
                            <th>پرینت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                $fees = $student->studentFees->keyBy('month_id');
                                $payments = $student->payments->groupBy('month_id');
                                $totalDiscount = $student->studentFees->sum('discount');
                                $totalPaidByStudent = $student->payments->sum('amount');

                                // ساخت JSON کامل برای استفاده در modal‌ها
                                $feesJson = $student->studentFees->map(fn($f) => [
                                    'id' => $f->id,
                                    'month_id' => $f->month_id,
                                    'amount' => $f->amount,
                                    'discount' => $f->discount,
                                    'fee_type_id' => $f->fee_type_id,
                                ])->values()->toJson();

                                $paymentsJson = $student->payments->map(fn($p) => [
                                    'id' => $p->id,
                                    'month_id' => $p->month_id,
                                    'amount' => $p->amount,
                                ])->values()->toJson();
                            @endphp
                            <tr data-fees="{{ $feesJson }}" data-payments="{{ $paymentsJson }}">
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $student->student_code }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->father_name }}</td>
                                <td>{{ $student->class ?? '—' }}</td>
                                @foreach($months as $month)
                                    @php
                                        $fee = $fees->get($month->id);
                                        $paidAmount = $payments->has($month->id) ? $payments->get($month->id)->sum('amount') : 0;
                                        $isPaid = $fee && ($paidAmount >= ($fee->amount - $fee->discount));
                                        $lastPayment = $isPaid ? $payments->get($month->id)->sortByDesc('id')->first() : null;
                                    @endphp
                                    <td class="text-center {{ $isPaid ? 'bg-success-light' : ($fee ? 'bg-warning-light' : '') }}">
                                        @if($fee)
                                            <div>{{ number_format($fee->amount) }}</div>
                                            @if($isPaid)
                                                <span class="badge bg-success">رسید</span>
                                                @if($lastPayment)
                                                    <a href="{{ route('school.payments.payment-slip', $lastPayment->id) }}"
                                                       class="btn btn-sm btn-outline-secondary mt-1" target="_blank" title="رسید چاپی">
                                                        <i class="fas fa-print"></i>
                                                    </a>
                                                @endif
                                            @else
                                                <span class="text-danger">{{ number_format(($fee->amount - $fee->discount) - $paidAmount) }}</span>
                                                <button class="btn btn-sm btn-outline-success mt-1 pay-btn"
                                                    data-student-id="{{ $student->id }}"
                                                    data-month-id="{{ $month->id }}"
                                                    data-amount="{{ $fee->amount - $fee->discount }}">
                                                    رسید
                                                </button>
                                            @endif
                                        @else
                                            —
                                        @endif
                                    </td>
                                @endforeach
                                <td class="text-warning fw-bold">{{ number_format($totalDiscount) }}</td>
                                <td class="text-success fw-bold">{{ number_format($totalPaidByStudent) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('school.student-fees.fee-preview', $student->id) }}" target="_blank" class="btn btn-sm btn-outline-secondary mb-1" title="پیش‌نمایش چاپ شهریه">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                                {{-- ستون عملیات (فقط آیکون‌ها، یک خط) --}}
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <button type="button" class="btn btn-sm btn-outline-warning edit-fee-btn p-1"
                                                data-student-id="{{ $student->id }}"
                                                title="ویرایش شهریه">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-info edit-payment-btn p-1"
                                                data-student-id="{{ $student->id }}"
                                                title="ویرایش پرداخت">
                                            <i class="fas fa-pencil-alt"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-danger delete-fee-btn p-1"
                                                data-student-id="{{ $student->id }}"
                                                title="حذف شهریه">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                        <form action="{{ route('school.students.destroy', $student->id) }}"
                                              method="POST" style="display:inline-block;"
                                              onsubmit="return confirm('آیا از حذف کامل این دانش‌آموز و تمام داده‌های مرتبط اطمینان دارید؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-dark p-1" title="حذف دانش‌آموز">
                                                <i class="fas fa-user-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="{{ 9 + $months->count() }}" class="text-center text-muted py-3">دانش‌آموزی یافت نشد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer">{{ $students->links() }}</div>
        </div>
    </div>
</div>

{{-- Modal پرداخت سریع (بدون تغییر) --}}
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-money-bill-wave me-1"></i> ثبت پرداخت شهریه</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickPaymentForm">
                    @csrf
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">صنف</label>
                            <select id="modal_class" class="form-select">
                                <option value="">-- انتخاب صنف --</option>
                                @foreach($classes as $cls)
                                    <option value="{{ $cls }}">{{ $cls }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">دانش‌آموز <span class="text-danger">*</span></label>
                            <select id="modal_student" class="form-select" required>
                                <option value="">ابتدا صنف را انتخاب کنید</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">ماه <span class="text-danger">*</span></label>
                            <select id="modal_month" class="form-select" required>
                                <option value="">-- انتخاب ماه --</option>
                                @foreach($allMonths as $m)
                                    <option value="{{ $m->id }}">{{ $m->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">مبلغ (افغانی) <span class="text-danger">*</span></label>
                            <input type="number" id="modal_amount" class="form-control" min="1" required>
                            <small class="text-muted">می‌توانید مبلغ را برای پرداخت جزیی تغییر دهید</small>
                            <div id="remaining_after_pay" class="text-info mt-1" style="display:none;">
                                باقی‌مانده: <span id="remaining_value">0</span> ؋
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">صندوق <span class="text-danger">*</span></label>
                            <select id="modal_cashbox" class="form-select" required>
                                <option value="">-- انتخاب --</option>
                                @foreach($cashboxes as $cb)
                                    <option value="{{ $cb->id }}">{{ $cb->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">تاریخ</label>
                            <input type="text" id="modal_date" class="form-control" value="{{ \App\Helpers\JalaliHelper::todayJalali() }}" required>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">شماره رسید</label>
                            <input type="text" id="modal_receipt" class="form-control">
                        </div>
                    </div>
                    <div class="text-start">
                        <button type="button" id="submitQuickPayment" class="btn btn-success">ثبت پرداخت</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal ویرایش شهریه --}}
<div class="modal fade" id="editFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="fas fa-edit me-1"></i> ویرایش شهریه</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="editFeeForm" method="POST">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="student_id" id="edit_fee_student_id">
                    <input type="hidden" name="fee_type_id" id="edit_fee_type_id">
                    <input type="hidden" name="month_id" id="edit_fee_month_id">
                    <div class="mb-3">
                        <label class="form-label">ماه</label>
                        <select class="form-select" id="edit_fee_month_select" required>
                            <option value="">-- انتخاب ماه --</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">مبلغ (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="edit_fee_amount" class="form-control" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تخفیف</label>
                        <input type="number" name="discount" id="edit_fee_discount" class="form-control" min="0">
                    </div>
                    <div class="text-start">
                        <button type="submit" class="btn btn-warning">ذخیره</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal ویرایش پرداخت (هدایت به صفحه ویرایش) --}}
<div class="modal fade" id="editPaymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="fas fa-pencil-alt me-1"></i> ویرایش پرداخت</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ماه</label>
                    <select class="form-select" id="edit_payment_month_select" required>
                        <option value="">-- انتخاب ماه --</option>
                    </select>
                </div>
                <div class="text-start">
                    <button type="button" id="goToEditPayment" class="btn btn-info">ادامه</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal حذف شهریه --}}
<div class="modal fade" id="deleteFeeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="fas fa-trash-alt me-1"></i> حذف شهریه</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="deleteFeeForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <div class="mb-3">
                        <label class="form-label">ماه</label>
                        <select class="form-select" name="fee_id" id="delete_fee_select" required>
                            <option value="">-- انتخاب ماه --</option>
                        </select>
                    </div>
                    <p class="text-danger">توجه: با حذف شهریه، پرداخت‌های مربوط به این ماه نیز حذف خواهند شد.</p>
                    <div class="text-start">
                        <button type="submit" class="btn btn-danger">حذف</button>
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@endsection

@push('styles')
<style>
    .sticky-toolbar {
        position: sticky;
        top: 0;
        z-index: 1020;
        background-color: #fff;
    }
</style>
@endpush

@push('scripts')
{{-- نگاشت نام ماه‌ها (ID -> name) --}}
<script>
    const monthNames = @json($allMonths->pluck('name', 'id'));
</script>

<script>
// ==================== Modal پرداخت سریع ====================
document.getElementById('modal_class').addEventListener('change', function() {
    const cls = this.value;
    const studentSelect = document.getElementById('modal_student');
    studentSelect.innerHTML = '<option value="">در حال بارگذاری...</option>';
    if (!cls) {
        studentSelect.innerHTML = '<option value="">ابتدا صنف را انتخاب کنید</option>';
        return;
    }
    fetch(`/school/api/students/by-class?class=${encodeURIComponent(cls)}`)
        .then(res => res.json())
        .then(students => {
            studentSelect.innerHTML = '<option value="">-- انتخاب دانش‌آموز --</option>';
            students.forEach(s => {
                studentSelect.innerHTML += `<option value="${s.id}">${s.first_name} ${s.last_name} (${s.student_code})</option>`;
            });
        });
});

document.querySelectorAll('.pay-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const studentId = this.dataset.studentId;
        const monthId = this.dataset.monthId;
        const amount = this.dataset.amount;
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        document.getElementById('modal_class').value = '';
        document.getElementById('modal_student').value = studentId;
        document.getElementById('modal_month').value = monthId;
        document.getElementById('modal_amount').value = amount;
        document.getElementById('modal_amount').setAttribute('max', amount);
        document.getElementById('modal_amount').setAttribute('data-max', amount);
        document.getElementById('remaining_value').textContent = '0';
        document.getElementById('remaining_after_pay').style.display = 'none';
        modal.show();
    });
});

document.getElementById('modal_amount').addEventListener('input', function() {
    const amount = parseFloat(this.value) || 0;
    const max = parseFloat(this.getAttribute('data-max')) || 0;
    const remaining = Math.max(0, max - amount);
    document.getElementById('remaining_value').textContent = remaining.toLocaleString();
    document.getElementById('remaining_after_pay').style.display = 'block';
});

document.getElementById('submitQuickPayment').addEventListener('click', function() {
    const data = {
        student_id: document.getElementById('modal_student').value,
        month_id: document.getElementById('modal_month').value,
        amount: document.getElementById('modal_amount').value,
        cashbox_id: document.getElementById('modal_cashbox').value,
        payment_date: document.getElementById('modal_date').value,
        receipt_number: document.getElementById('modal_receipt').value,
        payment_method: 'cash',
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    fetch('{{ route('school.payments.store') }}', {
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

// ==================== توابع کمکی ====================
function populateMonthSelect(selectElement, items, valueField) {
    selectElement.innerHTML = '<option value="">-- انتخاب ماه --</option>';
    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item[valueField];
        option.textContent = monthNames[item.month_id] || 'ماه ' + item.month_id;
        selectElement.appendChild(option);
    });
}

// ==================== ویرایش شهریه ====================
document.querySelectorAll('.edit-fee-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const studentId = this.dataset.studentId;
        const fees = JSON.parse(row.dataset.fees);

        const select = document.getElementById('edit_fee_month_select');
        populateMonthSelect(select, fees, 'id');

        document.getElementById('edit_fee_student_id').value = studentId;

        select.addEventListener('change', function() {
            const selectedFeeId = this.value;
            const fee = fees.find(f => f.id == selectedFeeId);
            if (fee) {
                document.getElementById('edit_fee_amount').value = fee.amount;
                document.getElementById('edit_fee_discount').value = fee.discount;
                document.getElementById('edit_fee_month_id').value = fee.month_id;
                document.getElementById('edit_fee_type_id').value = fee.fee_type_id;
            }
        });

        const modal = new bootstrap.Modal(document.getElementById('editFeeModal'));
        modal.show();
    });
});

// ==================== ویرایش پرداخت ====================
document.querySelectorAll('.edit-payment-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const payments = JSON.parse(row.dataset.payments);

        const select = document.getElementById('edit_payment_month_select');
        populateMonthSelect(select, payments, 'id');

        document.getElementById('goToEditPayment').onclick = function() {
            const paymentId = select.value;
            if (!paymentId) {
                alert('لطفاً یک ماه انتخاب کنید.');
                return;
            }
            window.location.href = '{{ route("school.payments.edit", ":id") }}'.replace(':id', paymentId);
        };

        const modal = new bootstrap.Modal(document.getElementById('editPaymentModal'));
        modal.show();
    });
});

// ==================== حذف شهریه ====================
document.querySelectorAll('.delete-fee-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const row = this.closest('tr');
        const fees = JSON.parse(row.dataset.fees);

        const select = document.getElementById('delete_fee_select');
        populateMonthSelect(select, fees, 'id');

        select.addEventListener('change', function() {
            const feeId = this.value;
            const form = document.getElementById('deleteFeeForm');
            form.action = '{{ route("school.student-fees.destroy", ":id") }}'.replace(':id', feeId);
        });

        const modal = new bootstrap.Modal(document.getElementById('deleteFeeModal'));
        modal.show();
    });
});
</script>
@endpush
