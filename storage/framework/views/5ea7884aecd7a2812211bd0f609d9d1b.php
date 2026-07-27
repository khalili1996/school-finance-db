<?php $__env->startSection('title', 'ویرایش صندوق'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">✏️ ویرایش صندوق: <?php echo e($cashbox->name); ?></h4>
        <a href="<?php echo e(route('school.cashboxes.index')); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="<?php echo e(route('school.cashboxes.update', $cashbox)); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <?php echo method_field('PUT'); ?>

                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <label for="name" class="form-label">نام صندوق <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="name"
                               class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('name', $cashbox->name)); ?>" required>
                        <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-md-6">
                        <label for="type" class="form-label">نوع صندوق <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="cash" <?php echo e(old('type', $cashbox->type) == 'cash' ? 'selected' : ''); ?>>نقدی</option>
                            <option value="bank" <?php echo e(old('type', $cashbox->type) == 'bank' ? 'selected' : ''); ?>>بانکی</option>
                        </select>
                        <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-md-6">
                        <label for="initial_balance" class="form-label">موجودی اولیه (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="initial_balance" id="initial_balance"
                               class="form-control <?php $__errorArgs = ['initial_balance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('initial_balance', $cashbox->initial_balance)); ?>" min="0" required>
                        <?php $__errorArgs = ['initial_balance'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>

                    
                    <div class="col-md-6">
                        <label for="is_active" class="form-label">وضعیت</label>
                        <select name="is_active" id="is_active" class="form-select">
                            <option value="1" <?php echo e(old('is_active', $cashbox->is_active) == 1 ? 'selected' : ''); ?>>فعال</option>
                            <option value="0" <?php echo e(old('is_active', $cashbox->is_active) == 0 ? 'selected' : ''); ?>>غیرفعال</option>
                        </select>
                    </div>

                    
                    <div class="col-12">
                        <label for="notes" class="form-label">توضیحات</label>
                        <textarea name="notes" id="notes" rows="3"
                                  class="form-control <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('notes', $cashbox->notes)); ?></textarea>
                        <?php $__errorArgs = ['notes'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                            <div class="invalid-feedback"><?php echo e($message); ?></div>
                        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>

                
                <div class="mt-4 text-start">
                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save me-1"></i> بروزرسانی صندوق
                    </button>
                    <a href="<?php echo e(route('school.cashboxes.index')); ?>" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times me-1"></i> انصراف
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\cashboxes\edit.blade.php ENDPATH**/ ?>