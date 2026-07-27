<?php $__env->startSection('title', 'ویرایش پرداخت قسط'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.loans.index')); ?>">قرض‌الحسنه</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.loans.installments', $installment->loan)); ?>">اقساط</a></li>
            <li class="breadcrumb-item active">ویرایش پرداخت</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش پرداخت قسط</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('school.installments.update', $installment)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ پرداختی (حداکثر <?php echo e(number_format($installment->amount)); ?>)</label>
                        <input type="number" name="paid_amount" class="form-control <?php $__errorArgs = ['paid_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('paid_amount', $installment->paid_amount)); ?>" min="1" max="<?php echo e($installment->amount); ?>" required>
                        <?php $__errorArgs = ['paid_amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ پرداخت (شمسی)</label>
                        <input type="text" name="paid_date" class="form-control <?php $__errorArgs = ['paid_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('paid_date', $installment->paid_date)); ?>" required>
                        <?php $__errorArgs = ['paid_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">صندوق</label>
                        <select name="cashbox_id" class="form-select <?php $__errorArgs = ['cashbox_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">-- انتخاب --</option>
                            <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cb->id); ?>" <?php echo e((old('cashbox_id', $installment->cashbox_id) == $cb->id) ? 'selected' : ''); ?>>
                                    <?php echo e($cb->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['cashbox_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?><div class="invalid-feedback"><?php echo e($message); ?></div><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">یادداشت</label>
                    <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes', $installment->notes)); ?></textarea>
                </div>
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> ذخیره تغییرات</button>
                <a href="<?php echo e(route('school.loans.installments', $installment->loan)); ?>" class="btn btn-secondary">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\loans\installment-edit.blade.php ENDPATH**/ ?>