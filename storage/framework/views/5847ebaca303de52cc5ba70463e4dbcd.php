<?php $__env->startSection('title', 'جزئیات صندوق: ' . $cashbox->name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">📋 جزئیات صندوق</h4>
        <div>
            <a href="<?php echo e(route('school.cashboxes.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-1"></i> بازگشت به لیست
            </a>
        </div>
    </div>

    
    <form method="GET" class="row g-2 mb-3">
        <div class="col-md-3">
            <select name="academic_year_id" class="form-select" onchange="this.form.submit()">
                <option value="">همه سال‌ها</option>
                <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($yr->id); ?>" <?php echo e(request('academic_year_id') == $yr->id ? 'selected' : ''); ?>>
                        <?php echo e($yr->name); ?>

                    </option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
    </form>

    
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-end">
                    <h5 class="card-title"><?php echo e($cashbox->name); ?></h5>
                    <p class="mb-2">
                        <span class="badge bg-<?php echo e($cashbox->type === 'bank' ? 'info' : 'success'); ?>">
                            <?php echo e($cashbox->type === 'bank' ? 'بانکی' : 'نقدی'); ?>

                        </span>
                        <?php if($cashbox->is_active): ?>
                            <span class="badge bg-success">فعال</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">غیرفعال</span>
                        <?php endif; ?>
                    </p>
                    <hr>
                    <div class="row text-center">
                        <div class="col-6">
                            <span class="text-muted small">موجودی فعلی</span><br>
                            <strong class="fs-4 text-<?php echo e($cashbox->current_balance >= 0 ? 'success' : 'danger'); ?>">
                                <?php echo e(number_format($cashbox->current_balance, 0)); ?>

                            </strong><br>
                            <small>افغانی</small>
                        </div>
                        <div class="col-6">
                            <span class="text-muted small">موجودی اولیه</span><br>
                            <strong class="fs-4 text-primary">
                                <?php echo e(number_format($cashbox->initial_balance, 0)); ?>

                            </strong><br>
                            <small>افغانی</small>
                        </div>
                    </div>
                    <?php if($cashbox->notes): ?>
                    <hr>
                    <p class="text-muted small mb-0"><strong>توضیحات:</strong> <?php echo e($cashbox->notes); ?></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        
        <div class="col-md-6 mb-3">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body text-end">
                    <h6 class="card-title">خلاصه تراکنش‌ها (کل)</h6>
                    <?php
                        $totalDeposit    = $cashbox->transactions()->where('type', 'deposit')->sum('amount');
                        $totalWithdrawal = $cashbox->transactions()->where('type', 'withdrawal')->sum('amount');
                    ?>
                    <div class="d-flex justify-content-around text-center">
                        <div>
                            <span class="text-success small">واریز کل</span><br>
                            <strong><?php echo e(number_format($totalDeposit, 0)); ?></strong>
                        </div>
                        <div>
                            <span class="text-danger small">برداشت کل</span><br>
                            <strong><?php echo e(number_format($totalWithdrawal, 0)); ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    
    <div class="btn-group mb-4">
        <form action="<?php echo e(route('school.cashboxes.sync-old')); ?>" method="POST" class="d-inline">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="cashbox_id" value="<?php echo e($cashbox->id); ?>">
            <button type="submit" class="btn btn-sm btn-outline-warning">
                <i class="fas fa-sync-alt me-1"></i> همگام‌سازی قدیمی
            </button>
        </form>

        <form action="<?php echo e(route('school.cashboxes.clean-orphan')); ?>" method="POST" class="d-inline ms-2">
            <?php echo csrf_field(); ?>
            <input type="hidden" name="cashbox_id" value="<?php echo e($cashbox->id); ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger">
                <i class="fas fa-broom me-1"></i> پاک‌سازی یتیم‌ها
            </button>
        </form>
    </div>

    
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <h5 class="card-title mb-3">
                تاریخچه تراکنش‌ها
                <?php if(request('academic_year_id')): ?>
                    <span class="badge bg-info ms-2"><?php echo e(\App\Models\AcademicYear::find(request('academic_year_id'))->name ?? ''); ?></span>
                <?php endif; ?>
            </h5>
            <?php if($transactions->isEmpty()): ?>
                <div class="alert alert-info text-center">هنوز هیچ تراکنشی برای این صندوق ثبت نشده است.</div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>تاریخ</th>
                                <th>نوع</th>
                                <th>مبلغ (افغانی)</th>
                                <th>شرح</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <tr>
                                <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($trx->transaction_date)); ?></td>
                                <td>
                                    <?php if($trx->type === 'deposit'): ?>
                                        <span class="badge bg-success">واریز</span>
                                    <?php elseif($trx->type === 'withdrawal'): ?>
                                        <span class="badge bg-danger">برداشت</span>
                                    <?php endif; ?>
                                </td>
                                <td class="<?php echo e($trx->type === 'deposit' ? 'text-success' : 'text-danger'); ?>">
                                    <?php echo e(number_format($trx->amount, 0)); ?>

                                </td>
                                <td><?php echo e($trx->description ?: '—'); ?></td>
                                <td>
                                    <form action="<?php echo e(route('school.cashbox-transactions.destroy', $trx->id)); ?>" method="POST"
                                          onsubmit="return confirm('آیا از حذف این تراکنش اطمینان دارید؟ (رکورد اصلی نیز حذف خواهد شد)')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="حذف">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    <?php echo e($transactions->links()); ?>

                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\cashboxes\show.blade.php ENDPATH**/ ?>