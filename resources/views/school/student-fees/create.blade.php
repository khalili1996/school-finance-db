@extends('layouts.admin')
@section('title', 'تعیین شهریه جدید')
@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.student-fees.index') }}">شهریه‌ها</a></li>
            <li class="breadcrumb-item active">تعیین جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-plus-circle ms-2"></i> فرم تعیین شهریه</h5></div>
        <div class="card-body">
            @if ($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div> @endif
            <form action="{{ route('school.student-fees.store') }}" method="POST" id="feeForm">
                @csrf
                <div class="row">
                    {{-- دانش‌آموز --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">دانش‌آموز <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- انتخاب --</option>

                            @foreach($students as $student)
                                <option value="{{ $student->id }}" {{ old('student_id') == $student->id ? 'selected' : '' }}>{{ $student->first_name }} {{ $student->last_name }} ({{ $student->student_code }})</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- نوع هزینه --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نوع هزینه <span class="text-danger">*</span></label>
                        <select name="fee_type_id" class="form-select" required>
                            <option value="">-- انتخاب --</option>
                            @foreach($feeTypes as $feeType)
                                <option value="{{ $feeType->id }}" {{ old('fee_type_id') == $feeType->id ? 'selected' : '' }}>{{ $feeType->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- انتخاب بازهٔ ماه (بدون ارسال خودکار) --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">بازهٔ ماه</label>
                        <select name="month_preset" id="month_preset" class="form-select">
                            <option value="">-- یک ماه خاص (پایین را انتخاب کنید) --</option>
                            <option value="9_regular" {{ old('month_preset') == '9_regular' ? 'selected' : '' }}>۹ ماه درسی (حمل ـ قوس)</option>
                            <option value="3_winter"  {{ old('month_preset') == '3_winter' ? 'selected' : '' }}>۳ ماه زمستان (جدی ـ حوت)</option>
                            <option value="12_all"    {{ old('month_preset') == '12_all' ? 'selected' : '' }}>۱۲ ماه کامل</option>
                            <option value="custom"    {{ old('month_preset') == 'custom' ? 'selected' : '' }}>دلخواه (انتخاب چند ماه)</option>
                        </select>
                    </div>

                    {{-- انتخاب تک‌ماه (همیشه در DOM، ولی با کلاس d-none) --}}
                    <div class="col-md-4 mb-3" id="single_month_box" style="{{ old('month_preset') ? 'display:none;' : '' }}">
                        <label class="form-label">ماه <span class="text-danger">*</span></label>
                        <select name="month_id" class="form-select">
                            <option value="">-- انتخاب ماه --</option>
                            @foreach($months as $month)
                                <option value="{{ $month->id }}" {{ old('month_id') == $month->id ? 'selected' : '' }}>{{ $month->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- چک‌باکس ماه‌های دلخواه (همیشه در DOM، ولی با کلاس d-none) --}}
                    <div class="col-12 mb-3" id="custom_months_box" style="{{ old('month_preset') == 'custom' ? '' : 'display:none;' }}">
                        <label class="form-label">ماه‌های مورد نظر</label>
                        <div class="row">
                            @foreach($months as $month)
                                <div class="col-md-3 form-check">
                                    <input class="form-check-input" type="checkbox" name="month_ids[]" value="{{ $month->id }}"
                                        id="month_{{ $month->id }}"
                                        {{ in_array($month->id, old('month_ids', [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="month_{{ $month->id }}">{{ $month->name }}</label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- مبلغ --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" value="{{ old('amount') }}" min="0" required>
                    </div>

                    {{-- تخفیف --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تخفیف</label>
                        <input type="number" name="discount" class="form-control" value="{{ old('discount', 0) }}" min="0">
                    </div>

                    {{-- توضیحات --}}
                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ذخیره</button>
                <a href="{{ route('school.student-fees.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const presetSelect = document.getElementById('month_preset');
    const singleBox = document.getElementById('single_month_box');
    const customBox = document.getElementById('custom_months_box');

    function toggleMonthInputs() {
        const value = presetSelect.value;
        if (value === 'custom') {
            singleBox.style.display = 'none';
            customBox.style.display = 'block';
        } else if (value === '') {
            singleBox.style.display = 'block';
            customBox.style.display = 'none';
        } else {
            // بازه‌های 3، 9، 12
            singleBox.style.display = 'none';
            customBox.style.display = 'none';
        }
    }

    presetSelect.addEventListener('change', toggleMonthInputs);
    // اجرای اولیه برای تنظیم حالت بارگذاری شده با old()
    toggleMonthInputs();
});
</script>
@endpush
