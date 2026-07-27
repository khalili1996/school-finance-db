<?php $__env->startSection('title', 'ویرایش کاربر'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-user-edit ms-2"></i> ویرایش کاربر: <?php echo e($user->name); ?></h5>
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

            <form method="POST" action="<?php echo e(route('school.users.update', $user->id)); ?>">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $user->name)); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ایمیل <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control" value="<?php echo e(old('email', $user->email)); ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">رمز عبور (در صورت نیاز به تغییر)</label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">شماره تماس</label>
                        <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $user->phone)); ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نقش <span class="text-danger">*</span></label>
                        <select name="role" class="form-select" required>
                            <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($role->name); ?>" <?php echo e($user->hasRole($role->name) ? 'selected' : ''); ?>>
                                    <?php echo e($role->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <div class="form-check mt-4">
                            <input type="checkbox" name="is_active" value="1" class="form-check-input" id="is_active"
                                   <?php echo e($user->is_active ? 'checked' : ''); ?>>
                            <label class="form-check-label" for="is_active">کاربر فعال باشد</label>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning btn-lg">
                    <i class="fas fa-save"></i> به‌روزرسانی
                </button>
                <a href="<?php echo e(route('school.users.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\users\edit.blade.php ENDPATH**/ ?>