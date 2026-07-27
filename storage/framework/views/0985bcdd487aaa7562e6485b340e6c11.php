<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش چاپی تجهیزات مکتب</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body {
            font-family: 'Vazir', Tahoma, sans-serif;
            background: #fff; margin: 0; padding: 10px; direction: rtl; color: #333;
        }
        .btn-print { background: #2c3e50; color: #fff; padding: 6px 18px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 10px; }
        @media print { .btn-print { display: none; } }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 11px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #555; }
    </style>
</head>
<body>
<button class="btn-print" onclick="window.print()">🖨️ چاپ گزارش</button>


<?php echo $__env->make('partials.report-header', [
    'title' => 'گزارش تجهیزات',
    'subtitle' => $selectedCategory ? 'دسته‌بندی: ' . $selectedCategory->name : ''
], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

<table>
    <thead>
        <tr>
            <th>کد اموال</th>
            <th>دسته‌بندی</th>
            <th>شرح</th>
            <th>تعداد</th>
            <th>تحویل‌گیرنده</th>
            <th>قیمت واحد</th>
            <th>قیمت کل</th>
            <th>تاریخ خرید</th>
            <th>وضعیت</th>
            <th>توضیحات</th>
        </tr>
    </thead>
    <tbody>
        <?php $__empty_1 = true; $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr>
            <td><?php echo e($asset->asset_code); ?></td>
            <td><?php echo e($asset->category->name ?? '—'); ?></td>
            <td><?php echo e($asset->description); ?></td>
            <td><?php echo e($asset->quantity); ?></td>
            <td><?php echo e($asset->custodian ?? '—'); ?></td>
            <td><?php echo e(number_format($asset->unit_price)); ?></td>
            <td><?php echo e(number_format($asset->total_price)); ?></td>
            <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($asset->purchase_date)); ?></td>
            <td>
                <?php switch($asset->status):
                    case ('active'): ?> فعال <?php break; ?>
                    <?php case ('transferred'): ?> انتقال <?php break; ?>
                    <?php case ('broken'): ?> خراب <?php break; ?>
                    <?php case ('scrap'): ?> اسقاط <?php break; ?>
                    <?php default: ?> <?php echo e($asset->status); ?>

                <?php endswitch; ?>
            </td>
            <td><?php echo e($asset->notes ?? '—'); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr>
            <td colspan="10" class="text-center text-muted py-4">تجهیزاتی با این فیلترها یافت نشد.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>

<div class="footer">
    تاریخ چاپ: <?php echo e(\App\Helpers\JalaliHelper::todayJalali()); ?> | سامانه مدیریت مالی الزهرا (س)
</div>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\assets\print.blade.php ENDPATH**/ ?>