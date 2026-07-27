@extends('layouts.admin')

@section('title', 'گزارش دانش‌آموزان')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-users ms-2"></i> گزارش دانش‌آموزان</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    {{-- ★ هدر یکسان (لوگو + نام مکتب + سال مالی) --}}
    @include('partials.report-header', ['title' => 'گزارش دانش‌آموزان', 'subtitle' => ''])

    {{-- فیلترها --}}
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <select name="class_filter" class="form-select form-select-sm">
                <option value="">همه صنف‌ها</option>
                @foreach($classes as $cls)
                    <option value="{{ $cls }}" {{ request('class_filter') == $cls ? 'selected' : '' }}>{{ $cls }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status_filter" class="form-select form-select-sm">
                <option value="">همه وضعیت‌ها</option>
                <option value="present" {{ request('status_filter') == 'present' ? 'selected' : '' }}>حاضر</option>
                <option value="blocked" {{ request('status_filter') == 'blocked' ? 'selected' : '' }}>محروم</option>
                <option value="temporary" {{ request('status_filter') == 'temporary' ? 'selected' : '' }}>موقت</option>
                <option value="three_piece" {{ request('status_filter') == 'three_piece' ? 'selected' : '' }}>سه‌پارچه</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="financial_filter" class="form-select form-select-sm">
                <option value="">همه وضعیت‌های مالی</option>
                <option value="debtor" {{ request('financial_filter') == 'debtor' ? 'selected' : '' }}>بدهکار</option>
                <option value="discount" {{ request('financial_filter') == 'discount' ? 'selected' : '' }}>تخفیف‌دار</option>
                <option value="free" {{ request('financial_filter') == 'free' ? 'selected' : '' }}>رایگان</option>
                <option value="orphan" {{ request('financial_filter') == 'orphan' ? 'selected' : '' }}>یتیم</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
            <a href="{{ route('school.reports.students') }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    {{-- جدول دانش‌آموزان --}}
    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>کد</th>
                        <th>نام</th>
                        <th>نام پدر</th>
                        <th>صنف</th>
                        <th>وضعیت</th>
                        <th>وضعیت مالی</th>
                        <th>تلفن</th>
                        <th>ولی</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($students as $student)
                    <tr>
                        <td>{{ $student->student_code }}</td>
                        <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                        <td>{{ $student->father_name ?? '—' }}</td>
                        <td>{{ $student->class ?? '—' }}</td>
                        <td>
                            @switch($student->status)
                                @case('present') <span class="badge bg-success">حاضر</span> @break
                                @case('blocked') <span class="badge bg-danger">محروم</span> @break
                                @case('temporary') <span class="badge bg-warning text-dark">موقت</span> @break
                                @case('three_piece') <span class="badge bg-info">سه‌پارچه</span> @break
                                @default {{ $student->status }}
                            @endswitch
                        </td>
                        <td>
                            @if($student->financial_status == 'free')
                                <span class="badge bg-primary">رایگان</span>
                            @elseif($student->studentFees->where('discount', '>', 0)->count() > 0)
                                <span class="badge bg-warning text-dark">تخفیف‌دار</span>
                            @else
                                <span class="badge bg-secondary">عادی</span>
                            @endif
                        </td>
                        <td>{{ $student->phone ?? '—' }}</td>
                        <td>{{ $student->guardian->full_name ?? '—' }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">دانش‌آموزی با این شرایط یافت نشد.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            {{ $students->appends(request()->query())->links() }}
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
