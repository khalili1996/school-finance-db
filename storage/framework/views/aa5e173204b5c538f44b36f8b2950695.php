<?php $__env->startSection('title', 'گزارش دانش‌آموزان'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-users ms-2"></i> گزارش دانش‌آموزان</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    
    <?php echo $__env->make('partials.report-header', ['title' => 'گزارش دانش‌آموزان', 'subtitle' => ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <select name="class_filter" class="form-select form-select-sm">
                <option value="">همه صنف‌ها</option>
                <?php $__currentLoopData = $classes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cls): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cls); ?>" <?php echo e(request('class_filter') == $cls ? 'selected' : ''); ?>><?php echo e($cls); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-3">
            <select name="status_filter" class="form-select form-select-sm">
                <option value="">همه وضعیت‌ها</option>
                <option value="present" <?php echo e(request('status_filter') == 'present' ? 'selected' : ''); ?>>حاضر</option>
                <option value="blocked" <?php echo e(request('status_filter') == 'blocked' ? 'selected' : ''); ?>>محروم</option>
                <option value="temporary" <?php echo e(request('status_filter') == 'temporary' ? 'selected' : ''); ?>>موقت</option>
                <option value="three_piece" <?php echo e(request('status_filter') == 'three_piece' ? 'selected' : ''); ?>>سه‌پارچه</option>
            </select>
        </div>
        <div class="col-md-3">
            <select name="financial_filter" class="form-select form-select-sm">
                <option value="">همه وضعیت‌های مالی</option>
                <option value="debtor" <?php echo e(request('financial_filter') == 'debtor' ? 'selected' : ''); ?>>بدهکار</option>
                <option value="discount" <?php echo e(request('financial_filter') == 'discount' ? 'selected' : ''); ?>>تخفیف‌دار</option>
                <option value="free" <?php echo e(request('financial_filter') == 'free' ? 'selected' : ''); ?>>رایگان</option>
                <option value="orphan" <?php echo e(request('financial_filter') == 'orphan' ? 'selected' : ''); ?>>یتیم</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
            <a href="<?php echo e(route('school.reports.students')); ?>" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    
    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>کد</th>
                        <th>نام</th>
                        <th>نام پدر</th>
                        <th>صنف</th>
                        <th>وضعیت</th>
                        <th>وضعیت مالی</th>
                        <th>تلفن</th>
                        <th>ولی</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($student->student_code); ?></td>
                        <td><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                        <td><?php echo e($student->father_name ?? '—'); ?></td>
                        <td><?php echo e($student->class ?? '—'); ?></td>
                        <td>
                            <?php switch($student->status):
                                case ('present'): ?> <span class="badge bg-success">حاضر</span> <?php break; ?>
                                <?php case ('blocked'): ?> <span class="badge bg-danger">محروم</span> <?php break; ?>
                                <?php case ('temporary'): ?> <span class="badge bg-warning text-dark">موقت</span> <?php break; ?>
                                <?php case ('three_piece'): ?> <span class="badge bg-info">سه‌پارچه</span> <?php break; ?>
                                <?php default: ?> <?php echo e($student->status); ?>

                            <?php endswitch; ?>
                        </td>
                        <td>
                            <?php if($student->financial_status == 'free'): ?>
                                <span class="badge bg-primary">رایگان</span>
                            <?php elseif($student->studentFees->where('discount', '>', 0)->count() > 0): ?>
                                <span class="badge bg-warning text-dark">تخفیف‌دار</span>
                            <?php else: ?>
                                <span class="badge bg-secondary">عادی</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($student->phone ?? '—'); ?></td>
                        <td><?php echo e($student->guardian->full_name ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">دانش‌آموزی با این شرایط یافت نشد.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            <?php echo e($students->appends(request()->query())->links()); ?>

        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    @media print {
        .btn, form, .breadcrumb, .card-footer, #sidebar-wrapper, header {
            display: none !important;
        }
        #page-content-wrapper {
            padding: 0 !important;
        }
        .table {
            font-size: 12px;
        }
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\reports\students.blade.php ENDPATH**/ ?>