<?php $__env->startSection('title', 'دفتر کل'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <h4 class="mb-3">📒 دفتر کل</h4>

    
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('school.ledger.index')); ?>" class="row g-2 align-items-end">
                
                <div class="col-md-2">
                    <label class="form-label">سال مالی</label>
                    <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                        <option value="">همه سال‌ها</option>
                        <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($yr->id); ?>" <?php echo e(request('academic_year_id') == $yr->id ? 'selected' : ''); ?>>
                                <?php echo e($yr->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">از تاریخ (شمسی)</label>
                    <input type="text" name="from_date" class="form-control"
                           value="<?php echo e(request('from_date')); ?>" placeholder="مثلاً ۱۴۰۴/۰۱/۰۱">
                </div>
                <div class="col-md-2">
                    <label class="form-label">تا تاریخ (شمسی)</label>
                    <input type="text" name="to_date" class="form-control"
                           value="<?php echo e(request('to_date')); ?>" placeholder="مثلاً ۱۴۰۴/۰۳/۳۰">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-outline-secondary w-100">فیلتر</button>
                </div>
                <div class="col-md-2">
                    <a href="<?php echo e(route('school.ledger.index')); ?>" class="btn btn-outline-danger w-100">پاک کردن</a>
                </div>
            </form>
        </div>
    </div>

    
    <?php
        $incomeEntries  = $entries->where('debit', '>', 0);
        $expenseEntries = $entries->where('credit', '>', 0);

        // تفکیک معاشات و سایر مصارف
        $salaryEntries = $expenseEntries->filter(function ($entry) {
            return $entry->reference instanceof \App\Models\SalaryPayment;
        });
        $otherExpenseEntries = $expenseEntries->filter(function ($entry) {
            return !($entry->reference instanceof \App\Models\SalaryPayment);
        });

        $totalIncome    = $incomeEntries->sum('debit');
        $totalSalary    = $salaryEntries->sum('credit');
        $totalOther     = $otherExpenseEntries->sum('credit');
        $totalExpense   = $totalSalary + $totalOther;
        $balance        = $totalIncome - $totalExpense;
    ?>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-success shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-success">جمع کل درآمد</h6>
                    <h4><?php echo e(number_format($totalIncome)); ?> ؋</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-danger shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-danger">جمع کل مصرف</h6>
                    <h4><?php echo e(number_format($totalExpense)); ?> ؋</h4>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-info shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-info">تراز</h6>
                    <h4 class="<?php echo e($balance >= 0 ? 'text-success' : 'text-danger'); ?>">
                        <?php echo e(number_format($balance)); ?> ؋
                    </h4>
                </div>
            </div>
        </div>
    </div>

    
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-success text-white">
            <h6 class="mb-0">💰 درآمد</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>شرح</th>
                        <th class="text-success">مبلغ (افغانی)</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $incomeEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($i + 1); ?></td>
                        <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($entry->entry_date)); ?></td>
                        <td><?php echo e($entry->description); ?></td>
                        <td class="text-success"><?php echo e(number_format($entry->debit)); ?></td>
                        <td>
                            <form action="<?php echo e(route('school.ledger.destroy', $entry->id)); ?>" method="POST"
                                  onsubmit="return confirm('آیا از حذف این سند اطمینان دارید؟')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-2">هیچ درآمدی ثبت نشده است.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-warning text-dark">
            <h6 class="mb-0">💵 پرداخت معاشات</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>شرح</th>
                        <th>کارمند</th>
                        <th>ماه</th>
                        <th class="text-danger">مبلغ (افغانی)</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $salaryEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php
                        $employeeName = '—';
                        $monthName    = '—';
                        if ($entry->reference && $entry->reference->salary) {
                            $emp = $entry->reference->salary->employee;
                            if ($emp) {
                                $employeeName = $emp->full_name ?? ($emp->first_name . ' ' . $emp->last_name);
                            }
                            $monthName = optional($entry->reference->salary->month)->name ?? '—';
                        }
                    ?>
                    <tr>
                        <td><?php echo e($i + 1); ?></td>
                        <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($entry->entry_date)); ?></td>
                        <td><?php echo e($entry->description); ?></td>
                        <td><?php echo e($employeeName); ?></td>
                        <td><?php echo e($monthName); ?></td>
                        <td class="text-danger"><?php echo e(number_format($entry->credit)); ?></td>
                        <td>
                            <form action="<?php echo e(route('school.ledger.destroy', $entry->id)); ?>" method="POST"
                                  onsubmit="return confirm('آیا از حذف این سند اطمینان دارید؟')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-2">هیچ پرداخت معاشی ثبت نشده است.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-end fw-bold">جمع معاشات:</td>
                        <td class="text-danger fw-bold"><?php echo e(number_format($totalSalary)); ?> ؋</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-secondary text-white">
            <h6 class="mb-0">🧾 سایر مصارف</h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>تاریخ</th>
                        <th>شرح</th>
                        <th class="text-danger">مبلغ (افغانی)</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $otherExpenseEntries; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $entry): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($i + 1); ?></td>
                        <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($entry->entry_date)); ?></td>
                        <td><?php echo e($entry->description); ?></td>
                        <td class="text-danger"><?php echo e(number_format($entry->credit)); ?></td>
                        <td>
                            <form action="<?php echo e(route('school.ledger.destroy', $entry->id)); ?>" method="POST"
                                  onsubmit="return confirm('آیا از حذف این سند اطمینان دارید؟')">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-2">هیچ مصرف دیگری ثبت نشده است.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <td colspan="3" class="text-end fw-bold">جمع سایر مصارف:</td>
                        <td class="text-danger fw-bold"><?php echo e(number_format($totalOther)); ?> ؋</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    
    <div class="row mb-4">
        <div class="col-md-6 offset-md-6">
            <div class="card border-danger shadow-sm">
                <div class="card-body text-center">
                    <h6 class="text-danger">مجموع کل مصارف (معاشات + سایر)</h6>
                    <h4 class="text-danger"><?php echo e(number_format($totalExpense)); ?> ؋</h4>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-3">
        <?php echo e($entries->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\ledger\index.blade.php ENDPATH**/ ?>