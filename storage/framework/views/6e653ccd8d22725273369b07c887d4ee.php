<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رسید پرداخت</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A5; margin: 8mm; }
        body {
            font-family: 'Vazir', Tahoma, sans-serif;
            background: #e9ecef; margin: 0; padding: 10px;
            display: flex; justify-content: center;
        }
        .receipt-container {
            width: 148mm; min-height: 210mm; background: #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.15); border-radius: 6px;
            padding: 10mm; box-sizing: border-box; margin: 0 auto; color: #333;
        }
        .btn-group { text-align: center; margin-bottom: 8mm; }
        .btn { padding: 4mm 10mm; margin: 0 3mm; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; text-decoration: none; display: inline-block; font-weight: bold; }
        .btn-print { background: #2c3e50; color: #fff; }

        .info-table {
            width: 100%; border-collapse: separate; border-spacing: 0;
            margin-bottom: 8mm; font-size: 12px;
            border: 1px solid #adb5bd; border-radius: 6px; overflow: hidden;
        }
        .info-table td {
            border: 1px solid #adb5bd; padding: 3mm 4mm;
            text-align: right; vertical-align: middle;
        }
        .info-table .label { background-color: #f8f9fa; font-weight: 600; width: 35%; color: #2c3e50; }
        .info-table .value { width: 65%; }

        .signature { margin-top: 10mm; display: flex; justify-content: space-between; }
        .signature div { text-align: center; }
        .signature .line { border-top: 1px solid #333; width: 150px; display: inline-block; }

        .footer { text-align: center; margin-top: 10mm; font-size: 9px; color: #888; border-top: 1px solid #ccc; padding-top: 4mm; }
        @media print {
            .btn-group { display: none; }
            body { background: #fff; margin: 0; padding: 0; }
            .receipt-container { box-shadow: none; width: 100%; min-height: auto; padding: 0; }
        }
    </style>
</head>
<body>
<div class="receipt-container">
    <div class="btn-group">
        <button class="btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> چاپ / ذخیره PDF</button>
    </div>

    
    <?php echo $__env->make('partials.report-header', [
        'title' => 'رسید پرداخت شهریه',
        'subtitle' => 'شماره رسید: ' . ($payment->receipt_number ?? $payment->id)
    ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <table class="info-table">
        <tr>
            <td class="label">شماره رسید</td>
            <td class="value"><?php echo e($payment->receipt_number ?? $payment->id); ?></td>
        </tr>
        <tr>
            <td class="label">تاریخ و ساعت</td>
            <td class="value"><?php echo \App\Helpers\JalaliHelper::toJalali($payment->payment_date); ?> | <?php echo e($payment->created_at->format('H:i')); ?></td>
        </tr>
        <tr>
            <td class="label">نام دانش‌آموز</td>
            <td class="value"><?php echo e($payment->student->first_name); ?> <?php echo e($payment->student->last_name); ?></td>
        </tr>
        <tr>
            <td class="label">نام پدر</td>
            <td class="value"><?php echo e($payment->student->father_name); ?></td>
        </tr>
        <tr>
            <td class="label">نمبر اساس</td>
            <td class="value"><?php echo e($payment->student->base_number ?? '—'); ?></td>
        </tr>
        <tr>
            <td class="label">صنف</td>
            <td class="value"><?php echo e($payment->student->class ?? '—'); ?></td>
        </tr>
        <?php if($payment->studentFee): ?>
        <tr>
            <td class="label">ماه</td>
            <td class="value"><?php echo e($payment->studentFee->month->name ?? '—'); ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <td class="label">شهریه تعیین شده</td>
            <td class="value"><?php echo e(number_format($totalFee)); ?> ؋</td>
        </tr>
        <tr>
            <td class="label">مبلغ پرداختی</td>
            <td class="value"><?php echo e(number_format($payment->amount)); ?> ؋</td>
        </tr>
        <tr>
            <td class="label">باقی‌مانده بدهی</td>
            <td class="value"><?php echo e(number_format($balance)); ?> ؋</td>
        </tr>
    </table>

    <div class="signature">
        <div>
            <span class="line"></span>
            <p>امضاء مسئول مالی</p>
        </div>
        <div>
            <span class="line"></span>
            <p>امضاء دریافت‌کننده</p>
        </div>
    </div>

    <div class="footer">
        تاریخ چاپ: <?php echo \App\Helpers\JalaliHelper::toJalali(now()); ?> | سامانه مدیریت مالی الزهرا (س)
    </div>
</div>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\payments\receipt.blade.php ENDPATH**/ ?>