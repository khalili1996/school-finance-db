<?php $__env->startSection('title', 'ویرایش شهریه'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.student-fees.index')); ?>">شهریه‌ها</a></li>
            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark"><h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش شهریه</h5></div>
        <div class="card-body">
            <?php if($errors->any()): ?> <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div> <?php endif; ?>
            <form action="<?php echo e(route('school.student-fees.update', $studentFee->id)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">دانش‌آموز <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($student->id); ?>" <?php echo e(old('student_id', $studentFee->student_id) == $student->id ? 'selected' : ''); ?>><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?> (<?php echo e($student->student_code); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نوع هزینه <span class="text-danger">*</span></label>
                        <select name="fee_type_id" class="form-select" required>
                            <?php $__currentLoopData = $feeTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feeType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($feeType->id); ?>" <?php echo e(old('fee_type_id', $studentFee->fee_type_id) == $feeType->id ? 'selected' : ''); ?>><?php echo e($feeType->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ماه (اختیاری)</label>
                        <select name="month_id" class="form-select">
                            <option value="">-- بدون ماه (کلی) --</option>
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($month->id); ?>" <?php echo e(old('month_id', $studentFee->month_id) == $month->id ? 'selected' : ''); ?>><?php echo e($month->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3"><label class="form-label">مبلغ <span class="text-danger">*</span></label><input type="number" name="amount" class="form-control" value="<?php echo e(old('amount', $studentFee->amount)); ?>" min="0" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">تخفیف</label><input type="number" name="discount" class="form-control" value="<?php echo e(old('discount', $studentFee->discount)); ?>" min="0"></div>
                    <div class="col-12 mb-3"><label class="form-label">توضیحات</label><textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes', $studentFee->notes)); ?></textarea></div>
                </div>
                <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                <a href="<?php echo e(route('school.student-fees.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\student-fees\edit.blade.php ENDPATH**/ ?>