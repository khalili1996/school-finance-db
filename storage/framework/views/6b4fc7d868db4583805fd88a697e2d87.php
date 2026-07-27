<?php $__env->startSection('title', 'مشاهده ولی'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.guardians.index')); ?>">اولیا</a></li>
            <li class="breadcrumb-item active"><?php echo e($guardian->full_name); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-id-card ms-2"></i> مشخصات ولی</h5>
                </div>
                <div class="card-body">
                    <table class="table table-bordered">
                        <tr><th style="width:200px;">نام کامل</th><td><?php echo e($guardian->full_name); ?></td></tr>
                        <tr>
    <th>نسبت</th>
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
</tr>
                        <tr><th>شماره تذکره</th><td><?php echo e($guardian->national_id ?? '—'); ?></td></tr>
                        <tr><th>شغل</th><td><?php echo e($guardian->job ?? '—'); ?></td></tr>
                        <tr><th>تحصیلات</th><td><?php echo e($guardian->education ?? '—'); ?></td></tr>
                        <tr><th>تلفن</th>
                            <td>
                                <?php echo e($guardian->phone ?? '—'); ?>

                                <?php if($guardian->phone): ?>
                                    <a href="tel:<?php echo e($guardian->phone); ?>" class="btn btn-sm btn-outline-success ms-2"><i class="fas fa-phone"></i> تماس</a>
                                    <a href="https://wa.me/<?php echo e(preg_replace('/[^0-9]/', '', $guardian->phone)); ?>" target="_blank" class="btn btn-sm btn-outline-success"><i class="fab fa-whatsapp"></i></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr><th>تلفن دوم</th><td><?php echo e($guardian->secondary_phone ?? '—'); ?></td></tr>
                        <tr><th>آدرس</th><td><?php echo e($guardian->address ?? '—'); ?></td></tr>
                        <tr><th>وضعیت</th><td><span class="badge bg-<?php echo e($guardian->is_active ? 'success' : 'danger'); ?>"><?php echo e($guardian->is_active ? 'فعال' : 'غیرفعال'); ?></span></td></tr>
                    </table>
                    <div class="mt-2">
                        <a href="<?php echo e(route('school.guardians.edit', $guardian->id)); ?>" class="btn btn-warning"><i class="fas fa-edit"></i> ویرایش</a>
                        <a href="<?php echo e(route('school.guardians.preview', $guardian->id)); ?>" class="btn btn-outline-secondary"><i class="fas fa-print"></i> پیش‌نمایش چاپ</a>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-users ms-2"></i> فرزندان</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr><th>نام</th><th>صنف</th><th>وضعیت</th><th>بدهی</th><th>عملیات</th></tr>
                        </thead>
                        <tbody>
                            <?php $__empty_1 = true; $__currentLoopData = $guardian->students; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $student): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <?php
                                    $fee = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                                    $paid = $student->payments->sum('amount');
                                    $debt = max($fee - $paid, 0);
                                ?>
                                <tr>
                                    <td><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td>
                                    <td><?php echo e($student->class ?? '—'); ?></td>
                                    <td>
                                        <?php switch($student->status):
                                            case ('present'): ?> <span class="badge bg-success">حاضر</span> <?php break; ?>
                                            <?php case ('blocked'): ?> <span class="badge bg-danger">محروم</span> <?php break; ?>
                                            <?php default: ?> <span class="badge bg-secondary"><?php echo e($student->status); ?></span>
                                        <?php endswitch; ?>
                                    </td>
                                    <td><?php echo e(number_format($debt)); ?> ؋</td>
                                    <td>
                                        <a href="<?php echo e(route('school.students.show', $student->id)); ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr><td colspan="5" class="text-muted text-center">دانش‌آموزی یافت نشد</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-pie ms-2"></i> خلاصه مالی خانواده</h5>
                </div>
                <div class="card-body">
                    <?php
                        $totalFee = $guardian->students->sum(fn($s) => $s->studentFees->sum(fn($f) => $f->amount - $f->discount));
                        $totalPaid = $guardian->students->sum(fn($s) => $s->payments->sum('amount'));
                        $totalDebt = max($totalFee - $totalPaid, 0);
                    ?>
                    <div class="mb-3"><strong>کل شهریه:</strong> <span class="float-end"><?php echo e(number_format($totalFee)); ?> ؋</span></div>
                    <div class="mb-3"><strong>پرداختی:</strong> <span class="float-end text-success"><?php echo e(number_format($totalPaid)); ?> ؋</span></div>
                    <div class="mb-3"><strong>بدهی:</strong> <span class="float-end <?php echo e($totalDebt > 0 ? 'text-danger' : 'text-success'); ?>"><?php echo e(number_format($totalDebt)); ?> ؋</span></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\guardians\show.blade.php ENDPATH**/ ?>