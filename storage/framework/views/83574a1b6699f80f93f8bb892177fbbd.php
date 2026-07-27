<?php $__env->startSection('title', 'سطل زباله کارمندان'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.employees.index')); ?>">کارمندان</a></li>
            <li class="breadcrumb-item active">سطل زباله</li>
        </ol>
    </nav>

    <h1><i class="fas fa-trash ms-2"></i> کارمندان حذف‌شده</h1>

    <div class="card shadow mt-3">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>کد</th>
                        <th>نام</th>
                        <th>سمت</th>
                        <th>تاریخ حذف</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($emp->employee_code); ?></td>
                            <td><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></td>
                            <td><?php echo e($emp->employeeRole->name ?? '—'); ?></td>
                            <td><?php echo e($emp->deleted_at->format('Y/m/d H:i')); ?></td>
                            <td>
                                
                                <form action="<?php echo e(route('school.employees.restore', $emp->id)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <button type="submit" class="btn btn-sm btn-success" title="بازیابی">
                                        <i class="fas fa-undo"></i>
                                    </button>
                                </form>
                                
                                <form action="<?php echo e(route('school.employees.force-delete', $emp->id)); ?>" method="POST" class="d-inline"
                                      onsubmit="return confirm('آیا از حذف دائمی این کارمند اطمینان دارید؟')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-danger" title="حذف دائمی">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">سطل زباله خالی است.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer"><?php echo e($employees->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\employees\trash.blade.php ENDPATH**/ ?>