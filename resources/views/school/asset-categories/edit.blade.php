@extends('layouts.admin')
@section('title', 'ویرایش دسته‌بندی')

@section('content')
<div class="container-fluid">
    <h4>ویرایش دسته‌بندی</h4>
    <form action="{{ route('school.asset-categories.update', $assetCategory) }}" method="POST">
        @csrf @method('PUT')
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">نام دسته <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $assetCategory->name) }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <button type="submit" class="btn btn-warning">به‌روزرسانی</button>
        <a href="{{ route('school.asset-categories.index') }}" class="btn btn-secondary">انصراف</a>
    </form>
</div>
@endsection
