@extends('layouts.admin')

@section('title', 'ویرایش مصرف')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.expenses.index') }}">مصارف</a></li>
            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش مصرف</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('school.expenses.update', $expense->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')
                <div class="row">
                    {{-- عنوان --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">عنوان <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title', $expense->title) }}" required>
                    </div>

                    {{-- دسته‌بندی --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">دسته‌بندی <span class="text-danger">*</span></label>
                        <select name="expense_category_id" class="form-select" required>
                            <option value="">-- انتخاب کنید --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('expense_category_id', $expense->expense_category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- مبلغ کل --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ کل (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="total_amount" class="form-control" value="{{ old('total_amount', $expense->total_amount) }}" min="1" required>
                    </div>

                    {{-- مبلغ پرداختی --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ پرداختی (افغانی)</label>
                        <input type="number" name="paid_amount" class="form-control" value="{{ old('paid_amount', $expense->paid_amount) }}" min="0">
                    </div>

                    {{-- 📅 تاریخ شمسی --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ <span class="text-danger">*</span></label>
                        <input type="text" name="expense_date" class="form-control"
                               value="{{ old('expense_date', $expense->expense_date) }}"
                               placeholder="مثلاً ۱۴۰۴/۰۱/۱۵" required>
                    </div>

                    {{-- وضعیت --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">وضعیت <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="due" {{ old('status', $expense->status) == 'due' ? 'selected' : '' }}>بدهکار</option>
                            <option value="partially_paid" {{ old('status', $expense->status) == 'partially_paid' ? 'selected' : '' }}>پرداخت جزئی</option>
                            <option value="paid" {{ old('status', $expense->status) == 'paid' ? 'selected' : '' }}>پرداخت کامل</option>
                            <option value="cancelled" {{ old('status', $expense->status) == 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                        </select>
                    </div>

                    {{-- صندوق --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">صندوق پرداخت</label>
                        <select name="cashbox_id" class="form-select">
                            <option value="">-- بدون صندوق --</option>
                            @foreach($cashboxes as $cb)
                                <option value="{{ $cb->id }}" {{ old('cashbox_id', optional($expense->cashboxTransactions()->first())->cashbox_id) == $cb->id ? 'selected' : '' }}>
                                    {{ $cb->name }} ({{ $cb->type === 'bank' ? 'بانکی' : 'نقدی' }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- ماه --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ماه</label>
                        <select name="month_id" class="form-select">
                            <option value="">-- انتخاب کنید --</option>
                            @foreach($months as $month)
                                <option value="{{ $month->id }}" {{ old('month_id', $expense->month_id) == $month->id ? 'selected' : '' }}>{{ $month->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- مقدار/تعداد --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مقدار/تعداد</label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $expense->quantity) }}" min="0">
                    </div>

                    {{-- واحد --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">واحد</label>
                        <input type="text" name="unit" class="form-control" value="{{ old('unit', $expense->unit) }}">
                    </div>

                    {{-- دریافت‌کننده --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">دریافت‌کننده</label>
                        <input type="text" name="received_by" class="form-control" value="{{ old('received_by', $expense->received_by) }}">
                    </div>

                    {{-- مصرف‌کننده --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مصرف‌کننده</label>
                        <input type="text" name="consumer_name" class="form-control" value="{{ old('consumer_name', $expense->consumer_name) }}">
                    </div>

                    {{-- شماره فاکتور --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره فاکتور</label>
                        <input type="text" name="invoice_number" class="form-control" value="{{ old('invoice_number', $expense->invoice_number) }}">
                    </div>

                    {{-- اسکن فاکتور --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">اسکن فاکتور (در صورت تغییر)</label>
                        <input type="file" name="scan_file" class="form-control" accept="image/*,application/pdf">
                        @if($expense->scan_file)
                            <small class="form-text text-muted">فایل فعلی: <a href="{{ asset('storage/'.$expense->scan_file) }}" target="_blank">مشاهده</a></small>
                        @endif
                    </div>

                    {{-- توضیحات --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description', $expense->description) }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                <a href="{{ route('school.expenses.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
