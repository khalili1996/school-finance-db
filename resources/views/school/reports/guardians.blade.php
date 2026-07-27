@extends('layouts.admin')

@section('title', 'گزارش اولیا')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-user-friends ms-2"></i> گزارش اولیا</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    {{-- ★ هدر یکسان (لوگو + نام مکتب + سال مالی) --}}
    @include('partials.report-header', ['title' => 'گزارش اولیا', 'subtitle' => ''])

    {{-- فیلتر جستجو --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="جستجوی نام ولی..." value="{{ request('search') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">جستجو</button>
            <a href="{{ route('school.reports.guardians') }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    {{-- جدول اولیا --}}
    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>نام ولی</th>
                        <th>تلفن</th>
                        <th>تعداد فرزندان</th>
                        <th>آدرس</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($guardians as $guardian)
                    <tr>
                        <td>{{ $loop->iteration + $guardians->firstItem() - 1 }}</td>
                        <td>{{ $guardian->full_name }}</td>
                        <td>{{ $guardian->phone ?? '—' }}</td>
                        <td>{{ $guardian->students_count }}</td>
                        <td>{{ $guardian->address ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">اولیایی با این مشخصات یافت نشد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $guardians->appends(request()->query())->links() }}
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
