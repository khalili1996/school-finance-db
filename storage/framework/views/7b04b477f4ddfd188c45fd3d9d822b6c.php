<?php $__env->startSection('title', 'ثبت نوع هزینه جدید'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.fee-types.index')); ?>">انواع هزینه‌ها</a></li>
            <li class="breadcrumb-item active">ثبت جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle ms-2"></i> فرم ثبت نوع هزینه</h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
            <?php endif; ?>

            <form action="<?php echo e(route('school.fee-types.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نام <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">دسته‌بندی <span class="text-danger">*</span></label>
                        <select name="category" class="form-select" required>
                            <option value="">-- انتخاب --</option>
                            <option value="tuition" <?php echo e(old('category') == 'tuition' ? 'selected' : ''); ?>>شهریه</option>
                            <option value="one_time" <?php echo e(old('category') == 'one_time' ? 'selected' : ''); ?>>یک‌باره</option>
                            <option value="other" <?php echo e(old('category') == 'other' ? 'selected' : ''); ?>>سایر</option>
                        </select>
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" class="form-control" rows="2"><?php echo e(old('description')); ?></textarea>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_optional" value="1" class="form-check-input" id="is_optional" <?php echo e(old('is_optional') ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="is_optional">اختیاری (می‌تواند از طرف دانش‌آموز انتخاب نشود)</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active" <?php echo e(old('is_active', '1') == '1' ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="is_active">فعال</label>
                        </div>
                    </div>
                </div>
                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ذخیره</button>
                <a href="<?php echo e(route('school.fee-types.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\fee-types\create.blade.php ENDPATH**/ ?>