<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش دانش‌آموزان – <?php echo e($selectedClass ?? 'کل مکتب'); ?></title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        body { font-family: 'Vazir', Tahoma, sans-serif; direction: rtl; padding: 20px; }
        .header { text-align: center; margin-bottom: 20px; }
        .header h2 { margin: 0; }
        .header h4 { margin: 5px 0; color: #555; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #333; padding: 6px; text-align: center; }
        th { background-color: #eee; }
        .no-print { text-align: center; margin-bottom: 20px; }
        .btn { padding: 8px 16px; background: #2c3e50; color: #fff; border: none; border-radius: 4px; cursor: pointer; font-family: 'Vazir'; }
        @media print {
            .no-print { display: none; }
            body { padding: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button class="btn" onclick="window.print()"><i class="fas fa-print"></i> چاپ / ذخیره PDF</button>
        <a href="javascript:history.back()" class="btn" style="background:#777;">بازگشت</a>
    </div>

    <div class="header">
        <h2>لیست دانش‌آموزان – <?php echo e($selectedClass ?? 'کل مکتب'); ?></h2>
        <h4>تعداد کل: <?php echo e($students->count()); ?> | تاریخ: <?php echo e(now()->format('Y/m/d')); ?></h4>
    </div>

    <table>
        <thead>
            <tr>
                <th>کد</th>
                <th>نام</th>
                <th>نام پدر</th>
                <th>پدرکلان</th>
                <th>نمبر اساس</th>
                <?php if(!$selectedClass): ?><th>صنف</th><?php endif; ?>
                <th>شماره تماس</th>
                <th>شهریه کل</th>
                <th>پرداختی</th>
                <th>باقی‌مانده</th>
                <th>ماه‌های پرداخت‌نشده</th>
            </tr>
        </thead>
        <tbody>
            <?php $totalFeeAll = 0; $totalPaidAll = 0; ?>
            <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    $totalFee = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                    $totalPaid = $student->payments->sum('amount');
                    $balance = $totalFee - $totalPaid;
                    $totalFeeAll += $totalFee;
                    $totalPaidAll += $totalPaid;
                ?>
                <tr>
                    <td><?php echo e($student->student_code); ?></td>
                    <td><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                    <td><?php echo e($student->father_name); ?></td>
                    <td><?php echo e($student->grandfather_name ?? '—'); ?></td>
                    <td><?php echo e($student->base_number ?? '—'); ?></td>
                    <?php if(!$selectedClass): ?><td><?php echo e($student->class ?? '—'); ?></td><?php endif; ?>
                    <td><?php echo e($student->phone ?? '—'); ?></td>
                    <td><?php echo e(number_format($totalFee)); ?></td>
                    <td><?php echo e(number_format($totalPaid)); ?></td>
                    <td><?php echo e(number_format($balance)); ?></td>
                    <td><?php echo e(implode('، ', $student->unpaidMonths) ?: '—'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
        <tfoot>
            <tr style="background:#f0f0f0; font-weight:bold;">
                <td colspan="<?php echo e($selectedClass ? '6' : '7'); ?>">مجموع</td>
                <td><?php echo e(number_format($totalFeeAll)); ?></td>
                <td><?php echo e(number_format($totalPaidAll)); ?></td>
                <td><?php echo e(number_format($totalFeeAll - $totalPaidAll)); ?></td>
                <td></td>
            </tr>
        </tfoot>
    </table>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\students\report.blade.php ENDPATH**/ ?>