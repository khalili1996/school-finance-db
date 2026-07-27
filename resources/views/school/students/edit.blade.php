@extends('layouts.admin')

@section('title', 'ویرایش دانش‌آموز')

@section('content')
<div class="container-fluid">
    {{-- breadcrumb بدون تغییر --}}
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.students.index') }}">دانش‌آموزان</a></li>
            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش: {{ $student->first_name }} {{ $student->last_name }}</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
            @endif

            <form action="{{ route('school.students.update', $student->id) }}" method="POST" enctype="multipart/form-data">
                @csrf @method('PUT')

                {{-- ========== مشخصات دانش‌آموز ========== --}}
                <h5 class="mb-3 text-primary"><i class="fas fa-id-card ms-2"></i> مشخصات فردی</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">نام <span class="text-danger">*</span></label><input type="text" name="first_name" class="form-control" value="{{ old('first_name', $student->first_name) }}" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">نام خانوادگی <span class="text-danger">*</span></label><input type="text" name="last_name" class="form-control" value="{{ old('last_name', $student->last_name) }}" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">نام پدر <span class="text-danger">*</span></label><input type="text" name="father_name" class="form-control" value="{{ old('father_name', $student->father_name) }}" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">پدرکلان</label><input type="text" name="grandfather_name" class="form-control" value="{{ old('grandfather_name', $student->grandfather_name) }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">شماره تذکره <span class="text-danger">*</span></label><input type="text" name="national_id" class="form-control" value="{{ old('national_id', $student->national_id) }}" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">نمبر اساس</label><input type="text" name="base_number" class="form-control" value="{{ old('base_number', $student->base_number) }}"></div>
                    {{-- 📅 تاریخ تولد شمسی --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ تولد</label>
                        <input type="text" name="birth_date" class="form-control" value="{{ old('birth_date', $student->birth_date) }}" placeholder="مثال: ۱۳۸۰/۰۱/۰۱">
                        <small class="text-muted">اختیاری</small>
                    </div>
                    <div class="col-md-4 mb-3"><label class="form-label">جنسیت <span class="text-danger">*</span></label><select name="gender" class="form-select" required><option value="male" {{ old('gender', $student->gender)=='male'?'selected':'' }}>پسر</option><option value="female" {{ old('gender', $student->gender)=='female'?'selected':'' }}>دختر</option></select></div>
                    <div class="col-md-4 mb-3"><label class="form-label">صنف / کلاس</label><input type="text" name="class" class="form-control" value="{{ old('class', $student->class) }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">سکونت اصلی</label><input type="text" name="original_residence" class="form-control" value="{{ old('original_residence', $student->original_residence) }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">سکونت فعلی / آدرس</label><textarea name="address" class="form-control" rows="2">{{ old('address', $student->address) }}</textarea></div>
                    <div class="col-md-4 mb-3"><label class="form-label">شماره تماس پدر</label><input type="text" name="father_phone" class="form-control" value="{{ old('father_phone', $student->phone) }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">شماره تماس (واتساپ)</label><input type="text" name="whatsapp_phone" class="form-control" value="{{ old('whatsapp_phone', $student->whatsapp_phone) }}"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">وضعیت <span class="text-danger">*</span></label><select name="status" class="form-select" required>
                        <option value="present" {{ old('status', $student->status)=='present'?'selected':'' }}>فعال</option>
                        <option value="blocked" {{ old('status', $student->status)=='blocked'?'selected':'' }}>غیرفعال</option>
                    </select></div>
                    <div class="col-md-4 mb-3"><label class="form-label">وضعیت مالی</label><select name="financial_status" class="form-select">
                        <option value="full" {{ old('financial_status', $student->financial_status)=='full'?'selected':'' }}>شهریه کامل</option>
                        <option value="discount" {{ old('financial_status', $student->financial_status)=='discount'?'selected':'' }}>دارای تخفیف</option>
                        <option value="free" {{ old('financial_status', $student->financial_status)=='free'?'selected':'' }}>رایگان</option>
                    </select></div>
                    <div class="col-md-4 mb-3"><label class="form-label">یتیم</label><select name="is_orphan" class="form-select">
                        <option value="0" {{ old('is_orphan', $student->is_orphan ? '0' : '0')=='0'?'selected':'' }}>خیر</option>
                        <option value="1" {{ old('is_orphan', $student->is_orphan ? '1' : '0')=='1'?'selected':'' }}>بلی</option>
                    </select></div>
                    <div class="col-md-4 mb-3"><label class="form-label">عکس دانش‌آموز</label><input type="file" name="photo" class="form-control" accept="image/*"><small class="text-muted">اختیاری</small></div>
                    {{-- 📅 تاریخ ثبت‌نام شمسی --}}
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ ثبت‌نام</label>
                        <input type="text" class="form-control" value="{{ old('enrollment_date', $student->enrollment_date) }}" readonly>
                    </div>
                </div>

                <hr>

                {{-- ========== مشخصات سرپرست / ولی ========== --}}
                {{-- بدون تغییر --}}
                {{-- ... --}}
                <h5 class="mb-3 text-success"><i class="fas fa-user-shield ms-2"></i> مشخصات سرپرست (ولی)</h5>
                @php
                    $guardian = $student->guardian;
                @endphp
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">نام کامل سرپرست</label><input type="text" name="new_guardian_name" class="form-control" value="{{ old('new_guardian_name', $guardian->full_name ?? '') }}" placeholder="نام پدر یا سرپرست"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">نسبت با دانش‌آموز</label><select name="new_guardian_relation" class="form-select">
                        <option value="">-- انتخاب --</option>
                        <option value="father" {{ old('new_guardian_relation', $guardian->relation ?? '')=='father'?'selected':'' }}>پدر</option>
                        <option value="mother" {{ old('new_guardian_relation', $guardian->relation ?? '')=='mother'?'selected':'' }}>مادر</option>
                        <option value="brother" {{ old('new_guardian_relation', $guardian->relation ?? '')=='brother'?'selected':'' }}>برادر</option>
                        <option value="uncle" {{ old('new_guardian_relation', $guardian->relation ?? '')=='uncle'?'selected':'' }}>کاکا / ماما</option>
                        <option value="other" {{ old('new_guardian_relation', $guardian->relation ?? '')=='other'?'selected':'' }}>سایر</option>
                    </select></div>
                    <div class="col-md-4 mb-3"><label class="form-label">تحصیلات</label><input type="text" name="guardian_education" class="form-control" value="{{ old('guardian_education', $guardian->education ?? '') }}" placeholder="مثلاً لیسانس"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">وظیفه / شغل</label><input type="text" name="guardian_job" class="form-control" value="{{ old('guardian_job', $guardian->job ?? '') }}" placeholder="مثلاً معلم"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">شماره تماس سرپرست</label><input type="text" name="new_guardian_phone" class="form-control" value="{{ old('new_guardian_phone', $guardian->phone ?? '') }}" placeholder="0799123456"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">آدرس سرپرست</label><input type="text" name="new_guardian_address" class="form-control" value="{{ old('new_guardian_address', $guardian->address ?? '') }}" placeholder="آدرس کامل"></div>
                </div>

                <div class="text-start mt-3">
                    <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                    <a href="{{ route('school.students.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
