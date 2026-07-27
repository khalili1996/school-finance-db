<?php $__env->startSection('title', 'گزارش اولیا'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-user-friends ms-2"></i> گزارش اولیا</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    
    <?php echo $__env->make('partials.report-header', ['title' => 'گزارش اولیا', 'subtitle' => ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="جستجوی نام ولی..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">جستجو</button>
            <a href="<?php echo e(route('school.reports.guardians')); ?>" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    
    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>نام ولی</th>
                        <th>تلفن</th>
                        <th>تعداد فرزندان</th>
                        <th>آدرس</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $guardians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guardian): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($loop->iteration + $guardians->firstItem() - 1); ?></td>
                        <td><?php echo e($guardian->full_name); ?></td>
                        <td><?php echo e($guardian->phone ?? '—'); ?></td>
                        <td><?php echo e($guardian->students_count); ?></td>
                        <td><?php echo e($guardian->address ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">اولیایی با این مشخصات یافت نشد.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white">
            <?php echo e($guardians->appends(request()->query())->links()); ?>

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

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\reports\guardians.blade.php ENDPATH**/ ?>