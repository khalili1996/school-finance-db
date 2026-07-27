<?php $__env->startSection('title', 'ثبت معاش جدید'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid px-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.salaries.index')); ?>">معاشات</a></li>
            <li class="breadcrumb-item active">ثبت جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle ms-2"></i> فرم ثبت معاش</h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
            <?php endif; ?>

            <form action="<?php echo e(route('school.salaries.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row">
                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">کارمند <span class="text-danger">*</span></label>
                        <select name="employee_id" id="employee_id" class="form-select" required>
                            <option value="">-- انتخاب کنید --</option>
                            <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($emp->id); ?>" <?php echo e(old('employee_id') == $emp->id ? 'selected' : ''); ?>>
                                    <?php echo e($emp->first_name); ?> <?php echo e($emp->last_name); ?>

                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">سال تحصیلی <span class="text-danger">*</span></label>
                        <select name="academic_year_id" class="form-select" required>
                            <option value="">-- انتخاب کنید --</option>
<?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $ay): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <option value="<?php echo e($ay->id); ?>"
        <?php echo e(old('academic_year_id', session('active_academic_year_id')) == $ay->id ? 'selected' : ''); ?>>
        <?php echo e($ay->name); ?>

    </option>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ترم</label>
                        <select name="term_id" class="form-select">
                            <option value="">-- بدون ترم --</option>
                            <?php $__currentLoopData = $terms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $term): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($term->id); ?>" <?php echo e(old('term_id') == $term->id ? 'selected' : ''); ?>><?php echo e($term->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ماه</label>
                        <select name="month_id" id="month_id" class="form-select">
                            <option value="">-- بدون ماه --</option>
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $m): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($m->id); ?>" <?php echo e(old('month_id') == $m->id ? 'selected' : ''); ?>><?php echo e($m->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">حقوق پایه (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="base_salary" id="base_salary" class="form-control" value="<?php echo e(old('base_salary')); ?>" min="0" required>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">اضافه‌کاری</label>
                        <input type="number" name="overtime_amount" id="overtime_amount" class="form-control" value="<?php echo e(old('overtime_amount', 0)); ?>" min="0">
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">پاداش</label>
                        <input type="number" name="bonus_amount" id="bonus_amount" class="form-control" value="<?php echo e(old('bonus_amount', 0)); ?>" min="0">
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">کسری‌ها</label>
                        <input type="number" name="deduction_amount" id="deduction_amount" class="form-control" value="<?php echo e(old('deduction_amount', 0)); ?>" min="0">
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ضمانت</label>
                        <input type="number" name="guarantee_amount" id="guarantee_amount" class="form-control" value="<?php echo e(old('guarantee_amount', 0)); ?>" min="0">
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ مالیات (افغانی)</label>
                        <input type="number" name="tax_amount" id="tax_amount" class="form-control" value="<?php echo e(old('tax_amount', 0)); ?>" min="0" step="0.01">
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">وضعیت <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="unpaid" <?php echo e(old('status') == 'unpaid' ? 'selected' : ''); ?>>پرداخت نشده</option>
                            <option value="partially_paid" <?php echo e(old('status') == 'partially_paid' ? 'selected' : ''); ?>>پرداخت جزئی</option>
                            <option value="paid" <?php echo e(old('status') == 'paid' ? 'selected' : ''); ?>>پرداخت کامل</option>
                        </select>
                    </div>

                    
                    <div class="col-12 mt-3">
                        <h5 class="text-primary">امتیازات کارمند (نرخ هر امتیاز = ۱ ؋)</h5>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">امتیاز سمت</label>
                        <input type="text" id="position_points_display" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">امتیاز سابقه کاری</label>
                        <input type="text" id="experience_points_display" class="form-control" readonly>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label class="form-label">امتیاز درجه تحصیل</label>
                        <input type="text" id="education_points_display" class="form-control" readonly>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مجموع امتیازات</label>
                        <input type="text" id="total_points_display" class="form-control" readonly>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مجموع پیش‌پرداخت‌ها (همین ماه)</label>
                        <input type="text" id="advance_sum_display" class="form-control" readonly value="0">
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">معاش خالص (پیش‌نمایش)</label>
                        <input type="text" id="net_salary_preview" class="form-control" readonly>
                    </div>

                    
                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="notes" class="form-control" rows="2"><?php echo e(old('notes')); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ثبت معاش</button>
                <a href="<?php echo e(route('school.salaries.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
const employeesPoints = {
    <?php $__currentLoopData = $employees; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $emp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php echo e($emp->id); ?>: {
            position_points: <?php echo e($emp->position_points ?? 0); ?>,
            experience_points: <?php echo e($emp->experience_points ?? 0); ?>,
            education_points: <?php echo e($emp->education_points ?? 0); ?>

        },
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
};

function updatePointsDisplay() {
    const empId = document.getElementById('employee_id').value;
    if (empId && employeesPoints[empId]) {
        document.getElementById('position_points_display').value = employeesPoints[empId].position_points;
        document.getElementById('experience_points_display').value = employeesPoints[empId].experience_points;
        document.getElementById('education_points_display').value = employeesPoints[empId].education_points;
    } else {
        document.getElementById('position_points_display').value = '';
        document.getElementById('experience_points_display').value = '';
        document.getElementById('education_points_display').value = '';
    }
    fetchAdvanceSum();
    calculateAll();
}

// 🆕 دریافت مجموع پیش‌پرداخت‌ها
function fetchAdvanceSum() {
    const empId = document.getElementById('employee_id').value;
    const monthId = document.getElementById('month_id').value;
    if (!empId || !monthId) {
        document.getElementById('advance_sum_display').value = 0;
        calculateAll();
        return;
    }
    fetch(`/school/api/employee-advances/sum?employee_id=${empId}&month_id=${monthId}`)
        .then(res => res.json())
        .then(data => {
            document.getElementById('advance_sum_display').value = data.sum ?? 0;
            calculateAll();
        })
        .catch(() => {
            document.getElementById('advance_sum_display').value = 0;
            calculateAll();
        });
}

function calculateAll() {
    const baseSalary = parseFloat(document.getElementById('base_salary').value) || 0;
    const overtime = parseFloat(document.getElementById('overtime_amount').value) || 0;
    const bonus = parseFloat(document.getElementById('bonus_amount').value) || 0;
    const deduction = parseFloat(document.getElementById('deduction_amount').value) || 0;
    const tax = parseFloat(document.getElementById('tax_amount').value) || 0;
    const guarantee = parseFloat(document.getElementById('guarantee_amount').value) || 0;
    const advanceSum = parseFloat(document.getElementById('advance_sum_display').value) || 0;

    const position = parseFloat(document.getElementById('position_points_display').value) || 0;
    const experience = parseFloat(document.getElementById('experience_points_display').value) || 0;
    const education = parseFloat(document.getElementById('education_points_display').value) || 0;
    const totalPoints = position + experience + education;
    const pointsAmount = totalPoints * 1;

    const gross = baseSalary + overtime + bonus - deduction + pointsAmount;
    const net = gross - tax - guarantee - advanceSum;   // 🆕 کسر پیش‌پرداخت

    document.getElementById('total_points_display').value = totalPoints;
    document.getElementById('net_salary_preview').value = net.toFixed(0);
}

document.getElementById('employee_id').addEventListener('change', updatePointsDisplay);
document.getElementById('month_id').addEventListener('change', fetchAdvanceSum);
['base_salary','overtime_amount','bonus_amount','deduction_amount','tax_amount','guarantee_amount'].forEach(id => {
    document.getElementById(id).addEventListener('input', calculateAll);
});

document.addEventListener('DOMContentLoaded', function() {
    if (document.getElementById('employee_id').value) {
        updatePointsDisplay();
    } else {
        calculateAll();
    }
});
</script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\salaries\create.blade.php ENDPATH**/ ?>