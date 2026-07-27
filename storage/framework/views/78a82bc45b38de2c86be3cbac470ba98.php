<?php $__env->startSection('title', 'سال‌های مالی'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-calendar-alt ms-2"></i> سال‌های مالی</h4>
        <a href="<?php echo e(route('school.academic-years.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> ایجاد سال مالی جدید
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>نام سال</th>
                        <th>تاریخ شروع</th>
                        <th>تاریخ پایان</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $year): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($year->name); ?></td>
                            <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($year->start_date, 'Y/m/d')); ?></td>
                            <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($year->end_date, 'Y/m/d')); ?></td>
                            <td>
                                <?php if($year->id == session('active_academic_year_id')): ?>
                                    <span class="badge bg-success">فعال</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">غیرفعال</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($year->id != session('active_academic_year_id')): ?>
                                    <a href="<?php echo e(route('school.set-academic-year', $year->id)); ?>" class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-check"></i> فعال‌سازی
                                    </a>
                                <?php else: ?>
                                    <span class="text-success"><i class="fas fa-check-circle"></i> سال جاری</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="6" class="text-center py-3">هیچ سال مالی ثبت نشده است.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\academic-years\index.blade.php ENDPATH**/ ?>