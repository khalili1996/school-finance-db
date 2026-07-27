<?php $__env->startSection('title', 'گزارش مصارف'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-arrow-up ms-2"></i> گزارش مصارف</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    
    <?php echo $__env->make('partials.report-header', ['title' => 'گزارش مصارف', 'subtitle' => ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-2">
            <input type="text" name="from_date" class="form-control form-control-sm" placeholder="از تاریخ (شمسی)" value="<?php echo e($fromDate); ?>">
        </div>
        <div class="col-md-2">
            <input type="text" name="to_date" class="form-control form-control-sm" placeholder="تا تاریخ (شمسی)" value="<?php echo e($toDate); ?>">
        </div>
        <div class="col-md-2">
            <select name="expense_category_filter" class="form-select form-select-sm">
                <option value="">همه دسته‌بندی‌ها (مصارف)</option>
                <?php $__currentLoopData = $expenseCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('expense_category_filter') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="month_filter" class="form-select form-select-sm">
                <option value="">همه ماه‌ها (معاشات)</option>
                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m->id); ?>" <?php echo e(request('month_filter') == $m->id ? 'selected' : ''); ?>><?php echo e($m->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
            <a href="<?php echo e(route('school.reports.financial.expenses')); ?>" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    
    <div class="card shadow mb-5">
        <div class="card-header bg-danger text-white">
            <h5 class="mb-0"><i class="fas fa-file-invoice me-1"></i> مصارف روزمره</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
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
                    <?php $__empty_1 = true; $__currentLoopData = $expensesGrouped; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $group): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
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
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">مصرفی با این فیلترها یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td colspan="5" class="text-end">جمع کل:</td>
                        <td><?php echo e(number_format($totalExpenses)); ?></td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    
    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-money-check-alt me-1"></i> حقوق کارمندان</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>ش فاکتور</th>
                        <th>نام و نام خانوادگی</th>
                        <th>سمت</th>
                        <th>وضعیت</th>
                        <th>آی‌دی کارمند</th>
                        <th>حقوق پایه</th>
                        <th>امتیاز سمت</th>
                        <th>امتیاز سابقه</th>
                        <th>امتیاز تحصیل</th>
                        <th>پاداش</th>
                        <th>اضافه‌کاری</th>
                        <th>کسورات</th>
                        <th>مالیات</th>
                        <th>خالص</th>
                        <th>پرداختی</th>
                        <th>مجموع</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $salaryRunningTotal = 0; ?>
                    <?php $__empty_1 = true; $__currentLoopData = $salariesQuery; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $salary): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php
                            $deduction = ($salary->deduction_amount ?? 0) + ($salary->guarantee_amount ?? 0);
                            $net = $salary->total_amount ?? 0;
                            $paid = $salary->paid_amount ?? 0;
                            $salaryRunningTotal += $paid;
                        ?>
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
                            <td><?php echo e(number_format($salary->overtime_amount ?? 0)); ?></td>
                            <td><?php echo e(number_format($deduction)); ?></td>
                            <td><?php echo e(number_format($salary->tax_amount ?? 0)); ?></td>
                            <td><?php echo e(number_format($net)); ?></td>
                            <td><?php echo e(number_format($paid)); ?></td>
                            <td><?php echo e(number_format($salaryRunningTotal)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="16" class="text-center text-muted py-4">حقوق پرداختی با این فیلترها یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td colspan="5" class="text-end">جمع کل:</td>
                        <td><?php echo e(number_format($salariesQuery->sum('base_salary'))); ?></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><?php echo e(number_format($salariesQuery->sum('bonus_amount'))); ?></td>
                        <td><?php echo e(number_format($salariesQuery->sum('overtime_amount'))); ?></td>
                        <td><?php echo e(number_format($salariesQuery->sum('deduction_amount') + $salariesQuery->sum('guarantee_amount'))); ?></td>
                        <td><?php echo e(number_format($salariesQuery->sum('tax_amount'))); ?></td>
                        <td><?php echo e(number_format($salariesQuery->sum('total_amount'))); ?></td>
                        <td><?php echo e(number_format($totalSalaries)); ?></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    @media print {
        .btn, form, .breadcrumb, .card-footer, #sidebar-wrapper, header {
            display: none !important;
        }
        #page-content-wrapper {
            padding: 0 !important;
        }
        .table { font-size: 12px; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\reports\financial-expenses.blade.php ENDPATH**/ ?>