<?php $__env->startSection('title', 'گزارشات'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h4 class="mb-4"><i class="fas fa-chart-bar ms-2"></i> گزارشات</h4>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-users fa-3x text-primary mb-3"></i>
                    <h5>گزارش دانش‌آموزان</h5>
                    <a href="<?php echo e(route('school.reports.students')); ?>" class="btn btn-primary mt-2">مشاهده</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-user-friends fa-3x text-success mb-3"></i>
                    <h5>گزارش اولیا</h5>
                    <a href="<?php echo e(route('school.reports.guardians')); ?>" class="btn btn-success mt-2">مشاهده</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-user-tie fa-3x text-warning mb-3"></i>
                    <h5>گزارش کارمندان</h5>
                    <a href="<?php echo e(route('school.reports.employees')); ?>" class="btn btn-warning mt-2">مشاهده</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-arrow-down fa-3x text-info mb-3"></i>
                    <h5>گزارش درآمدها</h5>
                    <a href="<?php echo e(route('school.reports.financial.incomes')); ?>" class="btn btn-info mt-2">مشاهده</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-arrow-up fa-3x text-danger mb-3"></i>
                    <h5>گزارش مصارف</h5>
                    <a href="<?php echo e(route('school.reports.financial.expenses')); ?>" class="btn btn-danger mt-2">مشاهده</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-cash-register fa-3x text-secondary mb-3"></i>
                    <h5>گزارش صندوق</h5>
                    <a href="<?php echo e(route('school.reports.financial.cashboxes')); ?>" class="btn btn-secondary mt-2">مشاهده</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-book fa-3x text-dark mb-3"></i>
                    <h5>گزارش دفتر کل</h5>
                    <a href="<?php echo e(route('school.reports.financial.ledger')); ?>" class="btn btn-dark mt-2">مشاهده</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-hand-holding-heart fa-3x text-pink mb-3"></i>
                    <h5>گزارش قرض‌الحسنه</h5>
                    <a href="<?php echo e(route('school.reports.loans')); ?>" class="btn btn-pink mt-2">مشاهده</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card shadow text-center">
                <div class="card-body">
                    <i class="fas fa-print fa-3x text-success mb-3"></i>
                    <h5>گزارش جامع (چاپی)</h5>
                    <a href="<?php echo e(route('school.reports.comprehensive')); ?>" target="_blank" class="btn btn-outline-success mt-2">مشاهده و چاپ</a>
                </div>
            </div>
        </div>
        
<div class="col-md-4">
    <div class="card shadow text-center">
        <div class="card-body">
            <i class="fas fa-cubes fa-3x text-teal mb-3"></i>
            <h5>گزارش تجهیزات</h5>
            <a href="<?php echo e(route('school.reports.assets')); ?>" class="btn btn-teal mt-2">مشاهده</a>
        </div>
    </div>
</div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\reports\index.blade.php ENDPATH**/ ?>