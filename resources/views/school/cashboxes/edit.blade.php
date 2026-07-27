@extends('layouts.admin')

@section('title', 'ویرایش صندوق')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">✏️ ویرایش صندوق: {{ $cashbox->name }}</h4>
        <a href="{{ route('school.cashboxes.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="{{ route('school.cashboxes.update', $cashbox) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    {{-- نام صندوق --}}
                    <div class="col-md-6">
                        <label for="name" class="form-label">نام صندوق <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                               class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $cashbox->name) }}" required>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- نوع صندوق --}}
                    <div class="col-md-6">
                        <label for="type" class="form-label">نوع صندوق <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select @error('type') is-invalid @enderror" required>
                            <option value="cash" {{ old('type', $cashbox->type) == 'cash' ? 'selected' : '' }}>نقدی</option>
                            <option value="bank" {{ old('type', $cashbox->type) == 'bank' ? 'selected' : '' }}>بانکی</option>
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
                               value="{{ old('initial_balance', $cashbox->initial_balance) }}" min="0" required>
                        @error('initial_balance')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    {{-- وضعیت فعال --}}
                    <div class="col-md-6">
                        <label for="is_active" class="form-label">وضعیت</label>
                        <select name="is_active" id="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $cashbox->is_active) == 1 ? 'selected' : '' }}>فعال</option>
                            <option value="0" {{ old('is_active', $cashbox->is_active) == 0 ? 'selected' : '' }}>غیرفعال</option>
                        </select>
                    </div>

                    {{-- توضیحات --}}
                    <div class="col-12">
                        <label for="notes" class="form-label">توضیحات</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="form-control @error('notes') is-invalid @enderror">{{ old('notes', $cashbox->notes) }}</textarea>
                        @error('notes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- دکمه‌ها --}}
                <div class="mt-4 text-start">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> بروزرسانی صندوق
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
