   

<?php $__env->startSection('title', 'ویرایش مدرسه'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h4><i class="fas fa-edit ms-2"></i> ویرایش مدرسه: <?php echo e($school->name); ?></h4>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger"><ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
    <?php endif; ?>

    <form action="<?php echo e(route('admin.schools.update', $school)); ?>" method="POST" enctype="multipart/form-data">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">نام مکتب <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $school->name)); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">تلفن</label>
                <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $settings['phone'])); ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">ایمیل عمومی مکتب</label>
                <input type="email" name="contact_email" class="form-control" value="<?php echo e(old('contact_email', $settings['email'])); ?>">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">آدرس</label>
                <textarea name="address" class="form-control" rows="2"><?php echo e(old('address', $settings['address'])); ?></textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">لوگوی مکتب</label>
                <input type="file" name="logo" class="form-control" accept="image/*">
                <?php if($settings['logo']): ?>
                    <div class="mt-2">
                        <img src="<?php echo e(asset('storage/' . $settings['logo'])); ?>" height="60" alt="لوگو">
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <hr>
        <h5>اطلاعات مدیر مدرسه</h5>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">ایمیل مدیر <span class="text-danger">*</span></label>
                <input type="email" name="admin_email" class="form-control"
                       value="<?php echo e(old('admin_email', $adminUser->email ?? '')); ?>" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">رمز عبور جدید (در صورت نیاز به تغییر)</label>
                <input type="password" name="admin_password" class="form-control" minlength="6">
                <small class="text-muted">اگر خالی بگذارید، رمز تغییر نمی‌کند.</small>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> ذخیره تغییرات</button>
            <a href="<?php echo e(route('admin.schools.index')); ?>" class="btn btn-secondary">انصراف</a>
        </div>
    </form>

    <hr>
    <div class="alert alert-warning">
        <strong>غیرفعال‌سازی مدرسه:</strong> با کلیک روی دکمهٔ زیر، مدرسه غیرفعال می‌شود و دیگر در لیست نمایش داده نمی‌شود.
    </div>
    <form action="<?php echo e(route('admin.schools.destroy', $school)); ?>" method="POST" onsubmit="return confirm('آیا از غیرفعال‌سازی این مدرسه اطمینان دارید؟')">
        <?php echo csrf_field(); ?>
        <?php echo method_field('DELETE'); ?>
        <button type="submit" class="btn btn-danger"><i class="fas fa-ban"></i> غیرفعال‌سازی مدرسه</button>
    </form>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\admin\schools\edit.blade.php ENDPATH**/ ?>