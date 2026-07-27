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
        <a href="<?php echo e(route('admin.schools.trash')); ?>" class="btn btn-outline-danger btn-sm me-2">
            <i class="fas fa-trash-alt"></i> مدارس غیرفعال
        </a>
        <form method="POST" action="/admin/logout" class="d-inline">
            <?php echo csrf_field(); ?>
            <button type="submit" class="btn btn-outline-danger btn-sm"><i class="fas fa-sign-out-alt"></i> خروج</button>
        </form>
    </div>
</div>

    
    <div class="card shadow-sm border-0 mb-3">
        <div class="card-body py-2">
            <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0 small">انتخاب مکتب:</label>
                </div>
                <div class="col-md-3">
                    <select name="school_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">همهٔ مکاتب</option>
                        <?php $__currentLoopData = $allSchools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($school->id); ?>" <?php echo e(request('school_filter') == $school->id ? 'selected' : ''); ?>>
                                <?php echo e($school->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <?php if(request('school_filter')): ?>
                    <div class="col-auto">
                        <a href="<?php echo e(route('admin.dashboard')); ?>" class="btn btn-sm btn-secondary">حذف فیلتر</a>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    
    <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="row g-2 mb-4">
        
        <?php if(request('school_filter')): ?>
            <input type="hidden" name="school_filter" value="<?php echo e(request('school_filter')); ?>">
        <?php endif; ?>
        <div class="col-auto">
            <select name="year_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">همه سال‌ها</option>
                <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($year->id); ?>" <?php echo e(request('year_filter') == $year->id ? 'selected' : ''); ?>>
                        سال <?php echo e($year->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <?php if(request('year_filter')): ?>
            <div class="col-auto">
                <a href="<?php echo e(route('admin.dashboard', request()->except('year_filter'))); ?>" class="btn btn-sm btn-secondary">حذف فیلتر</a>
            </div>
        <?php endif; ?>
    </form>

    
    <div class="stats-bar mb-4">
        <div class="stat-card bg-secondary">
            <div class="info">
                <h6>شعب مکاتب</h6>
                <h3><?php echo e($totalSchools); ?></h3>
                <div class="sub">
                    <span class="text-success">● <?php echo e($activeSchools); ?> فعال</span>
                    <span class="text-warning ms-1">● <?php echo e($inactiveSchools); ?> غیرفعال</span>
                </div>
            </div>
            <i class="fas fa-building"></i>
        </div>
        <div class="stat-card bg-success">
            <div class="info"><h6>کارمندان</h6><h3><?php echo e($totalEmployees); ?></h3></div>
            <i class="fas fa-user-tie"></i>
        </div>
        <div class="stat-card bg-primary">
            <div class="info"><h6>دانش‌آموزان</h6><h3><?php echo e($totalStudents); ?></h3></div>
            <i class="fas fa-user-graduate"></i>
        </div>
        <div class="stat-card bg-info">
            <div class="info"><h6>درآمد کل</h6><h3><?php echo e(number_format($totalIncome)); ?> ؋</h3></div>
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div class="stat-card bg-warning text-dark">
            <div class="info"><h6>مصارف کل</h6><h3><?php echo e(number_format($totalExpenses)); ?> ؋</h3></div>
            <i class="fas fa-file-invoice"></i>
        </div>
        <div class="stat-card bg-dark">
            <div class="info"><h6>موجودی صندوق</h6><h3><?php echo e(number_format($totalCashbox)); ?> ؋</h3></div>
            <i class="fas fa-cash-register"></i>
        </div>
        <div class="stat-card bg-danger">
            <div class="info"><h6>بدهی شاگردان</h6><h3><?php echo e(number_format($totalDebt)); ?> ؋</h3></div>
            <i class="fas fa-hand-holding-usd"></i>
        </div>
    </div>

    
    <ul class="nav nav-tabs mb-0" id="centralTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="schools-tab" data-bs-toggle="tab" data-bs-target="#schools-pane" type="button" role="tab">لیست مکاتب (<?php echo e($schools->total()); ?>)</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="employees-tab" data-bs-toggle="tab" data-bs-target="#employees-pane" type="button" role="tab">نمای کلی کارمندان (<?php echo e($employees->total()); ?>)</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="students-tab" data-bs-toggle="tab" data-bs-target="#students-pane" type="button" role="tab">نمای کلی دانش‌آموزان (<?php echo e($students->total()); ?>)</button>
        </li>
    </ul>

    <div class="tab-content shadow-sm" id="centralTabsContent">
        
        <div class="tab-pane fade show active" id="schools-pane" role="tabpanel">
            <div class="mb-4" style="max-height: 300px;">
                <canvas id="schoolsFinanceChart"></canvas>
            </div>
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold">مکاتب ثبت‌شده</h6>
                <a href="<?php echo e(route('admin.schools.create')); ?>" class="btn btn-success btn-sm"><i class="fas fa-plus"></i> ایجاد مکتب</a>
            </div>
            <table class="table table-sm table-hover">
                <thead><tr><th>کد</th><th>نام مکتب</th><th>درآمد کل</th><th>مصارف</th><th>موجودی صندوق</th><th>وضعیت مالی</th><th>عملیات</th></tr></thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $financialColor = $school->debt_ratio > 80 ? 'danger' : ($school->debt_ratio > 50 ? 'warning' : 'success');
                        $financialLabel = $school->debt_ratio > 80 ? 'نیاز به بررسی' : ($school->debt_ratio > 50 ? 'متوسط' : 'خوب');
                    ?>
                    <tr>
                        <td><?php echo e($school->code); ?></td>
                        <td><?php echo e($school->name); ?></td>
                        <td><?php echo e(number_format($school->total_income)); ?> ؋</td>
                        <td><?php echo e(number_format($school->total_expense)); ?> ؋</td>
                        <td><?php echo e(number_format($school->cashbox_balance)); ?> ؋</td>
                        <td><span class="badge bg-<?php echo e($financialColor); ?>"><?php echo e($financialLabel); ?></span></td>
                        <td>
    <a href="<?php echo e(route('admin.schools.edit', $school->id)); ?>" class="btn btn-sm btn-outline-warning" title="تنظیمات">
        <i class="fas fa-cog"></i>
    </a>
    <a href="<?php echo e(route('admin.schools.enter', $school->id)); ?>" class="btn btn-sm btn-outline-primary">ورود</a>
</td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center text-muted">هیچ مکتبی ثبت نشده است.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        
        <div class="tab-pane fade" id="employees-pane" role="tabpanel">
            <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="row g-2 mb-3">
                <input type="hidden" name="tab" value="employees">
                <div class="col-md-2">
                    <select name="school_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">همه مکاتب</option>
                        <?php $__currentLoopData = $allSchools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($sch->id); ?>" <?php echo e(request('school_filter') == $sch->id ? 'selected' : ''); ?>><?php echo e($sch->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="employee_role" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">همه سمت‌ها</option>
                        <?php $__currentLoopData = $employeeRoles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($role->id); ?>" <?php echo e(request('employee_role') == $role->id ? 'selected' : ''); ?>><?php echo e($role->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <input type="text" name="employee_search" class="form-control form-control-sm" placeholder="جستجوی نام یا کد..." value="<?php echo e(request('employee_search')); ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
                    <a href="<?php echo e(route('admin.dashboard', ['tab' => 'employees'])); ?>" class="btn btn-sm btn-secondary">حذف فیلتر</a>
                </div>
            </form>
            <div class="table-scroll">
                <table class="table table-sm table-hover mb-0">
                    <thead class="table-light sticky-top"><tr><th>کد</th><th>نام</th><th>سمت</th><th>مکتب</th><th>بخش</th><th>وضعیت</th></tr></thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $employee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $roleColors = ['مدیر' => 'danger', 'معلم' => 'success', 'حسابدار' => 'info', 'خدمتکار' => 'secondary', 'نگهبان' => 'dark'];
                            ?>
                            <tr>
                                <td><small><?php echo e($employee->employee_code); ?></small></td>
                                <td><?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?></td>
                                <td><span class="badge bg-<?php echo e($roleColors[$employee->employeeRole->name] ?? 'primary'); ?>"><?php echo e($employee->employeeRole->name ?? '—'); ?></span></td>
                                <td><?php echo e($employee->school->name ?? '—'); ?></td>
                                <td><?php echo e($employee->department ?? '—'); ?></td>
                                <td><span class="badge bg-<?php echo e($employee->status == 'active' ? 'success' : 'danger'); ?>"><?php echo e($employee->status == 'active' ? 'فعال' : 'غیرفعال'); ?></span></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="6" class="text-center text-muted py-3">کارمندی یافت نشد.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-dark fw-bold"><tr><td colspan="6">مجموع کارمندان: <?php echo e(number_format($employees->total())); ?> نفر</td></tr></tfoot>
                </table>
            </div>
            <div class="mt-2"><?php echo e($employees->links()); ?></div>
        </div>

        
        <div class="tab-pane fade" id="students-pane" role="tabpanel">
            <form method="GET" action="<?php echo e(route('admin.dashboard')); ?>" class="row g-2 mb-3">
                <input type="hidden" name="tab" value="students">
                <div class="col-md-2">
                    <select name="school_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">همه مکاتب</option>
                        <?php $__currentLoopData = $allSchools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sch): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($sch->id); ?>" <?php echo e(request('school_filter') == $sch->id ? 'selected' : ''); ?>><?php echo e($sch->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="student_status" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">همه وضعیت‌ها</option>
                        <?php $__currentLoopData = $studentStatuses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $status): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($status); ?>" <?php echo e(request('student_status') == $status ? 'selected' : ''); ?>>
                                <?php switch($status):
                                    case ('present'): ?> حاضر <?php break; ?>
                                    <?php case ('blocked'): ?> محروم <?php break; ?>
                                    <?php case ('temporary'): ?> موقت <?php break; ?>
                                    <?php case ('three_piece'): ?> سه‌پارچه <?php break; ?>
                                    <?php default: ?> <?php echo e($status); ?>

                                <?php endswitch; ?>
                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="student_financial" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">همه وضعیت مالی</option>
                        <option value="discount" <?php echo e(request('student_financial') == 'discount' ? 'selected' : ''); ?>>دارای تخفیف</option>
                        <option value="free" <?php echo e(request('student_financial') == 'free' ? 'selected' : ''); ?>>رایگان</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <input type="text" name="student_search" class="form-control form-control-sm" placeholder="جستجوی نام یا کد..." value="<?php echo e(request('student_search')); ?>">
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
                    <a href="<?php echo e(route('admin.dashboard', ['tab' => 'students'])); ?>" class="btn btn-sm btn-secondary">حذف فیلتر</a>
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
                        <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $totalFee = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                                $totalPaid = $student->payments->sum('amount');
                                $debt = $totalFee - $totalPaid;
                                $hasDiscount = \App\Models\StudentFee::where('student_id', $student->id)->where('discount', '>', 0)->exists();
                                $isFree = $totalFee == 0;
                            ?>
                            <tr>
                                <td><small><?php echo e($student->student_code); ?></small></td>
                                <td><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                                <td><?php echo e($student->school->name ?? '—'); ?></td>
                                <td><?php echo e($student->class ?? '—'); ?></td>
                                <td>
                                    <?php switch($student->status):
                                        case ('present'): ?> <span class="badge bg-success">حاضر</span> <?php break; ?>
                                        <?php case ('blocked'): ?> <span class="badge bg-danger">محروم</span> <?php break; ?>
                                        <?php case ('temporary'): ?> <span class="badge bg-warning text-dark">موقت</span> <?php break; ?>
                                        <?php case ('three_piece'): ?> <span class="badge bg-info">سه‌پارچه</span> <?php break; ?>
                                        <?php default: ?> <span class="badge bg-secondary"><?php echo e($student->status); ?></span>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <?php if($isFree): ?>
                                        <span class="badge bg-info">رایگان</span>
                                    <?php elseif($hasDiscount): ?>
                                        <span class="badge bg-success">تخفیف‌دار</span>
                                    <?php else: ?>
                                        <span class="badge bg-primary">شهریه کامل</span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo e(number_format($totalFee)); ?></td>
                                <td><?php echo e(number_format($totalPaid)); ?></td>
                                <td class="<?php echo e($debt > 0 ? 'text-danger' : 'text-success'); ?>"><?php echo e(number_format($debt)); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="9" class="text-center text-muted py-3">دانش‌آموزی یافت نشد.</td></tr>
                        <?php endif; ?>
                    </tbody>
                    <tfoot class="table-dark fw-bold">
                        <tr>
                            <td colspan="6">مجموع (<?php echo e($studentCount); ?> دانش‌آموز)</td>
                            <td><?php echo e(number_format($studentTotalFee)); ?> ؋</td>
                            <td><?php echo e(number_format($studentTotalPaid)); ?> ؋</td>
                            <td><?php echo e(number_format($studentTotalDebt)); ?> ؋</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            <div class="mt-2"><?php echo e($students->links()); ?></div>
        </div>
    </div>

    
    <div class="mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-light"><h6><i class="fas fa-history ms-2"></i> آخرین فعالیت‌ها</h6></div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $recentActivities; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><small><?php echo e($activity->created_at->format('H:i')); ?></small></td>
                                <td><?php echo e($activity->description); ?></td>
                                <td><?php echo e($activity->user->name ?? '—'); ?></td>
                                <td><?php echo e($activity->school->name ?? '—'); ?></td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="4" class="text-muted text-center">فعالیتی ثبت نشده است.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    const schools = <?php echo json_encode($schools->items(), 15, 512) ?>;
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
<?php /**PATH E:\school_finance_db\resources\views\admin\dashboard.blade.php ENDPATH**/ ?>