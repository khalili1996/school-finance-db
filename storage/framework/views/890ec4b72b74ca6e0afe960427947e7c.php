<?php $__env->startSection('title', 'ثبت کارمند جدید'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.employees.index')); ?>">کارمندان</a></li>
            <li class="breadcrumb-item active">ثبت جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-user-plus ms-2"></i> فرم ثبت کارمند</h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
            <?php endif; ?>

            <form action="<?php echo e(route('school.employees.store')); ?>" method="POST" enctype="multipart/form-data">
                <?php echo csrf_field(); ?>

                
                <h5 class="mb-3 text-primary">اطلاعات فردی</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label>نام <span class="text-danger">*</span></label><input type="text" name="first_name" class="form-control" value="<?php echo e(old('first_name')); ?>" required></div>
                    <div class="col-md-4 mb-3"><label>نام خانوادگی <span class="text-danger">*</span></label><input type="text" name="last_name" class="form-control" value="<?php echo e(old('last_name')); ?>" required></div>
                    <div class="col-md-4 mb-3"><label>نام پدر <span class="text-danger">*</span></label><input type="text" name="father_name" class="form-control" value="<?php echo e(old('father_name')); ?>" required></div>
                    <div class="col-md-4 mb-3"><label>پدرکلان</label><input type="text" name="grandfather_name" class="form-control" value="<?php echo e(old('grandfather_name')); ?>"></div>
                    <div class="col-md-4 mb-3"><label>شماره تذکره</label><input type="text" name="national_id" class="form-control" value="<?php echo e(old('national_id')); ?>"></div>

                    
                    <div class="col-md-4 mb-3">
                        <label>تاریخ تولد</label>
                        <input type="text" name="birth_date" class="form-control" value="<?php echo e(old('birth_date')); ?>" placeholder="مثلاً ۱۳۷۰/۰۱/۰۱">
                    </div>

                    <div class="col-md-4 mb-3"><label>جنسیت <span class="text-danger">*</span></label><select name="gender" class="form-select" required><option value="male" <?php echo e(old('gender')=='male'?'selected':''); ?>>مذکر</option><option value="female" <?php echo e(old('gender')=='female'?'selected':''); ?>>اناث</option></select></div>
                    <div class="col-md-4 mb-3"><label>شماره تماس</label><input type="text" name="phone" class="form-control" value="<?php echo e(old('phone')); ?>"></div>
                    <div class="col-md-4 mb-3"><label>شماره تماس دوم</label><input type="text" name="secondary_phone" class="form-control" value="<?php echo e(old('secondary_phone')); ?>"></div>
                    <div class="col-12 mb-3"><label>آدرس</label><textarea name="address" class="form-control" rows="2"><?php echo e(old('address')); ?></textarea></div>
                    <div class="col-md-4 mb-3"><label>درجه تحصیل</label><input type="text" name="education_level" class="form-control" value="<?php echo e(old('education_level')); ?>"></div>
                    <div class="col-md-4 mb-3"><label>رشته تحصیلی</label><input type="text" name="field_of_study" class="form-control" value="<?php echo e(old('field_of_study')); ?>"></div>
                    <div class="col-md-4 mb-3"><label>عکس</label><input type="file" name="photo" class="form-control" accept="image/*"></div>
                </div>

                
                <h5 class="mb-3 text-primary">اطلاعات استخدامی</h5>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label>سمت <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <select name="employee_role_id" class="form-select" required>
                                <option value="">-- انتخاب --</option>
                                <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($role->id); ?>" <?php echo e(old('employee_role_id') == $role->id ? 'selected' : ''); ?>><?php echo e($role->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                            <button type="button" class="btn btn-outline-success" data-bs-toggle="modal" data-bs-target="#quickRoleModal">
                                <i class="fas fa-plus"></i>
                            </button>
                        </div>
                    </div>

                    <div class="col-md-4 mb-3"><label>بخش</label><input type="text" name="department" class="form-control" value="<?php echo e(old('department')); ?>"></div>

                    
                    <div class="col-md-4 mb-3">
                        <label>تاریخ استخدام</label>
                        <input type="text" name="hire_date" class="form-control" value="<?php echo e(old('hire_date', \App\Helpers\JalaliHelper::todayJalali())); ?>" placeholder="مثلاً ۱۴۰۲/۰۶/۰۱">
                    </div>

                    <div class="col-md-4 mb-3"><label>نوع قرارداد <span class="text-danger">*</span></label><select name="contract_type" class="form-select" required><option value="permanent" <?php echo e(old('contract_type')=='permanent'?'selected':''); ?>>دایمی</option><option value="temporary" <?php echo e(old('contract_type')=='temporary'?'selected':''); ?>>موقت</option></select></div>
                    <div class="col-md-4 mb-3"><label>معاش پایه</label><input type="number" name="base_salary" class="form-control" value="<?php echo e(old('base_salary')); ?>" min="0"></div>
                    <div class="col-md-4 mb-3"><label>وضعیت <span class="text-danger">*</span></label><select name="status" class="form-select" required><option value="active" <?php echo e(old('status')=='active'?'selected':''); ?>>فعال</option><option value="inactive" <?php echo e(old('status')=='inactive'?'selected':''); ?>>غیرفعال</option></select></div>
                </div>

                
                <h5 class="mb-3 text-primary">امتیازات</h5>
                <div class="row">
                    <div class="col-md-4 mb-3"><label>امتیاز سمت</label><input type="number" name="position_points" class="form-control" value="<?php echo e(old('position_points')); ?>" min="0"></div>
                    <div class="col-md-4 mb-3"><label>امتیاز سابقه کاری</label><input type="number" name="experience_points" class="form-control" value="<?php echo e(old('experience_points')); ?>" min="0"></div>
                    <div class="col-md-4 mb-3"><label>امتیاز درجه تحصیل</label><input type="number" name="education_points" class="form-control" value="<?php echo e(old('education_points')); ?>" min="0"></div>
                </div>

                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ذخیره</button>
                <a href="<?php echo e(route('school.employees.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="quickRoleModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">ایجاد سمت جدید</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="text" id="new_role_name" class="form-control" placeholder="نام سمت">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                <button type="button" class="btn btn-success" onclick="quickAddRole()">ذخیره</button>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
function quickAddRole() {
    const name = document.getElementById('new_role_name').value.trim();
    if (!name) return alert('نام سمت را وارد کنید.');

    fetch('<?php echo e(route('school.employees.roles.quick-store')); ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ name: name })
    })
    .then(res => res.json())
    .then(data => {
        const select = document.querySelector('select[name="employee_role_id"]');
        const option = new Option(data.name, data.id);
        select.add(option);
        select.value = data.id;
        bootstrap.Modal.getInstance(document.getElementById('quickRoleModal')).hide();
        document.getElementById('new_role_name').value = '';
    })
    .catch(err => alert('خطا در ذخیره‌سازی'));
}
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\employees\create.blade.php ENDPATH**/ ?>