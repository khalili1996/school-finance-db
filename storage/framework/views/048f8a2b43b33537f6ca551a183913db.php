<?php $__env->startSection('title', 'راه‌اندازی سال مالی جدید'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    
    <div class="text-center mb-4">
        <?php if($logo): ?>
            <img src="<?php echo e(asset('storage/' . $logo)); ?>" alt="لوگو" height="80">
        <?php endif; ?>
        <h4 class="mt-2"><?php echo e($schoolName); ?></h4>
        <h5 class="text-primary">سال مالی جدید: <?php echo e($newYear->name); ?></h5>
    </div>

    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-play-circle me-1"></i> راه‌اندازی سال مالی جدید</h5>
        </div>
        <div class="card-body">
            <form action="<?php echo e(route('school.setup-year.start')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <input type="hidden" name="new_year_id" value="<?php echo e($newYear->id); ?>">

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="transfer_students" id="transferStudents" value="1" checked>
                        <label class="form-check-label" for="transferStudents">
                            <strong>انتقال همه دانش‌آموزان</strong>
                        </label>
                    </div>
                    <small class="text-muted">در صورت انتخاب، تمام دانش‌آموزان سال قبل به‌صورت کپی در سال جدید ایجاد می‌شوند و پایهٔ تحصیلی آن‌ها یک کلاس بالاتر می‌رود.</small>
                </div>

                <hr>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="transfer_employees" id="transferEmployees" value="1" checked>
                        <label class="form-check-label" for="transferEmployees">
                            <strong>انتقال همه کارمندان</strong>
                        </label>
                    </div>
                    <small class="text-muted">در صورت انتخاب، تمام کارمندان سال قبل در سال جدید کپی می‌شوند (بدون تغییر در سمت و حقوق).</small>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-rocket"></i> شروع انتقال
                    </button>
                    <a href="<?php echo e(route('school.dashboard')); ?>" class="btn btn-secondary btn-lg">بعداً انجام می‌دهم</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\setup-year.blade.php ENDPATH**/ ?>