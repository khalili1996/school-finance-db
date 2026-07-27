@extends('layouts.admin')
@section('title', 'ویرایش شهریه')
@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.student-fees.index') }}">شهریه‌ها</a></li>
            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark"><h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش شهریه</h5></div>
        <div class="card-body">
            @if ($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div> @endif
            <form action="{{ route('school.student-fees.update', $studentFee->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">دانش‌آموز <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id', $studentFee->student_id) == $student->id ? 'selected' : '' }}>{{ $student->first_name }} {{ $student->last_name }} ({{ $student->student_code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نوع هزینه <span class="text-danger">*</span></label>
                        <select name="fee_type_id" class="form-select" required>
                            @foreach($feeTypes as $feeType)
                                <option value="{{ $feeType->id }}" {{ old('fee_type_id', $studentFee->fee_type_id) == $feeType->id ? 'selected' : '' }}>{{ $feeType->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ماه (اختیاری)</label>
                        <select name="month_id" class="form-select">
                            <option value="">-- بدون ماه (کلی) --</option>
                            @foreach($months as $month)
                                <option value="{{ $month->id }}" {{ old('month_id', $studentFee->month_id) == $month->id ? 'selected' : '' }}>{{ $month->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3"><label class="form-label">مبلغ <span class="text-danger">*</span></label><input type="number" name="amount" class="form-control" value="{{ old('amount', $studentFee->amount) }}" min="0" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">تخفیف</label><input type="number" name="discount" class="form-control" value="{{ old('discount', $studentFee->discount) }}" min="0"></div>
                    <div class="col-12 mb-3"><label class="form-label">توضیحات</label><textarea name="notes" class="form-control" rows="2">{{ old('notes', $studentFee->notes) }}</textarea></div>
                </div>
                <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                <a href="{{ route('school.student-fees.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
