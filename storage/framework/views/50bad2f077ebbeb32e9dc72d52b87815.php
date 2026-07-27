<?php $__env->startSection('title', 'گزارش اولیا'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <h1><i class="fas fa-chart-bar ms-2"></i> گزارش اولیا</h1>
    <div class="row mt-4">
        <div class="col-md-3 mb-3"><div class="card bg-primary text-white"><div class="card-body"><h5>کل اولیا</h5><h2><?php echo e($totalGuardians); ?></h2></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-info text-white"><div class="card-body"><h5>پدران</h5><h2><?php echo e($totalFathers); ?></h2></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-success text-white"><div class="card-body"><h5>مادران</h5><h2><?php echo e($totalMothers); ?></h2></div></div></div>
        <div class="col-md-3 mb-3"><div class="card bg-secondary text-white"><div class="card-body"><h5>سایر</h5><h2><?php echo e($totalOthers); ?></h2></div></div></div>
    </div>
    <div class="row">
        <div class="col-md-4 mb-3"><div class="card bg-danger text-white"><div class="card-body"><h5>خانواده‌های بدهکار</h5><h2><?php echo e($debtorFamilies); ?></h2></div></div></div>
        <div class="col-md-4 mb-3"><div class="card bg-warning text-dark"><div class="card-body"><h5>دارای تخفیف</h5><h2><?php echo e($discountFamilies); ?></h2></div></div></div>
        <div class="col-md-4 mb-3"><div class="card bg-dark text-white"><div class="card-body"><h5>دارای یتیم</h5><h2><?php echo e($orphanFamilies); ?></h2></div></div></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\guardians\report.blade.php ENDPATH**/ ?>