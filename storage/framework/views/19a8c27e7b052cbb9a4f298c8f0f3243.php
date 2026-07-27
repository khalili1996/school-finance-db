<?php $__env->startSection('title', 'فاکتورها'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-file-invoice ms-2"></i> فاکتورهای ثبت‌شده</h1>
        <a href="<?php echo e(route('school.invoices.report')); ?>" class="btn btn-outline-primary">
            <i class="fas fa-print"></i> گزارش چاپی
        </a>
    </div>

    <form method="GET" action="<?php echo e(route('school.invoices.index')); ?>" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="جستجوی شماره فاکتور..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> جستجو</button>
            <?php if(request('search')): ?>
                <a href="<?php echo e(route('school.invoices.index')); ?>" class="btn btn-secondary">حذف</a>
            <?php endif; ?>
        </div>
    </form>

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>شماره فاکتور</th>
                        <th>عنوان هزینه</th>
                        <th>تاریخ</th>
                        <th>فایل</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $invoices; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($expense->invoice_number); ?></td>
                            <td><?php echo e($expense->title); ?></td>
                            <td><?php echo e($expense->expense_date); ?></td>
                            <td>
                                <?php if($expense->scan_file): ?>
                                    <a href="<?php echo e(asset('storage/'.$expense->scan_file)); ?>" target="_blank">
                                        <img src="<?php echo e(asset('storage/'.$expense->scan_file)); ?>" style="max-height: 50px;" alt="فاکتور">
                                    </a>
                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?php echo e(route('school.expenses.edit', $expense->id)); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="5" class="text-center text-muted py-3">هیچ فاکتوری یافت نشد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer"><?php echo e($invoices->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\invoices\index.blade.php ENDPATH**/ ?>