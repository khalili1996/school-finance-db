<?php $__env->startSection('title', 'گزارش دفتر کل'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-book ms-2"></i> گزارش دفتر کل</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    
    <?php echo $__env->make('partials.report-header', ['title' => 'دفتر کل', 'subtitle' => ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-2">
            <input type="text" name="from_date" class="form-control form-control-sm datepicker" placeholder="از تاریخ (شمسی)" value="<?php echo e($fromDate); ?>">
        </div>
        <div class="col-md-2">
            <input type="text" name="to_date" class="form-control form-control-sm datepicker" placeholder="تا تاریخ (شمسی)" value="<?php echo e($toDate); ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال فیلتر</button>
            <a href="<?php echo e(route('school.reports.financial.ledger')); ?>" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <h6>جمع کل درآمد</h6>
                    <h4 class="fw-bold text-success"><?php echo e(number_format($totalIncome)); ?> ؋</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger shadow-sm">
                <div class="card-body text-center">
                    <h6>جمع کل مصرف</h6>
                    <h4 class="fw-bold text-danger"><?php echo e(number_format($totalExpense)); ?> ؋</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center">
                    <h6>تراز</h6>
                    <h4 class="fw-bold text-info"><?php echo e(number_format($balance)); ?> ؋</h4>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow mb-5">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-arrow-down me-1"></i> لیست درآمدها</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>شرح</th>
                        <th>مبلغ (افغانی)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $incomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $income): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($incomes->firstItem() + $loop->index); ?></td>
                        <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($income->entry_date)); ?></td>
                        <td><?php echo e($income->description); ?></td>
                        <td><?php echo e(number_format($income->debit)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">درآمدی با این فیلترها یافت نشد.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            <?php echo e($incomes->appends(request()->except('income_page'))->links()); ?>

        </div>
    </div>

    
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-arrow-up me-1"></i> لیست مصارف</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>شرح</th>
                        <th>مبلغ (افغانی)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($expenses->firstItem() + $loop->index); ?></td>
                        <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($expense->entry_date)); ?></td>
                        <td><?php echo e($expense->description); ?></td>
                        <td><?php echo e(number_format($expense->credit)); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="4" class="text-center text-muted py-4">مصرفی با این فیلترها یافت نشد.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            <?php echo e($expenses->appends(request()->except('expense_page'))->links()); ?>

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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\reports\financial-ledger.blade.php ENDPATH**/ ?>