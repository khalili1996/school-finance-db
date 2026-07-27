<?php $__env->startSection('title', 'گزارش تجهیزات'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-cubes ms-2"></i> گزارش تجهیزات</h4>
        <button class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="fas fa-print"></i> چاپ گزارش
        </button>
    </div>

    
    <?php echo $__env->make('partials.report-header', ['title' => 'گزارش تجهیزات', 'subtitle' => ''], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    
    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control form-control-sm" placeholder="جستجوی شرح، کد یا تحویل‌گیرنده..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-md-2">
            <select name="category_id" class="form-select form-select-sm">
                <option value="">همه دسته‌بندی‌ها</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">همه وضعیت‌ها</option>
                <option value="active" <?php echo e(request('status') == 'active' ? 'selected' : ''); ?>>فعال</option>
                <option value="transferred" <?php echo e(request('status') == 'transferred' ? 'selected' : ''); ?>>انتقال</option>
                <option value="broken" <?php echo e(request('status') == 'broken' ? 'selected' : ''); ?>>خراب</option>
                <option value="scrap" <?php echo e(request('status') == 'scrap' ? 'selected' : ''); ?>>اسقاط</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-sm btn-primary">اعمال</button>
            <a href="<?php echo e(route('school.reports.assets')); ?>" class="btn btn-sm btn-secondary">حذف فیلتر</a>
        </div>
    </form>

    
    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" style="font-size: 12px;">
                <thead class="table-light">
                    <tr>
                        <th>کد اموال</th>
                        <th>دسته‌بندی</th>
                        <th>شرح</th>
                        <th>تعداد</th>
                        <th>تحویل‌گیرنده</th>
                        <th>قیمت واحد</th>
                        <th>قیمت کل</th>
                        <th>تاریخ خرید</th>
                        <th>وضعیت</th>
                        <th>توضیحات</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $assets; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $asset): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td><?php echo e($asset->asset_code); ?></td>
                        <td><?php echo e($asset->category->name ?? '—'); ?></td>
                        <td><?php echo e($asset->description); ?></td>
                        <td><?php echo e($asset->quantity); ?></td>
                        <td><?php echo e($asset->custodian ?? '—'); ?></td>
                        <td><?php echo e(number_format($asset->unit_price)); ?></td>
                        <td><?php echo e(number_format($asset->total_price)); ?></td>
                        <td><?php echo e(\App\Helpers\JalaliHelper::toJalali($asset->purchase_date)); ?></td>
                        <td>
                            <?php switch($asset->status):
                                case ('active'): ?> <span class="badge bg-success">فعال</span> <?php break; ?>
                                <?php case ('transferred'): ?> <span class="badge bg-info">انتقال</span> <?php break; ?>
                                <?php case ('broken'): ?> <span class="badge bg-danger">خراب</span> <?php break; ?>
                                <?php case ('scrap'): ?> <span class="badge bg-secondary">اسقاط</span> <?php break; ?>
                            <?php endswitch; ?>
                        </td>
                        <td><?php echo e($asset->notes ?? '—'); ?></td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr><td colspan="10" class="text-center text-muted py-4">تجهیزاتی با این فیلترها یافت نشد.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer bg-white"><?php echo e($assets->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('styles'); ?>
<style>
    @media print {
        .btn, form, .breadcrumb, .card-footer, #sidebar-wrapper, header {
            display: none !important;
        }
        #page-content-wrapper { padding: 0 !important; }
        .table { font-size: 11px; }
        .card { border: 1px solid #ddd !important; box-shadow: none !important; }
    }
</style>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\reports\assets.blade.php ENDPATH**/ ?>