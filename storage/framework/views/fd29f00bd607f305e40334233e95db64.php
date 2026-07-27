<?php $__env->startSection('title', 'ویرایش عاید'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.dashboard')); ?>">داشبورد</a></li>
            <li class="breadcrumb-item"><a href="<?php echo e(route('school.incomes.index')); ?>">عواید</a></li>
            <li class="breadcrumb-item active">ویرایش</li>
        </ol>
    </nav>

    <div class="card shadow">
        <div class="card-header bg-warning text-dark">
            <h5 class="mb-0"><i class="fas fa-edit ms-2"></i> ویرایش عاید</h5>
        </div>
        <div class="card-body">
            <?php if($errors->any()): ?>
                <div class="alert alert-danger"><ul class="mb-0"><?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $e): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><li><?php echo e($e); ?></li><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?></ul></div>
            <?php endif; ?>

            <form action="<?php echo e(route('school.incomes.update', $income->id)); ?>" method="POST">
                <?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
                <div class="row">
                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">عنوان <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" value="<?php echo e(old('title', $income->title)); ?>" required>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">دسته‌بندی <span class="text-danger">*</span></label>
                        <select name="income_category_id" class="form-select" required>
                            <option value="">-- انتخاب کنید --</option>
                            <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cat->id); ?>" <?php echo e(old('income_category_id', $income->income_category_id) == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ کل (افغانی) <span class="text-danger">*</span></label>
                        <input type="number" name="total_amount" class="form-control" value="<?php echo e(old('total_amount', $income->total_amount)); ?>" min="1" required>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">مبلغ دریافتی (افغانی)</label>
                        <input type="number" name="received_amount" class="form-control" value="<?php echo e(old('received_amount', $income->received_amount)); ?>" min="0">
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">تاریخ <span class="text-danger">*</span></label>
                        <input type="text" name="income_date" class="form-control"
                               value="<?php echo e(old('income_date', $income->income_date)); ?>"
                               placeholder="مثلاً ۱۴۰۴/۰۱/۱۵" required>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">وضعیت <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="due" <?php echo e(old('status', $income->status) == 'due' ? 'selected' : ''); ?>>طلبکار</option>
                            <option value="partially_received" <?php echo e(old('status', $income->status) == 'partially_received' ? 'selected' : ''); ?>>دریافت جزئی</option>
                            <option value="received" <?php echo e(old('status', $income->status) == 'received' ? 'selected' : ''); ?>>دریافت کامل</option>
                            <option value="cancelled" <?php echo e(old('status', $income->status) == 'cancelled' ? 'selected' : ''); ?>>لغو شده</option>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">صندوق دریافت (در صورت دریافت)</label>
                        <select name="cashbox_id" class="form-select">
                            <option value="">-- بدون صندوق --</option>
                            <?php $__currentLoopData = $cashboxes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cb): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <option value="<?php echo e($cb->id); ?>" <?php echo e(old('cashbox_id', optional($income->cashboxTransactions()->first())->cashbox_id) == $cb->id ? 'selected' : ''); ?>>
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
                                <option value="<?php echo e($month->id); ?>" <?php echo e(old('month_id', $income->month_id) == $month->id ? 'selected' : ''); ?>><?php echo e($month->name); ?></option>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </select>
                    </div>

                    
                    <div class="col-md-4 mb-3">
                        <label class="form-label">منبع</label>
                        <input type="text" name="source" class="form-control" value="<?php echo e(old('source', $income->source)); ?>">
                    </div>

                    
                    <div class="col-12 mb-3">
                        <label class="form-label">توضیحات</label>
                        <textarea name="description" class="form-control" rows="2"><?php echo e(old('description', $income->description)); ?></textarea>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning btn-lg"><i class="fas fa-save"></i> به‌روزرسانی</button>
                <a href="<?php echo e(route('school.incomes.index')); ?>" class="btn btn-secondary btn-lg">انصراف</a>
            </form>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\incomes\edit.blade.php ENDPATH**/ ?>