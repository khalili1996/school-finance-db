@extends('layouts.admin')

@section('title', 'ویرایش کارمند')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.employees.index') }}">کارمندان</a></li>
            <li class="breadcrumb-item active">ویرایش {{ $employee->first_name }} {{ $employee->last_name }}</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش: {{ $employee->first_name }} {{ $employee->last_name }}</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('school.employees.update', $employee->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                <h5 class="mb-3 text-primary">اطلاعات فردی</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label>نام <span class="text-danger">*</span></label><input type="text" name="first_name" class="form-control" value="{{ old('first_name', $employee->first_name) }}" required></div>
                    <div class="col-md-4 mb-3"><label>نام خانوادگی <span class="text-danger">*</span></label><input type="text" name="last_name" class="form-control" value="{{ old('last_name', $employee->last_name) }}" required></div>
                    <div class="col-md-4 mb-3"><label>نام پدر <span class="text-danger">*</span></label><input type="text" name="father_name" class="form-control" value="{{ old('father_name', $employee->father_name) }}" required></div>
                    <div class="col-md-4 mb-3"><label>پدرکلان</label><input type="text" name="grandfather_name" class="form-control" value="{{ old('grandfather_name', $employee->grandfather_name) }}"></div>
                    <div class="col-md-4 mb-3"><label>شماره تذکره</label><input type="text" name="national_id" class="form-control" value="{{ old('national_id', $employee->national_id) }}"></div>

                    {{-- 📅 تاریخ تولد شمسی --}}
                    <div class="col-md-4 mb-3">
                        <label>تاریخ تولد</label>
                        <input type="text" name="birth_date" class="form-control" value="{{ old('birth_date', $employee->birth_date) }}" placeholder="مثلاً ۱۳۷۰/۰۱/۰۱">
                    </div>

                    <div class="col-md-4 mb-3"><label>جنسیت <span class="text-danger">*</span></label><select name="gender" class="form-select" required><option value="male" {{ old('gender', $employee->gender)=='male'?'selected':'' }}>مذکر</option><option value="female" {{ old('gender', $employee->gender)=='female'?'selected':'' }}>اناث</option></select></div>
                    <div class="col-md-4 mb-3"><label>شماره تماس</label><input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}"></div>
                    <div class="col-md-4 mb-3"><label>شماره تماس دوم</label><input type="text" name="secondary_phone" class="form-control" value="{{ old('secondary_phone', $employee->secondary_phone) }}"></div>
                    <div class="col-12 mb-3"><label>آدرس</label><textarea name="address" class="form-control" rows="2">{{ old('address', $employee->address) }}</textarea></div>
                    <div class="col-md-4 mb-3"><label>درجه تحصیل</label><input type="text" name="education_level" class="form-control" value="{{ old('education_level', $employee->education_level) }}"></div>
                    <div class="col-md-4 mb-3"><label>رشته تحصیلی</label><input type="text" name="field_of_study" class="form-control" value="{{ old('field_of_study', $employee->field_of_study) }}"></div>
                    <div class="col-md-4 mb-3"><label>عکس</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
                </div>

                <h5 class="mb-3 text-primary">اطلاعات استخدامی</h5>
                <div class="row">
                    {{-- سمت با قابلیت افزودن سریع --}}
                    <div class="col-md-4 mb-3">
                        <label>سمت <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="employee_role_id" class="form-select" required>
                                <option value="">-- انتخاب --</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}" {{ old('employee_role_id', $employee->employee_role_id) == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                                @endforeach
                            </select>
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#quickRoleModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3"><label>بخش</label><input type="text" name="department" class="form-control" value="{{ old('department', $employee->department) }}"></div>

                    {{-- 📅 تاریخ استخدام شمسی --}}
                    <div class="col-md-4 mb-3">
                        <label>تاریخ استخدام</label>
                        <input type="text" name="hire_date" class="form-control" value="{{ old('hire_date', $employee->hire_date) }}" placeholder="مثلاً ۱۴۰۲/۰۶/۰۱">
                    </div>

                    <div class="col-md-4 mb-3"><label>نوع قرارداد <span class="text-danger">*</span></label><select name="contract_type" class="form-select" required><option value="permanent" {{ old('contract_type', $employee->contract_type)=='permanent'?'selected':'' }}>دایمی</option><option value="temporary" {{ old('contract_type', $employee->contract_type)=='temporary'?'selected':'' }}>موقت</option></select></div>
                    <div class="col-md-4 mb-3"><label>معاش پایه</label><input type="number" name="base_salary" class="form-control" value="{{ old('base_salary', $employee->base_salary) }}" min="0"></div>
                    <div class="col-md-4 mb-3"><label>وضعیت <span class="text-danger">*</span></label><select name="status" class="form-select" required><option value="active" {{ old('status', $employee->status)=='active'?'selected':'' }}>فعال</option><option value="inactive" {{ old('status', $employee->status)=='inactive'?'selected':'' }}>غیرفعال</option></select></div>
                </div>

                {{-- امتیازات --}}
                <h5 class="mb-3 text-primary">امتیازات</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label>امتیاز سمت</label><input type="number" name="position_points" class="form-control" value="{{ old('position_points', $employee->position_points) }}" min="0"></div>
                    <div class="col-md-4 mb-3"><label>امتیاز سابقه کاری</label><input type="number" name="experience_points" class="form-control" value="{{ old('experience_points', $employee->experience_points) }}" min="0"></div>
                    <div class="col-md-4 mb-3"><label>امتیاز درجه تحصیل</label><input type="number" name="education_points" class="form-control" value="{{ old('education_points', $employee->education_points) }}" min="0"></div>
                </div>

                <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                <a href="{{ route('school.employees.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>

{{-- Modal ایجاد سمت سریع (بدون تغییر) --}}
{{-- ... --}}
