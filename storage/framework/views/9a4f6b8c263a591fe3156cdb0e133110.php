<?php $__env->startSection('title', 'لیست درآمدها'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-coins ms-2"></i> مدیریت درآمد</h1>
        <div>
            <a href="<?php echo e(route('school.incomes.report')); ?>" class="btn btn-outline-primary btn-sm me-2">
                <i class="fas fa-print"></i> گزارش چاپی
            </a>
            <a href="<?php echo e(route('school.incomes.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> ثبت درآمد جدید
            </a>
        </div>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    <?php endif; ?>

    
    <form method="GET" action="<?php echo e(route('school.incomes.index')); ?>" class="row g-2 mb-4">
        <div class="col-md-2">
            <input type="text" name="search" class="form-control" placeholder="جستجوی عنوان یا منبع..." value="<?php echo e(request('search')); ?>">
        </div>
        <div class="col-md-2">
            <select name="category_id" class="form-select" onchange="this.form.submit()">
                <option value="">همه دسته‌بندی‌ها</option>
                <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cat): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($cat->id); ?>" <?php echo e(request('category_id') == $cat->id ? 'selected' : ''); ?>><?php echo e($cat->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">همه وضعیت‌ها</option>
                <option value="due" <?php echo e(request('status') == 'due' ? 'selected' : ''); ?>>دریافت نشده</option>
                <option value="partially_received" <?php echo e(request('status') == 'partially_received' ? 'selected' : ''); ?>>دریافت جزئی</option>
                <option value="received" <?php echo e(request('status') == 'received' ? 'selected' : ''); ?>>دریافت کامل</option>
                <option value="cancelled" <?php echo e(request('status') == 'cancelled' ? 'selected' : ''); ?>>لغو شده</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="month_id" class="form-select" onchange="this.form.submit()">
                <option value="">همه ماه‌ها</option>
                <?php $__currentLoopData = $months; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $month): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <option value="<?php echo e($month->id); ?>" <?php echo e(request('month_id') == $month->id ? 'selected' : ''); ?>><?php echo e($month->name); ?></option>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> اعمال</button>
            <?php if(request('search') || request('category_id') || request('status') || request('month_id')): ?>
                <a href="<?php echo e(route('school.incomes.index')); ?>" class="btn btn-secondary">حذف فیلترها</a>
            <?php endif; ?>
        </div>
    </form>

    
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>عنوان</th>
                            <th>دسته‌بندی</th>
                            <th>مبلغ کل</th>
                            <th>دریافتی</th>
                            <th>باقی‌مانده</th>
                            <th>تاریخ</th>
                            <th>ماه</th>
                            <th>منبع</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $incomes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $income): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <?php $remaining = $income->total_amount - $income->received_amount; ?>
                            <tr>
                                <td><?php echo e($income->title); ?></td>
                                <td><?php echo e($income->category->name ?? '—'); ?></td>
                                <td><?php echo e(number_format($income->total_amount)); ?> ؋</td>
                                <td><?php echo e(number_format($income->received_amount)); ?> ؋</td>
                                <td><?php echo e(number_format(max($remaining, 0))); ?> ؋</td>
                                
                                <td><?php echo e($income->income_date ? \App\Helpers\JalaliHelper::toJalali($income->income_date) : '—'); ?></td>
                                <td><?php echo e($income->month->name ?? '—'); ?></td>
                                <td><?php echo e($income->source ?? '—'); ?></td>
                                <td>
                                    <?php switch($income->status):
                                        case ('received'): ?> <span class="badge bg-success">دریافت کامل</span> <?php break; ?>
                                        <?php case ('partially_received'): ?> <span class="badge bg-warning text-dark">دریافت جزئی</span> <?php break; ?>
                                        <?php case ('due'): ?> <span class="badge bg-danger">دریافت نشده</span> <?php break; ?>
                                        <?php case ('cancelled'): ?> <span class="badge bg-secondary">لغو شده</span> <?php break; ?>
                                    <?php endswitch; ?>
                                </td>
                                <td>
                                    <a href="<?php echo e(route('school.incomes.edit', $income->id)); ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <form action="<?php echo e(route('school.incomes.destroy', $income->id)); ?>" method="POST" class="d-inline"
                                          onsubmit="return confirm('آیا از حذف این عاید اطمینان دارید؟')">
                                        <?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?>
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <tr><td colspan="10" class="text-center text-muted py-3">هیچ عایدی ثبت نشده است.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php if($incomes->hasPages()): ?>
        <div class="card-footer"><?php echo e($incomes->links()); ?></div>
        <?php endif; ?>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH E:\school_finance_db\resources\views\school\incomes\index.blade.php ENDPATH**/ ?>