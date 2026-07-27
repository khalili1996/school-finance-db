@extends('layouts.admin')

@section('title', 'ویرایش پرداخت قسط')

@section('content')
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.loans.index') }}">قرض‌الحسنه</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.loans.installments', $installment->loan) }}">اقساط</a></li>
            <li class="breadcrumb-item active">ویرایش پرداخت</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش پرداخت قسط</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('school.installments.update', $installment) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ پرداختی (حداکثر {{ number_format($installment->amount) }})</label>
                        <input type="number" name="paid_amount" class="form-control @error('paid_amount') is-invalid @enderror"
                               value="{{ old('paid_amount', $installment->paid_amount) }}" min="1" max="{{ $installment->amount }}" required>
                        @error('paid_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ پرداخت (شمسی)</label>
                        <input type="text" name="paid_date" class="form-control @error('paid_date') is-invalid @enderror"
                               value="{{ old('paid_date', $installment->paid_date) }}" required>
                        @error('paid_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">صندوق</label>
                        <select name="cashbox_id" class="form-select @error('cashbox_id') is-invalid @enderror" required>
                            <option value="">-- انتخاب --</option>
                            @foreach($cashboxes as $cb)
                                <option value="{{ $cb->id }}" {{ (old('cashbox_id', $installment->cashbox_id) == $cb->id) ? 'selected' : '' }}>
                                    {{ $cb->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('cashbox_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">یادداشت</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $installment->notes) }}</textarea>
                </div>
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> ذخیره تغییرات</button>
                <a href="{{ route('school.loans.installments', $installment->loan) }}" class="btn btn-secondary">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
