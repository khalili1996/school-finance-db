<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?> – فرم مشخصات کارمند</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A5; margin: 6mm; }
        body {
            font-family: 'Vazir', Tahoma, sans-serif;
            background: #e9ecef; margin: 0; padding: 5px;
            display: flex; justify-content: center;
        }
        .preview-container {
            width: 148mm; background: #fff;
            box-shadow: 0 0 10px rgba(0,0,0,0.1); border-radius: 4px;
            padding: 5mm; box-sizing: border-box; margin: 0 auto; color: #333;
        }
        .btn-group { text-align: center; margin-bottom: 4mm; }
        .btn { padding: 2mm 6mm; margin: 0 2mm; border: none; border-radius: 3px; cursor: pointer; font-size: 10px; text-decoration: none; display: inline-block; font-weight: bold; }
        .btn-print { background: #2c3e50; color: #fff; }

        .header { background: #2c3e50; color: #fff; padding: 3mm; border-radius: 3px; text-align: center; margin-bottom: 4mm; }
        .bismillah { font-size: 16px; margin-bottom: 1mm; }
        .school-name { font-size: 14px; font-weight: bold; }
        .year-info { font-size: 9px; margin-top: 1mm; opacity: 0.8; }

        .form-title {
            font-size: 11px; font-weight: bold; color: #2c3e50;
            border-bottom: 1px solid #2c3e50; padding-bottom: 1mm; margin: 3mm 0 2mm;
        }

        .info-table {
            width: 100%; border-collapse: separate; border-spacing: 0;
            margin-bottom: 3mm; font-size: 9px;
            border: 1px solid #adb5bd; border-radius: 4px; overflow: hidden;
        }
        .info-table td {
            border: 1px solid #adb5bd; padding: 1mm 2mm;
            text-align: right; vertical-align: middle;
        }
        .info-table .label { background-color: #f8f9fa; font-weight: 600; width: 20%; color: #2c3e50; }
        .info-table .value { width: 30%; color: #333; }

        .finance-grid {
            display: flex; flex-wrap: wrap; gap: 2mm;
            margin: 2mm 0;
        }
        .finance-item {
            flex: 1 1 calc(25% - 2mm);
            background: #f8f9fa; border: 1px solid #adb5bd;
            border-radius: 3px; padding: 1.5mm 2mm; text-align: center;
            box-sizing: border-box;
        }
        .finance-item h4 { margin: 0 0 1mm; font-size: 7px; color: #4a5568; font-weight: 500; }
        .finance-item p { margin: 0; font-size: 10px; font-weight: bold; color: #1e3a5f; }
        .finance-item small { font-size: 6px; color: #666; display: block; }

        .footer { text-align: center; margin-top: 4mm; font-size: 7px; color: #888; border-top: 1px solid #ccc; padding-top: 2mm; }

        @media print {
            .btn-group { display: none; }
            body { background: #fff; margin: 0; padding: 0; }
            .preview-container { box-shadow: none; width: 100%; padding: 0; }
            .info-table, .finance-item { border: 1px solid #333; }
        }
    </style>
</head>
<body>
<div class="preview-container">
    <div class="btn-group">
        <button class="btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> چاپ / ذخیره PDF</button>
    </div>

    
    <?php echo $__env->make('partials.report-header', ['title' => 'فرم مشخصات کارمند', 'subtitle' => $employee->first_name . ' ' . $employee->last_name], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <div class="form-title">مشخصات فردی</div>
    <table class="info-table">
        <tr><td class="label">کد</td><td class="value"><?php echo e($employee->employee_code); ?></td>
            
            <td class="label">تاریخ تولد</td><td class="value"><?php echo e($employee->birth_date ? \App\Helpers\JalaliHelper::toJalali($employee->birth_date) : '—'); ?></td></tr>
        <tr><td class="label">نام</td><td class="value"><?php echo e($employee->first_name); ?> <?php echo e($employee->last_name); ?></td><td class="label">جنسیت</td><td class="value"><?php echo e(($employee->gender ?? 'male') == 'male' ? 'مذکر' : 'اناث'); ?></td></tr>
        <tr><td class="label">پدر</td><td class="value"><?php echo e($employee->father_name); ?></td><td class="label">تماس</td><td class="value"><?php echo e($employee->phone ?? '—'); ?></td></tr>
        <tr><td class="label">تذکره</td><td class="value"><?php echo e($employee->national_id ?? '—'); ?></td><td class="label">آدرس</td><td class="value"><?php echo e($employee->address ?? '—'); ?></td></tr>
        <tr><td class="label">تحصیل</td><td class="value"><?php echo e($employee->education_level ?? '—'); ?></td><td class="label">رشته</td><td class="value"><?php echo e($employee->field_of_study ?? '—'); ?></td></tr>
    </table>

    
    <div class="form-title">اطلاعات استخدامی</div>
    <table class="info-table">
        <tr><td class="label">سمت</td><td class="value"><?php echo e($employee->employeeRole->name ?? '—'); ?></td><td class="label">بخش</td><td class="value"><?php echo e($employee->department ?? '—'); ?></td></tr>
        
        <tr><td class="label">استخدام</td><td class="value"><?php echo e($employee->hire_date ? \App\Helpers\JalaliHelper::toJalali($employee->hire_date) : '—'); ?></td><td class="label">نوع</td><td class="value"><?php echo e($employee->contract_type == 'permanent' ? 'دایمی' : 'موقت'); ?></td></tr>
        <tr><td class="label">معاش پایه</td><td class="value"><?php echo e(number_format($employee->base_salary)); ?> ؋</td><td class="label">وضعیت</td><td class="value"><?php echo e($employee->status == 'active' ? 'فعال' : 'غیرفعال'); ?></td></tr>
    </table>

    
    <div class="form-title">مجموعه امتیازات</div>
    <div class="finance-grid">
        <div class="finance-item"><h4>امتیاز سمت</h4><p><?php echo e($employee->position_points ?? 0); ?></p></div>
        <div class="finance-item"><h4>امتیاز سابقه</h4><p><?php echo e($employee->experience_points ?? 0); ?></p></div>
        <div class="finance-item"><h4>امتیاز تحصیل</h4><p><?php echo e($employee->education_points ?? 0); ?></p></div>
        <div class="finance-item"><h4>مجموع امتیازات</h4><p><?php echo e($totalPoints); ?></p></div>
    </div>

    
    <div class="form-title">محاسبهٔ معاش</div>
    <div class="finance-grid">
        <?php
            $salaryAfterTax = ($employee->base_salary ?? 0) - ($lastTaxAmount ?? 0) - ($lastGuarantee ?? 0);
        ?>
        <div class="finance-item">
            <h4>معاش بعد از کسر مالیات</h4>
            <p><?php echo e(number_format($salaryAfterTax)); ?> ؋</p>
            <small>(بدون امتیازات)</small>
        </div>
        <div class="finance-item">
            <h4>معاش بعد از کسر مالیات + امتیازات</h4>
            <p><?php echo e(number_format($calculatedNet)); ?> ؋</p>
            <small>(<?php echo e(number_format($employee->base_salary)); ?> + <?php echo e(number_format($totalPoints)); ?> - <?php echo e(number_format($lastTaxAmount)); ?> - <?php echo e(number_format($lastGuarantee)); ?>)</small>
        </div>
    </div>

    
    <div class="form-title">سایر جزئیات</div>
    <div class="finance-grid">
        <div class="finance-item"><h4>اضافه‌کاری</h4><p><?php echo e(number_format($overtimeAmount)); ?> ؋</p></div>
        <div class="finance-item"><h4>پاداش</h4><p><?php echo e(number_format($bonusAmount)); ?> ؋</p></div>
        <div class="finance-item"><h4>کسورات</h4><p><?php echo e(number_format($deductionAmount)); ?> ؋</p></div>
        <div class="finance-item"><h4>مالیات</h4><p><?php echo e(number_format($taxAmount)); ?> ؋</p></div>
    </div>

    
    <?php if($loans->isNotEmpty()): ?>
    <div class="form-title">قرض‌الحسنه‌ها</div>
    <table class="info-table">
        <thead><tr><td style="width:25%;">مبلغ کل</td><td style="width:25%;">پرداخت‌شده</td><td style="width:25%;">باقی‌مانده</td><td style="width:25%;">وضعیت</td></tr></thead>
        <tbody>
            <?php $__currentLoopData = $loans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $loan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php $remaining = $loan->total_amount - $loan->paid_amount; ?>
                <tr><td><?php echo e(number_format($loan->total_amount)); ?> ؋</td><td><?php echo e(number_format($loan->paid_amount)); ?> ؋</td><td><?php echo e(number_format($remaining)); ?> ؋</td><td><?php echo e($loan->status == 'active' ? 'در جریان' : 'تسویه شده'); ?></td></tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>
    </table>
    <?php endif; ?>

    
    <div class="form-title">وضعیت پرداخت</div>
    <div class="finance-grid">
        <div class="finance-item"><h4>معاش قابل پرداخت</h4><p><?php echo e(number_format($totalAmount)); ?> ؋</p></div>
        <div class="finance-item"><h4>پرداخت‌شده</h4><p><?php echo e(number_format($paidAmount)); ?> ؋</p></div>
        <div class="finance-item"><h4>باقی‌مانده</h4><p><?php echo e(number_format($balance)); ?> ؋</p></div>
    </div>

    <div class="footer">
        تاریخ چاپ: <?php echo e(now()->format('Y/m/d')); ?> | سامانه مدیریت مالی الزهرا (س)
    </div>
</div>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\employees\preview.blade.php ENDPATH**/ ?>