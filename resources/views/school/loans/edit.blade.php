@extends('layouts.admin')

@section('title', 'ویرایش قرض‌الحسنه')

@section('content')
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.loans.index') }}">قرض‌الحسنه</a></li>
            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h4 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش قرض‌الحسنه</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('school.loans.update', $loan) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <!-- بخش اول: اطلاعات قرض‌گیرنده -->
                <h5 class="border-bottom pb-2 text-primary">۱. اطلاعات قرض‌گیرنده</h5>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">نام <span class="text-danger">*</span></label>
                        <input type="text" name="borrower_name" class="form-control @error('borrower_name') is-invalid @enderror"
                               value="{{ old('borrower_name', $loan->borrower_name) }}">
                        @error('borrower_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">تخلص</label>
                        <input type="text" name="borrower_last_name" class="form-control @error('borrower_last_name') is-invalid @enderror"
                               value="{{ old('borrower_last_name', $loan->borrower_last_name) }}">
                        @error('borrower_last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">نام پدر</label>
                        <input type="text" name="borrower_father_name" class="form-control @error('borrower_father_name') is-invalid @enderror"
                               value="{{ old('borrower_father_name', $loan->borrower_father_name) }}">
                        @error('borrower_father_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">نام پدرکلان</label>
                        <input type="text" name="borrower_grandfather_name" class="form-control @error('borrower_grandfather_name') is-invalid @enderror"
                               value="{{ old('borrower_grandfather_name', $loan->borrower_grandfather_name) }}">
                        @error('borrower_grandfather_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">شماره تذکره</label>
                        <input type="text" name="borrower_national_id" class="form-control @error('borrower_national_id') is-invalid @enderror"
                               value="{{ old('borrower_national_id', $loan->borrower_national_id) }}">
                        @error('borrower_national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">تاریخ تولد (شمسی)</label>
                        <input type="text" name="borrower_birth_date" class="form-control @error('borrower_birth_date') is-invalid @enderror"
                               value="{{ old('borrower_birth_date', $loan->borrower_birth_date) }}" placeholder="مثلاً ۱۳۷۰/۰۱/۰۱">
                        @error('borrower_birth_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">شماره تماس</label>
                        <input type="text" name="borrower_phone" class="form-control @error('borrower_phone') is-invalid @enderror"
                               value="{{ old('borrower_phone', $loan->borrower_phone) }}">
                        @error('borrower_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">شماره تماس اقارب</label>
                        <input type="text" name="borrower_relative_phone" class="form-control @error('borrower_relative_phone') is-invalid @enderror"
                               value="{{ old('borrower_relative_phone', $loan->borrower_relative_phone) }}">
                        @error('borrower_relative_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <h6 class="text-secondary">سکونت اصلی</h6>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label class="form-label">ولایت</label>
                        <input type="text" name="borrower_original_province" class="form-control @error('borrower_original_province') is-invalid @enderror"
                               value="{{ old('borrower_original_province', $loan->borrower_original_province) }}">
                        @error('borrower_original_province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">ولسوالی</label>
                        <input type="text" name="borrower_original_district" class="form-control @error('borrower_original_district') is-invalid @enderror"
                               value="{{ old('borrower_original_district', $loan->borrower_original_district) }}">
                        @error('borrower_original_district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">قریه</label>
                        <input type="text" name="borrower_original_village" class="form-control @error('borrower_original_village') is-invalid @enderror"
                               value="{{ old('borrower_original_village', $loan->borrower_original_village) }}">
                        @error('borrower_original_village')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">آدرس فعلی کامل</label>
                        <textarea name="borrower_address" class="form-control @error('borrower_address') is-invalid @enderror" rows="2">{{ old('borrower_address', $loan->borrower_address) }}</textarea>
                        @error('borrower_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">عکس قرض‌گیرنده</label>
                        <input type="file" name="borrower_photo" class="form-control @error('borrower_photo') is-invalid @enderror" accept="image/*">
                        @error('borrower_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($loan->borrower_photo)
                            <img src="{{ asset('storage/'.$loan->borrower_photo) }}" height="60" class="mt-1">
                        @endif
                    </div>
                </div>

                <!-- بخش دوم: اطلاعات ضامن -->
                <h5 class="border-bottom pb-2 text-warning mt-4">۲. اطلاعات ضامن</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام ضامن <span class="text-danger">*</span></label>
                        <input type="text" name="guarantor_name" class="form-control @error('guarantor_name') is-invalid @enderror"
                               value="{{ old('guarantor_name', $loan->guarantor_name) }}">
                        @error('guarantor_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام پدر</label>
                        <input type="text" name="guarantor_father_name" class="form-control @error('guarantor_father_name') is-invalid @enderror"
                               value="{{ old('guarantor_father_name', $loan->guarantor_father_name) }}">
                        @error('guarantor_father_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره تذکره</label>
                        <input type="text" name="guarantor_national_id" class="form-control @error('guarantor_national_id') is-invalid @enderror"
                               value="{{ old('guarantor_national_id', $loan->guarantor_national_id) }}">
                        @error('guarantor_national_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره تماس</label>
                        <input type="text" name="guarantor_phone" class="form-control @error('guarantor_phone') is-invalid @enderror"
                               value="{{ old('guarantor_phone', $loan->guarantor_phone) }}">
                        @error('guarantor_phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-8 mb-3">
                        <label class="form-label">آدرس</label>
                        <textarea name="guarantor_address" class="form-control @error('guarantor_address') is-invalid @enderror" rows="1">{{ old('guarantor_address', $loan->guarantor_address) }}</textarea>
                        @error('guarantor_address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">عکس ضامن</label>
                        <input type="file" name="guarantor_photo" class="form-control @error('guarantor_photo') is-invalid @enderror" accept="image/*">
                        @error('guarantor_photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        @if($loan->guarantor_photo)
                            <img src="{{ asset('storage/'.$loan->guarantor_photo) }}" height="60" class="mt-1">
                        @endif
                    </div>
                </div>

                <!-- بخش سوم: جزئیات وام -->
                <h5 class="border-bottom pb-2 text-success mt-4">۳. جزئیات وام</h5>
                @php
                    $hasPaidInstallments = $loan->installments()->where('status', 'paid')->exists();
                @endphp

                @if($hasPaidInstallments)
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        اقساطی پرداخت شده‌اند؛ بنابراین <strong>فقط اطلاعات شخصی و ضامن</strong> قابل ویرایش است.
                    </div>
                @else
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> تغییر جزئیات مالی، اقساط قبلی را حذف و اقساط جدید ایجاد می‌کند.
                    </div>
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام مرجع قرض‌الحسنه</label>
                        <input type="text" name="loan_provider" class="form-control @error('loan_provider') is-invalid @enderror"
                               value="{{ old('loan_provider', $loan->loan_provider) }}">
                        @error('loan_provider')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">مبلغ قرضه (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="amount" class="form-control @error('amount') is-invalid @enderror"
                               value="{{ old('amount', $loan->amount) }}" min="1"
                               @if($hasPaidInstallments) readonly @endif>
                        @error('amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label">مدت (ماه) <span class="text-danger">*</span></label>
                        <input type="number" name="duration_months" id="duration_months" class="form-control @error('duration_months') is-invalid @enderror"
                               value="{{ old('duration_months', $loan->duration_months) }}" min="1"
                               @if($hasPaidInstallments) readonly @endif>
                        @error('duration_months')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-3 mb-3">
                        <label class="form-label">مبلغ هر قسط <span class="text-danger">*</span></label>
                        <input type="number" name="installment_amount" id="installment_amount" class="form-control @error('installment_amount') is-invalid @enderror"
                               value="{{ old('installment_amount', $loan->installment_amount) }}" min="1"
                               @if($hasPaidInstallments) readonly @endif>
                        @error('installment_amount')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-2 mb-3">
                        <label class="form-label">تاریخ شروع (شمسی)</label>
                        <input type="text" name="start_date" class="form-control @error('start_date') is-invalid @enderror"
                               value="{{ old('start_date', $loan->start_date) }}"
                               @if($hasPaidInstallments) readonly @endif>
                        @error('start_date')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    {{-- فیلد صندوق کاملاً حذف شد --}}
                </div>

                <div class="row">
                    <div class="col-md-12 mb-3">
                        <label class="form-label">ملاحظات</label>
                        <textarea name="notes" class="form-control @error('notes') is-invalid @enderror" rows="2">{{ old('notes', $loan->notes) }}</textarea>
                        @error('notes')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                </div>

                <div class="text-end mt-4">
                    <button type="submit" class="btn btn-warning btn-lg px-5"><i class="fas fa-save"></i> به‌روزرسانی</button>
                    <a href="{{ route('school.loans.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// محاسبه خودکار قسط (در صورت قابل ویرایش بودن)
document.addEventListener('DOMContentLoaded', function() {
    const amountEl = document.getElementById('amount');
    const durationEl = document.getElementById('duration_months');
    const installmentEl = document.getElementById('installment_amount');

    function updateInstallment() {
        if (!amountEl || !durationEl || !installmentEl) return;
        if (amountEl.hasAttribute('readonly') || durationEl.hasAttribute('readonly')) return;
        let amount = parseFloat(amountEl.value) || 0;
        let months = parseInt(durationEl.value) || 1;
        if (amount > 0 && months > 0) {
            installmentEl.value = Math.ceil(amount / months);
        }
    }

    if (amountEl && durationEl) {
        amountEl.addEventListener('input', updateInstallment);
        durationEl.addEventListener('input', updateInstallment);
    }
});
</script>
@endpush
