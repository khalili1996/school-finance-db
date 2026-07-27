@extends('layouts.admin')

@section('title', 'ویرایش پیش‌پرداخت')

@section('content')
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.employee-advances.index') }}">پیش‌پرداخت‌ها</a></li>
            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش پیش‌پرداخت</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('school.employee-advances.update', $employeeAdvance) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">کارمند <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id', $employeeAdvance->employee_id) == $emp->id ? 'selected' : '' }}>
                                    {{ $emp->first_name }} {{ $emp->last_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">ماه (از معاش کدام ماه کسر شود) <span class="text-danger">*</span></label>
                        <select name="month_id" class="form-select" required>
                            <option value="">-- انتخاب --</option>
                            @foreach($months as $m)
                                <option value="{{ $m->id }}" {{ old('month_id', $employeeAdvance->month_id) == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control"
                               value="{{ old('amount', $employeeAdvance->amount) }}" min="1" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ <span class="text-danger">*</span></label>
                        <input type="text" name="advance_date" class="form-control"
                               value="{{ old('advance_date', $employeeAdvance->advance_date) }}" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $employeeAdvance->notes) }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                <a href="{{ route('school.employee-advances.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
