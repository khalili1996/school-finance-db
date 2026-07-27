<?php $__env->startSection('title', 'ویرایش ولی'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.guardians.index')); ?>">اولیا</a></li>
            <li class="breadcrumb-item active">ویرایش <?php echo e($guardian->full_name); ?></li>
        </ol>
    </nav>

    <div class="row">
        
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش: <?php echo e($guardian->full_name); ?></h5>
                </div>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
                    <?php endif; ?>

                    <form action="<?php echo e(route('school.guardians.update', $guardian->id)); ?>" method="POST">
                        <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نام کامل <span class="text-danger">*</span></label>
                                <input type="text" name="full_name" class="form-control" value="<?php echo e(old('full_name', $guardian->full_name)); ?>" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">نسبت</label>
                                <select name="relation" class="form-select">
                                    <option value="">-- انتخاب --</option>
                                    <option value="father" <?php echo e(old('relation', $guardian->relation) == 'father' ? 'selected' : ''); ?>>پدر</option>
                                    <option value="mother" <?php echo e(old('relation', $guardian->relation) == 'mother' ? 'selected' : ''); ?>>مادر</option>
                                    <option value="brother" <?php echo e(old('relation', $guardian->relation) == 'brother' ? 'selected' : ''); ?>>برادر</option>
                                    <option value="uncle" <?php echo e(old('relation', $guardian->relation) == 'uncle' ? 'selected' : ''); ?>>کاکا / ماما</option>
                                    <option value="other" <?php echo e(old('relation', $guardian->relation) == 'other' ? 'selected' : ''); ?>>سایر</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">شماره تماس</label>
                                <input type="text" name="phone" class="form-control" value="<?php echo e(old('phone', $guardian->phone)); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">شماره تماس دوم</label>
                                <input type="text" name="secondary_phone" class="form-control" value="<?php echo e(old('secondary_phone', $guardian->secondary_phone)); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">کد ملی</label>
                                <input type="text" name="national_id" class="form-control" value="<?php echo e(old('national_id', $guardian->national_id)); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">تحصیلات</label>
                                <input type="text" name="education" class="form-control" value="<?php echo e(old('education', $guardian->education)); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">شغل</label>
                                <input type="text" name="job" class="form-control" value="<?php echo e(old('job', $guardian->job)); ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">وضعیت</label>
                                <select name="is_active" class="form-select">
                                    <option value="1" <?php echo e(old('is_active', $guardian->is_active) == '1' ? 'selected' : ''); ?>>فعال</option>
                                    <option value="0" <?php echo e(old('is_active', $guardian->is_active) == '0' ? 'selected' : ''); ?>>غیرفعال</option>
                                </select>
                            </div>
                            <div class="col-12 mb-3">
                                <label class="form-label">آدرس</label>
                                <textarea name="address" class="form-control" rows="2"><?php echo e(old('address', $guardian->address)); ?></textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                        <a href="<?php echo e(route('school.guardians.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
                    </form>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-users ms-2"></i> دانش‌آموزان مرتبط</h5>
                </div>
                <div class="card-body p-0">
                    <?php if($guardian->students->isNotEmpty()): ?>
                        <ul class="list-group list-group-flush">
                            <?php $__currentLoopData = $guardian->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></span>
                                    <span class="badge bg-primary"><?php echo e($student->class ?? '—'); ?></span>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </ul>
                    <?php else: ?>
                        <p class="text-muted p-3 mb-0">دانش‌آموزی به این ولی متصل نیست.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\guardians\edit.blade.php ENDPATH**/ ?>