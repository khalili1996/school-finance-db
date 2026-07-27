@extends('layouts.admin')

@section('title', 'ثبت ولی جدید')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.guardians.index') }}">اولیا</a></li>
            <li class="breadcrumb-item active">ثبت جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-user-plus ms-2"></i> فرم ثبت ولی جدید</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('school.guardians.store') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                        <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نسبت</label>
                        <select name="relation" class="form-select">
                            <option value="">-- انتخاب --</option>
                            <option value="father" {{ old('relation') == 'father' ? 'selected' : '' }}>پدر</option>
                            <option value="mother" {{ old('relation') == 'mother' ? 'selected' : '' }}>مادر</option>
                            <option value="brother" {{ old('relation') == 'brother' ? 'selected' : '' }}>برادر</option>
                            <option value="uncle" {{ old('relation') == 'uncle' ? 'selected' : '' }}>کاکا / ماما</option>
                            <option value="other" {{ old('relation') == 'other' ? 'selected' : '' }}>سایر</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">شماره تماس</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">شماره تماس دوم</label>
                        <input type="text" name="secondary_phone" class="form-control" value="{{ old('secondary_phone') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">شماره تذکره (کد ملی)</label>
                        <input type="text" name="national_id" class="form-control" value="{{ old('national_id') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">شغل</label>
                        <input type="text" name="job" class="form-control" value="{{ old('job') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">تحصیلات</label>
                        <input type="text" name="education" class="form-control" value="{{ old('education') }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">وضعیت</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', '1') == '1' ? 'selected' : '' }}>فعال</option>
                            <option value="0" {{ old('is_active', '1') == '0' ? 'selected' : '' }}>غیرفعال</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">آدرس</label>
                        <textarea name="address" class="form-control" rows="2">{{ old('address') }}</textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ذخیره</button>
                <a href="{{ route('school.guardians.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
