<?php $__env->startSection('title', 'صندوق قرض‌الحسنه'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-piggy-bank ms-2 text-success"></i> صندوق قرض‌الحسنه</h4>
        <a href="<?php echo e(route('school.loan-fund.deposit')); ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> واریز به صندوق
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white"><div class="card-body">
                <h5>موجودی فعلی</h5>
                <h2><?php echo e(number_format($balance)); ?> ؋</h2>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white"><div class="card-body">
                <h5>کل واریزی‌ها</h5>
                <h2><?php echo e(number_format($totalDeposits + $totalRepayments)); ?> ؋</h2>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white"><div class="card-body">
                <h5>کل برداشت‌ها (وام‌ها)</h5>
                <h2><?php echo e(number_format($totalWithdrawals)); ?> ؋</h2>
            </div></div>
        </div>
    </div>

    <h5>تراکنش‌های اخیر</h5>
    <table class="table table-bordered">
        <thead><tr><th>تاریخ</th><th>نوع</th><th>مبلغ</th><th>شرح</th></tr></thead>
        <tbody>
            <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($tr->transaction_date)); ?></td>
                <td>
                    <?php if($tr->type == 'deposit'): ?>
                        <span class="badge bg-primary">واریز دستی</span>
                    <?php elseif($tr->type == 'withdrawal_loan'): ?>
                        <span class="badge bg-danger">پرداخت وام</span>
                    <?php else: ?>
                        <span class="badge bg-success">پرداخت قسط</span>
                    <?php endif; ?>
                </td>
                <td><?php echo e(number_format($tr->amount)); ?></td>
                <td><?php echo e($tr->description); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php echo e($transactions->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\loan-fund\index.blade.php ENDPATH**/ ?>