<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش عواید</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #e9ecef; margin: 0; padding: 10px; }
        .report-container { max-width: 100%; background: #fff; padding: 15mm; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .filter-form { margin-bottom: 15mm; text-align: center; }
        .filter-form select, .filter-form button { padding: 5px 10px; font-family: 'Vazir'; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .summary { margin-top: 10mm; text-align: right; font-weight: bold; }
        .btn-print { background: #2c3e50; color: #fff; padding: 8px 16px; border: none; cursor: pointer; font-family: 'Vazir'; text-decoration: none; display: inline-block; }
        @media print {
            .btn-print, .filter-form button, .filter-form select { display: none; }
            body { background: #fff; margin: 0; padding: 0; }
            .report-container { box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>
<div class="report-container">
    
    <?php
        $subtitle = $selectedMonth ? 'ماه: ' . $selectedMonth->name : '';
    ?>
    <?php echo $__env->make('partials.report-header', [
        'title' => 'گزارش عواید',
        'subtitle' => $subtitle
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="filter-form">
        <form method="GET" action="<?php echo e(route('school.incomes.report')); ?>">
            <label>ماه:</label>
            <select name="month_id">
                <option value="">همه</option>
                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($month->id); ?>" <?php echo e(request('month_id') == $month->id ? 'selected' : ''); ?>><?php echo e($month->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <label>دسته‌بندی:</label>
            <select name="category_id">
                <option value="">همه</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>>
                        <?php echo e($cat->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <button type="submit" class="btn-print" style="display:inline-block;">اعمال</button>
            <button type="button" class="btn-print" onclick="window.print()" style="display:inline-block; margin-right:5px;">چاپ / PDF</button>
            <a href="<?php echo e(route('school.incomes.index')); ?>" class="btn-print" style="display:inline-block; background:#777;">بازگشت</a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ردیف</th>
                <th>عنوان</th>
                <th>دسته‌بندی</th>
                <th>مبلغ کل</th>
                <th>دریافتی</th>
                <th>باقی‌مانده</th>
                <th>تاریخ</th>
                <th>ماه</th>
                <th>منبع</th>
                <th>وضعیت</th>
                <th>توضیحات</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $incomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $income): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $remaining = $income->total_amount - $income->received_amount; ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e($income->title); ?></td>
                    <td><?php echo e($income->category->name ?? '—'); ?></td>
                    <td><?php echo e(number_format($income->total_amount)); ?></td>
                    <td><?php echo e(number_format($income->received_amount)); ?></td>
                    <td><?php echo e(number_format(max($remaining, 0))); ?></td>
                    <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($income->income_date)); ?></td>
                    <td><?php echo e($income->month->name ?? '—'); ?></td>
                    <td><?php echo e($income->source ?? '—'); ?></td>
                    <td><?php echo e($income->status == 'received' ? 'دریافت کامل' : ($income->status == 'partially_received' ? 'دریافت جزئی' : 'دریافت نشده')); ?></td>
                    <td><?php echo e($income->description ?? '—'); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="summary">
        <p>تعداد رکوردها: <?php echo e($incomes->count()); ?></p>
        <p>مجموع مبالغ کل: <?php echo e(number_format($totalAmount)); ?> ؋</p>
        <p>مجموع دریافتی‌ها: <?php echo e(number_format($totalReceived)); ?> ؋</p>
        <p>مجموع باقی‌مانده: <?php echo e(number_format($totalRemaining)); ?> ؋</p>
    </div>
</div>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\incomes\report.blade.php ENDPATH**/ ?>