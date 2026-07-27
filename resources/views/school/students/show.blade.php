@extends('layouts.admin')

@section('title', 'پروفایل ' . $student->first_name)

@section('content')
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('school.dashboard') }}">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="{{ route('school.students.index') }}">دانش‌آموزان</a></li>
            <li class="breadcrumb-item active">{{ $student->first_name }} {{ $student->last_name }}</li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-id-card ms-2"></i> اطلاعات دانش‌آموز</h5>
                    <span class="badge bg-light text-dark">{{ $student->student_code }}</span>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs" id="studentTabs" role="tablist">
                        <li class="nav-item" role="presentation"><button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab">اطلاعات عمومی</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="finance-tab" data-bs-toggle="tab" data-bs-target="#finance-pane" type="button" role="tab">مالی</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="receipts-tab" data-bs-toggle="tab" data-bs-target="#receipts-pane" type="button" role="tab">رسیدها</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab">تاریخچه</button></li>
                    </ul>
                    <div class="tab-content p-3" id="studentTabsContent">
                        {{-- تب اطلاعات عمومی --}}
                        <div class="tab-pane fade show active" id="info-pane" role="tabpanel">
                            <table class="table table-bordered">
                                <tr><th style="width:180px;">نام کامل</th><td>{{ $student->first_name }} {{ $student->last_name }}</td></tr>
                                <tr><th>نام پدر</th><td>{{ $student->father_name }}</td></tr>
                                <tr><th>پدرکلان</th><td>{{ $student->grandfather_name ?? '—' }}</td></tr>
                                {{-- 📅 تاریخ تولد شمسی --}}
                                <tr><th>تاریخ تولد</th><td>{{ $student->birth_date ? \App\Helpers\JalaliHelper::toJalali($student->birth_date) : '—' }}</td></tr>
                                <tr><th>شماره تذکره</th><td>{{ $student->national_id }}</td></tr>
                                <tr><th>نمبر اساس</th><td>{{ $student->base_number ?? '—' }}</td></tr>
                                <tr><th>جنسیت</th><td>{{ $student->gender == 'male' ? 'پسر' : 'دختر' }}</td></tr>
                                <tr><th>صنف</th><td>{{ $student->class ?? '—' }}</td></tr>
                                <tr><th>تلفن</th><td>{{ $student->phone ?? '—' }}</td></tr>
                                <tr><th>واتساپ</th><td>{{ $student->whatsapp_phone ?? '—' }}</td></tr>
                                <tr><th>سکونت اصلی</th><td>{{ $student->original_residence ?? '—' }}</td></tr>
                                <tr><th>آدرس</th><td>{{ $student->address ?? '—' }}</td></tr>
                                {{-- 📅 تاریخ ثبت‌نام شمسی --}}
                                <tr><th>تاریخ ثبت‌نام</th><td>{{ $student->enrollment_date ? \App\Helpers\JalaliHelper::toJalali($student->enrollment_date) : '—' }}</td></tr>
                                <tr><th>وضعیت</th><td>
                                    @switch($student->status)
                                        @case('present') <span class="badge bg-success">فعال</span> @break
                                        @case('blocked') <span class="badge bg-danger">غیرفعال</span> @break
                                        @case('temporary') <span class="badge bg-warning text-dark">موقت</span> @break
                                        @case('three_piece') <span class="badge bg-info">سه‌پارچه</span> @break
                                        @default <span class="badge bg-secondary">{{ $student->status }}</span>
                                    @endswitch
                                </td></tr>
                                <tr><th>وضعیت مالی</th><td>
                                    @switch($student->financial_status)
                                        @case('full') <span class="badge bg-primary">شهریه کامل</span> @break
                                        @case('discount') <span class="badge bg-success">تخفیف‌دار</span> @break
                                        @case('free') <span class="badge bg-info">رایگان</span> @break
                                        @default <span class="badge bg-secondary">تعیین نشده</span>
                                    @endswitch
                                </td></tr>
                                <tr><th>یتیم</th><td>{{ $student->is_orphan ? 'بلی' : 'خیر' }}</td></tr>
                                @if($student->photo)
                                <tr><th>عکس</th><td><img src="{{ asset('storage/'.$student->photo) }}" style="max-width: 150px;"></td></tr>
                                @endif
                            </table>
                            <a href="{{ route('school.students.edit', $student->id) }}" class="btn btn-warning"><i class="fas fa-edit"></i> ویرایش</a>
                            <a href="{{ route('school.students.preview', $student->id) }}" class="btn btn-outline-secondary"><i class="fas fa-print"></i> پیش‌نمایش چاپ</a>
                        </div>

                        {{-- تب مالی --}}
                        <div class="tab-pane fade" id="finance-pane" role="tabpanel">
                            @php
                                $totalFees = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                                $totalPaid = $student->payments->sum('amount');
                                $balance = $totalFees - $totalPaid;
                            @endphp
                            <div class="row text-center mb-3">
                                <div class="col-md-4"><div class="p-3 bg-light rounded"><h6>کل شهریه</h6><h4 class="text-primary">{{ number_format($totalFees) }} ؋</h4></div></div>
                                <div class="col-md-4"><div class="p-3 bg-light rounded"><h6>پرداخت‌شده</h6><h4 class="text-success">{{ number_format($totalPaid) }} ؋</h4></div></div>
                                <div class="col-md-4"><div class="p-3 bg-light rounded"><h6>باقی‌مانده</h6><h4 class="{{ $balance > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($balance) }} ؋</h4></div></div>
                            </div>
                            <h6>جزئیات شهریه‌های تعیین‌شده</h6>
                            <table class="table table-sm table-bordered">
                                <thead><tr><th>نوع هزینه</th><th>ماه</th><th>مبلغ</th><th>تخفیف</th><th>قابل پرداخت</th><th>پرداخت‌شده؟</th></tr></thead>
                                <tbody>
                                    @forelse($student->studentFees as $fee)
                                        @php
                                            $paidForThisFee = $student->payments->where('fee_id', $fee->id)->sum('amount');
                                            $remaining = ($fee->amount - $fee->discount) - $paidForThisFee;
                                        @endphp
                                        <tr>
                                            <td>{{ $fee->feeType->name ?? '—' }}</td>
                                            <td>{{ $fee->month->name ?? '—' }}</td>
                                            <td>{{ number_format($fee->amount) }} ؋</td>
                                            <td>{{ number_format($fee->discount) }} ؋</td>
                                            <td>{{ number_format($fee->amount - $fee->discount) }} ؋</td>
                                            <td>
                                                @if($remaining <= 0) <span class="badge bg-success">پرداخت کامل</span>
                                                @elseif($paidForThisFee > 0) <span class="badge bg-warning text-dark">پرداخت جزئی ({{ number_format($remaining) }} ؋ مانده)</span>
                                                @else <span class="badge bg-danger">پرداخت نشده</span> @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="6" class="text-muted text-center">هزینه‌ای ثبت نشده است.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- تب رسیدها --}}
                        <div class="tab-pane fade" id="receipts-pane" role="tabpanel">
                            <table class="table table-sm table-hover">
                                <thead><tr><th>شماره رسید</th><th>مبلغ</th><th>تاریخ</th><th>روش</th><th>توضیحات</th></tr></thead>
                                <tbody>
                                    @forelse($student->payments as $payment)
                                        <tr><td>{{ $payment->receipt_number ?? '—' }}</td><td>{{ number_format($payment->amount) }} ؋</td><td>{{ $payment->payment_date }}</td><td>{{ $payment->payment_method }}</td><td>{{ $payment->notes ?? '—' }}</td></tr>
                                    @empty
                                        <tr><td colspan="5" class="text-muted text-center">پرداختی ثبت نشده است.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        {{-- تب تاریخچه --}}
                        <div class="tab-pane fade" id="history-pane" role="tabpanel">
                            <table class="table table-sm table-hover">
                                <thead><tr><th>تاریخ</th><th>عملیات</th><th>کاربر</th><th>توضیحات</th></tr></thead>
                                <tbody>
                                    @forelse($student->auditLogs ?? [] as $log)
                                        <tr><td>{{ $log->created_at->format('Y/m/d H:i') }}</td><td>{{ $log->action }}</td><td>{{ $log->user->name ?? '—' }}</td><td>{{ $log->description ?? '—' }}</td></tr>
                                    @empty
                                        <tr><td colspan="4" class="text-muted text-center">تاریخچه‌ای ثبت نشده است.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- کارت ولی --}}
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-friends ms-2"></i> اطلاعات ولی</h5>
                </div>
                <div class="card-body">
                    @if($student->guardian)
                        <p><strong>نام:</strong> {{ $student->guardian->full_name }}</p>
                        <p><strong>نسبت:</strong>
    @switch($student->guardian->relation)
        @case('father') پدر @break
        @case('mother') مادر @break
        @case('brother') برادر @break
        @case('uncle') کاکا / ماما @break
        @case('other') سایر @break
        @default {{ $student->guardian->relation ?? '—' }}
    @endswitch
</p>
                        <p><strong>تحصیلات:</strong> {{ $student->guardian->education ?? '—' }}</p>
                        <p><strong>شغل:</strong> {{ $student->guardian->job ?? '—' }}</p>
                        <p><strong>شماره تماس:</strong> {{ $student->guardian->phone ?? '—' }}</p>
                        <p><strong>آدرس:</strong> {{ $student->guardian->address ?? '—' }}</p>
                        <a href="{{ route('school.guardians.edit', $student->guardian->id) }}" class="btn btn-sm btn-outline-warning mt-2">
                            <i class="fas fa-edit"></i> ویرایش اطلاعات ولی
                        </a>
                    @else
                        <div class="alert alert-warning mb-0">هیچ ولی‌ای ثبت نشده است.</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
