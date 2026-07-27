<?php $__env->startSection('title', 'واریز به صندوق قرض‌الحسنه'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white"><h5>واریز پول به صندوق</h5></div>
        <div class="card-body">
            <form method="POST" action="<?php echo e(route('school.loan-fund.deposit.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="mb-3">
                    <label class="form-label">مبلغ (افغانی)</label>
                    <input type="number" name="amount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">تاریخ (شمسی)</label>
                    <input type="text" name="transaction_date" class="form-control" value="<?php echo e(\App\Helpers\JalaliHelper::todayJalali()); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">شرح</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-success">ثبت واریز</button>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\loan-fund\deposit.blade.php ENDPATH**/ ?>