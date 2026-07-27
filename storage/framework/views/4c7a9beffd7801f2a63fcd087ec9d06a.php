<?php $__env->startSection('title', 'پشتیبان‌گیری'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h4 class="mb-4"><i class="fas fa-database ms-2"></i> پشتیبان‌گیری از پایگاه داده</h4>

    <div class="card shadow">
        <div class="card-body text-center py-5">
            <i class="fas fa-download fa-4x text-success mb-3"></i>
            <h5>دانلود فایل پشتیبان</h5>
            <p class="text-muted">با کلیک روی دکمهٔ زیر، یک نسخهٔ پشتیبان از کل اطلاعات سیستم تهیه و دانلود می‌شود.</p>
            <a href="<?php echo e(route('school.backup.download')); ?>" class="btn btn-success btn-lg px-5">
                <i class="fas fa-cloud-download-alt"></i> دریافت بکاپ
            </a>
            <?php if(session('error')): ?>
                <div class="alert alert-danger mt-3"><?php echo e(session('error')); ?></div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\backup\index.blade.php ENDPATH**/ ?>