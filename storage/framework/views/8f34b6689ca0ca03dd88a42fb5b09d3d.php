<?php $__env->startSection('title', 'مدیریت اولیا'); ?>
<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-friends ms-2"></i> مدیریت اولیا</h1>
        <a href="<?php echo e(route('school.guardians.create')); ?>" class="btn btn-success"><i class="fas fa-plus"></i> ثبت ولی جدید</a>
    </div>
    <?php if(session('success')): ?> <div class="alert alert-success"><?php echo e(session('success')); ?></div> <?php endif; ?>

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3"><input type="text" name="search" class="form-control" placeholder="جستجوی نام، تذکره، شغل..." value="<?php echo e(request('search')); ?>"></div>
        <div class="col-md-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">همه وضعیت‌ها</option>
                <option value="active" <?php echo e(request('status')=='active'?'selected':''); ?>>فعال</option>
                <option value="inactive" <?php echo e(request('status')=='inactive'?'selected':''); ?>>غیرفعال</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="relation" class="form-select" onchange="this.form.submit()">
                <option value="">همه نسبت‌ها</option>
                <option value="father" <?php echo e(request('relation')=='father'?'selected':''); ?>>پدر</option>
                <option value="mother" <?php echo e(request('relation')=='mother'?'selected':''); ?>>مادر</option>
                <option value="brother" <?php echo e(request('relation')=='brother'?'selected':''); ?>>برادر</option>
                <option value="uncle" <?php echo e(request('relation')=='uncle'?'selected':''); ?>>کاکا/ماما</option>
                <option value="other" <?php echo e(request('relation')=='other'?'selected':''); ?>>سایر</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="kids" class="form-select" onchange="this.form.submit()">
                <option value="">تعداد فرزندان</option>
                <option value="1" <?php echo e(request('kids')=='1'?'selected':''); ?>>1 فرزند</option>
                <option value="2" <?php echo e(request('kids')=='2'?'selected':''); ?>>2 فرزند</option>
                <option value="3+" <?php echo e(request('kids')=='3+'?'selected':''); ?>>3 و بیشتر</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="financial" class="form-select" onchange="this.form.submit()">
                <option value="">همه وضعیت مالی</option>
                <option value="debtor" <?php echo e(request('financial')=='debtor'?'selected':''); ?>>بدهکار</option>
                <option value="settled" <?php echo e(request('financial')=='settled'?'selected':''); ?>>تسویه شده</option>
                <option value="discount" <?php echo e(request('financial')=='discount'?'selected':''); ?>>دارای تخفیف</option>
                <option value="free" <?php echo e(request('financial')=='free'?'selected':''); ?>>رایگان</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> اعمال</button>
            <a href="<?php echo e(route('school.guardians.index')); ?>" class="btn btn-secondary">حذف فیلترها</a>
        </div>
    </form>

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr><th>نام کامل</th><th>نسبت</th><th>شماره تماس</th><th>شغل</th><th>تعداد فرزندان</th><th>بدهی کل</th><th>وضعیت</th><th>عملیات</th></tr>
                </thead>
                <tbody>
                    <?php $__empty_1 = true; $__currentLoopData = $guardians; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $guardian): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($guardian->full_name); ?></td>
                            <td>
                                <?php switch($guardian->relation):
                                case ('father'): ?> پدر <?php break; ?>
                                <?php case ('mother'): ?> مادر <?php break; ?>
                                <?php case ('brother'): ?> برادر <?php break; ?>
                                <?php case ('uncle'): ?> کاکا / ماما <?php break; ?>
                                <?php case ('other'): ?> سایر <?php break; ?>
                                <?php default: ?> <?php echo e($guardian->relation ?? '—'); ?>

                                <?php endswitch; ?>
                                </td>
                            <td>
                                <?php if($guardian->phone): ?>
                                    <a href="tel:<?php echo e($guardian->phone); ?>"><i class="fas fa-phone text-success"></i></a>
                                    <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $guardian->phone)); ?>" target="_blank"><i class="fab fa-whatsapp text-success"></i></a>
                                    <?php echo e($guardian->phone); ?>

                                <?php else: ?> — <?php endif; ?>
                            </td>
                            <td><?php echo e($guardian->job ?? '—'); ?></td>
                            <td>
                                <span class="badge bg-primary"><?php echo e($guardian->students_count); ?></span>
                                <?php if($guardian->students_count > 0): ?>
                                    <a href="#family-<?php echo e($guardian->id); ?>" data-bs-toggle="collapse" class="btn btn-sm btn-outline-info ms-1"><i class="fas fa-users"></i></a>
                                <?php endif; ?>
                            </td>
                            <td><?php if($guardian->total_debt > 0): ?><span class="text-danger"><?php echo e(number_format($guardian->total_debt)); ?> ؋</span><?php else: ?> <span class="text-success">تسویه</span> <?php endif; ?></td>
                            <td><span class="badge bg-<?php echo e($guardian->is_active?'success':'danger'); ?>"><?php echo e($guardian->is_active?'فعال':'غیرفعال'); ?></span></td>
                            <td>
                                <a href="<?php echo e(route('school.guardians.show', $guardian->id)); ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="<?php echo e(route('school.guardians.edit', $guardian->id)); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="<?php echo e(route('school.guardians.destroy', $guardian->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('حذف شود؟')">
                                    <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        <?php if($guardian->students_count > 0): ?>
                        <tr class="collapse" id="family-<?php echo e($guardian->id); ?>">
                            <td colspan="8">
                                <div class="p-3 bg-light">
                                    <h6>فرزندان <?php echo e($guardian->full_name); ?></h6>
                                    <table class="table table-sm table-bordered bg-white">
                                        <thead class="table-secondary"><tr><th>نام</th><th>نام پدر</th><th>پدرکلان</th><th>صنف</th><th>عملیات</th></tr></thead>
                                        <tbody>
                                            <?php $__currentLoopData = $guardian->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr>
                                                <td><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                                                <td><?php echo e($student->father_name); ?></td>
                                                <td><?php echo e($student->grandfather_name ?? '—'); ?></td>
                                                <td><?php echo e($student->class ?? '—'); ?></td>
                                                <td>
                                                    <a href="<?php echo e(route('school.students.show', $student->id)); ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                                    <a href="<?php echo e(route('school.students.edit', $student->id)); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                                    <form action="<?php echo e(route('school.students.destroy', $student->id)); ?>" method="POST" class="d-inline" onsubmit="return confirm('حذف دانش‌آموز؟')">
                                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr><td colspan="8" class="text-center text-muted py-3">هیچ ولی‌ای ثبت نشده است.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="card-footer"><?php echo e($guardians->links()); ?></div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\guardians\index.blade.php ENDPATH**/ ?>