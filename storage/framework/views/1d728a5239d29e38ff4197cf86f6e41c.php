<?php $__env->startSection('title', 'پیش‌پرداخت‌ها (مساعده)'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    <div class="sticky-toolbar bg-white shadow-sm border-bottom px-3 py-2 mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-hand-holding-usd fa-lg text-warning ms-2"></i>
                <h5 class="fw-bold mb-0 text-dark">پیش‌پرداخت‌ها (مساعده)</h5>
            </div>
            <div class="btn-group">
                <a href="<?php echo e(route('school.employee-advances.create')); ?>" class="btn btn-success rounded-pill px-3 py-2">
                    <i class="fas fa-plus-circle ms-1"></i> ثبت پیش‌پرداخت
                </a>
            </div>
        </div>
    </div>

    <div class="px-3">
        <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

        <form method="GET" class="row g-2 mb-4">
            <div class="col-md-3">
                <label class="form-label">کارمند</label>
                <select name="employee_id" class="form-select">
                    <option value="">همه</option>
                    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php echo e(request('employee_id') == $emp->id ? 'selected' : ''); ?>>
                            <?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-primary">اعمال</button>
                <a href="<?php echo e(route('school.employee-advances.index')); ?>" class="btn btn-secondary">حذف فیلتر</a>
            </div>
        </form>

        <div class="card shadow">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>کارمند</th>
                            <th>ماه</th>
                            <th>مبلغ (افغانی)</th>
                            <th>تاریخ</th>
                            <th>توضیحات</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $advances; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $advance): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($advance->employee->first_name ?? ''); ?> <?php echo e($advance->employee->last_name ?? ''); ?></td>
                            <td><?php echo e($advance->month->name ?? '—'); ?></td>
                            <td><?php echo e(number_format($advance->amount)); ?></td>
                            <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($advance->advance_date)); ?></td>
                            <td><?php echo e($advance->notes ?? '—'); ?></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('school.employee-advances.receipt', $advance)); ?>"
                                       target="_blank" class="btn btn-outline-info" title="رسید">
                                        <i class="fa fa-receipt"></i>
                                    </a>
                                    <a href="<?php echo e(route('school.employee-advances.edit', $advance)); ?>"
                                       class="btn btn-outline-warning" title="ویرایش">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('school.employee-advances.destroy', $advance)); ?>"
                                          method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger" title="حذف"
                                           onclick="return confirm('آیا مطمئن هستید؟')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="7" class="text-center text-muted py-3">هیچ پیش‌پرداختی ثبت نشده است.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white"><?php echo e($advances->links()); ?></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .sticky-toolbar { position: sticky; top: 0; z-index: 1020; background: #fff; }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\employee-advances\index.blade.php ENDPATH**/ ?>