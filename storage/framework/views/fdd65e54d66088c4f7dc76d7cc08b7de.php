<?php $__env->startSection('title', 'ویرایش پرداخت معاش'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h4 class="mb-3">ویرایش پرداخت معاش</h4>

    <?php if($errors->any()): ?>
        <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
    <?php endif; ?>

    <form action="<?php echo e(route('school.salary-payments.update', $salaryPayment->id)); ?>" method="POST">
        <?php echo csrf_field(); ?>
        <?php echo method_field('PUT'); ?>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">کارمند</label>
                <input type="text" class="form-control" value="<?php echo e($salaryPayment->employee->full_name ?? ''); ?>" disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">ماه</label>
                <input type="text" class="form-control" value="<?php echo e($salaryPayment->salary->month->name ?? ''); ?>" disabled>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">مبلغ (افغانی) <span class="text-danger">*</span></label>
                <input type="number" name="amount" class="form-control" value="<?php echo e(old('amount', $salaryPayment->amount)); ?>" min="1" required>
                <small class="text-muted">حداکثر مجاز: <?php echo e(number_format($remaining)); ?> ؋</small>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">تاریخ</label>
                <input type="text" name="payment_date" class="form-control" value="<?php echo e(old('payment_date', $salaryPayment->payment_date)); ?>" required>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">صندوق <span class="text-danger">*</span></label>
                <select name="cashbox_id" class="form-select" required>
                    <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($cb->id); ?>" <?php echo e(old('cashbox_id', $salaryPayment->cashbox_id) == $cb->id ? 'selected' : ''); ?>><?php echo e($cb->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label class="form-label">شماره رسید</label>
                <input type="text" name="receipt_number" class="form-control" value="<?php echo e(old('receipt_number', $salaryPayment->receipt_number)); ?>">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">توضیحات</label>
                <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes', $salaryPayment->notes)); ?></textarea>
            </div>
        </div>
        <button type="submit" class="btn btn-primary">به‌روزرسانی</button>
        <a href="<?php echo e(route('school.salaries.index')); ?>" class="btn btn-secondary">انصراف</a>
    </form>
</div>
<?php $__env->stopSection(); ?>


<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\salary_payments\edit.blade.php ENDPATH**/ ?>