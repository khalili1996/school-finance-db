<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش جامع مالی و آماری</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #fff; margin: 0; padding: 10px; direction: rtl; color: #333; }
        h2, h3 { text-align: center; margin: 8px 0; }
        .btn-print { background: #2c3e50; color: #fff; padding: 6px 18px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 10px; }
        @media print { .btn-print, .no-print { display: none; } }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 11px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .bold { font-weight: bold; }
        .summary-table td { font-weight: bold; background-color: #f0f0f0; }
        .filter-form { margin-bottom: 15px; }
        .filter-form select, .filter-form button { padding: 5px 10px; font-family: 'Vazir'; }
    </style>
</head>
<body>

<div class="no-print" style="display: flex; justify-content: space-between; align-items: center;">
    <button class="btn-print" onclick="window.print()">🖨️ چاپ گزارش</button>
    <form method="GET" action="<?php echo e(route('school.reports.comprehensive')); ?>" class="filter-form">
        <label>ماه:</label>
        <select name="month_id" onchange="this.form.submit()">
            <option value="">همه ماه‌ها</option>
            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <option value="<?php echo e($m->id); ?>" <?php echo e($monthId == $m->id ? 'selected' : ''); ?>><?php echo e($m->name); ?></option>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
    </form>
</div>


<?php
    $schoolId = session('active_school_id', auth()->user()->school_id ?? null);
    $logo = \App\Models\Setting::get('logo', null, $schoolId);
    $schoolName = \App\Models\Setting::get('school_name')
        ?: (\App\Models\School::find($schoolId)->name ?? 'مکتب');
    $yearName = session('active_academic_year_name', 'انتخاب نشده');
?>

<div style="
    background-color: #f8f9fa;
    border: 1px solid #000307;
    border-radius: 8px;
    padding: 15px 20px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    direction: rtl;
">
    
    <?php if($logo): ?>
        <?php
            $logoPath = storage_path('app/public/' . $logo);
            $logoData = '';
            if (file_exists($logoPath)) {
                $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                $data = file_get_contents($logoPath);
                $logoData = 'data:image/' . $type . ';base64,' . base64_encode($data);
            }
        ?>
        <?php if($logoData): ?>
            <div style="flex: 0 0 auto; margin-right: 40px; margin-left: 0;">
                <img src="<?php echo e($logoData); ?>" alt="لوگو"
                     style="width: 80px; height: 80px; object-fit: contain;">
            </div>
        <?php endif; ?>
    <?php endif; ?>

    
    <div style="flex: 1; text-align: center;">
        <h2 style="margin: 0; font-size: 20px; font-weight: bold; color: #2c3e50;">
            گزارش جامع مالی <?php echo e($schoolName); ?>

        </h2>
        <h3 style="margin: 8px 0 0 0; font-size: 13px; font-weight: normal; color: #777;">
            سال مالی: <?php echo e($yearName); ?>

        </h3>
        <h4 style="margin: 5px 0 0 0; font-size: 14px; font-weight: normal; color: #555;">
            <?php echo e($selectedMonth ? 'ماه: ' . $selectedMonth->name : 'کل سال'); ?>

        </h4>

    </div>

    
    <div style="flex: 0 0 auto; width: 70px; margin-right: 20px;"></div>
</div>

<!-- ======================== ۱. ورودی‌ها (درآمدها) ======================== -->
<h3>الف) ورودی‌ها – ماه <?php echo e($selectedMonth->name ?? 'جاری'); ?></h3>
...
<table>
    <thead>
        <tr>
            <th>پرداخت کننده</th>
            <th>تعداد</th>
            <th>مبلغ</th>
            <th>محل مصرف</th>
            <th>مبلغ افغانی</th>
            <th>شماره فاکتور</th>
        </tr>
    </thead>
    <tbody>
        <?php $__currentLoopData = $feePaymentsGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <tr>
                <td>شهریه دانش آموزان</td>
                <td><?php echo e($group['count']); ?></td>
                <td><?php echo e(number_format($group['amount'])); ?></td>
                <td>هزینه‌های جاری</td>
                <td><?php echo e(number_format($group['total'])); ?></td>
                <td></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr>
            <td class="bold" colspan="4">درآمد مستقل</td>
            <td class="bold"><?php echo e(number_format($otherIncomes)); ?></td>
            <td></td>
        </tr>
        <tr>
            <td class="bold" colspan="4">کمک هزینه دریافتی</td>
            <td class="bold"><?php echo e(number_format($grants)); ?></td>
            <td></td>
        </tr>
    </tbody>
    <tfoot>
        <tr>
            <td class="bold" colspan="4">مجموع</td>
            <td class="bold"><?php echo e(number_format($totalIncome)); ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<!-- ======================== ۲. مصارف ======================== -->
<h3>ب) مصارف</h3>
<table>
    <thead>
        <tr>
            <th>گروه هزینه</th>
            <th>ش فاکتور</th>
            <th>تاریخ</th>
            <th>شرح</th>
            <th>تعداد-مقدار</th>
            <th>مبلغ</th>
            <th>مجموع</th>
            <th>جمع گروه</th>
        </tr>
    </thead>
    <tbody>
        <?php $runningTotal = 0; ?>
        <?php $__currentLoopData = $expensesGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $group['items']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr>
                    <?php if($index === 0): ?>
                        <td rowspan="<?php echo e(count($group['items'])); ?>"><?php echo e($group['category']); ?></td>
                    <?php endif; ?>
                    <td><?php echo e($expense->id); ?></td>
                    <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($expense->expense_date)); ?></td>
                    <td><?php echo e($expense->description ?? $expense->title ?? '—'); ?></td>
                    <td><?php echo e($expense->quantity ?? '—'); ?></td>
                    
                    <td><?php echo e(number_format($expense->total_amount)); ?></td>
                    <?php if($index === 0): ?>
                        <?php $groupTotal = $group['total']; $runningTotal += $groupTotal; ?>
                        <td><?php echo e(number_format($runningTotal)); ?></td>
                        <td rowspan="<?php echo e(count($group['items'])); ?>"><?php echo e(number_format($groupTotal)); ?></td>
                    <?php else: ?>
                        <td><?php echo e(number_format($runningTotal)); ?></td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
        <tr class="summary-table">
            <td colspan="5">مجموع هزینه‌ها</td>
            <td><?php echo e(number_format($totalExpenses)); ?></td>
            <td></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<!-- ======================== ۳. حقوق ======================== -->
<h3>ج) حقوق کارمندان</h3>
<table>
    <thead>
        <tr>
            <th>ش فاکتور</th>
            <th>نام و نام خانوادگی</th>
            <th>سمت</th>
            <th>وضعیت</th>
            <th>آی‌دی کارمند</th>
            <th>حقوق پایه</th>
            <th>امتیاز سمت</th>
            <th>امتیاز سالانه</th>
            <th>امتیاز سند علمی</th>
            <th>کمک‌هزینه کارمندی</th>
            <th>حقوق نهایی</th>
            <th>اضافه‌کاری</th>
            <th>کسر حقوق</th>
            <th>حقوق پرداختی</th>
            <th>مجموع</th>
        </tr>
    </thead>
    <tbody>
        <?php $salaryRunningTotal = 0; ?>
        <?php $__currentLoopData = $salariesQuery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php $salaryRunningTotal += ($salary->paid_amount ?? 0); ?>
            <tr>
                <td><?php echo e($salary->id); ?></td>
                <td><?php echo e($salary->employee->first_name ?? ''); ?> <?php echo e($salary->employee->last_name ?? ''); ?></td>
                <td><?php echo e($salary->employee->position->name ?? $salary->employee->position ?? '—'); ?></td>
                <td><?php echo e($salary->employee->status ?? '—'); ?></td>
                <td><?php echo e($salary->employee->employee_code ?? '—'); ?></td>
                <td><?php echo e(number_format($salary->base_salary)); ?></td>
                <td><?php echo e($salary->employee->position_points ?? '—'); ?></td>
                <td><?php echo e($salary->employee->experience_points ?? '—'); ?></td>
                <td><?php echo e($salary->employee->education_points ?? '—'); ?></td>
                <td><?php echo e(number_format($salary->bonus_amount ?? 0)); ?></td>
                <td><?php echo e(number_format($salary->total_amount)); ?></td>
                <td><?php echo e(number_format($salary->overtime_amount ?? 0)); ?></td>
                <td><?php echo e(number_format(($salary->deduction_amount ?? 0) + ($salary->tax_amount ?? 0))); ?></td>
                <td><?php echo e(number_format($salary->paid_amount)); ?></td>
                <td><?php echo e(number_format($salaryRunningTotal)); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
    <tfoot>
        <tr class="summary-table">
            <td colspan="5">مجموع</td>
            <td><?php echo e(number_format($salariesQuery->sum('base_salary'))); ?></td>
            <td colspan="3"></td>
            <td><?php echo e(number_format($salariesQuery->sum('bonus_amount'))); ?></td>
            <td><?php echo e(number_format($salariesQuery->sum('total_amount'))); ?></td>
            <td><?php echo e(number_format($salariesQuery->sum('overtime_amount'))); ?></td>
            <td><?php echo e(number_format($salariesQuery->sum('deduction_amount') + $salariesQuery->sum('tax_amount'))); ?></td>
            <td><?php echo e(number_format($totalSalaries)); ?></td>
            <td></td>
        </tr>
    </tfoot>
</table>

<!-- ======================== ۴. خلاصه نهایی ======================== -->
<h3>د) خلاصه مالی</h3>
<table>
    <tr><td width="30%">درآمد مستقل</td><td><?php echo e(number_format($otherIncomes)); ?> ؋</td></tr>
    <tr><td>کمک هزینه ها</td><td><?php echo e(number_format($grants)); ?> ؋</td></tr>
    <tr><td>مجموع درآمدها</td><td><?php echo e(number_format($totalIncome)); ?> ؋</td></tr>
    <tr><td>مصارف</td><td><?php echo e(number_format($totalExpenses)); ?> ؋</td></tr>
    <tr><td>معاشات</td><td><?php echo e(number_format($totalSalaries)); ?> ؋</td></tr>
    <tr><td>مجموع مصارف</td><td><?php echo e(number_format($totalExpenses + $totalSalaries)); ?> ؋</td></tr>
    <tr><td>کسری/مازاد</td><td><?php echo e(number_format($totalIncome - ($totalExpenses + $totalSalaries))); ?> ؋</td></tr>
    <tr><td>موجودی صندوق</td><td><?php echo e(number_format($cashboxBalance)); ?> ؋</td></tr>
</table>

<p style="text-align:center; margin-top:20px;">گزارش جامع تولید شده توسط سامانه مدیریت مالی الزهرا (س) – <?php echo e(\App\Helpers\JalaliHelper::todayJalali()); ?></p>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\school\reports\comprehensive-report.blade.php ENDPATH**/ ?>