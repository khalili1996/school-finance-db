<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>پیش‌نمایش چاپ شهریه – <?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A5; margin: 8mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #fff; margin: 0; padding: 10px; direction: rtl; }
        .container { width: 100%; max-width: 148mm; margin: auto; }
        h2, h3, h4 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 11px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .paid { background: #d4edda; }
        .unpaid { background: #f8d7da; }
        .btn-print { display: inline-block; background: #2c3e50; color: #fff; padding: 4px 12px; border: none; cursor: pointer; font-family: 'Vazir'; margin-bottom: 10px; text-decoration: none; }
        .filter-form { margin-bottom: 10px; }
        .filter-form select, .filter-form .btn-sm { font-family: 'Vazir'; font-size: 12px; padding: 2px 5px; }
        @media print {
            .btn-print, .filter-form { display: none; }
        }
    </style>
</head>
<body>
<div class="container">
    <button class="btn-print" onclick="window.print()">چاپ</button>

    <h2><?php echo e($student->school->name ?? 'مکتب'); ?></h2>
    <h3>گزارش شهریه دانش‌آموز</h3>

    
    <table>
        <tr>
            <td><strong>کد:</strong> <?php echo e($student->student_code); ?></td>
            <td><strong>نام:</strong> <?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
        </tr>
        <tr>
            <td><strong>نام پدر:</strong> <?php echo e($student->father_name); ?></td>
            <td><strong>صنف:</strong> <?php echo e($student->class ?? '—'); ?></td>
        </tr>
        <tr>
            <td><strong>مجموع شهریه (قابل پرداخت):</strong> <?php echo e(number_format($totalFee)); ?> ؋</td>
            <td><strong>تخفیف کل:</strong>
                <?php echo e(number_format(
                    $selectedMonthName
                    ? $student->studentFees->where('month.name', $selectedMonthName)->sum('discount')
                    : $student->studentFees->sum('discount')
                )); ?> ؋
            </td>
        </tr>
        <tr>
            <td><strong>پرداختی کل:</strong> <?php echo e(number_format($totalPaid)); ?> ؋</td>
            <td><strong>باقی‌مانده:</strong> <?php echo e(number_format($totalRemaining)); ?> ؋</td>
        </tr>
    </table>

    
    <form method="GET" action="<?php echo e(route('school.student-fees.fee-preview', $student->id)); ?>" class="filter-form">
        <label for="monthFilter">فیلتر ماه:</label>
        <select name="month" id="monthFilter" onchange="this.form.submit()">
            <option value="">همهٔ ماه‌ها</option>
            <?php $__currentLoopData = $allMonths ?? $monthlyDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $monthName = is_array($item) ? $item['month_name'] : $item->name;
                ?>
                <option value="<?php echo e($monthName); ?>" <?php echo e(isset($selectedMonthName) && $selectedMonthName == $monthName ? 'selected' : ''); ?>>
                    <?php echo e($monthName); ?>

                </option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <a href="<?php echo e(route('school.student-fees.fee-preview', $student->id)); ?>" class="btn-sm" style="margin-right:8px;">حذف فیلتر</a>
    </form>

    <h4>وضعیت ماه‌ها</h4>
    <table>
        <thead>
            <tr>
                <th>ماه</th>
                <th>مبلغ</th>
                <th>تخفیف</th>
                <th>قابل پرداخت</th>
                <th>پرداخت شده</th>
                <th>باقی‌مانده</th>
                <th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            <?php $__empty_1 = true; $__currentLoopData = $monthlyDetails; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $detail): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
            <tr class="<?php echo e($detail['is_paid'] ? 'paid' : 'unpaid'); ?>">
                <td><?php echo e($detail['month_name']); ?></td>
                <td><?php echo e(number_format($detail['amount'])); ?></td>
                <td><?php echo e(number_format($detail['discount'])); ?></td>
                <td><?php echo e(number_format($detail['due'])); ?></td>
                <td><?php echo e(number_format($detail['paid'])); ?></td>
                <td><?php echo e(number_format($detail['remaining'])); ?></td>
                <td><?php echo e($detail['is_paid'] ? 'پرداخت' : 'بدهکار'); ?></td>
            </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
            <tr>
                <td colspan="7">داده‌ای برای این ماه وجود ندارد.</td>
            </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\student-fees\fee-preview.blade.php ENDPATH**/ ?>