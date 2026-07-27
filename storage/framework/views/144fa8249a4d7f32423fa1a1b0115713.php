<?php $__env->startSection('title', 'لیست پرداخت‌ها'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-hand-holding-usd ms-2"></i> مدیریت پرداخت‌ها</h1>

        <a href="<?php echo e(route('school.payments.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> ثبت پرداخت جدید
        </a>
        <form action="<?php echo e(route('school.payments.sync-to-ledger')); ?>" method="POST" class="d-inline">
    <?php echo csrf_field(); ?>
    <button type="submit" class="btn btn-outline-warning btn-sm" title="همگام‌سازی پرداخت‌های قدیمی با دفتر کل">
        <i class="fas fa-sync-alt me-1"></i> همگام‌سازی دفتر کل
    </button>
</form>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <form method="GET" action="<?php echo e(route('school.payments.index')); ?>" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="جستجوی نام، کد دانش‌آموز یا شماره رسید..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> جستجو</button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('school.payments.index')); ?>" class="btn btn-secondary">حذف جستجو</a>
            <?php endif; ?>
        </div>
    </form>

    
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>شماره رسید</th>
                            <th>دانش‌آموز</th>
                            <th>مبلغ</th>
                            <th>تاریخ</th>
                            <th>روش پرداخت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <tr>
                                <td><?php echo e($payment->receipt_number ?? $payment->id); ?></td>
                                <td><?php echo e($payment->student->first_name ?? '—'); ?> <?php echo e($payment->student->last_name ?? ''); ?></td>
                                <td><?php echo e(number_format($payment->amount)); ?> ؋</td>
                                
                                <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($payment->payment_date)); ?></td>
                                <td>
                                    <?php switch($payment->payment_method):
                                        case ('cash'): ?> <span class="badge bg-success">نقدی</span> <?php break; ?>
                                        <?php case ('bank'): ?> <span class="badge bg-info">بانکی</span> <?php break; ?>
                                        <?php default: ?> <span class="badge bg-secondary">سایر</span>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="<?php echo e(route('school.payments.receipt', $payment->id)); ?>" class="btn btn-outline-secondary" title="رسید چاپی">
                                            <i class="fas fa-print"></i>
                                        </a>
                                        <a href="<?php echo e(route('school.payments.edit', $payment->id)); ?>" class="btn btn-outline-warning" title="ویرایش">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="<?php echo e(route('school.payments.destroy', $payment->id)); ?>" method="POST" class="d-inline"
                                              onsubmit="return confirm('آیا از حذف این پرداخت اطمینان دارید؟')">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                    هیچ پرداختی ثبت نشده است.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($payments->hasPages()): ?>
            <div class="card-footer">
                <?php echo e($payments->links()); ?>

            </div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\payments\index.blade.php ENDPATH**/ ?>