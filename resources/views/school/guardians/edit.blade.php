@extends('layouts.admin')

@section('title', 'ویرایش ولی')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.guardians.index') }}">اولیا</a></li>
            <li class="breadcrumb-item active">ویرایش {{ $guardian->full_name }}</li>
        </ol>
    </nav>

    <div class="row">
        {{-- فرم ویرایش --}}
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش: {{ $guardian->full_name }}</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                    @endif

                    <form action="{{ route('school.guardians.update', $guardian->id) }}" method="POST">
                        @csrf @method('PUT')
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" value="{{ old('full_name', $guardian->full_name) }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نسبت</label>
                                <select name="relation" class="form-select">
                                    <option value="">-- انتخاب --</option>
                                    <option value="father" {{ old('relation', $guardian->relation) == 'father' ? 'selected' : '' }}>پدر</option>
                                    <option value="mother" {{ old('relation', $guardian->relation) == 'mother' ? 'selected' : '' }}>مادر</option>
                                    <option value="brother" {{ old('relation', $guardian->relation) == 'brother' ? 'selected' : '' }}>برادر</option>
                                    <option value="uncle" {{ old('relation', $guardian->relation) == 'uncle' ? 'selected' : '' }}>کاکا / ماما</option>
                                    <option value="other" {{ old('relation', $guardian->relation) == 'other' ? 'selected' : '' }}>سایر</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">شماره تماس</label>
                                <input type="text" name="phone" class="form-control" value="{{ old('phone', $guardian->phone) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">شماره تماس دوم</label>
                                <input type="text" name="secondary_phone" class="form-control" value="{{ old('secondary_phone', $guardian->secondary_phone) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">کد ملی</label>
                                <input type="text" name="national_id" class="form-control" value="{{ old('national_id', $guardian->national_id) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">تحصیلات</label>
                                <input type="text" name="education" class="form-control" value="{{ old('education', $guardian->education) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">شغل</label>
                                <input type="text" name="job" class="form-control" value="{{ old('job', $guardian->job) }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">وضعیت</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" {{ old('is_active', $guardian->is_active) == '1' ? 'selected' : '' }}>فعال</option>
                                    <option value="0" {{ old('is_active', $guardian->is_active) == '0' ? 'selected' : '' }}>غیرفعال</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">آدرس</label>
                                <textarea name="address" class="form-control" rows="2">{{ old('address', $guardian->address) }}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                        <a href="{{ route('school.guardians.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
                    </form>
                </div>
            </div>
        </div>

        {{-- دانش‌آموزان مرتبط --}}
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users ms-2"></i> دانش‌آموزان مرتبط</h5>
                </div>
                <div class="card-body p-0">
                    @if($guardian->students->isNotEmpty())
                        <ul class="list-group list-group-flush">
                            @foreach($guardian->students as $student)
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span>{{ $student->first_name }} {{ $student->last_name }}</span>
                                    <span class="badge bg-primary">{{ $student->class ?? '—' }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted p-3 mb-0">دانش‌آموزی به این ولی متصل نیست.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
