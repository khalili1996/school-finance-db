@extends('layouts.admin')
@section('title', 'ویرایش پرداخت معاش')

@section('content')
<div class="container-fluid">
    <h4 class="mb-3">ویرایش پرداخت معاش</h4>

    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('school.salary-payments.update', $salaryPayment->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">کارمند</label>
                <input type="text" class="form-control" value="{{ $salaryPayment->employee->full_name ?? '' }}" disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">ماه</label>
                <input type="text" class="form-control" value="{{ $salaryPayment->salary->month->name ?? '' }}" disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">مبلغ (افغانی) <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control" value="{{ old('amount', $salaryPayment->amount) }}" min="1" required>
                <small class="text-muted">حداکثر مجاز: {{ number_format($remaining) }} ؋</small>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">تاریخ</label>
                <input type="text" name="payment_date" class="form-control" value="{{ old('payment_date', $salaryPayment->payment_date) }}" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">صندوق <span class="text-danger">*</span></label>
                <select name="cashbox_id" class="form-select" required>
                    @foreach($cashboxes as $cb)
                        <option value="{{ $cb->id }}" {{ old('cashbox_id', $salaryPayment->cashbox_id) == $cb->id ? 'selected' : '' }}>{{ $cb->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">شماره رسید</label>
                <input type="text" name="receipt_number" class="form-control" value="{{ old('receipt_number', $salaryPayment->receipt_number) }}">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">توضیحات</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $salaryPayment->notes) }}</textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">به‌روزرسانی</button>
        <a href="{{ route('school.salaries.index') }}" class="btn btn-secondary">انصراف</a>
    </form>
</div>
@endsection

