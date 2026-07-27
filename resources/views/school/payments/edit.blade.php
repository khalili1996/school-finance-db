@extends('layouts.admin')

@section('title', 'ویرایش پرداخت')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.payments.index') }}">پرداخت‌ها</a></li>
            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش پرداخت</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('school.payments.update', $payment->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    {{-- جستجوی زنده دانش‌آموز --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">جستجوی دانش‌آموز <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" id="student_search" class="form-control"
                                   value="{{ old('student_search', $payment->student->first_name . ' ' . $payment->student->last_name . ' (' . $payment->student->student_code . ')') }}"
                                   autocomplete="off">
                            <input type="hidden" name="student_id" id="student_id" value="{{ old('student_id', $payment->student_id) }}" required>
                            <div id="student_results" class="list-group position-absolute w-100" style="z-index:1000; max-height:200px; overflow-y:auto; display:none;"></div>
                        </div>
                    </div>

                    {{-- نوع هزینه --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نوع هزینه (اختیاری)</label>
                        <select name="fee_id" id="fee_id" class="form-select">
                            <option value="">-- بدون انتخاب --</option>
                            @foreach($fees as $fee)
                                @php
                                    $paid = $fee->payments->sum('amount');
                                    $remaining = ($fee->amount - $fee->discount) - $paid;
                                @endphp
                                <option value="{{ $fee->id }}" {{ old('fee_id', $payment->fee_id) == $fee->id ? 'selected' : '' }}>
                                    {{ $fee->feeType->name ?? '—' }} ({{ $fee->month->name ?? '—' }}) - مانده: {{ max($remaining, 0) }} ؋
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- مبلغ --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" value="{{ old('amount', $payment->amount) }}" min="1" required>
                    </div>

                    {{-- 📅 تاریخ پرداخت شمسی --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ پرداخت <span class="text-danger">*</span></label>
                        <input type="text" name="payment_date" class="form-control"
                               placeholder="مثال: ۱۴۰۴/۰۳/۲۴"
                               value="{{ old('payment_date', \App\Helpers\JalaliHelper::toJalali($payment->payment_date)) }}" required>
                        <small class="form-text text-muted">تاریخ شمسی را ویرایش کنید</small>
                    </div>

                    {{-- روش پرداخت --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">روش پرداخت <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>نقدی</option>
                            <option value="bank" {{ old('payment_method', $payment->payment_method) == 'bank' ? 'selected' : '' }}>بانکی</option>
                            <option value="other" {{ old('payment_method', $payment->payment_method) == 'other' ? 'selected' : '' }}>سایر</option>
                        </select>
                    </div>

                    {{-- ماه --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ماه</label>
                        <select name="month_id" class="form-select">
                            <option value="">-- بدون ماه --</option>
                            @foreach($months as $month)
                                <option value="{{ $month->id }}" {{ old('month_id', $payment->month_id) == $month->id ? 'selected' : '' }}>{{ $month->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- صندوق مقصد --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">صندوق مقصد <span class="text-danger">*</span></label>
                        <select name="cashbox_id" class="form-select @error('cashbox_id') is-invalid @enderror" required>
                            <option value="">-- انتخاب کنید --</option>
                            @foreach($cashboxes as $cb)
                                <option value="{{ $cb->id }}" {{ old('cashbox_id', $payment->cashboxTransactions()->first()?->cashbox_id) == $cb->id ? 'selected' : '' }}>
                                    {{ $cb->name }} ({{ $cb->type === 'bank' ? 'بانکی' : 'نقدی' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- شماره رسید --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره رسید</label>
                        <input type="text" name="receipt_number" class="form-control" value="{{ old('receipt_number', $payment->receipt_number) }}">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">توضیحات</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $payment->notes) }}</textarea>
                </div>

                <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                <a href="{{ route('school.payments.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('student_search').addEventListener('input', function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('student_results');
    const hiddenInput = document.getElementById('student_id');
    const feeSelect = document.getElementById('fee_id');

    if (query.length < 2) {
        resultsDiv.style.display = 'none';
        return;
    }

    fetch(`/school/api/students/search?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(students => {
            resultsDiv.innerHTML = '';
            if (students.length === 0) {
                resultsDiv.innerHTML = '<div class="list-group-item text-muted">نتیجه‌ای یافت نشد</div>';
            } else {
                students.forEach(student => {
                    const a = document.createElement('a');
                    a.href = '#';
                    a.className = 'list-group-item list-group-item-action';
                    a.textContent = `${student.first_name} ${student.last_name} (${student.student_code})`;
                    a.addEventListener('click', function(e) {
                        e.preventDefault();
                        hiddenInput.value = student.id;
                        document.getElementById('student_search').value = `${student.first_name} ${student.last_name} (${student.student_code})`;
                        resultsDiv.style.display = 'none';

                        feeSelect.innerHTML = '<option value="">در حال بارگذاری...</option>';
                        feeSelect.disabled = true;
                        fetch(`/school/api/students/${student.id}/fees`)
                            .then(res => res.json())
                            .then(fees => {
                                feeSelect.innerHTML = '<option value="">-- بدون انتخاب --</option>';
                                fees.forEach(fee => {
                                    feeSelect.innerHTML += `<option value="${fee.id}">
                                        ${fee.fee_type} (${fee.month}) - مانده: ${fee.remaining} افغانی
                                    </option>`;
                                });
                                feeSelect.disabled = false;
                            });
                    });
                    resultsDiv.appendChild(a);
                });
            }
            resultsDiv.style.display = 'block';
        });
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('#student_search') && !e.target.closest('#student_results')) {
        document.getElementById('student_results').style.display = 'none';
    }
});
</script>
@endpush
