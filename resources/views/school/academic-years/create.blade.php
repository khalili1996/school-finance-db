@extends('layouts.admin')

@section('title', 'ایجاد سال مالی جدید')

@section('content')
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.academic-years.index') }}">سال‌های مالی</a></li>
            <li class="breadcrumb-item active">ایجاد جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle ms-2"></i> فرم ایجاد سال مالی</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('school.academic-years.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
    <label class="form-label">تاریخ شروع <span class="text-danger">*</span></label>
    <input type="text" name="start_date" class="form-control" placeholder="مثال: 1405/01/01"
           value="{{ old('start_date') }}" required>
</div>
<div class="col-md-4 mb-3">
    <label class="form-label">تاریخ پایان <span class="text-danger">*</span></label>
    <input type="text" name="end_date" class="form-control" placeholder="مثال: 1405/12/29"
           value="{{ old('end_date') }}" required>
</div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">وضعیت فعال</label>
                        <select name="is_active" class="form-select">
                            <option value="0" {{ old('is_active') == '0' ? 'selected' : '' }}>غیرفعال</option>
                            <option value="1" {{ old('is_active') == '1' ? 'selected' : '' }}>فعال</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ثبت</button>
                <a href="{{ route('school.academic-years.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
