<?php $__env->startSection('title', 'لیست دانش‌آموزان'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-graduate ms-2"></i> مدیریت دانش‌آموزان</h1>
        <a href="<?php echo e(route('school.students.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus"></i> ثبت دانش‌آموز جدید
        </a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    <?php endif; ?>

    
    <form method="GET" action="<?php echo e(route('school.students.index')); ?>" class="row g-2 mb-4">
        <div class="col-md-3">
            <select name="status_filter" class="form-select">
                <option value="">همه وضعیت‌ها</option>
                <option value="active" <?php echo e(request('status_filter') == 'active' ? 'selected' : ''); ?>>فعال</option>
                <option value="inactive" <?php echo e(request('status_filter') == 'inactive' ? 'selected' : ''); ?>>غیرفعال</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="financial_filter" class="form-select">
                <option value="">همه وضعیت مالی</option>
                <option value="full" <?php echo e(request('financial_filter') == 'full' ? 'selected' : ''); ?>>شهریه کامل</option>
                <option value="discount" <?php echo e(request('financial_filter') == 'discount' ? 'selected' : ''); ?>>دارای تخفیف</option>
                <option value="free" <?php echo e(request('financial_filter') == 'free' ? 'selected' : ''); ?>>رایگان</option>
            </select>
        </div>
        <div class="col-md-4">
            <input type="text" name="search" class="form-control" placeholder="جستجو بر اساس نام، کد، تذکره یا نمبر اساس..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> اعمال</button>
            <?php if(request('status_filter') || request('financial_filter') || request('search')): ?>
                <a href="<?php echo e(route('school.students.index')); ?>" class="btn btn-secondary">حذف فیلترها</a>
            <?php endif; ?>
        </div>
    </form>

    
    <?php if($nextYears->isNotEmpty()): ?>
    <form method="POST" action="<?php echo e(route('school.students.transfer-multiple')); ?>" id="bulkTransferForm" class="mb-3">
        <?php echo csrf_field(); ?>
        <input type="hidden" name="student_ids" id="selectedStudentIds">
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
                <button type="submit" class="btn btn-sm btn-success" onclick="return confirm('آیا از انتقال دانش‌آموزان انتخاب‌شده اطمینان دارید؟')">
                    <i class="fas fa-arrow-right"></i> انتقال انتخاب‌شده‌ها
                </button>
            </div>
        </div>
    </form>
    <?php endif; ?>

    
    <div class="mb-3">
        <span class="badge bg-info fs-6"><?php echo e($studentsCount); ?> دانش‌آموز یافت شد</span>
    </div>

    
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th><input type="checkbox" id="selectAll"></th>
                            <th>کد</th>
                            <th>نام</th>
                            <th>نام پدر</th>
                            <th>صنف</th>
                            <th>وضعیت</th>
                            <th>وضعیت مالی</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php
                                $financialStatus = $student->financial_status;
                                $isFree = $financialStatus === 'free';
                                $hasDiscount = $financialStatus === 'discount';
                            ?>
                            <tr>
                                <td><input type="checkbox" class="student-checkbox" value="<?php echo e($student->id); ?>"></td>
                                <td><?php echo e($student->student_code); ?></td>
                                <td><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                                <td><?php echo e($student->father_name); ?></td>
                                <td><?php echo e($student->class ?? '—'); ?></td>
                                <td>
                                    <?php switch($student->status):
                                        case ('present'): ?> <span class="badge bg-success">فعال</span> <?php break; ?>
                                        <?php case ('blocked'): ?> <span class="badge bg-danger">غیرفعال</span> <?php break; ?>
                                        <?php case ('temporary'): ?> <span class="badge bg-warning text-dark">موقت</span> <?php break; ?>
                                        <?php case ('three_piece'): ?> <span class="badge bg-info">سه‌پارچه</span> <?php break; ?>
                                        <?php default: ?> <span class="badge bg-secondary"><?php echo e($student->status); ?></span>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <?php if($isFree): ?> <span class="badge bg-info">رایگان</span>
                                    <?php elseif($hasDiscount): ?> <span class="badge bg-success">تخفیف‌دار</span>
                                    <?php elseif($financialStatus === 'full'): ?> <span class="badge bg-primary">شهریه کامل</span>
                                    <?php else: ?> <span class="badge bg-secondary">تعیین نشده</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('school.students.show', $student->id)); ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    <a href="<?php echo e(route('school.students.edit', $student->id)); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="<?php echo e(route('school.students.preview', $student->id)); ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-print"></i></a>
                                    <form action="<?php echo e(route('school.students.destroy', $student->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('آیا از حذف این دانش‌آموز اطمینان دارید؟')">
                                        <?php echo csrf_field(); ?>
                                        <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger" title="حذف"><i class="fas fa-trash"></i></button>
                                    </form>

                                    
                                    <?php if($nextYears->isNotEmpty()): ?>
                                    <form method="POST" action="<?php echo e(route('school.students.transfer-single', $student)); ?>" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <input type="hidden" name="target_year_id" value="">
                                        <button type="button" class="btn btn-sm btn-outline-primary transfer-btn" data-student-name="<?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?>">
                                            <i class="fas fa-exchange-alt"></i>
                                        </button>
                                    </form>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="8" class="text-center text-muted py-3">دانش‌آموزی یافت نشد</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer"><?php echo e($students->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
    // انتخاب همه
    document.getElementById('selectAll').addEventListener('change', function() {
        const checkboxes = document.querySelectorAll('.student-checkbox');
        checkboxes.forEach(cb => cb.checked = this.checked);
        updateSelectedIds();
    });

    // به‌روزرسانی فیلد مخفی هنگام تغییر چک‌باکس‌ها
    document.querySelectorAll('.student-checkbox').forEach(cb => {
        cb.addEventListener('change', updateSelectedIds);
    });

    function updateSelectedIds() {
        const selected = [];
        document.querySelectorAll('.student-checkbox:checked').forEach(cb => selected.push(cb.value));
        document.getElementById('selectedStudentIds').value = JSON.stringify(selected);
    }

    // انتقال تکی – انتخاب سال مقصد
    document.querySelectorAll('.transfer-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const form = this.closest('form');
            const year = prompt('لطفاً نام سال مقصد را وارد کنید (مثلاً ۱۴۰۶):', '');
            if (year) {
                // پیدا کردن option متناظر در select گروهی
                const select = document.querySelector('select[name="target_year_id"]');
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
                if (confirm(`آیا از انتقال ${this.dataset.studentName} به سال ${year} اطمینان دارید؟`)) {
                    form.submit();
                }
            }
        });
    });
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\students\index.blade.php ENDPATH**/ ?>