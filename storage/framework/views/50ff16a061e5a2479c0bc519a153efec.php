<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش مصارف</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #e9ecef; margin: 0; padding: 10px; }
        .report-container { max-width: 100%; background: #fff; padding: 15mm; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .filter-form { margin-bottom: 15mm; text-align: center; }
        .filter-form input, .filter-form select, .filter-form button { padding: 5px 10px; font-family: 'Vazir'; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .summary { margin-top: 10mm; text-align: right; font-weight: bold; }
        .btn-print { background: #2c3e50; color: #fff; padding: 8px 16px; border: none; cursor: pointer; font-family: 'Vazir'; }
        @media print {
            .btn-print, .filter-form button { display: none; }
            body { background: #fff; margin: 0; padding: 0; }
            .report-container { box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>
<div class="report-container">
    
    <?php
        $subtitle = '';
        if ($from && $to) {
            $subtitle = "از تاریخ {$from} تا {$to}";
        }
    ?>
    <?php echo $__env->make('partials.report-header', [
        'title' => 'گزارش مصارف',
        'subtitle' => $subtitle
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="filter-form">
        <form method="GET" action="<?php echo e(route('school.expenses.report')); ?>">
            <label>از تاریخ (شمسی):</label>
            <input type="text" name="date_from" value="<?php echo e($from); ?>" placeholder="مثلاً ۱۴۰۴/۰۱/۰۱">

            <label>تا تاریخ (شمسی):</label>
            <input type="text" name="date_to" value="<?php echo e($to); ?>" placeholder="مثلاً ۱۴۰۴/۰۳/۳۰">

            <label>دسته‌بندی:</label>
            <select name="category_id">
                <option value="">همه</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>>
                        <?php echo e($cat->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>

            <button type="submit" class="btn-print" style="display:inline-block;">اعمال فیلتر</button>
            <button type="button" class="btn-print" onclick="window.print()" style="display:inline-block; margin-right:5px;">چاپ / PDF</button>
            <a href="<?php echo e(route('school.expenses.index')); ?>" class="btn-print" style="display:inline-block; background:#777;">بازگشت</a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ردیف</th>
                <th>عنوان</th>
                <th>دسته‌بندی</th>
                <th>تعداد/مقدار</th>
                <th>واحد</th>
                <th>مبلغ کل</th>
                <th>پرداختی</th>
                <th>باقی‌مانده</th>
                <th>تاریخ</th>
                <th>شماره فاکتور</th>
                <th>وضعیت</th>
                <th>توضیحات</th>
            </tr>
        </thead>
        <tbody>
            <?php $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $remaining = $expense->total_amount - $expense->paid_amount; ?>
                <tr>
                    <td><?php echo e($i + 1); ?></td>
                    <td><?php echo e($expense->title); ?></td>
                    <td><?php echo e($expense->category->name ?? '—'); ?></td>
                    <td><?php echo e($expense->quantity); ?></td>
                    <td><?php echo e($expense->unit); ?></td>
                    <td><?php echo e(number_format($expense->total_amount)); ?></td>
                    <td><?php echo e(number_format($expense->paid_amount)); ?></td>
                    <td><?php echo e(number_format(max($remaining, 0))); ?></td>
                    <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($expense->expense_date)); ?></td>
                    <td><?php echo e($expense->invoice_number); ?></td>
                    <td><?php echo e($expense->status == 'paid' ? 'پرداخت کامل' : ($expense->status == 'partially_paid' ? 'پرداخت جزئی' : 'پرداخت نشده')); ?></td>
                    <td><?php echo e($expense->description); ?></td>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>

    <div class="summary">
        <p>تعداد رکوردها: <?php echo e($expenses->count()); ?></p>
        <p>مجموع مبالغ کل: <?php echo e(number_format($totalAmount)); ?> ؋</p>
        <p>مجموع پرداختی‌ها: <?php echo e(number_format($totalPaid)); ?> ؋</p>
        <p>مجموع باقی‌مانده: <?php echo e(number_format($totalRemaining)); ?> ؋</p>
    </div>
</div>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\expenses\report.blade.php ENDPATH**/ ?>