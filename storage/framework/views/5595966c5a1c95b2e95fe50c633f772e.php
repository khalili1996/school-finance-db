<?php $__env->startSection('title', 'گزارش درآمدها'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-arrow-down ms-2"></i> گزارش درآمدها</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    
    <?php echo $__env->make('partials.report-header', ['title' => 'گزارش درآمدها', 'subtitle' => ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-2">
            <input type="text" name="from_date" class="form-control form-control-sm" placeholder="از تاریخ (شمسی)" value="<?php echo e($fromDate); ?>">
        </div>
        <div class="col-md-2">
            <input type="text" name="to_date" class="form-control form-control-sm" placeholder="تا تاریخ (شمسی)" value="<?php echo e($toDate); ?>">
        </div>
        <div class="col-md-2">
            <select name="class_filter" class="form-select form-select-sm">
                <option value="">همه صنف‌ها</option>
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cls); ?>" <?php echo e(request('class_filter') == $cls ? 'selected' : ''); ?>><?php echo e($cls); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="month_filter" class="form-select form-select-sm">
                <option value="">همه ماه‌ها</option>
                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($m->id); ?>" <?php echo e(request('month_filter') == $m->id ? 'selected' : ''); ?>><?php echo e($m->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="category_filter" class="form-select form-select-sm">
                <option value="">همه دسته‌بندی‌ها</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_filter') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
            <a href="<?php echo e(route('school.reports.financial.incomes')); ?>" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    
    <div class="card shadow mb-5">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="fas fa-file-invoice-dollar me-1"></i> شهریه‌های پرداختی – تفکیک صنف</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>صنف</th>
                        <th>تعداد دانش‌آموز</th>
                        <th>مجموع شهریه</th>
                        <th>ملاحظات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $feePaymentsByClass; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($loop->iteration); ?></td>
                            <td><?php echo e($item['class']); ?></td>
                            <td><?php echo e($item['count']); ?></td>
                            <td><?php echo e(number_format($item['total'])); ?> ؋</td>
                            <td>—</td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">پرداخت شهریه‌ای با این فیلترها یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td colspan="3" class="text-end">جمع کل شهریه‌ها:</td>
                        <td colspan="2"><?php echo e(number_format($totalFeeIncome)); ?> ؋</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    
    <div class="card shadow mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-coins me-1"></i> سایر درآمدها (غیر از شهریه)</h5>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>دسته‌بندی</th>
                        <th>شرح</th>
                        <th>مبلغ (افغانی)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $otherIncomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $income): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($otherIncomes->firstItem() + $loop->index); ?></td>
                            <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($income->income_date)); ?></td>
                            <td><?php echo e($income->incomeCategory->name ?? '—'); ?></td>
                            <td><?php echo e($income->description ?? '—'); ?></td>
                            <td><?php echo e(number_format($income->received_amount ?? 0)); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">درآمد دیگری با این فیلترها یافت نشد.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold bg-light">
                        <td colspan="4" class="text-end">جمع کل سایر درآمدها:</td>
                        <td><?php echo e(number_format($totalOtherIncomes)); ?> ؋</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <div class="card-footer bg-white">
            <?php echo e($otherIncomes->links()); ?>

        </div>
    </div>

    
    <div class="row mt-4">
        <div class="col-md-6 offset-md-6">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center">
                    <h5 class="text-info">مجموع کل درآمدها (شهریه + سایر)</h5>
                    <h3><?php echo e(number_format($grandTotalIncome)); ?> ؋</h3>
                </div>
            </div>
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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\reports\financial-incomes.blade.php ENDPATH**/ ?>