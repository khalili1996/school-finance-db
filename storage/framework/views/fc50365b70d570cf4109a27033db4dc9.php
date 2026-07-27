<?php $__env->startSection('title', 'ایجاد سال مالی جدید'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.academic-years.index')); ?>">سال‌های مالی</a></li>
            <li class="breadcrumb-item active">ایجاد جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle ms-2"></i> فرم ایجاد سال مالی</h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
            <?php endif; ?>

            <form action="<?php echo e(route('school.academic-years.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نام <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
    <label class="form-label">تاریخ شروع <span class="text-danger">*</span></label>
    <input type="text" name="start_date" class="form-control" placeholder="مثال: 1405/01/01"
           value="<?php echo e(old('start_date')); ?>" required>
</div>
<div class="col-md-4 mb-3">
    <label class="form-label">تاریخ پایان <span class="text-danger">*</span></label>
    <input type="text" name="end_date" class="form-control" placeholder="مثال: 1405/12/29"
           value="<?php echo e(old('end_date')); ?>" required>
</div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">وضعیت فعال</label>
                        <select name="is_active" class="form-select">
                            <option value="0" <?php echo e(old('is_active') == '0' ? 'selected' : ''); ?>>غیرفعال</option>
                            <option value="1" <?php echo e(old('is_active') == '1' ? 'selected' : ''); ?>>فعال</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ثبت</button>
                <a href="<?php echo e(route('school.academic-years.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\academic-years\create.blade.php ENDPATH**/ ?>