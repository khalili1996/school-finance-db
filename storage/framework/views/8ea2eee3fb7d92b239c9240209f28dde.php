<?php $__env->startSection('title', 'صندوق‌ها'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">💰 مدیریت صندوق‌ها</h4>
        <a href="<?php echo e(route('school.cashboxes.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus-circle me-1"></i> ایجاد صندوق جدید
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    <?php if($cashboxes->isEmpty()): ?>
        <div class="alert alert-info text-center">
            هنوز هیچ صندوقی ایجاد نشده است. لطفاً یک صندوق جدید بسازید.
        </div>
    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>نام صندوق</th>
                            <th>نوع</th>
                            <th>موجودی اولیه</th>
                            <th>مجموع درآمد</th>
                            <th>مجموع مصرف</th>
                            <th>موجودی فعلی</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $box): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <?php
                                $totalIncome   = $box->transactions()->where('type', 'deposit')->sum('amount');
                                $totalExpense  = $box->transactions()->where('type', 'withdrawal')->sum('amount');
                                $currentBalance = $box->initial_balance + $totalIncome - $totalExpense;
                            ?>
                            <tr>
                                <td><?php echo e($loop->iteration); ?></td>
                                <td><?php echo e($box->name); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo e($box->type === 'bank' ? 'info' : 'success'); ?>">
                                        <?php echo e($box->type === 'bank' ? 'بانکی' : 'نقدی'); ?>

                                    </span>
                                </td>
                                <td><?php echo e(number_format($box->initial_balance, 0)); ?> ؋</td>
                                <td class="text-success"><?php echo e(number_format($totalIncome, 0)); ?> ؋</td>
                                <td class="text-danger"><?php echo e(number_format($totalExpense, 0)); ?> ؋</td>
                                <td class="fw-bold <?php echo e($currentBalance >= 0 ? 'text-success' : 'text-danger'); ?>">
                                    <?php echo e(number_format($currentBalance, 0)); ?> ؋
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="<?php echo e(route('school.cashboxes.show', $box)); ?>" class="btn btn-outline-secondary" title="جزئیات">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="<?php echo e(route('school.cashboxes.edit', $box)); ?>" class="btn btn-outline-warning" title="ویرایش">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <form action="<?php echo e(route('school.cashboxes.destroy', $box)); ?>" method="POST" class="d-inline">
                                            <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                            <button type="submit" class="btn btn-outline-danger" title="حذف"
                                                onclick="return confirm('آیا از حذف این صندوق اطمینان دارید؟')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\cashboxes\index.blade.php ENDPATH**/ ?>