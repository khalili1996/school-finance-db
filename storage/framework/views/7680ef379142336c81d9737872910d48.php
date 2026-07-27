<?php $__env->startSection('title', 'قرض‌الحسنه'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    
    <div class="sticky-toolbar bg-white shadow-sm border-bottom px-3 py-2 mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-hand-holding-heart fa-lg text-danger ms-2"></i>
                <h5 class="fw-bold mb-0 text-dark">مدیریت قرض‌الحسنه</h5>
            </div>
            <div class="btn-group">
                <a href="<?php echo e(route('school.loans.create')); ?>" class="btn btn-success rounded-pill px-3 py-2">
                    <i class="fas fa-plus-circle ms-1"></i> ثبت قرض‌الحسنه جدید
                </a>
            </div>
        </div>
    </div>

    <div class="px-3">
        <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

        <div class="card shadow">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>قرض‌گیرنده</th>
                            <th>مبلغ (افغانی)</th>
                            <th>اقساط</th>
                            <th>تاریخ شروع</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
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
                                <div class="btn-group btn-group-sm">
                                    
                                    <a href="<?php echo e(route('school.loans.installments', $loan)); ?>" class="btn btn-outline-success" title="اقساط">
                                        <i class="fa fa-calendar-alt"></i> اقساط
                                    </a>
                                    
                                    <a href="<?php echo e(route('school.loans.show', $loan)); ?>" target="_blank" class="btn btn-outline-info" title="پیش‌نمایش"><i class="fa fa-print"></i></a>
                                    
                                    <a href="<?php echo e(route('school.loans.edit', $loan)); ?>" class="btn btn-outline-warning" title="ویرایش"><i class="fa fa-pencil"></i></a>
                                    
                                    <form action="<?php echo e(route('school.loans.destroy', $loan)); ?>" method="POST" class="d-inline"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger" title="حذف" onclick="return confirm('آیا مطمئن هستید؟')"><i class="fa fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="text-center text-muted py-3">هیچ قرض‌الحسنه‌ای ثبت نشده است.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white"><?php echo e($loans->links()); ?></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .sticky-toolbar { position: sticky; top: 0; z-index: 1020; background: #fff; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\loans\index.blade.php ENDPATH**/ ?>