<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></title>
    <style>
        /* جاسازی فونت وزیر با base64 برای mPDF */
        @font-face {
            font-family: 'Vazir';
            src: url('data:font/truetype;charset=utf-8;base64, <?php echo e(base64_encode(file_get_contents(storage_path('fonts/Vazir.ttf')))); ?>') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'Vazir', Tahoma, sans-serif;
            direction: rtl;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #b8860b;
        }
        .bismillah { font-size: 22px; color: #1e3a5f; margin-bottom: 5px; }
        .school-name { font-size: 19px; font-weight: bold; color: #1e3a5f; }
        .form-title {
            font-size: 15px;
            margin: 15px 0 8px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #b8860b;
            font-weight: bold;
            color: #2c3e50;
        }

        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 10px;
            font-size: 13px;
            border: 1px solid #adb5bd;
            border-radius: 8px;
            overflow: hidden;
        }
        .info-table td {
            border: 1px solid #adb5bd;
            padding: 6px 10px;
            text-align: right;
            vertical-align: middle;
        }
        .info-table .label {
            background-color: #f8f9fa;
            font-weight: 600;
            width: 22%;
            color: #2c3e50;
        }
        .info-table .value {
            width: 28%;
            color: #333;
        }
        .info-table tr:nth-child(even) td {
            background-color: #fcfcfc;
        }

        .finance-row { display: flex; gap: 10px; margin: 10px 0; }
        .finance-item {
            flex: 1;
            background: #f8f9fa;
            border: 1px solid #adb5bd;
            border-radius: 8px;
            padding: 12px 10px;
            text-align: center;
        }
        .finance-item h4 { margin: 0 0 5px; font-size: 12px; color: #4a5568; font-weight: 500; }
        .finance-item p { margin: 0; font-size: 17px; font-weight: bold; color: #1e3a5f; }

        .signature {
            margin-top: 30px; text-align: left; padding-left: 15px; font-size: 13px; color: #4a5568;
        }
        .signature .line { display: inline-block; width: 200px; border-bottom: 1px solid #333; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="bismillah">بسمه تعالی</div>
        <div class="school-name"><?php echo e($student->school->name ?? 'مکتب'); ?></div>
        <div>سال تعلیمی: <?php echo e(request('year_filter') ?? '—'); ?></div>
    </div>

    <table style="width:100%; border-collapse: collapse;">
        <tr>
            <td style="width:50%; vertical-align:top; padding:0 5px 0 0;">
                <table class="info-table">
                    <tr><td class="label">کد دانش‌آموز</td><td class="value"><?php echo e($student->student_code); ?></td></tr>
                    <tr><td class="label">نام</td><td class="value"><?php echo e($student->first_name); ?></td></tr>
                    <tr><td class="label">تخلص</td><td class="value"><?php echo e($student->last_name); ?></td></tr>
                    <tr><td class="label">نام پدر</td><td class="value"><?php echo e($student->father_name); ?></td></tr>
                    <tr><td class="label">پدرکلان</td><td class="value"><?php echo e($student->grandfather_name ?? '—'); ?></td></tr>
                    <tr><td class="label">تاریخ تولد</td><td class="value"><?php echo e($student->birth_date ? date('Y/m/d', strtotime($student->birth_date)) : ($student->birth_year ?? '—')); ?></td></tr>
                    <tr><td class="label">شماره تذکره</td><td class="value"><?php echo e($student->national_id); ?></td></tr>
                    <tr><td class="label">نمبر اساس</td><td class="value"><?php echo e($student->base_number ?? '—'); ?></td></tr>
                    <tr><td class="label">جنسیت</td><td class="value"><?php echo e($student->gender == 'male' ? 'پسر' : 'دختر'); ?></td></tr>
                    <tr><td class="label">صنف</td><td class="value"><?php echo e($student->class ?? '—'); ?></td></tr>
                </table>
            </td>
            <td style="width:50%; vertical-align:top; padding:0 0 0 5px;">
                <table class="info-table">
                    <tr><td class="label">تلفن</td><td class="value"><?php echo e($student->phone ?? '—'); ?></td></tr>
                    <tr><td class="label">واتساپ</td><td class="value"><?php echo e($student->whatsapp_phone ?? '—'); ?></td></tr>
                    <tr><td class="label">سکونت اصلی</td><td class="value"><?php echo e($student->original_residence ?? '—'); ?></td></tr>
                    <tr><td class="label">سکونت فعلی / آدرس</td><td class="value"><?php echo e($student->address ?? '—'); ?></td></tr>
                    <tr><td class="label">تاریخ ثبت‌نام</td><td class="value"><?php echo e($student->enrollment_date ? date('Y/m/d', strtotime($student->enrollment_date)) : '—'); ?></td></tr>
                    <tr><td class="label">وضعیت</td><td class="value">
                        <?php switch($student->status):
                            case ('present'): ?> <span style="color:#27ae60;">فعال</span> <?php break; ?>
                            <?php case ('blocked'): ?> <span style="color:#e74c3c;">غیرفعال</span> <?php break; ?>
                            <?php case ('temporary'): ?> <span style="color:#f39c12;">موقت</span> <?php break; ?>
                            <?php case ('three_piece'): ?> <span style="color:#2980b9;">سه‌پارچه</span> <?php break; ?>
                            <?php default: ?> <?php echo e($student->status); ?>

                        <?php endswitch; ?>
                    </td></tr>
                    <tr><td class="label">وضعیت مالی</td><td class="value">
                        <?php switch($student->financial_status):
                            case ('full'): ?> شهریه کامل <?php break; ?>
                            <?php case ('discount'): ?> دارای تخفیف <?php break; ?>
                            <?php case ('free'): ?> <span style="color:#8e44ad;">رایگان</span> <?php break; ?>
                            <?php default: ?> تعیین نشده
                        <?php endswitch; ?>
                    </td></tr>
                    <tr><td class="label">یتیم</td><td class="value"><?php echo e($student->is_orphan ? 'بلی' : 'خیر'); ?></td></tr>
                </table>
            </td>
        </tr>
    </table>

    <?php if($student->guardian): ?>
    <div class="form-title">مشخصات ولی / سرپرست</div>
    <table class="info-table">
        <tr>
            <td class="label">نام کامل</td><td class="value"><?php echo e($student->guardian->full_name); ?></td>
            <td class="label">نسبت</td><td class="value">
                <?php switch($student->guardian->relation):
                    case ('father'): ?> پدر <?php break; ?>
                    <?php case ('mother'): ?> مادر <?php break; ?>
                    <?php case ('brother'): ?> برادر <?php break; ?>
                    <?php case ('uncle'): ?> کاکا / ماما <?php break; ?>
                    <?php case ('other'): ?> سایر <?php break; ?>
                    <?php default: ?> <?php echo e($student->guardian->relation ?? '—'); ?>

                <?php endswitch; ?>
            </td>
        </tr>
        <tr>
            <td class="label">تحصیلات</td><td class="value"><?php echo e($student->guardian->education ?? '—'); ?></td>
            <td class="label">شغل</td><td class="value"><?php echo e($student->guardian->job ?? '—'); ?></td>
        </tr>
    </table>
    <?php endif; ?>

    <?php
        $totalFees = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
        $totalPaid = $student->payments->sum('amount');
        $balance = $totalFees - $totalPaid;
    ?>
    <div class="form-title">وضعیت مالی</div>
    <div class="finance-row">
        <div class="finance-item">
            <h4>کل شهریه تعیین‌شده</h4>
            <p><?php echo e(number_format($totalFees)); ?> ؋</p>
        </div>
        <div class="finance-item">
            <h4>مجموع پرداخت‌ها</h4>
            <p><?php echo e(number_format($totalPaid)); ?> ؋</p>
        </div>
        <div class="finance-item">
            <h4>باقی‌مانده</h4>
            <p><?php echo e(number_format($balance)); ?> ؋</p>
        </div>
    </div>

    <div class="signature">
        <span>امضاء مسئول مالی:</span>
        <span class="line"></span>
    </div>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\students\pdf.blade.php ENDPATH**/ ?>