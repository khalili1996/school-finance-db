<?php $__env->startSection('title', 'ثبت تراکنش جدید'); ?>

<?php $__env->startPush('styles'); ?>
<style>
    #transfer_fields { display: none; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">💰 ثبت تراکنش صندوق</h4>
        <a href="<?php echo e(url()->previous()); ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-1"></i> بازگشت
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            <form action="<?php echo e(route('school.cashbox-transactions.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>

                <div class="row g-3">
                    
                    <div class="col-md-6">
                        <label for="type" class="form-label">نوع تراکنش <span class="text-danger">*</span></label>
                        <select name="type" id="type" class="form-select <?php $__errorArgs = ['type'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">-- انتخاب کنید --</option>
                            <option value="deposit" <?php echo e(old('type') === 'deposit' ? 'selected' : ''); ?>>واریز (افزایش موجودی)</option>
                            <option value="withdrawal" <?php echo e(old('type') === 'withdrawal' ? 'selected' : ''); ?>>برداشت (کاهش موجودی)</option>
                            <option value="transfer" <?php echo e(old('type') === 'transfer' ? 'selected' : ''); ?>>انتقال به صندوق دیگر</option>
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
                        <label for="cashbox_id" class="form-label">صندوق <span class="text-danger">*</span></label>
                        <select name="cashbox_id" id="cashbox_id" class="form-select <?php $__errorArgs = ['cashbox_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">-- انتخاب کنید --</option>
                            <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cb->id); ?>" <?php echo e((old('cashbox_id', request('cashbox_id')) == $cb->id) ? 'selected' : ''); ?>>
                                    <?php echo e($cb->name); ?> (<?php echo e($cb->type === 'bank' ? 'بانکی' : 'نقدی'); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['cashbox_id'];
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

                    
                    <div class="col-md-6" id="transfer_fields">
                        <label for="to_cashbox_id" class="form-label">صندوق مقصد <span class="text-danger">*</span></label>
                        <select name="to_cashbox_id" id="to_cashbox_id" class="form-select <?php $__errorArgs = ['to_cashbox_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>">
                            <option value="">-- انتخاب کنید --</option>
                            <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cb->id); ?>" <?php echo e(old('to_cashbox_id') == $cb->id ? 'selected' : ''); ?>>
                                    <?php echo e($cb->name); ?> (<?php echo e($cb->type === 'bank' ? 'بانکی' : 'نقدی'); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                        <?php $__errorArgs = ['to_cashbox_id'];
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
                        <label for="amount" class="form-label">مبلغ (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="amount"
                               class="form-control <?php $__errorArgs = ['amount'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('amount')); ?>" min="1" required>
                        <?php $__errorArgs = ['amount'];
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
                        <label for="transaction_date" class="form-label">تاریخ <span class="text-danger">*</span></label>
                        <input type="date" name="transaction_date" id="transaction_date"
                               class="form-control <?php $__errorArgs = ['transaction_date'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                               value="<?php echo e(old('transaction_date', date('Y-m-d'))); ?>" required>
                        <?php $__errorArgs = ['transaction_date'];
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

                    
                    <div class="col-12">
                        <label for="description" class="form-label">شرح</label>
                        <textarea name="description" id="description" rows="2"
                                  class="form-control <?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"><?php echo e(old('description')); ?></textarea>
                        <?php $__errorArgs = ['description'];
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
                        <i class="fas fa-save me-1"></i> ثبت تراکنش
                    </button>
                    <a href="<?php echo e(url()->previous()); ?>" class="btn btn-outline-secondary ms-2">
                        <i class="fas fa-times me-1"></i> انصراف
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    const typeSelect = document.getElementById('type');
    const transferFields = document.getElementById('transfer_fields');
    const toCashboxSelect = document.getElementById('to_cashbox_id');

    function toggleTransfer() {
        if (typeSelect.value === 'transfer') {
            transferFields.style.display = 'block';
            toCashboxSelect.required = true;
        } else {
            transferFields.style.display = 'none';
            toCashboxSelect.required = false;
        }
    }

    typeSelect.addEventListener('change', toggleTransfer);
    // اجرای اولیه برای حالت ویرایش/old
    document.addEventListener('DOMContentLoaded', toggleTransfer);
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\cashbox_transactions\create.blade.php ENDPATH**/ ?>