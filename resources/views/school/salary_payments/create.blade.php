@extends('layouts.admin')

@section('title', 'پرداخت معاش')

@section('content')
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.salaries.index') }}">معاشات</a></li>
            <li class="breadcrumb-item active">پرداخت</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-money-bill-wave ms-2"></i> پرداخت معاش</h5>
        </div>
        <div class="card-body">
            {{-- خلاصه معاش --}}
            <div class="alert alert-secondary">
                <strong>کارمند:</strong> {{ $salary->employee->first_name }} {{ $salary->employee->last_name }}<br>
                <strong>ماه:</strong> {{ $salary->month->name ?? '—' }}<br>
                <strong>مبلغ کل معاش:</strong> {{ number_format($salary->total_amount) }} ؋<br>
                <strong>پرداخت شده تاکنون:</strong> {{ number_format($salary->paid_amount) }} ؋<br>
                <strong>مانده قابل پرداخت:</strong> {{ number_format($remaining) }} ؋
            </div>

            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('school.salary-payments.store') }}" method="POST">
                @csrf
                <input type="hidden" name="salary_id" value="{{ $salary->id }}">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ پرداختی (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" value="{{ old('amount', $remaining) }}" min="1" max="{{ $remaining }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ پرداخت <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="{{ old('payment_date', now()->toDateString()) }}" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">صندوق <span class="text-danger">*</span></label>
                        <select name="cashbox_id" class="form-select" required>
                            <option value="">-- انتخاب کنید --</option>
                            @foreach($cashboxes as $cb)
                                <option value="{{ $cb->id }}" {{ old('cashbox_id') == $cb->id ? 'selected' : '' }}>
                                    {{ $cb->name }} ({{ $cb->type === 'bank' ? 'بانکی' : 'نقدی' }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره رسید</label>
                        <input type="text" name="receipt_number" class="form-control" value="{{ old('receipt_number') }}">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-check-circle"></i> ثبت پرداخت</button>
                <a href="{{ route('school.salaries.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
