@extends('layouts.admin')

@section('title', 'لیست تجهیزات مکتب')

@section('content')
<div class="container-fluid px-4">
    {{-- نوار ابزار --}}
    <div class="sticky-toolbar bg-white shadow-sm border-bottom px-3 py-2 mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-cubes fa-lg text-primary ms-2"></i>
                <h5 class="fw-bold mb-0 text-dark">تجهیزات مکتب</h5>
            </div>
            <div class="btn-group">
                <a href="{{ route('school.assets.create') }}" class="btn btn-success rounded-pill px-3 py-2">
                    <i class="fas fa-plus-circle ms-1"></i> ثبت تجهیز جدید
                </a>
            </div>
            <div class="btn-group">
                <a href="{{ route('school.assets.print', request()->query()) }}" target="_blank" class="btn btn-outline-info rounded-pill px-3 py-2">
                    <i class="fas fa-print ms-1"></i> چاپ گزارش
                </a>
            </div>
        </div>
    </div>

    <div class="px-3">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

        {{-- فیلترها --}}
        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="جستجوی شرح، کد یا تحویل‌گیرنده..." value="{{ request('search') }}">
            </div>
            <div class="col-md-2">
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">همه دسته‌بندی‌ها</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">همه وضعیت‌ها</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>فعال</option>
                    <option value="transferred" {{ request('status') == 'transferred' ? 'selected' : '' }}>انتقال</option>
                    <option value="broken" {{ request('status') == 'broken' ? 'selected' : '' }}>خراب</option>
                    <option value="scrap" {{ request('status') == 'scrap' ? 'selected' : '' }}>اسقاط</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
                <a href="{{ route('school.assets.index') }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
            </div>
        </form>

        {{-- جدول تجهیزات --}}
        <div class="card shadow">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0" style="font-size: 12px;">
                    <thead class="table-light">
                        <tr>
                            <th>کد اموال</th>
                            <th>دسته‌بندی</th>
                            <th>شرح</th>
                            <th>تعداد</th>
                            <th>تحویل‌گیرنده</th>
                            <th>قیمت واحد</th>
                            <th>قیمت کل</th>
                            <th>تاریخ خرید</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $asset)
                        <tr>
                            <td>{{ $asset->asset_code }}</td>
                            <td>{{ $asset->category->name ?? '—' }}</td>
                            <td>{{ $asset->description }}</td>
                            <td>{{ $asset->quantity }}</td>
                            <td>{{ $asset->custodian ?? '—' }}</td>
                            <td>{{ number_format($asset->unit_price) }}</td>
                            <td>{{ number_format($asset->total_price) }}</td>
                            <td>{{ \App\Helpers\JalaliHelper::toJalali($asset->purchase_date) }}</td>
                            <td>
                                @switch($asset->status)
                                    @case('active') <span class="badge bg-success">فعال</span> @break
                                    @case('transferred') <span class="badge bg-info">انتقال</span> @break
                                    @case('broken') <span class="badge bg-danger">خراب</span> @break
                                    @case('scrap') <span class="badge bg-secondary">اسقاط</span> @break
                                @endswitch
                            </td>
                            <td>
                                {{-- دکمه ویرایش --}}
                                <a href="{{ route('school.assets.edit', $asset->id) }}" class="btn btn-sm btn-outline-warning" title="ویرایش">
                                    <i class="fa fa-pencil"></i>
                                </a>
                                {{-- دکمه حذف --}}
                                <form action="{{ route('school.assets.destroy', $asset->id) }}" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این تجهیز اطمینان دارید؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="10" class="text-center text-muted py-4">تجهیزاتی یافت نشد.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">{{ $assets->links() }}</div>
        </div>
    </div>
</div>
@endsection
