<?php $__env->startSection('title', 'ثبت پرداخت جدید'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.payments.index')); ?>">پرداخت‌ها</a></li>
            <li class="breadcrumb-item active">ثبت جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle ms-2"></i> فرم ثبت پرداخت</h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
            <?php endif; ?>

            <form action="<?php echo e(route('school.payments.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row">
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">جستجوی دانش‌آموز <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <input type="text" id="student_search" class="form-control" placeholder="نام، کد یا شماره تذکره را تایپ کنید..." autocomplete="off">
                            <input type="hidden" name="student_id" id="student_id" value="<?php echo e(old('student_id')); ?>" required>
                            <div id="student_results" class="list-group position-absolute w-100" style="z-index:1000; max-height:200px; overflow-y:auto; display:none;"></div>
                        </div>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نوع هزینه (اختیاری)</label>
                        <select name="fee_id" id="fee_id" class="form-select" disabled>
                            <option value="">ابتدا دانش‌آموز را انتخاب کنید</option>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ <span class="text-danger">*</span></label>
                        <input type="number" name="amount" id="amount" class="form-control" value="<?php echo e(old('amount')); ?>" min="1" required>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ پرداخت <span class="text-danger">*</span></label>
                        <input type="text" name="payment_date" class="form-control"
                               placeholder="مثال: ۱۴۰۴/۰۳/۲۴"
                               value="<?php echo e(old('payment_date', \App\Helpers\JalaliHelper::todayJalali())); ?>" required>
                        <small class="form-text text-muted">تاریخ را به صورت شمسی وارد کنید (مثال: ۱۴۰۴/۰۳/۲۴)</small>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">روش پرداخت <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select" required>
                            <option value="cash" <?php echo e(old('payment_method') == 'cash' ? 'selected' : ''); ?>>نقدی</option>
                            <option value="bank" <?php echo e(old('payment_method') == 'bank' ? 'selected' : ''); ?>>بانکی</option>
                            <option value="other" <?php echo e(old('payment_method') == 'other' ? 'selected' : ''); ?>>سایر</option>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ماه</label>
                        <select name="month_id" class="form-select">
                            <option value="">-- بدون ماه --</option>
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($month->id); ?>" <?php echo e(old('month_id') == $month->id ? 'selected' : ''); ?>><?php echo e($month->name); ?></option>
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
                </div>

                <div class="mb-3">
                    <label class="form-label">توضیحات</label>
                    <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes')); ?></textarea>
                </div>

                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ثبت پرداخت</button>
                <a href="<?php echo e(route('school.payments.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>


<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('student_search');
    const resultsDiv = document.getElementById('student_results');
    const hiddenInput = document.getElementById('student_id');
    const feeSelect = document.getElementById('fee_id');

    if (!searchInput) {
        console.error('عنصر student_search یافت نشد!');
        return;
    }

    searchInput.addEventListener('input', function() {
        const query = this.value.trim();
        if (query.length < 2) {
            resultsDiv.style.display = 'none';
            return;
        }

        resultsDiv.innerHTML = '<div class="list-group-item text-muted">در حال جستجو...</div>';
        resultsDiv.style.display = 'block';

        fetch(`/school/api/students/search?q=${encodeURIComponent(query)}`)
            .then(response => {
                if (!response.ok) throw new Error('پاسخ شبکه اشتباه بود');
                return response.json();
            })
            .then(students => {
                resultsDiv.innerHTML = '';
                if (!Array.isArray(students) || students.length === 0) {
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
                            searchInput.value = `${student.first_name} ${student.last_name} (${student.student_code})`;
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
            })
            .catch(error => {
                console.error('خطا در fetch:', error);
                resultsDiv.innerHTML = '<div class="list-group-item text-danger">خطا در برقراری ارتباط</div>';
            });
    });

    document.addEventListener('click', function(e) {
        if (!e.target.closest('#student_search') && !e.target.closest('#student_results')) {
            resultsDiv.style.display = 'none';
        }
    });
});
</script>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\payments\create.blade.php ENDPATH**/ ?>