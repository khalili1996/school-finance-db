<?php $__env->startSection('title', 'لیست کارمندان'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-user-tie ms-2"></i> مدیریت کارمندان</h1>
    <div class="d-flex gap-2">
        <a href="<?php echo e(route('school.employees.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> ثبت کارمند جدید
        </a>
        <a href="<?php echo e(route('school.employees.trash')); ?>" class="btn btn-outline-danger">
            <i class="fas fa-trash-alt"></i> سطل زباله
            (<?php echo e(\App\Models\Employee::onlyTrashed()->where('school_id', session('active_school_id', auth()->user()->school_id))->count()); ?>)
        </a>
    </div>
</div>

    <?php if(session('success')): ?> <div class="alert alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>
    <?php if(session('error')): ?> <div class="alert alert-danger"><?php echo e(session('error')); ?></div> <?php endif; ?>

    
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-2"><input type="text" name="search" class="form-control" placeholder="جستجوی نام، کد، تماس..." value="<?php echo e(request('search')); ?>"></div>
        <div class="col-md-2">
            <select name="role_id" class="form-select" onchange="this.form.submit()">
                <option value="">همه سمت‌ها</option>
                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($role->id); ?>" <?php echo e(request('role_id') == $role->id ? 'selected' : ''); ?>><?php echo e($role->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">همه وضعیت‌ها</option>
                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>فعال</option>
                <option value="inactive" <?php echo e(request('status') == 'inactive' ? 'selected' : ''); ?>>غیرفعال</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> اعمال</button>
            <a href="<?php echo e(route('school.employees.index')); ?>" class="btn btn-secondary">حذف فیلترها</a>
        </div>
    </form>

    
    <?php if(isset($nextYears) && $nextYears->isNotEmpty()): ?>
    <form method="POST" action="<?php echo e(route('school.employees.transfer-multiple')); ?>" id="bulkTransferForm" class="mb-3">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="employee_ids" id="selectedEmployeeIds">
        <div class="row g-2 align-items-center">
            <div class="col-auto">
                <label>انتقال انتخاب‌شده‌ها به سال:</label>
            </div>
            <div class="col-md-3">
                <select name="target_year_id" class="form-select form-select-sm" required>
                    <option value="">-- انتخاب سال --</option>
                    <?php $__currentLoopData = $nextYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <option value="<?php echo e($yr->id); ?>"><?php echo e($yr->name); ?></option>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('آیا از انتقال کارمندان انتخاب‌شده اطمینان دارید؟')">
                    <i class="fas fa-arrow-right"></i> انتقال انتخاب‌شده‌ها
                </button>
            </div>
        </div>
    </form>
    <?php endif; ?>

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        
                        <th><input type="checkbox" id="selectAll"></th>
                        <th>کد</th><th>نام</th><th>سمت</th><th>تماس</th><th>وضعیت</th><th>تاریخ استخدام</th><th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            
                            <td><input type="checkbox" class="employee-checkbox" value="<?php echo e($emp->id); ?>"></td>
                            <td><?php echo e($emp->employee_code); ?></td>
                            <td><?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?></td>
                            <td><?php echo e($emp->employeeRole->name ?? '—'); ?></td>
                            <td><?php echo e($emp->phone ?? '—'); ?></td>
                            <td><span class="badge bg-<?php echo e($emp->status == 'active' ? 'success' : 'danger'); ?>"><?php echo e($emp->status == 'active' ? 'فعال' : 'غیرفعال'); ?></span></td>
                            <td><?php echo e($emp->hire_date ? \App\Helpers\JalaliHelper::toJalali($emp->hire_date) : '—'); ?></td>
                            <td>
                                <a href="<?php echo e(route('school.employees.show', $emp->id)); ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('school.employees.edit', $emp->id)); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('school.employees.destroy', $emp->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('حذف شود؟')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>

                                
                                <?php if(isset($nextYears) && $nextYears->isNotEmpty()): ?>
                                <form method="POST" action="<?php echo e(route('school.employees.transfer-single', $emp)); ?>" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <input type="hidden" name="target_year_id" value="">
                                    <button type="button" class="btn btn-sm btn-outline-primary transfer-btn" data-employee-name="<?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?>">
                                        <i class="fas fa-exchange-alt"></i>
                                    </button>
                                </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="text-center text-muted py-3">کارمندی یافت نشد</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer"><?php echo e($employees->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // انتخاب همه
    document.getElementById('selectAll')?.addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.employee-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateSelectedIds();
    });

    // به‌روزرسانی فیلد مخفی هنگام تغییر چک‌باکس‌ها
    document.querySelectorAll('.employee-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedIds);
    });

    function updateSelectedIds() {
        const selected = [];
        document.querySelectorAll('.employee-checkbox:checked').forEach(cb => selected.push(cb.value));
        const hiddenInput = document.getElementById('selectedEmployeeIds');
        if (hiddenInput) {
            hiddenInput.value = JSON.stringify(selected);
        }
    }

    // انتقال تکی – انتخاب سال مقصد
    document.querySelectorAll('.transfer-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('form');
            const year = prompt('لطفاً نام سال مقصد را وارد کنید (مثلاً ۱۴۰۶):', '');
            if (year) {
                const select = document.querySelector('select[name="target_year_id"]');
                if (!select) {
                    alert('سال مقصد یافت نشد. لطفاً با پشتیبانی تماس بگیرید.');
                    return;
                }
                let found = false;
                for (let opt of select.options) {
                    if (opt.text === year) {
                        form.querySelector('input[name="target_year_id"]').value = opt.value;
                        found = true;
                        break;
                    }
                }
                if (!found) {
                    alert('سال وارد شده یافت نشد. لطفاً دقت کنید.');
                    return;
                }
                if (confirm(`آیا از انتقال ${this.dataset.employeeName} به سال ${year} اطمینان دارید؟`)) {
                    form.submit();
                }
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\employees\index.blade.php ENDPATH**/ ?>