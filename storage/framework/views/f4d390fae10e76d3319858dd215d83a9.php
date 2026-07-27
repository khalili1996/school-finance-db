<?php $__env->startSection('title', 'گزارش قرض‌الحسنه'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-hand-holding-heart ms-2"></i> گزارش قرض‌الحسنه</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    
    <?php echo $__env->make('partials.report-header', ['title' => 'گزارش قرض‌الحسنه', 'subtitle' => ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-primary shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-piggy-bank fa-2x text-primary mb-2"></i>
                    <h6>موجودی اولیه (کل واریزی‌ها)</h6>
                    <h4 class="fw-bold"><?php echo e(number_format($totalDeposits)); ?> ؋</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-danger shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-hand-holding-usd fa-2x text-danger mb-2"></i>
                    <h6>کل وام‌های پرداختی</h6>
                    <h4 class="fw-bold"><?php echo e(number_format($totalLoansGiven)); ?> ؋</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-undo-alt fa-2x text-success mb-2"></i>
                    <h6>اقساط بازپرداختی</h6>
                    <h4 class="fw-bold"><?php echo e(number_format($totalRepayments)); ?> ؋</h4>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-wallet fa-2x text-info mb-2"></i>
                    <h6>موجودی فعلی صندوق</h6>
                    <h4 class="fw-bold"><?php echo e(number_format($fundBalance)); ?> ؋</h4>
                </div>
            </div>
        </div>
    </div>

    
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">همه وضعیت‌ها</option>
                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>فعال</option>
                <option value="completed" <?php echo e(request('status') == 'completed' ? 'selected' : ''); ?>>تسویه شده</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
            <a href="<?php echo e(route('school.reports.loans')); ?>" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-list me-1"></i> وام‌های قرض‌الحسنه</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>قرض‌گیرنده</th>
                        <th>مبلغ (افغانی)</th>
                        <th>اقساط</th>
                        <th>تاریخ شروع</th>
                        <th>وضعیت</th>
                        <th>جزئیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loans->firstItem() + $loop->index); ?></td>
                        <td>
                            <?php if($loan->employee): ?>
                                <?php echo e($loan->employee->first_name); ?> <?php echo e($loan->employee->last_name); ?>

                            <?php else: ?>
                                <?php echo e($loan->borrower_name); ?> <?php echo e($loan->borrower_last_name); ?>

                            <?php endif; ?>
                        </td>
                        <td><?php echo e(number_format($loan->amount)); ?></td>
                        <td><?php echo e($loan->installments->where('status', 'paid')->count()); ?>/<?php echo e($loan->duration_months); ?></td>
                        <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($loan->start_date)); ?></td>
                        <td>
                            <?php if($loan->status == 'completed'): ?>
                                <span class="badge bg-success">تسویه شده</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">فعال</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="<?php echo e(route('school.loans.installments', $loan)); ?>" class="btn btn-sm btn-outline-info">
                                <i class="fas fa-eye"></i> اقساط
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-4">وام قرض‌الحسنه‌ای با این شرایط یافت نشد.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            <?php echo e($loans->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
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
        .card {
            border: 1px solid #ddd !important;
            box-shadow: none !important;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\reports\loans.blade.php ENDPATH**/ ?>