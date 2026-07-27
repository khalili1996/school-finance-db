@extends('layouts.admin')

@section('title', 'ایجاد صندوق جدید')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">➕ ایجاد صندوق جدید</h4>
        <a href="{{ route('school.cashboxes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('school.cashboxes.store') }}" method="POST">
                @csrf

                <div class="row g-3">
                    {{-- نام صندوق --}}
                    <div class="col-md-6">
                        <label for="name" class="form-label">نام صندوق <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name') }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- نوع صندوق --}}
                    <div class="col-md-6">
                        <label for="type" class="form-label">نوع صندوق <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="">-- انتخاب کنید --</option>
                            <option value="cash" {{ old('type') == 'cash' ? 'selected' : '' }}>نقدی</option>
                            <option value="bank" {{ old('type') == 'bank' ? 'selected' : '' }}>بانکی</option>
                        </select>
                        @error('type')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- موجودی اولیه --}}
                    <div class="col-md-6">
                        <label for="initial_balance" class="form-label">موجودی اولیه (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="initial_balance" id="initial_balance"
                               class="form-control @error('initial_balance') is-invalid @enderror"
                               value="{{ old('initial_balance', 0) }}" min="0" required>
                        @error('initial_balance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- توضیحات --}}
                    <div class="col-12">
                        <label for="notes" class="form-label">توضیحات</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="form-control @error('notes') is-invalid @enderror">{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- دکمه‌ها --}}
                <div class="mt-4 text-start">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> ثبت صندوق
                    </button>
                    <a href="{{ route('school.cashboxes.index') }}" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times me-1"></i> انصراف
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
