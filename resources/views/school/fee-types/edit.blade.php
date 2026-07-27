@extends('layouts.admin')

@section('title', 'ویرایش نوع هزینه')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.fee-types.index') }}">انواع هزینه‌ها</a></li>
            <li class="breadcrumb-item active">ویرایش {{ $feeType->name }}</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش: {{ $feeType->name }}</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('school.fee-types.update', $feeType->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نام <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $feeType->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">دسته‌بندی <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">-- انتخاب --</option>
                            <option value="tuition" {{ old('category', $feeType->category) == 'tuition' ? 'selected' : '' }}>شهریه</option>
                            <option value="one_time" {{ old('category', $feeType->category) == 'one_time' ? 'selected' : '' }}>یک‌باره</option>
                            <option value="other" {{ old('category', $feeType->category) == 'other' ? 'selected' : '' }}>سایر</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $feeType->description) }}</textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_optional" value="1" class="form-check-input" id="is_optional" {{ old('is_optional', $feeType->is_optional) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_optional">اختیاری</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" {{ old('is_active', $feeType->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">فعال</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                <a href="{{ route('school.fee-types.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
