<?php $__env->startSection('title', 'ثبت دانش‌آموز جدید'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.students.index')); ?>">دانش‌آموزان</a></li>
            <li class="breadcrumb-item active">ثبت جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-user-plus ms-2"></i> فرم ثبت دانش‌آموز جدید</h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($error); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
            <?php endif; ?>

            <form action="<?php echo e(route('school.students.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                
                <h5 class="mb-3 text-primary"><i class="fas fa-id-card ms-2"></i> مشخصات فردی</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">نام <span class="text-danger">*</span></label><input type="text" name="first_name" class="form-control" value="<?php echo e(old('first_name')); ?>" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">نام خانوادگی <span class="text-danger">*</span></label><input type="text" name="last_name" class="form-control" value="<?php echo e(old('last_name')); ?>" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">نام پدر <span class="text-danger">*</span></label><input type="text" name="father_name" class="form-control" value="<?php echo e(old('father_name')); ?>" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">پدرکلان</label><input type="text" name="grandfather_name" class="form-control" value="<?php echo e(old('grandfather_name')); ?>"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">شماره تذکره <span class="text-danger">*</span></label><input type="text" name="national_id" class="form-control" value="<?php echo e(old('national_id')); ?>" required></div>
                    <div class="col-md-4 mb-3"><label class="form-label">نمبر اساس</label><input type="text" name="base_number" class="form-control" value="<?php echo e(old('base_number')); ?>"></div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ تولد</label>
                        <input type="text" name="birth_date" class="form-control" value="<?php echo e(old('birth_date')); ?>" placeholder="مثال: ۱۳۸۰/۰۱/۰۱">
                        <small class="text-muted">اختیاری</small>
                    </div>
                    <div class="col-md-4 mb-3"><label class="form-label">جنسیت <span class="text-danger">*</span></label><select name="gender" class="form-select" required><option value="">-- انتخاب کنید --</option><option value="male" <?php echo e(old('gender')=='male'?'selected':''); ?>>پسر</option><option value="female" <?php echo e(old('gender')=='female'?'selected':''); ?>>دختر</option></select></div>
                    <div class="col-md-4 mb-3"><label class="form-label">صنف / کلاس</label><input type="text" name="class" class="form-control" value="<?php echo e(old('class')); ?>" placeholder="مثلاً اول"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">سکونت اصلی</label><input type="text" name="original_residence" class="form-control" value="<?php echo e(old('original_residence')); ?>"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">سکونت فعلی / آدرس</label><textarea name="address" class="form-control" rows="2"><?php echo e(old('address')); ?></textarea></div>
                    <div class="col-md-4 mb-3"><label class="form-label">شماره تماس پدر</label><input type="text" name="father_phone" class="form-control" value="<?php echo e(old('father_phone')); ?>"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">شماره تماس (واتساپ)</label><input type="text" name="whatsapp_phone" class="form-control" value="<?php echo e(old('whatsapp_phone')); ?>"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">وضعیت <span class="text-danger">*</span></label><select name="status" class="form-select" required><option value="present" <?php echo e(old('status')=='present'?'selected':''); ?>>فعال</option><option value="blocked" <?php echo e(old('status')=='blocked'?'selected':''); ?>>غیرفعال</option></select></div>
                    <div class="col-md-4 mb-3"><label class="form-label">وضعیت مالی</label><select name="financial_status" class="form-select"><option value="full" <?php echo e(old('financial_status')=='full'?'selected':''); ?>>شهریه کامل</option><option value="discount" <?php echo e(old('financial_status')=='discount'?'selected':''); ?>>دارای تخفیف</option><option value="free" <?php echo e(old('financial_status')=='free'?'selected':''); ?>>رایگان</option></select></div>
                    <div class="col-md-4 mb-3"><label class="form-label">یتیم</label><select name="is_orphan" class="form-select"><option value="0" <?php echo e(old('is_orphan')=='0'?'selected':''); ?>>خیر</option><option value="1" <?php echo e(old('is_orphan')=='1'?'selected':''); ?>>بلی</option></select></div>
                    <div class="col-md-4 mb-3"><label class="form-label">عکس دانش‌آموز</label><input type="file" name="photo" class="form-control" accept="image/*"><small class="text-muted">اختیاری</small></div>
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ ثبت‌نام</label>
                        <input type="text" class="form-control" value="<?php echo e(\App\Helpers\JalaliHelper::todayJalali()); ?>" readonly>
                        <small class="text-muted">به‌صورت خودکار ثبت می‌شود</small>
                    </div>
                </div>

                <hr>

                
                
                <h5 class="mb-3 text-success"><i class="fas fa-user-shield ms-2"></i> مشخصات سرپرست (ولی)</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label class="form-label">نام کامل سرپرست</label><input type="text" name="new_guardian_name" class="form-control" value="<?php echo e(old('new_guardian_name')); ?>" placeholder="نام پدر یا سرپرست"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">نسبت با دانش‌آموز</label><select name="new_guardian_relation" class="form-select"><option value="">-- انتخاب --</option><option value="father" <?php echo e(old('new_guardian_relation')=='father'?'selected':''); ?>>پدر</option><option value="mother" <?php echo e(old('new_guardian_relation')=='mother'?'selected':''); ?>>مادر</option><option value="brother" <?php echo e(old('new_guardian_relation')=='brother'?'selected':''); ?>>برادر</option><option value="uncle" <?php echo e(old('new_guardian_relation')=='uncle'?'selected':''); ?>>کاکا / ماما</option><option value="other" <?php echo e(old('new_guardian_relation')=='other'?'selected':''); ?>>سایر</option></select></div>
                    <div class="col-md-4 mb-3"><label class="form-label">تحصیلات</label><input type="text" name="guardian_education" class="form-control" value="<?php echo e(old('guardian_education')); ?>" placeholder="مثلاً لیسانس"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">وظیفه / شغل</label><input type="text" name="guardian_job" class="form-control" value="<?php echo e(old('guardian_job')); ?>" placeholder="مثلاً معلم"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">شماره تماس سرپرست</label><input type="text" name="new_guardian_phone" class="form-control" value="<?php echo e(old('new_guardian_phone')); ?>" placeholder="0799123456"></div>
                    <div class="col-md-4 mb-3"><label class="form-label">آدرس سرپرست</label><input type="text" name="new_guardian_address" class="form-control" value="<?php echo e(old('new_guardian_address')); ?>" placeholder="آدرس کامل"></div>
                </div>

                <div class="text-start mt-3">
                    <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ذخیره‌ی دانش‌آموز</button>
                    <a href="<?php echo e(route('school.students.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\students\create.blade.php ENDPATH**/ ?>