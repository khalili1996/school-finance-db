<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش چاپی معاشات</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #fff; margin: 0; padding: 10px; }
        h2 { text-align: center; margin-bottom: 5px; }
        .btn-print { display: inline-block; background: #2c3e50; color: #fff; padding: 4px 12px; border: none; cursor: pointer; margin-bottom: 10px; }
        @media print { .btn-print { display: none; } }

        .employee-block { margin-bottom: 20px; border: 1px solid #ccc; padding: 10px; }
        .employee-header { background: #f8f9fa; padding: 5px 10px; font-weight: bold; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .totals { font-weight: bold; background: #e9ecef; }
    </style>
</head>
<body>
<button class="btn-print" onclick="window.print()">چاپ گزارش</button>
<h2>گزارش معاشات کارمندان</h2>
<?php if(request('employee_id')): ?>
    <p>فیلتر کارمند: <?php echo e(\App\Models\Employee::find(request('employee_id'))->first_name ?? ''); ?></p>
<?php endif; ?>

<?php $__empty_1 = true; $__currentLoopData = $reportData; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <?php $emp = $data['employee']; ?>
    <div class="employee-block">
        <div class="employee-header">
            <?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?> (<?php echo e($emp->father_name ?? '—'); ?>) – سمت: <?php echo e($emp->position->name ?? $emp->position ?? '—'); ?>

        </div>
        <?php if(count($data['salaries']) > 0): ?>
            <table>
                <thead>
                    <tr>
                        <th>ماه</th>
                        <th>حقوق پایه</th>
                        <th>اضافه‌کاری</th>
                        <th>پاداش</th>
                        <th>کسورات</th>
                        <th>مالیات</th>
                        <th>ضمانت</th>
                        <th>پیش‌پرداخت</th>
                        <th>جمع کل</th>
                        <th>پرداخت شده</th>
                        <th>باقی‌مانده</th>
                        <th>تاریخ پرداخت</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $data['salaries']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sal): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td><?php echo e($sal['month']); ?></td>
                        <td><?php echo e(number_format($sal['base_salary'])); ?></td>
                        <td><?php echo e(number_format($sal['overtime_amount'])); ?></td>
                        <td><?php echo e(number_format($sal['bonus_amount'])); ?></td>
                        <td><?php echo e(number_format($sal['deduction_amount'])); ?></td>
                        <td><?php echo e(number_format($sal['tax_amount'])); ?></td>
                        <td><?php echo e(number_format($sal['guarantee_amount'])); ?></td>
                        <td><?php echo e(number_format($sal['advance_amount'])); ?></td>
                        <td><?php echo e(number_format($sal['total_amount'])); ?></td>
                        <td><?php echo e(number_format($sal['paid_amount'])); ?></td>
                        <td><?php echo e(number_format($sal['remaining'])); ?></td>
                        <td><?php echo e($sal['payment_date'] ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <tr class="totals">
                        <td colspan="7">مجموع</td>
                        <td><?php echo e(number_format($data['total_advance'])); ?></td>
                        <td><?php echo e(number_format($data['total_amount'])); ?></td>
                        <td><?php echo e(number_format($data['total_paid'])); ?></td>
                        <td><?php echo e(number_format($data['total_remaining'])); ?></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        <?php else: ?>
            <p>داده‌ای برای ماه‌های انتخاب‌شده وجود ندارد.</p>
        <?php endif; ?>
    </div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
    <p>هیچ رکوردی برای نمایش وجود ندارد.</p>
<?php endif; ?>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\salaries\print-report.blade.php ENDPATH**/ ?>