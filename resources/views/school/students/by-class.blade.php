@extends('layouts.admin')

@section('title', 'صنف‌بندی دانش‌آموزان')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-layer-group ms-2"></i> صنف‌بندی دانش‌آموزان</h1>
        <a href="{{ route('school.students.report') }}" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-print"></i> چاپ گزارش کل مکتب
        </a>
    </div>

    {{-- فیلتر صنف --}}
    <form method="GET" action="{{ route('school.students.index') }}" class="row g-2 mb-4">
        <input type="hidden" name="filter" value="senfi">
        <div class="col-auto">
            <select name="class_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">همه‌ی صنف‌ها</option>
                @foreach($classes as $className)
                    <option value="{{ $className }}" {{ request('class_filter') == $className ? 'selected' : '' }}>{{ $className }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm">اعمال</button>
            <a href="{{ route('school.students.index', ['filter' => 'senfi']) }}" class="btn btn-secondary btn-sm">حذف فیلتر</a>
        </div>
    </form>

    @forelse($studentsByClass as $className => $students)
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="fw-bold">{{ $className ?: 'بدون صنف' }} ({{ $students->count() }} دانش‌آموز)</span>
                <a href="{{ route('school.students.report', ['class_filter' => $className]) }}" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-print"></i> چاپ
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>کد</th><th>نام</th><th>نام پدر</th><th>وضعیت</th></tr></thead>
                    <tbody>
                        @foreach($students as $student)
                            <tr>
                                <td>{{ $student->student_code }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->father_name }}</td>
                                <td>
                                    @switch($student->status)
                                        @case('present') <span class="badge bg-success">حاضر</span> @break
                                        @case('blocked') <span class="badge bg-danger">محروم</span> @break
                                        @case('temporary') <span class="badge bg-warning text-dark">موقت</span> @break
                                        @case('three_piece') <span class="badge bg-info">سه‌پارچه</span> @break
                                        @default <span class="badge bg-secondary">{{ $student->status }}</span>
                                    @endswitch
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @empty
        <p class="text-muted">دانش‌آموزی یافت نشد.</p>
    @endforelse
</div>
@endsection
