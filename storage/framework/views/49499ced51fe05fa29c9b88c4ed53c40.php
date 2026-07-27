<?php $__env->startSection('title', 'گزارش صندوق'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-cash-register ms-2"></i> گزارش صندوق</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    
    <?php echo $__env->make('partials.report-header', ['title' => 'گزارش صندوق', 'subtitle' => ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="row mb-4">
        <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $box): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="col-md-3 mb-3">
            <div class="card border-<?php echo e($box->type == 'bank' ? 'primary' : 'success'); ?> shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-<?php echo e($box->type == 'bank' ? 'university' : 'money-bill-wave'); ?> fa-2x text-<?php echo e($box->type == 'bank' ? 'primary' : 'success'); ?> mb-2"></i>
                    <h6><?php echo e($box->name); ?></h6>
                    <h4 class="fw-bold"><?php echo e(number_format($box->current_balance)); ?> ؋</h4>
                    <small class="text-muted"><?php echo e($box->type == 'bank' ? 'بانکی' : 'نقدی'); ?></small>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>

    
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-2">
            <input type="text" name="from_date" class="form-control form-control-sm datepicker" placeholder="از تاریخ (شمسی)" value="<?php echo e($fromDate); ?>">
        </div>
        <div class="col-md-2">
            <input type="text" name="to_date" class="form-control form-control-sm datepicker" placeholder="تا تاریخ (شمسی)" value="<?php echo e($toDate); ?>">
        </div>
        <div class="col-md-2">
            <select name="type_filter" class="form-select form-select-sm">
                <option value="">همه انواع</option>
                <option value="deposit" <?php echo e(request('type_filter') == 'deposit' ? 'selected' : ''); ?>>واریز</option>
                <option value="withdrawal" <?php echo e(request('type_filter') == 'withdrawal' ? 'selected' : ''); ?>>برداشت</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال فیلتر</button>
            <a href="<?php echo e(route('school.reports.financial.cashboxes')); ?>" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    
    <div class="card shadow">
        <div class="card-header bg-secondary text-white">
            <h5 class="mb-0"><i class="fas fa-exchange-alt me-1"></i> تراکنش‌های صندوق</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>صندوق</th>
                        <th>نوع</th>
                        <th>مبلغ (افغانی)</th>
                        <th>شرح</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($transactions->firstItem() + $loop->index); ?></td>
                        <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($tr->transaction_date)); ?></td>
                        <td><?php echo e($tr->cashbox->name ?? '—'); ?></td>
                        <td>
                            <?php if($tr->type == 'deposit'): ?>
                                <span class="badge bg-success">واریز</span>
                            <?php else: ?>
                                <span class="badge bg-danger">برداشت</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e(number_format($tr->amount)); ?></td>
                        <td><?php echo e($tr->description ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-4">تراکنشی با این فیلترها یافت نشد.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            <?php echo e($transactions->links()); ?>

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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\reports\financial-cashboxes.blade.php ENDPATH**/ ?>