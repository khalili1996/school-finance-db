<?php $__env->startSection('title', 'داشبورد'); ?>

<?php $__env->startSection('content'); ?>

    <h1>خوش آمدید، <?php echo e(auth()->user()->name); ?>!</h1>
    <p>خلاصه‌ای از وضعیت کلی مکتب در سال جاری</p>
    <hr>

    <div class="row">
        <!-- دانش‌آموزان -->
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-primary shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><i class="fas fa-user-graduate"></i> دانش‌آموزان</h5>
                            <h2><?php echo e($activeStudents); ?></h2>
                        </div>
                        <i class="fas fa-users fa-3x opacity-50"></i>
                    </div>
                    <small>فعال در سال جاری</small>
                </div>
            </div>
        </div>

        <!-- کادر آموزشی -->
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-success shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><i class="fas fa-chalkboard-teacher"></i> کادر آموزشی</h5>
                            <h2><?php echo e($activeEmployees); ?></h2>
                        </div>
                        <i class="fas fa-user-tie fa-3x opacity-50"></i>
                    </div>
                    <small>معلمین + کارمندان</small>
                </div>
            </div>
        </div>

        <!-- درآمد کل -->
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-info shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><i class="fas fa-hand-holding-usd"></i> درآمد کل</h5>
                            <h2><?php echo e(number_format($totalIncome)); ?> افغانی</h2>
                        </div>
                        <i class="fas fa-chart-line fa-3x opacity-50"></i>
                    </div>
                    <small>شهریه + عواید دیگر</small>
                </div>
            </div>
        </div>

        <!-- مصارف کل -->
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-danger shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><i class="fas fa-file-invoice-dollar"></i> مصارف کل</h5>
                            <h2><?php echo e(number_format($totalExpenses)); ?> افغانی</h2>
                        </div>
                        <i class="fas fa-chart-pie fa-3x opacity-50"></i>
                    </div>
                    <small>معاشات + مصارف عمومی</small>
                </div>
            </div>
        </div>

        <!-- بدهی‌ها -->
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-warning shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><i class="fas fa-exclamation-triangle"></i> بدهی‌ها</h5>
                            <h2><?php echo e(number_format($totalDebt)); ?> افغانی</h2>
                        </div>
                        <i class="fas fa-coins fa-3x opacity-50"></i>
                    </div>
                    <small>از دانش‌آموزان</small>
                </div>
            </div>
        </div>

        <!-- صندوق -->
        <div class="col-md-4 mb-3">
            <div class="card text-white bg-secondary shadow">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h5 class="card-title"><i class="fas fa-cash-register"></i> موجودی صندوق</h5>
                            <h2><?php echo e(number_format($cashboxBalance)); ?> افغانی</h2>
                        </div>
                        <i class="fas fa-wallet fa-3x opacity-50"></i>
                    </div>
                    <small>تمام صندوق‌ها</small>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\dashboard.blade.php ENDPATH**/ ?>