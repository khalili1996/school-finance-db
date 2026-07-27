<?php $__env->startSection('title', 'ویرایش پرداخت'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.payments.index')); ?>">پرداخت‌ها</a></li>
            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش پرداخت</h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
            <?php endif; ?>

            <form action="<?php echo e(route('school.payments.update', $payment->id)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="row">
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">جستجوی دانش‌آموز <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" id="student_search" class="form-control"
                                   value="<?php echo e(old('student_search', $payment->student->first_name . ' ' . $payment->student->last_name . ' (' . $payment->student->student_code . ')')); ?>"
                                   autocomplete="off">
                            <input type="hidden" name="student_id" id="student_id" value="<?php echo e(old('student_id', $payment->student_id)); ?>" required>
                            <div id="student_results" class="list-group position-absolute w-100" style="z-index:1000; max-height:200px; overflow-y:auto; display:none;"></div>
                        </div>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نوع هزینه (اختیاری)</label>
                        <select name="fee_id" id="fee_id" class="form-select">
                            <option value="">-- بدون انتخاب --</option>
                            <?php $__currentLoopData = $fees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $paid = $fee->payments->sum('amount');
                                    $remaining = ($fee->amount - $fee->discount) - $paid;
                                ?>
                                <option value="<?php echo e($fee->id); ?>" <?php echo e(old('fee_id', $payment->fee_id) == $fee->id ? 'selected' : ''); ?>>
                                    <?php echo e($fee->feeType->name ?? '—'); ?> (<?php echo e($fee->month->name ?? '—'); ?>) - مانده: <?php echo e(max($remaining, 0)); ?> ؋
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control" value="<?php echo e(old('amount', $payment->amount)); ?>" min="1" required>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ پرداخت <span class="text-danger">*</span></label>
                        <input type="text" name="payment_date" class="form-control"
                               placeholder="مثال: ۱۴۰۴/۰۳/۲۴"
                               value="<?php echo e(old('payment_date', \App\Helpers\JalaliHelper::toJalali($payment->payment_date))); ?>" required>
                        <small class="form-text text-muted">تاریخ شمسی را ویرایش کنید</small>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">روش پرداخت <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash" <?php echo e(old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : ''); ?>>نقدی</option>
                            <option value="bank" <?php echo e(old('payment_method', $payment->payment_method) == 'bank' ? 'selected' : ''); ?>>بانکی</option>
                            <option value="other" <?php echo e(old('payment_method', $payment->payment_method) == 'other' ? 'selected' : ''); ?>>سایر</option>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ماه</label>
                        <select name="month_id" class="form-select">
                            <option value="">-- بدون ماه --</option>
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($month->id); ?>" <?php echo e(old('month_id', $payment->month_id) == $month->id ? 'selected' : ''); ?>><?php echo e($month->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">صندوق مقصد <span class="text-danger">*</span></label>
                        <select name="cashbox_id" class="form-select <?php $__errorArgs = ['cashbox_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" required>
                            <option value="">-- انتخاب کنید --</option>
                            <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cb->id); ?>" <?php echo e(old('cashbox_id', $payment->cashboxTransactions()->first()?->cashbox_id) == $cb->id ? 'selected' : ''); ?>>
                                    <?php echo e($cb->name); ?> (<?php echo e($cb->type === 'bank' ? 'بانکی' : 'نقدی'); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">شماره رسید</label>
                        <input type="text" name="receipt_number" class="form-control" value="<?php echo e(old('receipt_number', $payment->receipt_number)); ?>">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">توضیحات</label>
                    <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes', $payment->notes)); ?></textarea>
                </div>

                <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                <a href="<?php echo e(route('school.payments.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.getElementById('student_search').addEventListener('input', function() {
    const query = this.value.trim();
    const resultsDiv = document.getElementById('student_results');
    const hiddenInput = document.getElementById('student_id');
    const feeSelect = document.getElementById('fee_id');

    if (query.length < 2) {
        resultsDiv.style.display = 'none';
        return;
    }

    fetch(`/school/api/students/search?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(students => {
            resultsDiv.innerHTML = '';
            if (students.length === 0) {
                resultsDiv.innerHTML = '<div class="list-group-item text-muted">نتیجه‌ای یافت نشد</div>';
            } else {
                students.forEach(student => {
                    const a = document.createElement('a');
                    a.href = '#';
                    a.className = 'list-group-item list-group-item-action';
                    a.textContent = `${student.first_name} ${student.last_name} (${student.student_code})`;
                    a.addEventListener('click', function(e) {
                        e.preventDefault();
                        hiddenInput.value = student.id;
                        document.getElementById('student_search').value = `${student.first_name} ${student.last_name} (${student.student_code})`;
                        resultsDiv.style.display = 'none';

                        feeSelect.innerHTML = '<option value="">در حال بارگذاری...</option>';
                        feeSelect.disabled = true;
                        fetch(`/school/api/students/${student.id}/fees`)
                            .then(res => res.json())
                            .then(fees => {
                                feeSelect.innerHTML = '<option value="">-- بدون انتخاب --</option>';
                                fees.forEach(fee => {
                                    feeSelect.innerHTML += `<option value="${fee.id}">
                                        ${fee.fee_type} (${fee.month}) - مانده: ${fee.remaining} افغانی
                                    </option>`;
                                });
                                feeSelect.disabled = false;
                            });
                    });
                    resultsDiv.appendChild(a);
                });
            }
            resultsDiv.style.display = 'block';
        });
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('#student_search') && !e.target.closest('#student_results')) {
        document.getElementById('student_results').style.display = 'none';
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\payments\edit.blade.php ENDPATH**/ ?>