<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>ایجاد مکتب جدید</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">پنل مرکزی</a></li>
                <li class="breadcrumb-item"><a href="{{ route('admin.schools.index') }}">مکاتب</a></li>
                <li class="breadcrumb-item active">ایجاد جدید</li>
            </ol>
        </nav>

        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-plus-circle ms-2"></i> فرم ایجاد مکتب جدید</h5>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.schools.store') }}">
                    @csrf

                    <fieldset class="mb-4">
                        <legend class="h5 text-primary">اطلاعات مکتب</legend>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نام مکتب <span class="text-danger">*</span></label>
                                <input type="text" name="school_name" class="form-control" value="{{ old('school_name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">کد یکتا <span class="text-danger">*</span></label>
                                <input type="text" name="school_code" class="form-control" value="{{ old('school_code') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">شماره تماس</label>
                                <input type="text" name="school_phone" class="form-control" value="{{ old('school_phone') }}">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ایمیل</label>
                                <input type="email" name="school_email" class="form-control" value="{{ old('school_email') }}">
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">آدرس</label>
                                <textarea name="school_address" class="form-control" rows="2">{{ old('school_address') }}</textarea>
                            </div>
                        </div>
                    </fieldset>

                    <fieldset class="mb-4">
                        <legend class="h5 text-primary">مدیر مکتب</legend>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نام کامل مدیر <span class="text-danger">*</span></label>
                                <input type="text" name="admin_name" class="form-control" value="{{ old('admin_name') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">ایمیل مدیر <span class="text-danger">*</span></label>
                                <input type="email" name="admin_email" class="form-control" value="{{ old('admin_email') }}" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">رمز عبور <span class="text-danger">*</span></label>
                                <input type="password" name="admin_password" class="form-control" required>
                            </div>
                        </div>
                    </fieldset>

                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-save"></i> ایجاد مکتب و ثبت مدیر
                    </button>
                    <a href="{{ route('admin.schools.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
