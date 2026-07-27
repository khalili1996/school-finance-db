<?php $__env->startSection('title', 'ویرایش ترم'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.terms.index')); ?>">ترم‌ها</a></li>
            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش ترم</h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
            <?php endif; ?>

            <form action="<?php echo e(route('school.terms.update', $term)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">سال مالی <span class="text-danger">*</span></label>
                        <select name="academic_year_id" class="form-select" required>
                            <option value="">-- انتخاب کنید --</option>
                            <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($ay->id); ?>" <?php echo e(old('academic_year_id', $term->academic_year_id) == $ay->id ? 'selected' : ''); ?>><?php echo e($ay->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نام <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?php echo e(old('name', $term->name)); ?>" required>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">نوع</label>
                        <select name="type" class="form-select">
                            <option value="">-- بدون نوع --</option>
                            <option value="spring" <?php echo e(old('type', $term->type) == 'spring' ? 'selected' : ''); ?>>بهار</option>
                            <option value="fall" <?php echo e(old('type', $term->type) == 'fall' ? 'selected' : ''); ?>>پاییز</option>
                            <option value="winter" <?php echo e(old('type', $term->type) == 'winter' ? 'selected' : ''); ?>>زمستان</option>
                            <option value="summer" <?php echo e(old('type', $term->type) == 'summer' ? 'selected' : ''); ?>>تابستان</option>
                        </select>
                    </div>
                   <div class="col-md-4 mb-3">
    <label class="form-label">تاریخ شروع <span class="text-danger">*</span></label>
    <input type="text" name="start_date" class="form-control" placeholder="مثال: 1405/06/01"
           value="<?php echo e(old('start_date', \App\Helpers\JalaliHelper::toJalali($term->start_date))); ?>" required>
</div>
<div class="col-md-4 mb-3">
    <label class="form-label">تاریخ پایان <span class="text-danger">*</span></label>
    <input type="text" name="end_date" class="form-control" placeholder="مثال: 1405/09/01"
           value="<?php echo e(old('end_date', \App\Helpers\JalaliHelper::toJalali($term->end_date))); ?>" required>
</div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">وضعیت</label>
                        <select name="is_active" class="form-select">
                            <option value="0" <?php echo e(old('is_active', $term->is_active) == '0' ? 'selected' : ''); ?>>غیرفعال</option>
                            <option value="1" <?php echo e(old('is_active', $term->is_active) == '1' ? 'selected' : ''); ?>>فعال</option>
                        </select>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                <a href="<?php echo e(route('school.terms.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\terms\edit.blade.php ENDPATH**/ ?>