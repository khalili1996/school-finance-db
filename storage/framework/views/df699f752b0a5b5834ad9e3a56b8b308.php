<?php $__env->startSection('title', 'ایجاد کاربر جدید'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-user-plus ms-2"></i> ایجاد کاربر جدید</h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <form method="POST" action="<?php echo e(route('school.users.store')); ?>">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?php echo e(old('name')); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ایمیل <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?php echo e(old('email')); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">رمز عبور <span class="text-danger">*</span></label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">شماره تماس</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone')); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نقش <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <option value="">-- انتخاب کنید --</option>
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role->name); ?>" <?php echo e(old('role') == $role->name ? 'selected' : ''); ?>>
                                    <?php echo e($role->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> ذخیره کاربر
                </button>
                <a href="<?php echo e(route('school.users.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\users\create.blade.php ENDPATH**/ ?>