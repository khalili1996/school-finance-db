@extends('layouts.admin')

@section('title', 'گزارش کارمندان')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-user-tie ms-2"></i> گزارش کارمندان</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    {{-- ★ هدر یکسان برای چاپ (لوگو + نام مکتب + سال مالی) --}}
    @include('partials.report-header', ['title' => 'گزارش کارمندان', 'subtitle' => ''])

    {{-- فیلتر وضعیت --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">همه وضعیت‌ها</option>
                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>فعال</option>
                <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غیرفعال</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
            <a href="{{ route('school.reports.employees') }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    {{-- جدول کارمندان --}}
    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>کد</th>
                        <th>نام</th>
                        <th>نام پدر</th>
                        <th>سمت</th>
                        <th>بخش</th>
                        <th>تلفن</th>
                        <th>وضعیت</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $employee)
                    <tr>
                        <td>{{ $employee->employee_code }}</td>
                        <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                        <td>{{ $employee->father_name ?? '—' }}</td>
                        <td>{{ $employee->position->name ?? $employee->position ?? '—' }}</td>
                        <td>{{ $employee->department ?? '—' }}</td>
                        <td>{{ $employee->phone ?? '—' }}</td>
                        <td>
                            @if($employee->status == 'active')
                                <span class="badge bg-success">فعال</span>
                            @else
                                <span class="badge bg-danger">غیرفعال</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">کارمندی با این شرایط یافت نشد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    @media print {
        .btn, form, .breadcrumb, .card-footer, #sidebar-wrapper, header {
            display: none !important;
        }
        #page-content-wrapper {
            padding: 0 !important;
        }
        .table {
            font-size: 12px;
        }
    }
</style>
@endpush
