<?php $__env->startSection('title', 'ثبت پیش‌پرداخت جدید'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.employee-advances.index')); ?>">پیش‌پرداخت‌ها</a></li>
            <li class="breadcrumb-item active">ثبت جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle ms-2"></i> ثبت پیش‌پرداخت</h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul>
                </div>
            <?php endif; ?>

            <form action="<?php echo e(route('school.employee-advances.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label">کارمند <span class="text-danger">*</span></label>
                        <select name="employee_id" class="form-select" required>
                            <option value="">-- انتخاب --</option>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($emp->id); ?>" <?php echo e(old('employee_id') == $emp->id ? 'selected' : ''); ?>>
                                    <?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">ماه (از معاش کدام ماه کسر شود) <span class="text-danger">*</span></label>
                        <select name="month_id" class="form-select" required>
                            <option value="">-- انتخاب --</option>
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($m->id); ?>" <?php echo e(old('month_id') == $m->id ? 'selected' : ''); ?>>
                                    <?php echo e($m->name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="amount" class="form-control"
                               value="<?php echo e(old('amount')); ?>" min="1" required>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ <span class="text-danger">*</span></label>
                        <input type="text" name="advance_date" class="form-control"
                               value="<?php echo e(old('advance_date', \App\Helpers\JalaliHelper::todayJalali())); ?>" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes')); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ذخیره</button>
                <a href="<?php echo e(route('school.employee-advances.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\employee-advances\create.blade.php ENDPATH**/ ?>