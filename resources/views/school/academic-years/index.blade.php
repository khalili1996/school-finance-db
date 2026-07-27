@extends('layouts.admin')

@section('title', 'سال‌های مالی')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-calendar-alt ms-2"></i> سال‌های مالی</h4>
        <a href="{{ route('school.academic-years.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> ایجاد سال مالی جدید
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>نام سال</th>
                        <th>تاریخ شروع</th>
                        <th>تاریخ پایان</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($academicYears as $year)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $year->name }}</td>
                            <td>{{ \App\Helpers\JalaliHelper::toJalali($year->start_date, 'Y/m/d') }}</td>
                            <td>{{ \App\Helpers\JalaliHelper::toJalali($year->end_date, 'Y/m/d') }}</td>
                            <td>
                                @if($year->id == session('active_academic_year_id'))
                                    <span class="badge bg-success">فعال</span>
                                @else
                                    <span class="badge bg-secondary">غیرفعال</span>
                                @endif
                            </td>
                            <td>
                                @if($year->id != session('active_academic_year_id'))
                                    <a href="{{ route('school.set-academic-year', $year->id) }}" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-check"></i> فعال‌سازی
                                    </a>
                                @else
                                    <span class="text-success"><i class="fas fa-check-circle"></i> سال جاری</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center py-3">هیچ سال مالی ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
