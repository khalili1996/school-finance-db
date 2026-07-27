<?php $__env->startSection('title', 'ویرایش دسته‌بندی'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h4>ویرایش دسته‌بندی</h4>
    <form action="<?php echo e(route('school.asset-categories.update', $assetCategory)); ?>" method="POST">
        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">نام دسته <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" value="<?php echo e(old('name', $assetCategory->name)); ?>" required>
                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>
        <button type="submit" class="btn btn-warning">به‌روزرسانی</button>
        <a href="<?php echo e(route('school.asset-categories.index')); ?>" class="btn btn-secondary">انصراف</a>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\asset-categories\edit.blade.php ENDPATH**/ ?>