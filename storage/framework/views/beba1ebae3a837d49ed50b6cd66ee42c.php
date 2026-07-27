<?php $__env->startSection('title', 'تعیین شهریه جدید'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.student-fees.index')); ?>">شهریه‌ها</a></li>
            <li class="breadcrumb-item active">تعیین جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="fas fa-plus-circle ms-2"></i> فرم تعیین شهریه</h5></div>
        <div class="card-body">
            <?php if($errors->any()): ?> <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div> <?php endif; ?>
            <form action="<?php echo e(route('school.student-fees.store')); ?>" method="POST" id="feeForm">
                <?php echo csrf_field(); ?>
                <div class="row">
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">دانش‌آموز <span class="text-danger">*</span></label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- انتخاب --</option>

                            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($student->id); ?>" <?php echo e(old('student_id') == $student->id ? 'selected' : ''); ?>><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?> (<?php echo e($student->student_code); ?>)</option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نوع هزینه <span class="text-danger">*</span></label>
                        <select name="fee_type_id" class="form-select" required>
                            <option value="">-- انتخاب --</option>
                            <?php $__currentLoopData = $feeTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $feeType): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($feeType->id); ?>" <?php echo e(old('fee_type_id') == $feeType->id ? 'selected' : ''); ?>><?php echo e($feeType->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">بازهٔ ماه</label>
                        <select name="month_preset" id="month_preset" class="form-select">
                            <option value="">-- یک ماه خاص (پایین را انتخاب کنید) --</option>
                            <option value="9_regular" <?php echo e(old('month_preset') == '9_regular' ? 'selected' : ''); ?>>۹ ماه درسی (حمل ـ قوس)</option>
                            <option value="3_winter"  <?php echo e(old('month_preset') == '3_winter' ? 'selected' : ''); ?>>۳ ماه زمستان (جدی ـ حوت)</option>
                            <option value="12_all"    <?php echo e(old('month_preset') == '12_all' ? 'selected' : ''); ?>>۱۲ ماه کامل</option>
                            <option value="custom"    <?php echo e(old('month_preset') == 'custom' ? 'selected' : ''); ?>>دلخواه (انتخاب چند ماه)</option>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3" id="single_month_box" style="<?php echo e(old('month_preset') ? 'display:none;' : ''); ?>">
                        <label class="form-label">ماه <span class="text-danger">*</span></label>
                        <select name="month_id" class="form-select">
                            <option value="">-- انتخاب ماه --</option>
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($month->id); ?>" <?php echo e(old('month_id') == $month->id ? 'selected' : ''); ?>><?php echo e($month->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-12 mb-3" id="custom_months_box" style="<?php echo e(old('month_preset') == 'custom' ? '' : 'display:none;'); ?>">
                        <label class="form-label">ماه‌های مورد نظر</label>
                        <div class="row">
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="col-md-3 form-check">
                                    <input class="form-check-input" type="checkbox" name="month_ids[]" value="<?php echo e($month->id); ?>"
                                        id="month_<?php echo e($month->id); ?>"
                                        <?php echo e(in_array($month->id, old('month_ids', [])) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="month_<?php echo e($month->id); ?>"><?php echo e($month->name); ?></label>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" value="<?php echo e(old('amount')); ?>" min="0" required>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تخفیف</label>
                        <input type="number" name="discount" class="form-control" value="<?php echo e(old('discount', 0)); ?>" min="0">
                    </div>

                    
                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes')); ?></textarea>
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ذخیره</button>
                <a href="<?php echo e(route('school.student-fees.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const presetSelect = document.getElementById('month_preset');
    const singleBox = document.getElementById('single_month_box');
    const customBox = document.getElementById('custom_months_box');

    function toggleMonthInputs() {
        const value = presetSelect.value;
        if (value === 'custom') {
            singleBox.style.display = 'none';
            customBox.style.display = 'block';
        } else if (value === '') {
            singleBox.style.display = 'block';
            customBox.style.display = 'none';
        } else {
            // بازه‌های 3، 9، 12
            singleBox.style.display = 'none';
            customBox.style.display = 'none';
        }
    }

    presetSelect.addEventListener('change', toggleMonthInputs);
    // اجرای اولیه برای تنظیم حالت بارگذاری شده با old()
    toggleMonthInputs();
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\student-fees\create.blade.php ENDPATH**/ ?>