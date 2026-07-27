<?php $__env->startSection('title', 'ترم‌ها'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">📆 ترم‌های تحصیلی</h4>
        <a href="<?php echo e(route('school.terms.create')); ?>" class="btn btn-success">
            <i class="fas fa-plus-circle me-1"></i> ایجاد ترم جدید
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="<?php echo e(route('school.terms.index')); ?>" class="row g-2">
                <div class="col-md-4">
                    <select name="academic_year_id" class="form-select">
                        <option value="">همه سال‌ها</option>
                        <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($ay->id); ?>" <?php echo e(request('academic_year_id') == $ay->id ? 'selected' : ''); ?>><?php echo e($ay->name); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-secondary">فیلتر</button>
                </div>
            </form>
        </div>
    </div>

    <?php if($terms->isEmpty()): ?>
        <div class="alert alert-info">هیچ ترمی یافت نشد.</div>
    <?php else: ?>
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>سال مالی</th>
                            <th>نام</th>
                            <th>نوع</th>
                            <th>شروع</th>
                            <th>پایان</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($term->academicYear->name ?? '—'); ?></td>
                            <td><?php echo e($term->name); ?></td>
                            <td><?php echo e($term->type ?: '—'); ?></td>
                            <td><?php echo \App\Helpers\JalaliHelper::toJalali($term->start_date); ?></td>
                            <td><?php echo \App\Helpers\JalaliHelper::toJalali($term->end_date); ?></td>
                            <td>
                                <?php if($term->is_active): ?>
                                    <span class="badge bg-success">فعال</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">غیرفعال</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="<?php echo e(route('school.terms.edit', $term)); ?>" class="btn btn-outline-warning"><i class="fa fa-pencil"></i></a>
                                    <form action="<?php echo e(route('school.terms.destroy', $term)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button class="btn btn-outline-danger" onclick="return confirm('حذف شود؟')"><i class="fa fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                <?php echo e($terms->links()); ?>

            </div>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\terms\index.blade.php ENDPATH**/ ?>