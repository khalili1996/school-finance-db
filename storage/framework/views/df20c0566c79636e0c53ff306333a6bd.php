<?php $__env->startSection('title', 'داشبورد ' . ($school->name ?? 'مکتب')); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold"><i class="fas fa-tachometer-alt ms-2"></i> داشبورد <?php echo e($school->name ?? 'مکتب'); ?></h5>
        <div>
            <?php if(auth()->user()->hasRole('Super Admin') && session()->has('active_school_id')): ?>
                <a href="<?php echo e(route('admin.exit-school')); ?>" class="btn btn-outline-secondary btn-sm me-2">
                    <i class="fas fa-arrow-right ms-1"></i> بازگشت به پنل مرکزی
                </a>
            <?php endif; ?>
            <form method="GET" class="d-inline">
                <select name="year_filter" class="form-select form-select-sm d-inline w-auto" onchange="this.form.submit()">
                    <option value="">همه سال‌ها</option>
                    <?php $__currentLoopData = $academicYears ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($year->id); ?>" <?php echo e(request('year_filter') == $year->id ? 'selected' : ''); ?>>سال <?php echo e($year->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </form>
        </div>
    </div>

    
    <div class="row row-cols-2 row-cols-md-3 row-cols-lg-6 g-2 mb-2">
        <div class="col"><div class="stat-card bg-primary"><div class="info"><h6>دانش‌آموزان</h6><h3><?php echo e($totalStudents ?? 0); ?></h3></div><i class="fas fa-user-graduate"></i></div></div>
        <div class="col"><div class="stat-card bg-success"><div class="info"><h6>کارمندان</h6><h3><?php echo e($totalEmployees ?? 0); ?></h3></div><i class="fas fa-user-tie"></i></div></div>
        <div class="col"><div class="stat-card bg-info"><div class="info"><h6>درآمد امروز</h6><h3><?php echo e(number_format($todayIncome ?? 0)); ?> ؋</h3></div><i class="fas fa-calendar-day"></i></div></div>
        <div class="col"><div class="stat-card bg-teal text-white"><div class="info"><h6>عواید این ماه</h6><h3><?php echo e(number_format($monthIncome ?? 0)); ?> ؋</h3></div><i class="fas fa-chart-line"></i></div></div>
        <div class="col"><div class="stat-card bg-warning text-dark"><div class="info"><h6>مصارف این ماه</h6><h3><?php echo e(number_format($monthExpenses ?? 0)); ?> ؋</h3></div><i class="fas fa-file-invoice"></i></div></div>
        <div class="col"><div class="stat-card bg-dark"><div class="info"><h6>موجودی صندوق</h6><h3><?php echo e(number_format($cashboxBalance ?? 0)); ?> ؋</h3></div><i class="fas fa-cash-register"></i></div></div>
    </div>

    
    <div class="row row-cols-2 row-cols-md-4 g-2 mb-3">
        <div class="col"><div class="stat-card bg-secondary"><div class="info"><h6>عواید کل تا امروز</h6><h3><?php echo e(number_format($totalIncomeAllTime ?? 0)); ?> ؋</h3></div><i class="fas fa-coins"></i></div></div>
        <div class="col"><div class="stat-card bg-danger"><div class="info"><h6>مصارف کل تا امروز</h6><h3><?php echo e(number_format($totalExpensesAllTime ?? 0)); ?> ؋</h3></div><i class="fas fa-file-contract"></i></div></div>
        <div class="col"><div class="stat-card bg-danger bg-opacity-75"><div class="info"><h6>بدهی شاگردان</h6><h3><?php echo e(number_format($totalDebt ?? 0)); ?> ؋</h3></div><i class="fas fa-hand-holding-usd"></i></div></div>
        <div class="col"><div class="stat-card bg-info bg-opacity-75"><div class="info"><h6>سود/زیان کل</h6><h3><?php echo e(number_format(($totalIncomeAllTime ?? 0) - ($totalExpensesAllTime ?? 0))); ?> ؋</h3></div><i class="fas fa-balance-scale"></i></div></div>
    </div>

    
    <div class="row">
        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-danger bg-opacity-10 text-danger fw-bold"><i class="fas fa-exclamation-circle ms-1"></i> شاگردان بدهکار (<?php echo e(($debtorStudents ?? collect([]))->count()); ?>)</div>
                <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                    <?php $__empty_1 = true; $__currentLoopData = $debtorStudents ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $totalFee = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                            $balance = $totalFee - ($student->total_paid ?? 0);
                        ?>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
                            <span><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></span>
                            <span class="text-danger"><?php echo e(number_format($balance)); ?> ؋</span>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-muted p-2">شاگرد بدهکاری وجود ندارد</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-success bg-opacity-10 text-success fw-bold"><i class="fas fa-receipt ms-1"></i> پرداخت‌های امروز</div>
                <div class="card-body p-0" style="max-height: 200px; overflow-y: auto;">
                    <?php $__empty_1 = true; $__currentLoopData = $todayPayments ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="list-group-item d-flex justify-content-between">
                            <span><?php echo e($payment->student->first_name ?? '—'); ?> <?php echo e($payment->student->last_name ?? ''); ?></span>
                            <span class="text-success"><?php echo e(number_format($payment->amount)); ?> ؋</span>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-muted p-2">امروز پرداختی ثبت نشده</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-md-4 mb-3">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning bg-opacity-10 text-warning fw-bold"><i class="fas fa-bell ms-1"></i> هشدارها</div>
                <div class="list-group list-group-flush">
                    <?php if(($unpaidSalaries ?? 0) > 0): ?>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
                            معاشات پرداخت‌نشده
                            <span class="badge bg-warning rounded-pill"><?php echo e($unpaidSalaries); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if(($negativeCashboxes ?? 0) > 0): ?>
                        <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between">
                            صندوق منفی
                            <span class="badge bg-dark rounded-pill"><?php echo e($negativeCashboxes); ?></span>
                        </a>
                    <?php endif; ?>
                    <?php if(($unpaidSalaries ?? 0) == 0 && ($negativeCashboxes ?? 0) == 0): ?>
                        <div class="list-group-item text-muted">موردی برای هشدار وجود ندارد</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow-sm">
        <div class="card-header bg-light"><h6><i class="fas fa-history ms-2"></i> آخرین تراکنش‌های مالی</h6></div>
        <div class="card-body p-0">
            <table class="table table-sm table-hover mb-0">
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $recentTransactions ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php if($trx instanceof \App\Models\Payment): ?>
                            <tr>
                                <td><small><?php echo e($trx->created_at->format('Y/m/d H:i')); ?></small></td>
                                <td>پرداخت شهریه</td>
                                <td><?php echo e($trx->student->first_name ?? '—'); ?> <?php echo e($trx->student->last_name ?? ''); ?></td>
                                <td class="text-success"><?php echo e(number_format($trx->amount)); ?> ؋</td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td><small><?php echo e($trx->created_at->format('Y/m/d H:i')); ?></small></td>
                                <td>دریافت <?php echo e($trx->income->title ?? '—'); ?></td>
                                <td></td>
                                <td class="text-success"><?php echo e(number_format($trx->amount)); ?> ؋</td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="4" class="text-muted text-center">تراکنشی یافت نشد</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
    .stat-card {
        border-radius: 6px;
        padding: 0.5rem 0.7rem;
        color: #fff;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.04);
    }
    .stat-card i { font-size: 1.2rem; opacity: 0.7; }
    .stat-card .info h6 { margin: 0; font-size: 0.65rem; opacity: 0.9; white-space: nowrap; }
    .stat-card .info h3 { margin: 0; font-size: 1rem; font-weight: 700; white-space: nowrap; }
    .bg-teal { background-color: #20c997; }
</style>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\dashboard.blade.php ENDPATH**/ ?>