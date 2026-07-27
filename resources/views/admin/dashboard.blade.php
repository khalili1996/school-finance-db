<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>پنل مدیریت مرکزی – دیتابیس مالی الزهرا (س)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>

        body { background: #f5f6fa; }
        .stats-bar {
            display: flex;
            flex-wrap: nowrap;
            overflow-x: auto;
            gap: 0.5rem;
            padding-bottom: 0.5rem;
        }
        .stat-card {
            border-radius: 8px;
            padding: 0.5rem 0.7rem;
            color: #fff;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            box-shadow: 0 2px 6px rgba(0,0,0,0.04);
            min-width: 100px;
            flex: 1 1 0;
            text-align: right;
        }
        .stat-card i { font-size: 1.2rem; opacity: 0.7; }
        .stat-card .info h6 { margin: 0; font-size: 0.65rem; opacity: 0.9; letter-spacing: 0.3px; white-space: nowrap; }
        .stat-card .info h3 { margin: 0; font-size: 1rem; font-weight: 700; white-space: nowrap; }
        .stat-card .sub { font-size: 0.55rem; opacity: 0.8; white-space: nowrap; }
        .table-scroll { max-height: 450px; overflow-y: auto; }
        .tab-content { background: #fff; border: 1px solid #dee2e6; border-top: none; border-radius: 0 0 10px 10px; padding: 1.5rem; }
    </style>
</head>
<body class="bg-light">
<div class="container-fluid py-3">
    <div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold"><i class="fas fa-chart-pie ms-2"></i> داشبورد مدیریت مرکزی</h4>
    <div>
        <a href="{{ route('admin.schools.trash') }}" class="btn btn-outline-danger btn-sm me-2">
            <i class="fas fa-trash-alt"></i> مدارس غیرفعال
        </a>
        <form method="POST" action="/admin/logout" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i> خروج</button>
        </form>
    </div>
</div>

    {{-- ========== فیلتر مکتب (جدید) ========== --}}
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0 small">انتخاب مکتب:</label>
                </div>
                <div class="col-md-3">
                    <select name="school_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">همهٔ مکاتب</option>
                        @foreach($allSchools as $school)
                            <option value="{{ $school->id }}" {{ request('school_filter') == $school->id ? 'selected' : '' }}>
                                {{ $school->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if(request('school_filter'))
                    <div class="col-auto">
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- فیلتر سال تعلیمی (موجود) --}}
    <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-2 mb-4">
        {{-- حفظ school_filter در هنگام تغییر سال --}}
        @if(request('school_filter'))
            <input type="hidden" name="school_filter" value="{{ request('school_filter') }}">
        @endif
        <div class="col-auto">
            <select name="year_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">همه سال‌ها</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year->id }}" {{ request('year_filter') == $year->id ? 'selected' : '' }}>
                        سال {{ $year->name }}
                    </option>
                @endforeach
            </select>
        </div>
        @if(request('year_filter'))
            <div class="col-auto">
                <a href="{{ route('admin.dashboard', request()->except('year_filter')) }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
            </div>
        @endif
    </form>

    {{-- نوار ابزار آماری --}}
    <div class="stats-bar mb-4">
        <div class="stat-card bg-secondary">
            <div class="info">
                <h6>شعب مکاتب</h6>
                <h3>{{ $totalSchools }}</h3>
                <div class="sub">
                    <span class="text-success">● {{ $activeSchools }} فعال</span>
                    <span class="text-warning ms-1">● {{ $inactiveSchools }} غیرفعال</span>
                </div>
            </div>
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-card bg-success">
            <div class="info"><h6>کارمندان</h6><h3>{{ $totalEmployees }}</h3></div>
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="stat-card bg-primary">
            <div class="info"><h6>دانش‌آموزان</h6><h3>{{ $totalStudents }}</h3></div>
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="stat-card bg-info">
            <div class="info"><h6>درآمد کل</h6><h3>{{ number_format($totalIncome) }} ؋</h3></div>
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-card bg-warning text-dark">
            <div class="info"><h6>مصارف کل</h6><h3>{{ number_format($totalExpenses) }} ؋</h3></div>
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="stat-card bg-dark">
            <div class="info"><h6>موجودی صندوق</h6><h3>{{ number_format($totalCashbox) }} ؋</h3></div>
            <i class="fas fa-cash-register"></i>
        </div>
        <div class="stat-card bg-danger">
            <div class="info"><h6>بدهی شاگردان</h6><h3>{{ number_format($totalDebt) }} ؋</h3></div>
            <i class="fas fa-hand-holding-usd"></i>
        </div>
    </div>

    {{-- تب‌ها (بدون تغییر) --}}
    <ul class="nav nav-tabs mb-0" id="centralTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="schools-tab" data-bs-toggle="tab" data-bs-target="#schools-pane" type="button" role="tab">لیست مکاتب ({{ $schools->total() }})</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="employees-tab" data-bs-toggle="tab" data-bs-target="#employees-pane" type="button" role="tab">نمای کلی کارمندان ({{ $employees->total() }})</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students-pane" type="button" role="tab">نمای کلی دانش‌آموزان ({{ $students->total() }})</button>
        </li>
    </ul>

    <div class="tab-content shadow-sm" id="centralTabsContent">
        {{-- تب لیست مکاتب --}}
        <div class="tab-pane fade show active" id="schools-pane" role="tabpanel">
            <div class="mb-4" style="max-height: 300px;">
                <canvas id="schoolsFinanceChart"></canvas>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold">مکاتب ثبت‌شده</h6>
                <a href="{{ route('admin.schools.create') }}" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> ایجاد مکتب</a>
            </div>
            <table class="table table-sm table-hover">
                <thead><tr><th>کد</th><th>نام مکتب</th><th>درآمد کل</th><th>مصارف</th><th>موجودی صندوق</th><th>وضعیت مالی</th><th>عملیات</th></tr></thead>
                <tbody>
                    @forelse($schools as $school)
                    @php
                        $financialColor = $school->debt_ratio > 80 ? 'danger' : ($school->debt_ratio > 50 ? 'warning' : 'success');
                        $financialLabel = $school->debt_ratio > 80 ? 'نیاز به بررسی' : ($school->debt_ratio > 50 ? 'متوسط' : 'خوب');
                    @endphp
                    <tr>
                        <td>{{ $school->code }}</td>
                        <td>{{ $school->name }}</td>
                        <td>{{ number_format($school->total_income) }} ؋</td>
                        <td>{{ number_format($school->total_expense) }} ؋</td>
                        <td>{{ number_format($school->cashbox_balance) }} ؋</td>
                        <td><span class="badge bg-{{ $financialColor }}">{{ $financialLabel }}</span></td>
                        <td>
    <a href="{{ route('admin.schools.edit', $school->id) }}" class="btn btn-sm btn-outline-warning" title="تنظیمات">
        <i class="fas fa-cog"></i>
    </a>
    <a href="{{ route('admin.schools.enter', $school->id) }}" class="btn btn-sm btn-outline-primary">ورود</a>
</td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">هیچ مکتبی ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- تب نمای کلی کارمندان (بدون تغییر) --}}
        <div class="tab-pane fade" id="employees-pane" role="tabpanel">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-2 mb-3">
                <input type="hidden" name="tab" value="employees">
                <div class="col-md-2">
                    <select name="school_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">همه مکاتب</option>
                        @foreach($allSchools as $sch)
                            <option value="{{ $sch->id }}" {{ request('school_filter') == $sch->id ? 'selected' : '' }}>{{ $sch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="employee_role" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">همه سمت‌ها</option>
                        @foreach($employeeRoles as $role)
                            <option value="{{ $role->id }}" {{ request('employee_role') == $role->id ? 'selected' : '' }}>{{ $role->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="employee_search" class="form-control form-control-sm" placeholder="جستجوی نام یا کد..." value="{{ request('employee_search') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
                    <a href="{{ route('admin.dashboard', ['tab' => 'employees']) }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
                </div>
            </form>
            <div class="table-scroll">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top"><tr><th>کد</th><th>نام</th><th>سمت</th><th>مکتب</th><th>بخش</th><th>وضعیت</th></tr></thead>
                    <tbody>
                        @forelse($employees as $employee)
                            @php
                                $roleColors = ['مدیر' => 'danger', 'معلم' => 'success', 'حسابدار' => 'info', 'خدمتکار' => 'secondary', 'نگهبان' => 'dark'];
                            @endphp
                            <tr>
                                <td><small>{{ $employee->employee_code }}</small></td>
                                <td>{{ $employee->first_name }} {{ $employee->last_name }}</td>
                                <td><span class="badge bg-{{ $roleColors[$employee->employeeRole->name] ?? 'primary' }}">{{ $employee->employeeRole->name ?? '—' }}</span></td>
                                <td>{{ $employee->school->name ?? '—' }}</td>
                                <td>{{ $employee->department ?? '—' }}</td>
                                <td><span class="badge bg-{{ $employee->status == 'active' ? 'success' : 'danger' }}">{{ $employee->status == 'active' ? 'فعال' : 'غیرفعال' }}</span></td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center text-muted py-3">کارمندی یافت نشد.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-dark fw-bold"><tr><td colspan="6">مجموع کارمندان: {{ number_format($employees->total()) }} نفر</td></tr></tfoot>
                </table>
            </div>
            <div class="mt-2">{{ $employees->links() }}</div>
        </div>

        {{-- تب نمای کلی دانش‌آموزان (دست‌نخورده) --}}
        <div class="tab-pane fade" id="students-pane" role="tabpanel">
            <form method="GET" action="{{ route('admin.dashboard') }}" class="row g-2 mb-3">
                <input type="hidden" name="tab" value="students">
                <div class="col-md-2">
                    <select name="school_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">همه مکاتب</option>
                        @foreach($allSchools as $sch)
                            <option value="{{ $sch->id }}" {{ request('school_filter') == $sch->id ? 'selected' : '' }}>{{ $sch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="student_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">همه وضعیت‌ها</option>
                        @foreach($studentStatuses as $status)
                            <option value="{{ $status }}" {{ request('student_status') == $status ? 'selected' : '' }}>
                                @switch($status)
                                    @case('present') حاضر @break
                                    @case('blocked') محروم @break
                                    @case('temporary') موقت @break
                                    @case('three_piece') سه‌پارچه @break
                                    @default {{ $status }}
                                @endswitch
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="student_financial" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">همه وضعیت مالی</option>
                        <option value="discount" {{ request('student_financial') == 'discount' ? 'selected' : '' }}>دارای تخفیف</option>
                        <option value="free" {{ request('student_financial') == 'free' ? 'selected' : '' }}>رایگان</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="student_search" class="form-control form-control-sm" placeholder="جستجوی نام یا کد..." value="{{ request('student_search') }}">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
                    <a href="{{ route('admin.dashboard', ['tab' => 'students']) }}" class="btn btn-sm btn-secondary">حذف فیلتر</a>
                </div>
            </form>
            <div class="table-scroll">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top">
                        <tr>
                            <th>کد</th><th>نام</th><th>مکتب</th><th>صنف</th>
                            <th>وضعیت</th><th>وضعیت مالی</th>
                            <th>شهریه تعیین‌شده</th><th>شهریه پرداخت‌شده</th><th>بدهکاری</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            @php
                                $totalFee = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                                $totalPaid = $student->payments->sum('amount');
                                $debt = $totalFee - $totalPaid;
                                $hasDiscount = \App\Models\StudentFee::where('student_id', $student->id)->where('discount', '>', 0)->exists();
                                $isFree = $totalFee == 0;
                            @endphp
                            <tr>
                                <td><small>{{ $student->student_code }}</small></td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                <td>{{ $student->school->name ?? '—' }}</td>
                                <td>{{ $student->class ?? '—' }}</td>
                                <td>
                                    @switch($student->status)
                                        @case('present') <span class="badge bg-success">حاضر</span> @break
                                        @case('blocked') <span class="badge bg-danger">محروم</span> @break
                                        @case('temporary') <span class="badge bg-warning text-dark">موقت</span> @break
                                        @case('three_piece') <span class="badge bg-info">سه‌پارچه</span> @break
                                        @default <span class="badge bg-secondary">{{ $student->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    @if($isFree)
                                        <span class="badge bg-info">رایگان</span>
                                    @elseif($hasDiscount)
                                        <span class="badge bg-success">تخفیف‌دار</span>
                                    @else
                                        <span class="badge bg-primary">شهریه کامل</span>
                                    @endif
                                </td>
                                <td>{{ number_format($totalFee) }}</td>
                                <td>{{ number_format($totalPaid) }}</td>
                                <td class="{{ $debt > 0 ? 'text-danger' : 'text-success' }}">{{ number_format($debt) }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-3">دانش‌آموزی یافت نشد.</td></tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="6">مجموع ({{ $studentCount }} دانش‌آموز)</td>
                            <td>{{ number_format($studentTotalFee) }} ؋</td>
                            <td>{{ number_format($studentTotalPaid) }} ؋</td>
                            <td>{{ number_format($studentTotalDebt) }} ؋</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="mt-2">{{ $students->links() }}</div>
        </div>
    </div>

    {{-- آخرین فعالیت‌ها --}}
    <div class="mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light"><h6><i class="fas fa-history ms-2"></i> آخرین فعالیت‌ها</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <tbody>
                        @forelse($recentActivities as $activity)
                            <tr>
                                <td><small>{{ $activity->created_at->format('H:i') }}</small></td>
                                <td>{{ $activity->description }}</td>
                                <td>{{ $activity->user->name ?? '—' }}</td>
                                <td>{{ $activity->school->name ?? '—' }}</td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-muted text-center">فعالیتی ثبت نشده است.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const schools = @json($schools->items());
    const labels = schools.map(s => s.name);
    const ratios = schools.map(s => s.debt_ratio);
    const barColors = ratios.map(r => r > 80 ? '#dc3545' : (r > 50 ? '#ffc107' : '#198754'));

    new Chart(document.getElementById('schoolsFinanceChart'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'نسبت مصرف به درآمد (%)',
                data: ratios,
                backgroundColor: barColors,
                borderRadius: 4
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { max: 100, ticks: { callback: v => v + '%' } }
            }
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        const urlParams = new URLSearchParams(window.location.search);
        const activeTab = urlParams.get('tab') || 'schools';
        const triggerEl = document.querySelector(`#centralTabs button[data-bs-target="#${activeTab}-pane"]`);
        if (triggerEl) {
            new bootstrap.Tab(triggerEl).show();
        }
    });
</script>
</body>
</html>
