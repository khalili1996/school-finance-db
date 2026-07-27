<?php $__env->startSection('title', 'سطل زباله دانش‌آموزان'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h1><i class="fas fa-trash ms-2"></i> دانش‌آموزان حذف‌شده</h1>
    <table class="table table-hover">
        <thead><tr><th>کد</th><th>نام</th><th>تاریخ حذف</th><th>عملیات</th></tr></thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr>
                <td><?php echo e($student->student_code); ?></td>
                <td><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                <td><?php echo e($student->deleted_at->format('Y/m/d H:i')); ?></td>
                <td>
                    <form action="<?php echo e(route('school.students.restore', $student->id)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <button class="btn btn-sm btn-success"><i class="fas fa-undo"></i> بازیابی</button>
                    </form>
                    <form action="<?php echo e(route('school.students.force-delete', $student->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('کاملاً حذف شود؟')">
                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                        <button class="btn btn-sm btn-danger"><i class="fas fa-times"></i> حذف دائمی</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr><td colspan="4" class="text-muted">سطل زباله خالی است</td></tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\students\trash.blade.php ENDPATH**/ ?>