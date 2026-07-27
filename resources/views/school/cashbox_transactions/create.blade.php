@extends('layouts.admin')

@section('title', 'ثبت تراکنش جدید')

@push('styles')
<style>
    #transfer_fields { display: none; }
</style>
@endpush

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">💰 ثبت تراکنش صندوق</h4>
        <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> بازگشت
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('school.cashbox-transactions.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    {{-- نوع تراکنش --}}
                    <div class="col-md-6">
                        <label for="type" class="form-label">نوع تراکنش <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">-- انتخاب کنید --</option>
                            <option value="deposit" {{ old('type') === 'deposit' ? 'selected' : '' }}>واریز (افزایش موجودی)</option>
                            <option value="withdrawal" {{ old('type') === 'withdrawal' ? 'selected' : '' }}>برداشت (کاهش موجودی)</option>
                            <option value="transfer" {{ old('type') === 'transfer' ? 'selected' : '' }}>انتقال به صندوق دیگر</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- صندوق --}}
                    <div class="col-md-6">
                        <label for="cashbox_id" class="form-label">صندوق <span class="text-danger">*</span></label>
                        <select name="cashbox_id" id="cashbox_id" class="form-select @error('cashbox_id') is-invalid @enderror" required>
                            <option value="">-- انتخاب کنید --</option>
                            @foreach($cashboxes as $cb)
                                <option value="{{ $cb->id }}" {{ (old('cashbox_id', request('cashbox_id')) == $cb->id) ? 'selected' : '' }}>
                                    {{ $cb->name }} ({{ $cb->type === 'bank' ? 'بانکی' : 'نقدی' }})
                                </option>
                            @endforeach
                        </select>
                        @error('cashbox_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- صندوق مقصد (فقط برای انتقال) --}}
                    <div class="col-md-6" id="transfer_fields">
                        <label for="to_cashbox_id" class="form-label">صندوق مقصد <span class="text-danger">*</span></label>
                        <select name="to_cashbox_id" id="to_cashbox_id" class="form-select @error('to_cashbox_id') is-invalid @enderror">
                            <option value="">-- انتخاب کنید --</option>
                            @foreach($cashboxes as $cb)
                                <option value="{{ $cb->id }}" {{ old('to_cashbox_id') == $cb->id ? 'selected' : '' }}>
                                    {{ $cb->name }} ({{ $cb->type === 'bank' ? 'بانکی' : 'نقدی' }})
                                </option>
                            @endforeach
                        </select>
                        @error('to_cashbox_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- مبلغ --}}
                    <div class="col-md-6">
                        <label for="amount" class="form-label">مبلغ (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="amount"
                               class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount') }}" min="1" required>
                        @error('amount')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- تاریخ تراکنش --}}
                    <div class="col-md-6">
                        <label for="transaction_date" class="form-label">تاریخ <span class="text-danger">*</span></label>
                        <input type="date" name="transaction_date" id="transaction_date"
                               class="form-control @error('transaction_date') is-invalid @enderror"
                               value="{{ old('transaction_date', date('Y-m-d')) }}" required>
                        @error('transaction_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- شرح --}}
                    <div class="col-12">
                        <label for="description" class="form-label">شرح</label>
                        <textarea name="description" id="description" rows="2"
                                  class="form-control @error('description') is-invalid @enderror">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 text-start">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> ثبت تراکنش
                    </button>
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times me-1"></i> انصراف
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const typeSelect = document.getElementById('type');
    const transferFields = document.getElementById('transfer_fields');
    const toCashboxSelect = document.getElementById('to_cashbox_id');

    function toggleTransfer() {
        if (typeSelect.value === 'transfer') {
            transferFields.style.display = 'block';
            toCashboxSelect.required = true;
        } else {
            transferFields.style.display = 'none';
            toCashboxSelect.required = false;
        }
    }

    typeSelect.addEventListener('change', toggleTransfer);
    // اجرای اولیه برای حالت ویرایش/old
    document.addEventListener('DOMContentLoaded', toggleTransfer);
</script>
@endpush
