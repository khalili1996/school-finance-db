<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>لیست مکاتب</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container-fluid mt-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-school ms-2"></i> مدیریت مکاتب</h1>
            <a href="<?php echo e(route('admin.schools.create')); ?>" class="btn btn-success">
                <i class="fas fa-plus"></i> ایجاد مکتب جدید
            </a>
        </div>

        <?php if(session('success')): ?>
            <div class="alert alert-success"><?php echo e(session('success')); ?></div>
        <?php endif; ?>

        <div class="card shadow">
            <div class="card-body">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>کد</th>
                            <th>نام مکتب</th>
                            <th>تلفن</th>
                            <th>ایمیل</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $__empty_1 = true; $__currentLoopData = $schools; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $school): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <tr>
                            <td><?php echo e($school->code); ?></td>
                            <td><?php echo e($school->name); ?></td>
                            <td><?php echo e($school->phone ?? '—'); ?></td>
                            <td><?php echo e($school->email ?? '—'); ?></td>
                            <td>
                                <span class="badge bg-<?php echo e($school->is_active ? 'success' : 'danger'); ?>">
                                    <?php echo e($school->is_active ? 'فعال' : 'غیرفعال'); ?>

                                </span>
                            </td>
                            <td>
                                <a href="<?php echo e(route('admin.schools.enter', $school->id)); ?>" class="btn btn-sm btn-primary">
                                    <i class="fas fa-sign-in-alt"></i> ورود به مکتب
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <tr>
                            <td colspan="6" class="text-center">هیچ مکتبی ثبت نشده است.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <a href="/admin/dashboard" class="btn btn-secondary">بازگشت به داشبورد</a>
            <form method="POST" action="/admin/logout" class="d-inline">
                <?php echo csrf_field(); ?>
                <button type="submit" class="btn btn-danger">خروج</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\admin\schools\index.blade.php ENDPATH**/ ?>