<?php $__env->startSection('title', 'انواع هزینه‌ها'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tags ms-2"></i> انواع هزینه‌ها</h1>
        <a href="<?php echo e(route('school.fee-types.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> ثبت نوع هزینه جدید
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>نام</th>
                        <th>دسته‌بندی</th>
                        <th>اختیاری</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $feeTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feeType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($feeType->name); ?></td>
                            <td>
                                <?php switch($feeType->category):
                                    case ('tuition'): ?> <span class="badge bg-primary">شهریه</span> <?php break; ?>
                                    <?php case ('one_time'): ?> <span class="badge bg-info">یک‌باره</span> <?php break; ?>
                                    <?php case ('other'): ?> <span class="badge bg-secondary">سایر</span> <?php break; ?>
                                <?php endswitch; ?>
                            </td>
                            <td><?php echo e($feeType->is_optional ? 'بله' : 'خیر'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($feeType->is_active ? 'success' : 'danger'); ?>">
                                    <?php echo e($feeType->is_active ? 'فعال' : 'غیرفعال'); ?>

                                </span>
                            </td>
                            <td>
                                <a href="<?php echo e(route('school.fee-types.edit', $feeType->id)); ?>" class="btn btn-sm btn-warning">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="<?php echo e(route('school.fee-types.destroy', $feeType->id)); ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('آیا از حذف این نوع هزینه اطمینان دارید؟')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">هیچ نوع هزینه‌ای ثبت نشده است.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\fee-types\index.blade.php ENDPATH**/ ?>