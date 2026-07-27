@extends('layouts.admin')

@section('title', 'مشاهده ولی')

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.guardians.index') }}">اولیا</a></li>
            <li class="breadcrumb-item active">{{ $guardian->full_name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-id-card ms-2"></i> مشخصات ولی</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th style="width:200px;">نام کامل</th><td>{{ $guardian->full_name }}</td></tr>
                        <tr>
    <th>نسبت</th>
    <td>
        @switch($guardian->relation)
            @case('father') پدر @break
            @case('mother') مادر @break
            @case('brother') برادر @break
            @case('uncle') کاکا / ماما @break
            @case('other') سایر @break
            @default {{ $guardian->relation ?? '—' }}
        @endswitch
    </td>
</tr>
                        <tr><th>شماره تذکره</th><td>{{ $guardian->national_id ?? '—' }}</td></tr>
                        <tr><th>شغل</th><td>{{ $guardian->job ?? '—' }}</td></tr>
                        <tr><th>تحصیلات</th><td>{{ $guardian->education ?? '—' }}</td></tr>
                        <tr><th>تلفن</th>
                            <td>
                                {{ $guardian->phone ?? '—' }}
                                @if($guardian->phone)
                                    <a href="tel:{{ $guardian->phone }}" class="btn btn-sm btn-outline-success ms-2"><i class="fas fa-phone"></i> تماس</a>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $guardian->phone) }}" target="_blank" class="btn btn-sm btn-outline-success"><i class="fab fa-whatsapp"></i></a>
                                @endif
                            </td>
                        </tr>
                        <tr><th>تلفن دوم</th><td>{{ $guardian->secondary_phone ?? '—' }}</td></tr>
                        <tr><th>آدرس</th><td>{{ $guardian->address ?? '—' }}</td></tr>
                        <tr><th>وضعیت</th><td><span class="badge bg-{{ $guardian->is_active ? 'success' : 'danger' }}">{{ $guardian->is_active ? 'فعال' : 'غیرفعال' }}</span></td></tr>
                    </table>
                    <div class="mt-2">
                        <a href="{{ route('school.guardians.edit', $guardian->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i> ویرایش</a>
                        <a href="{{ route('school.guardians.preview', $guardian->id) }}" class="btn btn-outline-secondary"><i class="fas fa-print"></i> پیش‌نمایش چاپ</a>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users ms-2"></i> فرزندان</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>نام</th><th>صنف</th><th>وضعیت</th><th>بدهی</th><th>عملیات</th></tr>
                        </thead>
                        <tbody>
                            @forelse($guardian->students as $student)
                                @php
                                    $fee = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                                    $paid = $student->payments->sum('amount');
                                    $debt = max($fee - $paid, 0);
                                @endphp
                                <tr>
                                    <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                    <td>{{ $student->class ?? '—' }}</td>
                                    <td>
                                        @switch($student->status)
                                            @case('present') <span class="badge bg-success">حاضر</span> @break
                                            @case('blocked') <span class="badge bg-danger">محروم</span> @break
                                            @default <span class="badge bg-secondary">{{ $student->status }}</span>
                                        @endswitch
                                    </td>
                                    <td>{{ number_format($debt) }} ؋</td>
                                    <td>
                                        <a href="{{ route('school.students.show', $student->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-muted text-center">دانش‌آموزی یافت نشد</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie ms-2"></i> خلاصه مالی خانواده</h5>
                </div>
                <div class="card-body">
                    @php
                        $totalFee = $guardian->students->sum(fn($s) => $s->studentFees->sum(fn($f) => $f->amount - $f->discount));
                        $totalPaid = $guardian->students->sum(fn($s) => $s->payments->sum('amount'));
                        $totalDebt = max($totalFee - $totalPaid, 0);
                    @endphp
                    <div class="mb-3"><strong>کل شهریه:</strong> <span class="float-end">{{ number_format($totalFee) }} ؋</span></div>
                    <div class="mb-3"><strong>پرداختی:</strong> <span class="float-end text-success">{{ number_format($totalPaid) }} ؋</span></div>
                    <div class="mb-3"><strong>بدهی:</strong> <span class="float-end {{ $totalDebt > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($totalDebt) }} ؋</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
