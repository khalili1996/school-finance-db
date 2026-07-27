@extends('layouts.admin')

@section('title', 'ثبت پیش‌پرداخت جدید')

@section('content')
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.employee-advances.index') }}">پیش‌پرداخت‌ها</a></li>
            <li class="breadcrumb-item active">ثبت جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle ms-2"></i> ثبت پیش‌پرداخت</h5>
        </div>
        <div class="card-body">
            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                </div>
            @endif

            <form action="{{ route('school.employee-advances.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">کارمند <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">-- انتخاب --</option>
                            @foreach($employees as $emp)
                                <option value="{{ $emp->id }}" {{ old('employee_id') == $emp->id ? 'selected' : '' }}>
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
                                <option value="{{ $m->id }}" {{ old('month_id') == $m->id ? 'selected' : '' }}>
                                    {{ $m->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control"
                               value="{{ old('amount') }}" min="1" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ <span class="text-danger">*</span></label>
                        <input type="text" name="advance_date" class="form-control"
                               value="{{ old('advance_date', \App\Helpers\JalaliHelper::todayJalali()) }}" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ذخیره</button>
                <a href="{{ route('school.employee-advances.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
