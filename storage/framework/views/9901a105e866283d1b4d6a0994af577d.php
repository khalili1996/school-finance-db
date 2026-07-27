<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش فاکتورها</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4 portrait; margin: 10mm; }
        body {
            font-family: 'Vazir', Tahoma, sans-serif;
            background: #e9ecef;
            margin: 0;
            padding: 10px;
        }
        .report-container {
            background: #fff;
            padding: 10mm;
            box-shadow: 0 0 10px rgba(0,0,0,.1);
        }
        .filter-form {
            text-align: center;
            margin-bottom: 15px;
        }
        .btn-print {
            background: #2c3e50;
            color: #fff;
            padding: 8px 14px;
            border: none;
            cursor: pointer;
            font-family: 'Vazir';
            text-decoration: none;
            display: inline-block;
        }
        .invoice-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            justify-content: flex-start;
        }
        .invoice-card {
            width: 280px;
            border: 1px solid #333;
            border-radius: 6px;
            padding: 8px;
            background: #fff;
            page-break-inside: avoid;
        }
        .invoice-info {
            font-size: 12px;
            line-height: 2;
            margin-bottom: 8px;
        }
        .invoice-info div {
            border-bottom: 1px solid #eee;
            padding: 3px 0;
        }
        .label { font-weight: bold; }
        .invoice-image {
            display: block;
            width: 100%;
            height: auto;
            max-height: 250px;
            object-fit: contain;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        .no-image {
            text-align: center;
            padding: 25px;
            border: 1px dashed #ccc;
            color: #777;
        }
        @media print {
            .btn-print, .filter-form button, .filter-form select { display: none; }
            body { background: #fff; padding: 0; margin: 0; }
            .report-container { box-shadow: none; padding: 0; }
            .invoice-grid { gap: 8px; }
            .invoice-card { width: 250px; }
        }
    </style>
</head>
<body>
<div class="report-container">

    
    <?php
        $subtitle = $selectedMonth ? 'ماه: ' . $selectedMonth->name : 'همه';
    ?>
    <?php echo $__env->make('partials.report-header', [
        'title' => 'گزارش فاکتورها',
        'subtitle' => $subtitle
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <div class="filter-form">
        <form method="GET" action="<?php echo e(route('school.invoices.report')); ?>">
            <label>ماه:</label>
            <select name="month_id" onchange="this.form.submit()">
                <option value="">همه</option>
                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($month->id); ?>" <?php echo e(request('month_id') == $month->id ? 'selected' : ''); ?>>
                        <?php echo e($month->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
            <button type="submit" class="btn-print">اعمال</button>
            <button type="button" class="btn-print" onclick="window.print()">چاپ / PDF</button>
            <a href="<?php echo e(route('school.invoices.index')); ?>" class="btn-print" style="background:#777;">بازگشت</a>
        </form>
    </div>

    <div class="invoice-grid">
        <?php $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <div class="invoice-card">
                <div class="invoice-info">
                    <div><span class="label">شماره فاکتور:</span> <?php echo e($expense->invoice_number ?? '—'); ?></div>
                    <div><span class="label">عنوان هزینه:</span> <?php echo e($expense->title); ?></div>
                    <div><span class="label">تاریخ:</span> <?php echo e($expense->expense_date); ?></div>
                    <div><span class="label">ماه:</span> <?php echo e($expense->month->name ?? '—'); ?></div>
                </div>
                <?php if($expense->scan_file): ?>
                    <img src="<?php echo e(asset('storage/'.$expense->scan_file)); ?>" class="invoice-image" alt="فاکتور">
                <?php else: ?>
                    <div class="no-image">فایلی ثبت نشده است</div>
                <?php endif; ?>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
</div>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\invoices\report.blade.php ENDPATH**/ ?>