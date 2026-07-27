<?php $__env->startSection('title', 'پروفایل کارمند'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.employees.index')); ?>">کارمندان</a></li>
            <li class="breadcrumb-item active"><?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-id-card ms-2"></i> اطلاعات کارمند</h5>
                    <span class="badge bg-light text-dark"><?php echo e($employee->employee_code); ?></span>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th style="width:180px;">نام کامل</th><td><?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?></td></tr>
                        <tr><th>نام پدر</th><td><?php echo e($employee->father_name); ?></td></tr>
                        <tr><th>پدرکلان</th><td><?php echo e($employee->grandfather_name ?? '—'); ?></td></tr>
                        <tr><th>شماره تذکره</th><td><?php echo e($employee->national_id ?? '—'); ?></td></tr>
                        
                        <tr><th>تاریخ تولد</th><td><?php echo e($employee->birth_date ? \App\Helpers\JalaliHelper::toJalali($employee->birth_date) : '—'); ?></td></tr>
                        <tr><th>جنسیت</th><td><?php echo e(($employee->gender ?? 'male') == 'male' ? 'مذکر' : 'اناث'); ?></td></tr>
                        <tr><th>شماره تماس</th><td><?php echo e($employee->phone ?? '—'); ?></td></tr>
                        <tr><th>شماره تماس دوم</th><td><?php echo e($employee->secondary_phone ?? '—'); ?></td></tr>
                        <tr><th>آدرس</th><td><?php echo e($employee->address ?? '—'); ?></td></tr>
                        <tr><th>سمت</th><td><?php echo e($employee->employeeRole->name ?? '—'); ?></td></tr>
                        <tr><th>بخش</th><td><?php echo e($employee->department ?? '—'); ?></td></tr>
                        
                        <tr><th>تاریخ استخدام</th><td><?php echo e($employee->hire_date ? \App\Helpers\JalaliHelper::toJalali($employee->hire_date) : '—'); ?></td></tr>
                        <tr><th>نوع قرارداد</th><td><?php echo e($employee->contract_type == 'permanent' ? 'دایمی' : 'موقت'); ?></td></tr>
                        <tr><th>معاش پایه</th><td><?php echo e(number_format($employee->base_salary)); ?> ؋</td></tr>
                        <tr><th>وضعیت</th><td><span class="badge bg-<?php echo e($employee->status == 'active' ? 'success' : 'danger'); ?>"><?php echo e($employee->status == 'active' ? 'فعال' : 'غیرفعال'); ?></span></td></tr>
                    </table>
                    <a href="<?php echo e(route('school.employees.edit', $employee->id)); ?>" class="btn btn-warning"><i class="fas fa-edit"></i> ویرایش</a>
                    <a href="<?php echo e(route('school.employees.preview', $employee->id)); ?>" class="btn btn-outline-secondary"><i class="fas fa-print"></i> پیش‌نمایش چاپ</a>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie ms-2"></i> خلاصه مالی</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2"><strong>معاش پایه (ماهانه):</strong> <span class="float-end"><?php echo e(number_format($employee->base_salary)); ?> ؋</span></div>
                    <div class="mb-2"><strong>مجموع اضافه‌کاری:</strong> <span class="float-end"><?php echo e(number_format($overtimeAmount)); ?> ؋</span></div>
                    <div class="mb-2"><strong>مجموع پاداش:</strong> <span class="float-end"><?php echo e(number_format($bonusAmount)); ?> ؋</span></div>
                    <div class="mb-2"><strong>مجموع کسورات:</strong> <span class="float-end text-danger"><?php echo e(number_format($deductionAmount)); ?> ؋</span></div>
                    <div class="mb-2"><strong>مجموع مالیات:</strong> <span class="float-end text-danger"><?php echo e(number_format($taxAmount)); ?> ؋</span></div>
                    <hr>
                    <div class="mb-2"><strong>کل معاش (تعهدی):</strong> <span class="float-end"><?php echo e(number_format($totalAmount)); ?> ؋</span></div>
                    <div class="mb-2"><strong>پرداخت‌شده:</strong> <span class="float-end text-success"><?php echo e(number_format($paidAmount)); ?> ؋</span></div>
                    <div class="mb-2"><strong>باقی‌مانده:</strong> <span class="float-end <?php echo e($balance > 0 ? 'text-danger' : 'text-success'); ?>"><?php echo e(number_format($balance)); ?> ؋</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\employees\show.blade.php ENDPATH**/ ?>