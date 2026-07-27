<?php $__env->startSection('title', 'اقساط قرض‌الحسنه – ' . $loan->borrower_name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.loans.index')); ?>">قرض‌الحسنه</a></li>
            <li class="breadcrumb-item active">اقساط</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0"><i class="fas fa-calendar-check ms-2"></i> اقساط وام: <?php echo e($loan->borrower_name); ?> <?php echo e($loan->borrower_last_name); ?></h5>
            <a href="<?php echo e(route('school.loans.show', $loan)); ?>" target="_blank" class="btn btn-light btn-sm">
                <i class="fas fa-print"></i> پیش‌نمایش چاپ
            </a>
        </div>
        <div class="card-body">
            <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>

            <table class="table table-bordered table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>مبلغ قسط</th>
                        <th>سررسید</th>
                        <th>پرداخت شده</th>
                        <th>تاریخ پرداخت</th>
                        <th>وضعیت</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $loan->installments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $installment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration); ?></td>
                        <td><?php echo e(number_format($installment->amount)); ?> ؋</td>
                        <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($installment->due_date)); ?></td>
                        <td><?php echo e($installment->paid_amount ? number_format($installment->paid_amount) : '—'); ?></td>
                        <td><?php echo e($installment->paid_date ? \App\Helpers\JalaliHelper::toJalali($installment->paid_date) : '—'); ?></td>
                        <td>
                            <?php if($installment->status == 'paid'): ?>
                                <span class="badge bg-success">پرداخت</span>
                            <?php else: ?>
                                <span class="badge bg-danger">معوق</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group btn-group-sm">
                                <?php if($installment->status == 'pending'): ?>
                                    
                                    <button class="btn btn-outline-success pay-installment-btn"
                                            data-installment-id="<?php echo e($installment->id); ?>"
                                            data-amount="<?php echo e($installment->amount); ?>">
                                        <i class="fa fa-money-bill-wave"></i> پرداخت
                                    </button>
                                <?php else: ?>
                                    
                                    <a href="<?php echo e(route('school.installments.receipt', $installment)); ?>"
                                       target="_blank" class="btn btn-outline-info" title="رسید چاپی">
                                        <i class="fa fa-receipt"></i>
                                    </a>
                                    
                                    <a href="<?php echo e(route('school.installments.edit', $installment)); ?>"
                                       class="btn btn-outline-warning" title="ویرایش پرداخت">
                                        <i class="fa fa-pencil"></i>
                                    </a>
                                    
                                    <form action="<?php echo e(route('school.installments.destroy', $installment)); ?>"
                                          method="POST" class="d-inline"
                                          onsubmit="return confirm('با حذف این پرداخت، قسط به حالت معوق برمی‌گردد. ادامه؟')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-outline-danger" title="حذف پرداخت">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="7" class="text-center">هیچ قسطی وجود ندارد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>


<div class="modal fade" id="payInstallmentModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">پرداخت قسط</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="payForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="installment_id" name="installment_id">
                    <div class="mb-3">
                        <label class="form-label">مبلغ (افغانی)</label>
                        <input type="text" id="modal_amount" class="form-control" readonly>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">تاریخ پرداخت (شمسی)</label>
                        <input type="text" id="modal_paid_date" class="form-control" value="<?php echo e(\App\Helpers\JalaliHelper::todayJalali()); ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">یادداشت</label>
                        <input type="text" id="modal_notes" class="form-control">
                    </div>
                    <button type="button" id="submitPayInstallment" class="btn btn-success w-100">ثبت پرداخت</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.querySelectorAll('.pay-installment-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('installment_id').value = this.dataset.installmentId;
        document.getElementById('modal_amount').value = this.dataset.amount;
        new bootstrap.Modal(document.getElementById('payInstallmentModal')).show();
    });
});

document.getElementById('submitPayInstallment').addEventListener('click', function() {
    const data = {
        installment_id: document.getElementById('installment_id').value,
        paid_date: document.getElementById('modal_paid_date').value,
        notes: document.getElementById('modal_notes').value,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    fetch('<?php echo e(route('school.installments.pay')); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            alert('پرداخت ثبت شد.');
            location.reload();
        } else {
            alert('خطا: ' + (response.message || 'مشکلی رخ داد'));
        }
    })
    .catch(() => alert('خطا در ارتباط با سرور'));
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\loans\installments.blade.php ENDPATH**/ ?>