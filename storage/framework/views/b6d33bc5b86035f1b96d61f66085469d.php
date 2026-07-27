<?php $__env->startSection('title', 'ثبت عاید جدید'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.incomes.index')); ?>">عواید</a></li>
            <li class="breadcrumb-item active">ثبت جدید</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-plus-circle ms-2"></i> فرم ثبت عاید جدید</h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
            <?php endif; ?>

            <form action="<?php echo e(route('school.incomes.store')); ?>" method="POST">
                <?php echo csrf_field(); ?>
                <div class="row">
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">عنوان <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?php echo e(old('title')); ?>" required>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">دسته‌بندی <span class="text-danger">*</span></label>
                        <select name="income_category_id" class="form-select" required>
                            <option value="">-- انتخاب کنید --</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>" <?php echo e(old('income_category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ کل (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="total_amount" class="form-control" value="<?php echo e(old('total_amount')); ?>" min="1" required>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ دریافتی (افغانی)</label>
                        <input type="number" name="received_amount" class="form-control" value="<?php echo e(old('received_amount', 0)); ?>" min="0">
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ <span class="text-danger">*</span></label>
                        <input type="text" name="income_date" class="form-control"
                               value="<?php echo e(old('income_date', \App\Helpers\JalaliHelper::todayJalali())); ?>"
                               placeholder="مثلاً ۱۴۰۴/۰۱/۱۵" required>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">وضعیت <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="due" <?php echo e(old('status') == 'due' ? 'selected' : ''); ?>>طلبکار</option>
                            <option value="partially_received" <?php echo e(old('status') == 'partially_received' ? 'selected' : ''); ?>>دریافت جزئی</option>
                            <option value="received" <?php echo e(old('status') == 'received' ? 'selected' : ''); ?>>دریافت کامل</option>
                            <option value="cancelled" <?php echo e(old('status') == 'cancelled' ? 'selected' : ''); ?>>لغو شده</option>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">صندوق دریافت (در صورت دریافت)</label>
                        <select name="cashbox_id" class="form-select">
                            <option value="">-- بدون صندوق --</option>
                            <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cb->id); ?>" <?php echo e(old('cashbox_id') == $cb->id ? 'selected' : ''); ?>>
                                    <?php echo e($cb->name); ?> (<?php echo e($cb->type === 'bank' ? 'بانکی' : 'نقدی'); ?>)
                                </option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">ماه</label>
                        <select name="month_id" class="form-select">
                            <option value="">-- انتخاب کنید --</option>
                            <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($month->id); ?>" <?php echo e(old('month_id') == $month->id ? 'selected' : ''); ?>><?php echo e($month->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">منبع</label>
                        <input type="text" name="source" class="form-control" value="<?php echo e(old('source')); ?>" placeholder="مثلاً کمک مردمی">
                    </div>

                    
                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" class="form-control" rows="2"><?php echo e(old('description')); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-success btn-lg"><i class="fas fa-save"></i> ثبت عاید</button>
                <a href="<?php echo e(route('school.incomes.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\incomes\create.blade.php ENDPATH**/ ?>