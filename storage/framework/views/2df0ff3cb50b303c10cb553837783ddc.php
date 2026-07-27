<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش چاپی شهریه‌ها</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #fff; margin: 0; padding: 10px; }
        h2 { text-align: center; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .btn-print { display: inline-block; background: #2c3e50; color: #fff; padding: 4px 12px; border: none; cursor: pointer; margin-bottom: 10px; }
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body>
<button class="btn-print" onclick="window.print()">چاپ</button>
<h2>گزارش شهریه‌ها <?php echo e($classFilter ? '– صنف '.$classFilter : ''); ?></h2>
<table>
    <thead>
        <tr>
            <th>کد</th>
            <th>نام</th>
            <th>نام پدر</th>
            <th>صنف</th>
            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <th><?php echo e($month->name); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <th>مجموع تخفیف</th>
            <th>مجموع رسید شده</th>
            <th>باقی‌مانده کل</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $totalPaid = $student->payments->sum('amount');
                $totalDiscount = $student->studentFees->sum('discount');
                $totalFee  = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                $remaining = max($totalFee - $totalPaid, 0);
            ?>
            <tr>
                <td><?php echo e($student->student_code); ?></td>
                <td><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                <td><?php echo e($student->father_name); ?></td>
                <td><?php echo e($student->class ?? '—'); ?></td>
                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <?php
                        $fee  = $student->studentFees->firstWhere('month_id', $month->id);
                        $paid = $student->payments->where('month_id', $month->id)->sum('amount');
                        $text = $fee ? number_format($fee->amount - $fee->discount) . ' / ' . number_format($paid) : '—';
                    ?>
                    <td><?php echo e($text); ?></td>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                <td><?php echo e(number_format($totalDiscount)); ?></td>
                <td><?php echo e(number_format($totalPaid)); ?></td>
                <td><?php echo e(number_format($remaining)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\student-fees\print.blade.php ENDPATH**/ ?>