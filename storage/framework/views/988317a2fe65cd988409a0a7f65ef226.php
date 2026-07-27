<?php $__env->startSection('title', 'پروفایل ' . $student->first_name); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.students.index')); ?>">دانش‌آموزان</a></li>
            <li class="breadcrumb-item active"><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></li>
        </ol>
    </nav>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-id-card ms-2"></i> اطلاعات دانش‌آموز</h5>
                    <span class="badge bg-light text-dark"><?php echo e($student->student_code); ?></span>
                </div>
                <div class="card-body p-0">
                    <ul class="nav nav-tabs" id="studentTabs" role="tablist">
                        <li class="nav-item" role="presentation"><button class="nav-link active" id="info-tab" data-bs-toggle="tab" data-bs-target="#info-pane" type="button" role="tab">اطلاعات عمومی</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="finance-tab" data-bs-toggle="tab" data-bs-target="#finance-pane" type="button" role="tab">مالی</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="receipts-tab" data-bs-toggle="tab" data-bs-target="#receipts-pane" type="button" role="tab">رسیدها</button></li>
                        <li class="nav-item" role="presentation"><button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab">تاریخچه</button></li>
                    </ul>
                    <div class="tab-content p-3" id="studentTabsContent">
                        
                        <div class="tab-pane fade show active" id="info-pane" role="tabpanel">
                            <table class="table table-bordered">
                                <tr><th style="width:180px;">نام کامل</th><td><?php echo e($student->first_name); ?> <?php echo e($student->last_name); ?></td></tr>
                                <tr><th>نام پدر</th><td><?php echo e($student->father_name); ?></td></tr>
                                <tr><th>پدرکلان</th><td><?php echo e($student->grandfather_name ?? '—'); ?></td></tr>
                                
                                <tr><th>تاریخ تولد</th><td><?php echo e($student->birth_date ? \App\Helpers\JalaliHelper::toJalali($student->birth_date) : '—'); ?></td></tr>
                                <tr><th>شماره تذکره</th><td><?php echo e($student->national_id); ?></td></tr>
                                <tr><th>نمبر اساس</th><td><?php echo e($student->base_number ?? '—'); ?></td></tr>
                                <tr><th>جنسیت</th><td><?php echo e($student->gender == 'male' ? 'پسر' : 'دختر'); ?></td></tr>
                                <tr><th>صنف</th><td><?php echo e($student->class ?? '—'); ?></td></tr>
                                <tr><th>تلفن</th><td><?php echo e($student->phone ?? '—'); ?></td></tr>
                                <tr><th>واتساپ</th><td><?php echo e($student->whatsapp_phone ?? '—'); ?></td></tr>
                                <tr><th>سکونت اصلی</th><td><?php echo e($student->original_residence ?? '—'); ?></td></tr>
                                <tr><th>آدرس</th><td><?php echo e($student->address ?? '—'); ?></td></tr>
                                
                                <tr><th>تاریخ ثبت‌نام</th><td><?php echo e($student->enrollment_date ? \App\Helpers\JalaliHelper::toJalali($student->enrollment_date) : '—'); ?></td></tr>
                                <tr><th>وضعیت</th><td>
                                    <?php switch($student->status):
                                        case ('present'): ?> <span class="badge bg-success">فعال</span> <?php break; ?>
                                        <?php case ('blocked'): ?> <span class="badge bg-danger">غیرفعال</span> <?php break; ?>
                                        <?php case ('temporary'): ?> <span class="badge bg-warning text-dark">موقت</span> <?php break; ?>
                                        <?php case ('three_piece'): ?> <span class="badge bg-info">سه‌پارچه</span> <?php break; ?>
                                        <?php default: ?> <span class="badge bg-secondary"><?php echo e($student->status); ?></span>
                                    <?php endswitch; ?>
                                </td></tr>
                                <tr><th>وضعیت مالی</th><td>
                                    <?php switch($student->financial_status):
                                        case ('full'): ?> <span class="badge bg-primary">شهریه کامل</span> <?php break; ?>
                                        <?php case ('discount'): ?> <span class="badge bg-success">تخفیف‌دار</span> <?php break; ?>
                                        <?php case ('free'): ?> <span class="badge bg-info">رایگان</span> <?php break; ?>
                                        <?php default: ?> <span class="badge bg-secondary">تعیین نشده</span>
                                    <?php endswitch; ?>
                                </td></tr>
                                <tr><th>یتیم</th><td><?php echo e($student->is_orphan ? 'بلی' : 'خیر'); ?></td></tr>
                                <?php if($student->photo): ?>
                                <tr><th>عکس</th><td><img src="<?php echo e(asset('storage/'.$student->photo)); ?>" style="max-width: 150px;"></td></tr>
                                <?php endif; ?>
                            </table>
                            <a href="<?php echo e(route('school.students.edit', $student->id)); ?>" class="btn btn-warning"><i class="fas fa-edit"></i> ویرایش</a>
                            <a href="<?php echo e(route('school.students.preview', $student->id)); ?>" class="btn btn-outline-secondary"><i class="fas fa-print"></i> پیش‌نمایش چاپ</a>
                        </div>

                        
                        <div class="tab-pane fade" id="finance-pane" role="tabpanel">
                            <?php
                                $totalFees = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                                $totalPaid = $student->payments->sum('amount');
                                $balance = $totalFees - $totalPaid;
                            ?>
                            <div class="row text-center mb-3">
                                <div class="col-md-4"><div class="p-3 bg-light rounded"><h6>کل شهریه</h6><h4 class="text-primary"><?php echo e(number_format($totalFees)); ?> ؋</h4></div></div>
                                <div class="col-md-4"><div class="p-3 bg-light rounded"><h6>پرداخت‌شده</h6><h4 class="text-success"><?php echo e(number_format($totalPaid)); ?> ؋</h4></div></div>
                                <div class="col-md-4"><div class="p-3 bg-light rounded"><h6>باقی‌مانده</h6><h4 class="<?php echo e($balance > 0 ? 'text-danger' : 'text-success'); ?>"><?php echo e(number_format($balance)); ?> ؋</h4></div></div>
                            </div>
                            <h6>جزئیات شهریه‌های تعیین‌شده</h6>
                            <table class="table table-sm table-bordered">
                                <thead><tr><th>نوع هزینه</th><th>ماه</th><th>مبلغ</th><th>تخفیف</th><th>قابل پرداخت</th><th>پرداخت‌شده؟</th></tr></thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $student->studentFees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fee): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <?php
                                            $paidForThisFee = $student->payments->where('fee_id', $fee->id)->sum('amount');
                                            $remaining = ($fee->amount - $fee->discount) - $paidForThisFee;
                                        ?>
                                        <tr>
                                            <td><?php echo e($fee->feeType->name ?? '—'); ?></td>
                                            <td><?php echo e($fee->month->name ?? '—'); ?></td>
                                            <td><?php echo e(number_format($fee->amount)); ?> ؋</td>
                                            <td><?php echo e(number_format($fee->discount)); ?> ؋</td>
                                            <td><?php echo e(number_format($fee->amount - $fee->discount)); ?> ؋</td>
                                            <td>
                                                <?php if($remaining <= 0): ?> <span class="badge bg-success">پرداخت کامل</span>
                                                <?php elseif($paidForThisFee > 0): ?> <span class="badge bg-warning text-dark">پرداخت جزئی (<?php echo e(number_format($remaining)); ?> ؋ مانده)</span>
                                                <?php else: ?> <span class="badge bg-danger">پرداخت نشده</span> <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="6" class="text-muted text-center">هزینه‌ای ثبت نشده است.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        
                        <div class="tab-pane fade" id="receipts-pane" role="tabpanel">
                            <table class="table table-sm table-hover">
                                <thead><tr><th>شماره رسید</th><th>مبلغ</th><th>تاریخ</th><th>روش</th><th>توضیحات</th></tr></thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $student->payments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $payment): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr><td><?php echo e($payment->receipt_number ?? '—'); ?></td><td><?php echo e(number_format($payment->amount)); ?> ؋</td><td><?php echo e($payment->payment_date); ?></td><td><?php echo e($payment->payment_method); ?></td><td><?php echo e($payment->notes ?? '—'); ?></td></tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="5" class="text-muted text-center">پرداختی ثبت نشده است.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        
                        <div class="tab-pane fade" id="history-pane" role="tabpanel">
                            <table class="table table-sm table-hover">
                                <thead><tr><th>تاریخ</th><th>عملیات</th><th>کاربر</th><th>توضیحات</th></tr></thead>
                                <tbody>
                                    <?php $__empty_1 = true; $__currentLoopData = $student->auditLogs ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                        <tr><td><?php echo e($log->created_at->format('Y/m/d H:i')); ?></td><td><?php echo e($log->action); ?></td><td><?php echo e($log->user->name ?? '—'); ?></td><td><?php echo e($log->description ?? '—'); ?></td></tr>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                        <tr><td colspan="4" class="text-muted text-center">تاریخچه‌ای ثبت نشده است.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-user-friends ms-2"></i> اطلاعات ولی</h5>
                </div>
                <div class="card-body">
                    <?php if($student->guardian): ?>
                        <p><strong>نام:</strong> <?php echo e($student->guardian->full_name); ?></p>
                        <p><strong>نسبت:</strong>
    <?php switch($student->guardian->relation):
        case ('father'): ?> پدر <?php break; ?>
        <?php case ('mother'): ?> مادر <?php break; ?>
        <?php case ('brother'): ?> برادر <?php break; ?>
        <?php case ('uncle'): ?> کاکا / ماما <?php break; ?>
        <?php case ('other'): ?> سایر <?php break; ?>
        <?php default: ?> <?php echo e($student->guardian->relation ?? '—'); ?>

    <?php endswitch; ?>
</p>
                        <p><strong>تحصیلات:</strong> <?php echo e($student->guardian->education ?? '—'); ?></p>
                        <p><strong>شغل:</strong> <?php echo e($student->guardian->job ?? '—'); ?></p>
                        <p><strong>شماره تماس:</strong> <?php echo e($student->guardian->phone ?? '—'); ?></p>
                        <p><strong>آدرس:</strong> <?php echo e($student->guardian->address ?? '—'); ?></p>
                        <a href="<?php echo e(route('school.guardians.edit', $student->guardian->id)); ?>" class="btn btn-sm btn-outline-warning mt-2">
                            <i class="fas fa-edit"></i> ویرایش اطلاعات ولی
                        </a>
                    <?php else: ?>
                        <div class="alert alert-warning mb-0">هیچ ولی‌ای ثبت نشده است.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\students\show.blade.php ENDPATH**/ ?>