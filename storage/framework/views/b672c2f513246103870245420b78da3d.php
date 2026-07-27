<?php $__env->startSection('title', 'پرداخت معاش'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.salaries.index')); ?>">معاشات</a></li>
            <li class="breadcrumb-item active">پرداخت</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-money-bill-wave ms-2"></i> پرداخت معاش</h5>
        </div>
        <div class="card-body">
            
            <div class="alert alert-secondary">
                <strong>کارمند:</strong> <?php echo e($salary->employee->first_name); ?> <?php echo e($salary->employee->last_name); ?><br>
                <strong>ماه:</strong> <?php echo e($salary->month->name ?? '—'); ?><br>
                <strong>مبلغ کل معاش:</strong> <?php echo e(number_format($salary->total_amount)); ?> ؋<br>
                <strong>پرداخت شده تاکنون:</strong> <?php echo e(number_format($salary->paid_amount)); ?> ؋<br>
                <strong>مانده قابل پرداخت:</strong> <?php echo e(number_format($remaining)); ?> ؋
            </div>

            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
            <?php endif; ?>

            <form action="<?php echo e(route('school.salary-payments.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="salary_id" value="<?php echo e($salary->id); ?>">

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ پرداختی (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" value="<?php echo e(old('amount', $remaining)); ?>" min="1" max="<?php echo e($remaining); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ پرداخت <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control" value="<?php echo e(old('payment_date', now()->toDateString())); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">صندوق <span class="text-danger">*</span></label>
                        <select name="cashbox_id" class="form-select" required>
                            <option value="">-- انتخاب کنید --</option>
                            <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cb->id); ?>" <?php echo e(old('cashbox_id') == $cb->id ? 'selected' : ''); ?>>
                                    <?php echo e($cb->name); ?> (<?php echo e($cb->type === 'bank' ? 'بانکی' : 'نقدی'); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره رسید</label>
                        <input type="text" name="receipt_number" class="form-control" value="<?php echo e(old('receipt_number')); ?>">
                    </div>
                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes')); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-check-circle"></i> ثبت پرداخت</button>
                <a href="<?php echo e(route('school.salaries.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\salary_payments\create.blade.php ENDPATH**/ ?>