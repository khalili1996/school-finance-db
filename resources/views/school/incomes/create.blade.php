@extends('layouts.admin')

@section('title', 'ثبت عاید جدید')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.incomes.index') }}">عواید</a></li>
            <li class="breadcrumb-item active">ثبت جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle ms-2"></i> فرم ثبت عاید جدید</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('school.incomes.store') }}" method="POST">
                @csrf
                <div class="row">
                    {{-- عنوان --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">عنوان <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="{{ old('title') }}" required>
                    </div>

                    {{-- دسته‌بندی --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">دسته‌بندی <span class="text-danger">*</span></label>
                        <select name="income_category_id" class="form-select" required>
                            <option value="">-- انتخاب کنید --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('income_category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- مبلغ کل --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ کل (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="total_amount" class="form-control" value="{{ old('total_amount') }}" min="1" required>
                    </div>

                    {{-- مبلغ دریافتی --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ دریافتی (افغانی)</label>
                        <input type="number" name="received_amount" class="form-control" value="{{ old('received_amount', 0) }}" min="0">
                    </div>

                    {{-- 📅 تاریخ شمسی --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ <span class="text-danger">*</span></label>
                        <input type="text" name="income_date" class="form-control"
                               value="{{ old('income_date', \App\Helpers\JalaliHelper::todayJalali()) }}"
                               placeholder="مثلاً ۱۴۰۴/۰۱/۱۵" required>
                    </div>

                    {{-- وضعیت --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">وضعیت <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="due" {{ old('status') == 'due' ? 'selected' : '' }}>طلبکار</option>
                            <option value="partially_received" {{ old('status') == 'partially_received' ? 'selected' : '' }}>دریافت جزئی</option>
                            <option value="received" {{ old('status') == 'received' ? 'selected' : '' }}>دریافت کامل</option>
                            <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>لغو شده</option>
                        </select>
                    </div>

                    {{-- صندوق (اختیاری) --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">صندوق دریافت (در صورت دریافت)</label>
                        <select name="cashbox_id" class="form-select">
                            <option value="">-- بدون صندوق --</option>
                            @foreach($cashboxes as $cb)
                                <option value="{{ $cb->id }}" {{ old('cashbox_id') == $cb->id ? 'selected' : '' }}>
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
                                <option value="{{ $month->id }}" {{ old('month_id') == $month->id ? 'selected' : '' }}>{{ $month->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- منبع --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">منبع</label>
                        <input type="text" name="source" class="form-control" value="{{ old('source') }}" placeholder="مثلاً کمک مردمی">
                    </div>

                    {{-- توضیحات --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" class="form-control" rows="2">{{ old('description') }}</textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ثبت عاید</button>
                <a href="{{ route('school.incomes.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
