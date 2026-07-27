<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رسید پرداخت قسط</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A5; margin: 8mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #fff; margin: 0; padding: 10px; display: flex; justify-content: center; }
        .receipt-container { width: 148mm; min-height: 210mm; background: #fff; box-shadow: 0 0 15px rgba(0,0,0,0.15); border-radius: 6px; padding: 10mm; }
        .btn-print { background: #2c3e50; color: #fff; padding: 4mm 10mm; border: none; cursor: pointer; font-weight: bold; margin-bottom: 10mm; }
        .header { background: #2c3e50; color: #fff; padding: 6mm; border-radius: 4px; text-align: center; margin-bottom: 8mm; }
        .bismillah { font-size: 20px; }
        .info-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 8mm; font-size: 12px; border: 1px solid #adb5bd; border-radius: 6px; overflow: hidden; }
        .info-table td { border: 1px solid #adb5bd; padding: 3mm 4mm; text-align: right; }
        .info-table .label { background: #f8f9fa; font-weight: 600; width: 35%; }
        .signature { margin-top: 10mm; display: flex; justify-content: space-between; }
        .signature div { text-align: center; }
        .line { border-top: 1px solid #333; width: 150px; display: inline-block; }
        .footer { text-align: center; margin-top: 10mm; font-size: 9px; color: #888; border-top: 1px solid #ccc; padding-top: 4mm; }
        @media print { .btn-print { display: none; } .receipt-container { box-shadow: none; } }
    </style>
</head>
<body>
<div class="receipt-container">
    <button class="btn-print" onclick="window.print()">چاپ / PDF</button>
    <div class="header">
        <div class="bismillah">بسمه تعالی</div>
        <div><?php echo e($installment->loan->loan_provider ?? $installment->loan->school->name ?? 'صندوق قرض‌الحسنه'); ?></div>
        <h4>رسید پرداخت قسط وام</h4>
    </div>

    <table class="info-table">
        <tr><td class="label">قرض‌گیرنده</td><td><?php echo e($installment->loan->borrower_name); ?> <?php echo e($installment->loan->borrower_last_name); ?></td></tr>
        <tr><td class="label">شماره قسط</td><td><?php echo e($installment->loan->installments->search($installment) + 1); ?> از <?php echo e($installment->loan->duration_months); ?></td></tr>
        <tr><td class="label">مبلغ پرداختی</td><td><?php echo e(number_format($installment->paid_amount)); ?> ؋</td></tr>
        <tr><td class="label">تاریخ پرداخت</td><td><?php echo e(\App\Helpers\JalaliHelper::toJalali($installment->paid_date)); ?></td></tr>
        <tr><td class="label">صندوق</td><td><?php echo e($installment->cashbox->name ?? '—'); ?></td></tr>
    </table>

    <div class="signature">
        <div>
            <span class="line"></span>
            <p>امضاء پرداخت‌کننده</p>
        </div>
        <div>
            <span class="line"></span>
            <p>امضاء مسئول صندوق</p>
        </div>
    </div>

    <div class="footer">تاریخ چاپ: <?php echo e(\App\Helpers\JalaliHelper::todayJalali()); ?></div>
</div>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\loans\installment-receipt.blade.php ENDPATH**/ ?>