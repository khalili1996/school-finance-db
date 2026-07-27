@extends('layouts.admin')

@section('title', 'ویرایش کاربر')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-user-edit ms-2"></i> ویرایش کاربر: {{ $user->name }}</h5>
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

            <form method="POST" action="{{ route('school.users.update', $user->id) }}">
                @csrf @method('PUT')
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="{{ old('name', $user->name) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ایمیل <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="{{ old('email', $user->email) }}" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">رمز عبور (در صورت نیاز به تغییر)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">شماره تماس</label>
                        <input type="text" name="phone" class="form-control" value="{{ old('phone', $user->phone) }}">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نقش <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            @foreach($roles as $role)
                                <option value="{{ $role->name }}" {{ $user->hasRole($role->name) ? 'selected' : '' }}>
                                    {{ $role->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                                   {{ $user->is_active ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">کاربر فعال باشد</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning btn-lg">
                    <i class="fas fa-save"></i> به‌روزرسانی
                </button>
                <a href="{{ route('school.users.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
