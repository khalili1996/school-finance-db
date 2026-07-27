<?php $__env->startSection('title', 'معاشات کارمندان'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid p-0">
    
    <div class="sticky-toolbar bg-white shadow-sm border-bottom px-3 py-2 mb-3">
        <div class="d-flex align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center">
                <i class="fas fa-money-check-alt fa-lg text-success ms-2"></i>
                <h5 class="fw-bold mb-0 text-dark">معاشات کارمندان (نمای ماهانه)</h5>
            </div>
            <div class="btn-group">
                <a href="<?php echo e(route('school.salaries.create')); ?>" class="btn btn-success rounded-pill px-3 py-2">
                    <i class="fas fa-plus-circle ms-1"></i> ثبت معاش
                </a>
            </div>
            <div class="btn-group">
                <a href="<?php echo e(route('school.salaries.print-report', request()->query())); ?>"
                   target="_blank"
                   class="btn btn-outline-info rounded-pill px-3 py-2">
                    <i class="fas fa-print ms-1"></i> چاپ گزارش
                </a>
            </div>
        </div>
    </div>

    <div class="px-3">
        <?php if(session('success')): ?><div class="alert alert-success"><?php echo e(session('success')); ?></div><?php endif; ?>
        <?php if(session('error')): ?><div class="alert alert-danger"><?php echo e(session('error')); ?></div><?php endif; ?>

        
        <form method="GET" action="<?php echo e(route('school.salaries.index')); ?>" class="row g-2 mb-4">
            <div class="col-md-2">
                <label class="form-label">کارمند</label>
                <select name="employee_id" class="form-select">
                    <option value="">همه</option>
                    <?php $__currentLoopData = $allEmployees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($emp->id); ?>" <?php echo e(request('employee_id') == $emp->id ? 'selected' : ''); ?>>
                            <?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?>

                        </option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">از ماه</label>
                <select name="month_from" class="form-select">
                    <option value="">انتخاب</option>
                    <?php $__currentLoopData = $allMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m->id); ?>" <?php echo e(request('month_from') == $m->id ? 'selected' : ''); ?>><?php echo e($m->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">تا ماه</label>
                <select name="month_to" class="form-select">
                    <option value="">انتخاب</option>
                    <?php $__currentLoopData = $allMonths; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($m->id); ?>" <?php echo e(request('month_to') == $m->id ? 'selected' : ''); ?>><?php echo e($m->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">وضعیت پرداخت</label>
                <select name="payment_status" class="form-select">
                    <option value="">همه</option>
                    <option value="unpaid" <?php echo e(request('payment_status') == 'unpaid' ? 'selected' : ''); ?>>پرداخت نشده</option>
                    <option value="paid"   <?php echo e(request('payment_status') == 'paid' ? 'selected' : ''); ?>>پرداخت کامل</option>
                </select>
            </div>
            <div class="col-auto align-self-end">
                <button type="submit" class="btn btn-primary">اعمال</button>
                <a href="<?php echo e(route('school.salaries.index')); ?>" class="btn btn-secondary">حذف فیلتر</a>
            </div>
        </form>

        
        <?php if(empty($matrix) || count($matrix) == 0): ?>
            <div class="alert alert-info text-center py-5">
                <h4>هیچ معاشی برای نمایش وجود ندارد</h4>
                <p>لطفاً ابتدا از دکمه «ثبت معاش» برای ثبت معاش یک کارمند استفاده کنید.</p>
                <a href="<?php echo e(route('school.salaries.create')); ?>" class="btn btn-success mt-2">
                    <i class="fas fa-plus-circle"></i> ثبت اولین معاش
                </a>
            </div>
        <?php else: ?>
            <div class="card shadow">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover mb-0" style="font-size: 12px;">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>نام کارمند</th>
                                <th>سمت</th>
                                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <th class="text-center"><?php echo e($month->name); ?></th>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                <th>جمع کل</th>
                                <th>پرداخت شده</th>
                                <th>باقی‌مانده</th>
                                <th>پرینت</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $matrix; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php $emp = $row['employee']; ?>
                                <tr>
                                    <td><?php echo e($loop->iteration); ?></td>
                                    <td><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></td>
                                    <td><?php echo e($emp->position->name ?? '—'); ?></td>
                                    <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <?php $cell = $row['months'][$month->id] ?? null; ?>
                                        <td class="text-center <?php echo e($cell['isPaid'] ?? false ? 'bg-success-light' : ($cell['amount'] > 0 ? 'bg-warning-light' : '')); ?>">
                                            <?php if($cell && $cell['amount'] > 0): ?>
                                                <div><?php echo e(number_format($cell['amount'])); ?></div>
                                                <?php if($cell['isPaid']): ?>
                                                    <span class="badge bg-success">پرداخت</span>
                                                <?php else: ?>
                                                    <span class="text-danger"><?php echo e(number_format($cell['remaining'])); ?></span>
                                                    <button class="btn btn-sm btn-outline-success mt-1 pay-salary-btn"
                                                        data-employee-id="<?php echo e($emp->id); ?>"
                                                        data-month-id="<?php echo e($month->id); ?>"
                                                        data-remaining="<?php echo e($cell['remaining']); ?>">
                                                        پرداخت
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <div class="mt-1">
                                                    <a href="<?php echo e(route('school.salaries.edit', $cell['salary']->id)); ?>"
                                                       class="btn btn-sm btn-outline-warning px-1" title="ویرایش">
                                                        <i class="fa fa-pencil"></i>
                                                    </a>
                                                    <form action="<?php echo e(route('school.salaries.destroy', $cell['salary']->id)); ?>"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('آیا از حذف این معاش اطمینان دارید؟')">
                                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                        <button type="submit" class="btn btn-sm btn-outline-danger px-1" title="حذف">
                                                            <i class="fa fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            <?php else: ?>
                                                —
                                            <?php endif; ?>
                                        </td>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    <td class="fw-bold"><?php echo e(number_format($row['totalAmount'])); ?></td>
                                    <td class="text-success fw-bold"><?php echo e(number_format($row['totalPaid'])); ?></td>
                                    <td class="text-danger fw-bold"><?php echo e(number_format($row['totalRemaining'])); ?></td>
                                    <td class="text-center">
                                        <a href="<?php echo e(route('school.salaries.preview', ['employee' => $emp->id, 'month_from' => request('month_from'), 'month_to' => request('month_to')])); ?>"
                                           target="_blank" class="btn btn-sm btn-outline-secondary" title="پیش‌نمایش چاپ">
                                            <i class="fas fa-print"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>


<div class="modal fade" id="quickPayModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title"><i class="fas fa-money-bill-wave me-1"></i> پرداخت سریع معاش</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="quickPayForm">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" id="quick_employee_id" name="employee_id">
                    <input type="hidden" id="quick_month_id" name="month_id">
                    <div class="mb-3">
                        <label class="form-label">مبلغ (افغانی)</label>
                        <input type="number" id="quick_amount" name="amount" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">صندوق</label>
                        <select id="quick_cashbox" name="cashbox_id" class="form-select" required>
                            <option value="">-- انتخاب --</option>
                            <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cb->id); ?>"><?php echo e($cb->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">تاریخ</label>
                        <input type="text" id="quick_date" name="payment_date" class="form-control"
                               value="<?php echo e(\App\Helpers\JalaliHelper::todayJalali()); ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">شماره رسید</label>
                        <input type="text" id="quick_receipt" name="receipt_number" class="form-control">
                    </div>
                    <button type="button" id="submitQuickPay" class="btn btn-success w-100">ثبت پرداخت</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    .sticky-toolbar { position: sticky; top: 0; z-index: 1020; background: #fff; }
    .bg-success-light { background-color: #d4edda !important; }
    .bg-warning-light { background-color: #fff3cd !important; }
</style>
<?php $__env->stopPush(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
document.querySelectorAll('.pay-salary-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        document.getElementById('quick_employee_id').value = this.dataset.employeeId;
        document.getElementById('quick_month_id').value = this.dataset.monthId;
        document.getElementById('quick_amount').value = this.dataset.remaining;
        new bootstrap.Modal(document.getElementById('quickPayModal')).show();
    });
});

document.getElementById('submitQuickPay').addEventListener('click', function() {
    const data = {
        employee_id: document.getElementById('quick_employee_id').value,
        month_id: document.getElementById('quick_month_id').value,
        amount: document.getElementById('quick_amount').value,
        cashbox_id: document.getElementById('quick_cashbox').value,
        payment_date: document.getElementById('quick_date').value,
        receipt_number: document.getElementById('quick_receipt').value,
        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
    };

    fetch('<?php echo e(route('school.salary-payments.quick-store')); ?>', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'Accept': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(response => {
        if (response.success) {
            alert('پرداخت با موفقیت ثبت شد.');
            location.reload();
        } else {
            alert('خطا: ' + (response.message || 'مشکلی رخ داد'));
        }
    })
    .catch(() => alert('خطا در ارتباط با سرور'));
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\salaries\index.blade.php ENDPATH**/ ?>