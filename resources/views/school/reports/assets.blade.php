@extends('layouts.admin')

@section('title', 'گزارش تجهیزات')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-cubes ms-2"></i> گزارش تجهیزات</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    {{-- ★ هدر یکسان (لوگو + نام مکتب + سال مالی) --}}
    @include('partials.report-header', ['title' => 'گزارش تجهیزات', 'subtitle' => ''])

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
            <a href="{{ route('school.reports.assets') }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    {{-- جدول --}}
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
                        <th>توضیحات</th>
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
                        <td>{{ $asset->notes ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="text-center text-muted py-4">تجهیزاتی با این فیلترها یافت نشد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">{{ $assets->links() }}</div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, form, .breadcrumb, .card-footer, #sidebar-wrapper, header {
            display: none !important;
        }
        #page-content-wrapper { padding: 0 !important; }
        .table { font-size: 11px; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>
@endpush
