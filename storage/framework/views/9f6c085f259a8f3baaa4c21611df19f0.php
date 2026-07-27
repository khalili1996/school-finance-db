<?php $__env->startSection('title', 'لیست مصارف'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-file-invoice ms-2"></i> مدیریت مصارف</h1>
        <div>
            <a href="<?php echo e(route('school.expenses.report')); ?>" class="btn btn-outline-primary btn-sm me-2">
                <i class="fas fa-print"></i> گزارش چاپی
            </a>
            <a href="<?php echo e(route('school.expenses.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> ثبت مصرف جدید
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <form method="GET" action="<?php echo e(route('school.expenses.index')); ?>" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="جستجوی عنوان، توضیحات، شماره فاکتور..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-md-2">
            <select name="category_id" class="form-select" onchange="this.form.submit()">
                <option value="">همه دسته‌بندی‌ها</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">همه وضعیت‌ها</option>
                <option value="due" <?php echo e(request('status') == 'due' ? 'selected' : ''); ?>>پرداخت نشده</option>
                <option value="partially_paid" <?php echo e(request('status') == 'partially_paid' ? 'selected' : ''); ?>>پرداخت جزئی</option>
                <option value="paid" <?php echo e(request('status') == 'paid' ? 'selected' : ''); ?>>پرداخت کامل</option>
                <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>لغو شده</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> اعمال</button>
            <?php if(request('search') || request('category_id') || request('status')): ?>
                <a href="<?php echo e(route('school.expenses.index')); ?>" class="btn btn-secondary">حذف فیلترها</a>
            <?php endif; ?>
        </div>
    </form>

    
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>عنوان</th>
                            <th>دسته‌بندی</th>
                            <th>ماه</th>
                            <th>مبلغ کل</th>
                            <th>پرداختی</th>
                            <th>باقی‌مانده</th>
                            <th>تاریخ</th>
                            <th>شماره فاکتور</th>
                            <th>فاکتور</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $expenses; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $expense): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $remaining = $expense->total_amount - $expense->paid_amount; ?>
                            <tr>
                                <td><?php echo e($expense->title); ?></td>
                                <td><?php echo e($expense->category->name ?? '—'); ?></td>
                                <td><?php echo e($expense->month->name ?? '—'); ?></td>
                                <td><?php echo e(number_format($expense->total_amount)); ?> ؋</td>
                                <td><?php echo e(number_format($expense->paid_amount)); ?> ؋</td>
                                <td><?php echo e(number_format(max($remaining, 0))); ?> ؋</td>
                                
                                <td><?php echo e($expense->expense_date ? \App\Helpers\JalaliHelper::toJalali($expense->expense_date) : '—'); ?></td>
                                <td><?php echo e($expense->invoice_number ?? '—'); ?></td>
                                <td>
                                    <?php if($expense->scan_file): ?>
                                        <a href="<?php echo e(asset('storage/'.$expense->scan_file)); ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-file-image"></i>
                                        </a>
                                    <?php else: ?>
                                        —
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php switch($expense->status):
                                        case ('paid'): ?> <span class="badge bg-success">پرداخت کامل</span> <?php break; ?>
                                        <?php case ('partially_paid'): ?> <span class="badge bg-warning text-dark">پرداخت جزئی</span> <?php break; ?>
                                        <?php case ('due'): ?> <span class="badge bg-danger">پرداخت نشده</span> <?php break; ?>
                                        <?php case ('cancelled'): ?> <span class="badge bg-secondary">لغو شده</span> <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('school.expenses.edit', $expense->id)); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <form action="<?php echo e(route('school.expenses.destroy', $expense->id)); ?>" method="POST" class="d-inline"
                                          onsubmit="return confirm('آیا از حذف این مصرف اطمینان دارید؟')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="11" class="text-center text-muted py-3">هیچ مصرفی ثبت نشده است.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($expenses->hasPages()): ?>
        <div class="card-footer"><?php echo e($expenses->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\expenses\index.blade.php ENDPATH**/ ?>