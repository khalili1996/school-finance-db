<?php $__env->startSection('title', 'تراکنش‌های صندوق'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">📊 تراکنش‌های صندوق</h4>
        <a href="<?php echo e(route('school.cashbox-transactions.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus-circle me-1"></i> تراکنش جدید
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('school.cashbox-transactions.index')); ?>" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label for="academic_year_id" class="form-label">سال مالی</label>
                    <select name="academic_year_id" id="academic_year_id" class="form-select" onchange="this.form.submit()">
                        <option value="">همه سال‌ها</option>
                        <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($yr->id); ?>" <?php echo e(request('academic_year_id') == $yr->id ? 'selected' : ''); ?>>
                                <?php echo e($yr->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">نوع تراکنش</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">همه</option>
                        <option value="deposit" <?php echo e(request('type') === 'deposit' ? 'selected' : ''); ?>>واریز</option>
                        <option value="withdrawal" <?php echo e(request('type') === 'withdrawal' ? 'selected' : ''); ?>>برداشت</option>
                        <option value="transfer" <?php echo e(request('type') === 'transfer' ? 'selected' : ''); ?>>انتقال</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="cashbox_id" class="form-label">صندوق</label>
                    <select name="cashbox_id" id="cashbox_id" class="form-select">
                        <option value="">همه</option>
                        <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($cb->id); ?>" <?php echo e(request('cashbox_id') == $cb->id ? 'selected' : ''); ?>>
                                <?php echo e($cb->name); ?>

                            </option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="from_date" class="form-label">از تاریخ</label>
                    <input type="date" name="from_date" id="from_date" class="form-control" value="<?php echo e(request('from_date')); ?>">
                </div>
                <div class="col-md-2">
                    <label for="to_date" class="form-label">تا تاریخ</label>
                    <input type="date" name="to_date" id="to_date" class="form-control" value="<?php echo e(request('to_date')); ?>">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-search me-1"></i> فیلتر
                    </button>
                    <a href="<?php echo e(route('school.cashbox-transactions.index')); ?>" class="btn btn-outline-danger w-100">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    
    <?php if($transactions->isEmpty()): ?>
        <div class="alert alert-info text-center">هیچ تراکنشی یافت نشد.</div>
    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>تاریخ</th>
                            <th>صندوق</th>
                            <th>نوع</th>
                            <th>مبلغ (افغانی)</th>
                            <th>شرح</th>
                            <th>منبع</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $transactions; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trx): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($trx->transaction_date); ?></td>
                            <td><?php echo e($trx->cashbox->name ?? '—'); ?></td>
                            <td>
                                <?php if($trx->type === 'deposit'): ?>
                                    <span class="badge bg-success">واریز</span>
                                <?php elseif($trx->type === 'withdrawal'): ?>
                                    <span class="badge bg-danger">برداشت</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark">انتقال</span>
                                <?php endif; ?>
                            </td>
                            <td class="<?php echo e($trx->type === 'deposit' ? 'text-success' : 'text-danger'); ?>">
                                <?php echo e(number_format($trx->amount, 0)); ?>

                            </td>
                            <td><?php echo e($trx->description ?: '—'); ?></td>
                            <td>
                                <?php if($trx->reference): ?>
                                    <?php echo e(class_basename($trx->reference_type)); ?> #<?php echo e($trx->reference_id); ?>

                                <?php else: ?>
                                    —
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('school.cashbox-transactions.show', $trx)); ?>" class="btn btn-outline-secondary" title="جزئیات">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="<?php echo e(route('school.cashbox-transactions.receipt', $trx)); ?>" class="btn btn-outline-info" title="رسید چاپی">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <?php echo e($transactions->links()); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\cashbox_transactions\index.blade.php ENDPATH**/ ?>