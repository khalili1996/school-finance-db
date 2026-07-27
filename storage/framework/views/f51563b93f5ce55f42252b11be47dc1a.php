<?php $__env->startSection('title', 'صنف‌بندی دانش‌آموزان'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-layer-group ms-2"></i> صنف‌بندی دانش‌آموزان</h1>
        <a href="<?php echo e(route('school.students.report')); ?>" class="btn btn-outline-primary btn-sm">
            <i class="fas fa-print"></i> چاپ گزارش کل مکتب
        </a>
    </div>

    
    <form method="GET" action="<?php echo e(route('school.students.index')); ?>" class="row g-2 mb-4">
        <input type="hidden" name="filter" value="senfi">
        <div class="col-auto">
            <select name="class_filter" class="form-select form-select-sm" onchange="this.form.submit()">
                <option value="">همه‌ی صنف‌ها</option>
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $className): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($className); ?>" <?php echo e(request('class_filter') == $className ? 'selected' : ''); ?>><?php echo e($className); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary btn-sm">اعمال</button>
            <a href="<?php echo e(route('school.students.index', ['filter' => 'senfi'])); ?>" class="btn btn-secondary btn-sm">حذف فیلتر</a>
        </div>
    </form>

    <?php $__empty_1 = true; $__currentLoopData = $studentsByClass; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $className => $students): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card mb-3 shadow-sm">
            <div class="card-header bg-light d-flex justify-content-between align-items-center">
                <span class="fw-bold"><?php echo e($className ?: 'بدون صنف'); ?> (<?php echo e($students->count()); ?> دانش‌آموز)</span>
                <a href="<?php echo e(route('school.students.report', ['class_filter' => $className])); ?>" class="btn btn-sm btn-outline-secondary">
                    <i class="fas fa-print"></i> چاپ
                </a>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-hover mb-0">
                    <thead><tr><th>کد</th><th>نام</th><th>نام پدر</th><th>وضعیت</th></tr></thead>
                    <tbody>
                        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e($student->student_code); ?></td>
                                <td><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                                <td><?php echo e($student->father_name); ?></td>
                                <td>
                                    <?php switch($student->status):
                                        case ('present'): ?> <span class="badge bg-success">حاضر</span> <?php break; ?>
                                        <?php case ('blocked'): ?> <span class="badge bg-danger">محروم</span> <?php break; ?>
                                        <?php case ('temporary'): ?> <span class="badge bg-warning text-dark">موقت</span> <?php break; ?>
                                        <?php case ('three_piece'): ?> <span class="badge bg-info">سه‌پارچه</span> <?php break; ?>
                                        <?php default: ?> <span class="badge bg-secondary"><?php echo e($student->status); ?></span>
                                    <?php endswitch; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <p class="text-muted">دانش‌آموزی یافت نشد.</p>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\students\by-class.blade.php ENDPATH**/ ?>