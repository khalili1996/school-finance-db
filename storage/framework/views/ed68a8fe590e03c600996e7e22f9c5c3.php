<?php $__env->startSection('title', 'مدارس غیرفعال'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h4><i class="fas fa-trash-alt ms-2"></i> مدارس غیرفعال</h4>
    <a href="<?php echo e(route('admin.schools.index')); ?>" class="btn btn-primary mb-3">
        <i class="fas fa-arrow-right"></i> بازگشت به مدارس فعال
    </a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>نام مکتب</th>
                <th>کد</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($loop->iteration); ?></td>
                <td><?php echo e($school->name); ?></td>
                <td><?php echo e($school->code); ?></td>
                <td>
                    
                    <form action="<?php echo e(route('admin.schools.restore', $school->id)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-sm btn-success" title="فعال‌سازی مجدد">
                            <i class="fas fa-undo"></i>
                        </button>
                    </form>
                    
                    <form action="<?php echo e(route('admin.schools.force-delete', $school->id)); ?>" method="POST" class="d-inline"
                          onsubmit="return confirm('آیا مطمئن هستید؟ این مدرسه برای همیشه حذف خواهد شد و قابل بازگشت نیست.')">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-danger" title="حذف دائمی">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4">هیچ مدرسه غیرفعالی وجود ندارد.</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
    <?php echo e($schools->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\admin\schools\trash.blade.php ENDPATH**/ ?>