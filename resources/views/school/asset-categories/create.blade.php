@extends('layouts.admin')
@section('title', 'دسته‌بندی جدید')

@section('content')
<div class="container-fluid">
    <h4>دسته‌بندی جدید</h4>
    <form action="{{ route('school.asset-categories.store') }}" method="POST">
        @csrf
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">نام دسته <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required>
                @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
        </div>
        <button type="submit" class="btn btn-success">ذخیره</button>
        <a href="{{ route('school.asset-categories.index') }}" class="btn btn-secondary">انصراف</a>
    </form>
</div>
@endsection
