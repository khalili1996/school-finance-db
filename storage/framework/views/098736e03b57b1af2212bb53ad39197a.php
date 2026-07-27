<?php $__env->startSection('title', 'ویرایش تجهیز'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.assets.index')); ?>">تجهیزات</a></li>
            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark"><h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش تجهیز</h5></div>
        <div class="card-body">
            <?php if($errors->any()): ?><div class="alert alert-danger"><ul><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div><?php endif; ?>

            <form action="<?php echo e(route('school.assets.update', $asset->id)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="row">
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">کد اموال <span class="text-danger">*</span></label>
                        <input type="text" name="asset_code" class="form-control <?php $__errorArgs = ['asset_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('asset_code', $asset->asset_code)); ?>" maxlength="30" required>
                        <?php $__errorArgs = ['asset_code'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">دسته‌بندی <span class="text-danger">*</span></label>
                        <select name="category_id" class="form-select" required>
                            <option value="">-- انتخاب --</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>" <?php echo e(old('category_id', $asset->category_id) == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-2 mb-3">
                        <label class="form-label">تعداد <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" value="<?php echo e(old('quantity', $asset->quantity)); ?>" min="1" required>
                    </div>

                    
                    <div class="col-md-2 mb-3">
                        <label class="form-label">قیمت واحد (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="unit_price" class="form-control" value="<?php echo e(old('unit_price', $asset->unit_price)); ?>" min="0" required>
                    </div>
                </div>

                <div class="row">
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">شرح تجهیزات <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" value="<?php echo e(old('description', $asset->description)); ?>" required>
                    </div>

                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">تحویل‌گیرنده / موقعیت</label>
                        <input type="text" name="custodian" class="form-control" value="<?php echo e(old('custodian', $asset->custodian)); ?>" placeholder="نام شخص یا دفتر">
                    </div>

                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">تاریخ خرید (شمسی) <span class="text-danger">*</span></label>
                        <input type="text" name="purchase_date" class="form-control"
                               value="<?php echo e(old('purchase_date', $asset->purchase_date)); ?>" required>
                    </div>
                </div>

                <div class="row">
                    
                    <div class="col-md-3 mb-3">
                        <label class="form-label">وضعیت <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" <?php echo e(old('status', $asset->status) == 'active' ? 'selected' : ''); ?>>فعال</option>
                            <option value="transferred" <?php echo e(old('status', $asset->status) == 'transferred' ? 'selected' : ''); ?>>انتقال</option>
                            <option value="broken" <?php echo e(old('status', $asset->status) == 'broken' ? 'selected' : ''); ?>>خراب</option>
                            <option value="scrap" <?php echo e(old('status', $asset->status) == 'scrap' ? 'selected' : ''); ?>>اسقاط</option>
                        </select>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">توضیحات</label>
                    <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes', $asset->notes)); ?></textarea>
                </div>

                <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                <a href="<?php echo e(route('school.assets.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\assets\edit.blade.php ENDPATH**/ ?>