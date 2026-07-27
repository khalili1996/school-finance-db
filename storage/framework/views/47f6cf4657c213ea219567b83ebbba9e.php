<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($guardian->full_name); ?> – فرم مشخصات ولی</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page {
            size: A5;
            margin: 8mm;
        }
        body {
            font-family: 'Vazir', Tahoma, sans-serif;
            background: #e9ecef;
            margin: 0;
            padding: 10px;
            display: flex;
            justify-content: center;
        }
        .preview-container {
            width: 148mm;
            min-height: 210mm;
            background: #fff;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
            border-radius: 6px;
            padding: 10mm;
            box-sizing: border-box;
            margin: 0 auto;
            color: #333;
        }
        .btn-group {
            text-align: center;
            margin-bottom: 8mm;
        }
        .btn {
            padding: 4mm 10mm;
            margin: 0 3mm;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            display: inline-block;
            font-weight: bold;
        }
        .btn-print { background: #2c3e50; color: #fff; }
        .header {
            background: #2c3e50;
            color: #fff;
            padding: 6mm;
            border-radius: 4px;
            text-align: center;
            margin-bottom: 8mm;
        }
        .bismillah { font-size: 20px; margin-bottom: 2mm; }
        .school-name { font-size: 18px; font-weight: bold; }
        .year-info { font-size: 11px; margin-top: 2mm; opacity: 0.8; }
        .form-title {
            font-size: 15px;
            font-weight: bold;
            color: #2c3e50;
            border-bottom: 2px solid #2c3e50;
            padding-bottom: 3mm;
            margin: 8mm 0 5mm;
        }
        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 10px;
            font-size: 12px;
            border: 1px solid #adb5bd;
            border-radius: 8px;
            overflow: hidden;
        }
        .info-table td {
            border: 1px solid #adb5bd;
            padding: 4px 8px;
            text-align: right;
            vertical-align: middle;
        }
        .info-table .label {
            background-color: #f8f9fa;
            font-weight: 600;
            width: 30%;
            color: #2c3e50;
        }
        .info-table tr:nth-child(even) td { background-color: #fcfcfc; }
        .student-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 10px;
        }
        .student-table th, .student-table td {
            border: 1px solid #adb5bd;
            padding: 3px 5px;
            text-align: center;
        }
        .student-table th { background-color: #ecf0f1; }
        .finance-summary {
            display: flex;
            gap: 10px;
            margin: 10px 0;
        }
        .finance-item {
            flex: 1;
            background: #f8f9fa;
            border: 1px solid #adb5bd;
            border-radius: 8px;
            padding: 8px 6px;
            text-align: center;
        }
        .finance-item h4 { margin: 0 0 5px; font-size: 11px; color: #4a5568; }
        .finance-item p { margin: 0; font-size: 14px; font-weight: bold; color: #1e3a5f; }
        .footer {
            text-align: center;
            margin-top: 8mm;
            font-size: 9px;
            color: #888;
            border-top: 1px solid #ccc;
            padding-top: 4mm;
        }
        @media print {
            .btn-group { display: none; }
            body { background: #fff; margin: 0; padding: 0; }
            .preview-container { box-shadow: none; width: 100%; min-height: auto; padding: 0; }
        }
    </style>
</head>
<body>
    <div class="preview-container">
        <div class="btn-group">
            <button class="btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> چاپ / ذخیره PDF</button>
        </div>

        <div class="header">
            <div class="bismillah">بسمه تعالی</div>
            <div class="school-name"><?php echo e($guardian->students->first()->school->name ?? 'مکتب'); ?></div>
            <div class="year-info">سال تعلیمی: <?php echo e(request('year_filter') ?? '—'); ?></div>
        </div>

        <div class="form-title">فرم مشخصات ولی / سرپرست</div>

        <table class="info-table">
            <tr><td class="label">اسم</td><td><?php echo e($guardian->full_name ?? '—'); ?></td></tr>
            <tr>
    <td class="label">نسبت</td>
    <td>
        <?php switch($guardian->relation):
            case ('father'): ?> پدر <?php break; ?>
            <?php case ('mother'): ?> مادر <?php break; ?>
            <?php case ('brother'): ?> برادر <?php break; ?>
            <?php case ('uncle'): ?> کاکا / ماما <?php break; ?>
            <?php case ('other'): ?> سایر <?php break; ?>
            <?php default: ?> <?php echo e($guardian->relation ?? '—'); ?>

        <?php endswitch; ?>
    </td>
</tr>
            <tr><td class="label">شماره تماس</td><td><?php echo e($guardian->phone ?? '—'); ?></td></tr>
            <tr><td class="label">تعداد فرزندان</td><td><?php echo e($guardian->students->count()); ?> نفر</td></tr>
        </table>

        <?php if($guardian->students->isNotEmpty()): ?>
        <div class="form-title">لیست فرزندان</div>
        <table class="student-table">
            <thead>
                <tr>
                    <th>نام</th>
                    <th>صنف</th>
                    <th>شهریه کل</th>
                    <th>پرداختی</th>
                    <th>باقی‌مانده</th>
                    <th>ماه‌های پرداخت‌نشده</th>
                </tr>
            </thead>
            <tbody>
                <?php $__currentLoopData = $guardian->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <td><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                    <td><?php echo e($student->class ?? '—'); ?></td>
                    <td><?php echo e(number_format($student->total_fee)); ?> ؋</td>
                    <td><?php echo e(number_format($student->total_paid)); ?> ؋</td>
                    <td><?php echo e(number_format($student->balance)); ?> ؋</td>
                    <td><?php echo e(implode('، ', $student->unpaid_months) ?: '—'); ?></td>
                </tr>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
        </table>

        <div class="form-title">خلاصه مالی خانواده</div>
        <div class="finance-summary">
            <div class="finance-item">
                <h4>کل شهریه خانواده</h4>
                <p><?php echo e(number_format($totalFamilyFee)); ?> ؋</p>
            </div>
            <div class="finance-item">
                <h4>مجموع پرداختی‌ها</h4>
                <p><?php echo e(number_format($totalFamilyPaid)); ?> ؋</p>
            </div>
            <div class="finance-item">
                <h4>بدهی کل خانواده</h4>
                <p><?php echo e(number_format($totalFamilyDebt)); ?> ؋</p>
            </div>
        </div>
        <?php else: ?>
        <p class="text-muted">هیچ دانش‌آموزی به این ولی متصل نیست.</p>
        <?php endif; ?>

        <div class="footer">
            تاریخ چاپ: <?php echo e(now()->format('Y/m/d')); ?> | سامانه مدیریت مالی الزهرا (س)
        </div>
    </div>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\guardians\preview.blade.php ENDPATH**/ ?>