@extends('layouts.admin')

@section('title', 'ویرایش تجهیز')

@section('content')
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.assets.index') }}">تجهیزات</a></li>
            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark"><h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش تجهیز</h5></div>
        <div class="card-body">
            @if($errors->any())<div class="alert alert-danger"><ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif

            <form action="{{ route('school.assets.update', $asset->id) }}" method="POST">
                @csrf @method('PUT')
                <div class="row">
                    {{-- کد اموال (دستی) --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">کد اموال <span class="text-danger">*</span></label>
                        <input type="text" name="asset_code" class="form-control @error('asset_code') is-invalid @enderror"
                               value="{{ old('asset_code', $asset->asset_code) }}" maxlength="30" required>
                        @error('asset_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- دسته‌بندی --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">دسته‌بندی <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- انتخاب --</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" {{ old('category_id', $asset->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- تعداد --}}
                    <div class="col-md-2 mb-3">
                        <label class="form-label">تعداد <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" value="{{ old('quantity', $asset->quantity) }}" min="1" required>
                    </div>

                    {{-- قیمت واحد --}}
                    <div class="col-md-2 mb-3">
                        <label class="form-label">قیمت واحد (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="unit_price" class="form-control" value="{{ old('unit_price', $asset->unit_price) }}" min="0" required>
                    </div>
                </div>

                <div class="row">
                    {{-- شرح --}}
                    <div class="col-md-6 mb-3">
                        <label class="form-label">شرح تجهیزات <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" value="{{ old('description', $asset->description) }}" required>
                    </div>

                    {{-- تحویل‌گیرنده --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">تحویل‌گیرنده / موقعیت</label>
                        <input type="text" name="custodian" class="form-control" value="{{ old('custodian', $asset->custodian) }}" placeholder="نام شخص یا دفتر">
                    </div>

                    {{-- تاریخ خرید (شمسی) --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">تاریخ خرید (شمسی) <span class="text-danger">*</span></label>
                        <input type="text" name="purchase_date" class="form-control"
                               value="{{ old('purchase_date', $asset->purchase_date) }}" required>
                    </div>
                </div>

                <div class="row">
                    {{-- وضعیت --}}
                    <div class="col-md-3 mb-3">
                        <label class="form-label">وضعیت <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" {{ old('status', $asset->status) == 'active' ? 'selected' : '' }}>فعال</option>
                            <option value="transferred" {{ old('status', $asset->status) == 'transferred' ? 'selected' : '' }}>انتقال</option>
                            <option value="broken" {{ old('status', $asset->status) == 'broken' ? 'selected' : '' }}>خراب</option>
                            <option value="scrap" {{ old('status', $asset->status) == 'scrap' ? 'selected' : '' }}>اسقاط</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">توضیحات</label>
                    <textarea name="notes" class="form-control" rows="2">{{ old('notes', $asset->notes) }}</textarea>
                </div>

                <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                <a href="{{ route('school.assets.index') }}" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
@endsection
