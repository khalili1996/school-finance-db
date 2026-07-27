@extends('layouts.admin')   {{-- یا layout مخصوص پنل ادمین --}}

@section('title', 'ویرایش مدرسه')

@section('content')
<div class="container-fluid px-4">
    <h4><i class="fas fa-edit ms-2"></i> ویرایش مدرسه: {{ $school->name }}</h4>

    @if ($errors->any())
        <div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
    @endif

    <form action="{{ route('admin.schools.update', $school) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">نام مکتب <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $school->name) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">تلفن</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $settings['phone']) }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">ایمیل عمومی مکتب</label>
                <input type="email" name="contact_email" class="form-control" value="{{ old('contact_email', $settings['email']) }}">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">آدرس</label>
                <textarea name="address" class="form-control" rows="2">{{ old('address', $settings['address']) }}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">لوگوی مکتب</label>
                <input type="file" name="logo" class="form-control" accept="image/*">
                @if($settings['logo'])
                    <div class="mt-2">
                        <img src="{{ asset('storage/' . $settings['logo']) }}" height="60" alt="لوگو">
                    </div>
                @endif
            </div>
        </div>

        <hr>
        <h5>اطلاعات مدیر مدرسه</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">ایمیل مدیر <span class="text-danger">*</span></label>
                <input type="email" name="admin_email" class="form-control"
                       value="{{ old('admin_email', $adminUser->email ?? '') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">رمز عبور جدید (در صورت نیاز به تغییر)</label>
                <input type="password" name="admin_password" class="form-control" minlength="6">
                <small class="text-muted">اگر خالی بگذارید، رمز تغییر نمی‌کند.</small>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> ذخیره تغییرات</button>
            <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary">انصراف</a>
        </div>
    </form>

    <hr>
    <div class="alert alert-warning">
        <strong>غیرفعال‌سازی مدرسه:</strong> با کلیک روی دکمهٔ زیر، مدرسه غیرفعال می‌شود و دیگر در لیست نمایش داده نمی‌شود.
    </div>
    <form action="{{ route('admin.schools.destroy', $school) }}" method="POST" onsubmit="return confirm('آیا از غیرفعال‌سازی این مدرسه اطمینان دارید؟')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger"><i class="fas fa-ban"></i> غیرفعال‌سازی مدرسه</button>
    </form>
</div>
@endsection
