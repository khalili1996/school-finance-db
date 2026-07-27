<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'پنل مدیریت'); ?> – دیتابیس مالی الزهرا (س)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <?php echo $__env->yieldPushContent('styles'); ?>
    <style>
        html,
        body {
            height: 100%;
            overflow: hidden;
            background: #f7fafc;
        }

        /* کل صفحه */
        #wrapper {
            display: flex;
            height: calc(100vh - 60px);
            margin-top: 60px;
        }

        /* سربرگ ثابت */
        .top-header {
            position: fixed;
            top: 0;
            right: 0;
            left: 0;
            height: 60px;
            z-index: 2000;
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, .25);
        }

        /* سایدبار ثابت */
        #sidebar-wrapper {
            width: 260px;
            flex-shrink: 0;
            position: fixed;
            right: 0;
            top: 60px;
            bottom: 0;
            overflow-y: auto;
            overflow-x: hidden;
            background: linear-gradient(180deg, #1a1a2e, #16213e);
            color: #e2e8f0;
            border-left: 1px solid #2d3748;
            box-shadow: -2px 0 12px rgba(0, 0, 0, .25);
            padding: 18px 10px;
        }

        /* اسکرول زیبای سایدبار */
        #sidebar-wrapper::-webkit-scrollbar {
            width: 8px;
        }
        #sidebar-wrapper::-webkit-scrollbar-track {
            background: #20263e;
        }
        #sidebar-wrapper::-webkit-scrollbar-thumb {
            background: #4f6ea7;
            border-radius: 20px;
        }
        #sidebar-wrapper::-webkit-scrollbar-thumb:hover {
            background: #6f92d3;
        }

        /* محتوای اصلی */
        #page-content-wrapper {
            margin-right: 260px;
            width: calc(100% - 260px);
            height: calc(100vh - 60px);
            overflow-y: auto;
            padding: 25px;
            background: #f7fafc;
        }

        /* اسکرول محتوای اصلی */
        #page-content-wrapper::-webkit-scrollbar {
            width: 9px;
        }
        #page-content-wrapper::-webkit-scrollbar-thumb {
            background: #b7bcc8;
            border-radius: 20px;
        }

        .nav-link {
            color: #d7dfef;
            padding: .6rem .8rem;
            border-radius: 8px;
            transition: .25s;
        }
        .nav-link:hover {
            background: rgba(255, 255, 255, .08);
            color: #fff;
        }
        .nav-link.active {
            background: #294d8c;
            color: #fff;
            border-right: 4px solid #67b8ff;
        }
        .nav-link i {
            width: 24px;
            color: #67b8ff;
        }
        .school-header {
            display: none;
        }
        /* حذف لوگوی سایدبار */
        .sidebar-logo {
            display: none;
        }
        .btn-logout {
            background: #dc3545;
            border: none;
        }
        .btn-logout:hover {
            background: #bb2d3b;
        }

        @media (max-width: 768px) {
            #sidebar-wrapper {
                width: 220px;
            }
            #page-content-wrapper {
                margin-right: 220px;
                width: calc(100% - 220px);
            }
        }
    </style>
</head>
<body>
<?php if(request()->is('school/*') && !session('active_academic_year_id')): ?>
    <div class="alert alert-warning text-center mb-0 rounded-0">
        ⚠️ سال مالی جاری انتخاب نشده است.
        <a href="<?php echo e(route('school.academic-years.index')); ?>" class="alert-link">برای تنظیم سال مالی کلیک کنید</a>.
    </div>
<?php endif; ?>
    
    <div class="top-header">
        <?php
            $schoolId = session('active_school_id', auth()->user()->school_id ?? null);
            $logo = \App\Models\Setting::get('logo', null, $schoolId);
            $schoolName = \App\Models\Setting::get('school_name') ?: (\App\Models\School::find($schoolId)->name ?? 'مکتب');
            $address = \App\Models\Setting::get('address', '', $schoolId);
            $phone   = \App\Models\Setting::get('phone', '', $schoolId);
            $email   = \App\Models\Setting::get('email', '', $schoolId);
        ?>
        <div style="display: flex; align-items: center; gap: 12px;">
            <?php if($logo): ?>
                <?php
                    $logoPath = storage_path('app/public/' . $logo);
                    $logoData = '';
                    if (file_exists($logoPath)) {
                        $type = pathinfo($logoPath, PATHINFO_EXTENSION);
                        $data = file_get_contents($logoPath);
                        $logoData = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                ?>
                <?php if($logoData): ?>
                    <img src="<?php echo e($logoData); ?>" alt="لوگو" style="width: 36px; height: 36px; object-fit: contain; border-radius: 4px;">
                <?php endif; ?>
            <?php endif; ?>
            <strong style="font-size: 16px; color: #f0d78c;"><?php echo e($schoolName); ?></strong>
        </div>
        <div style="display: flex; gap: 20px; font-size: 12px;">
            <?php if($address): ?><span><i class="fas fa-map-marker-alt"></i> <?php echo e($address); ?></span><?php endif; ?>
            <?php if($phone): ?><span><i class="fas fa-phone"></i> <?php echo e($phone); ?></span><?php endif; ?>
            <?php if($email): ?><span><i class="fas fa-envelope"></i> <?php echo e($email); ?></span><?php endif; ?>
        </div>
    </div>

    <div id="wrapper">
        <!-- سایدبار -->
        <div id="sidebar-wrapper">
            <?php
                $currentSchoolId = session('active_school_id', auth()->user()->school_id);
                $currentSchool = \App\Models\School::find($currentSchoolId);
                $stats = $studentStats ?? ['total'=>0,'debtor'=>0,'discount'=>0,'orphan'=>0,'left'=>0,'trash'=>0,'free'=>0,'fullFee'=>0];
            ?>

            

            <ul class="nav flex-column">
                
                <?php
                    $schoolId = session('active_school_id', auth()->user()->school_id ?? null);
                    $academicYears = \App\Models\AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();
                ?>
                <?php if($academicYears->isNotEmpty()): ?>
                    <li class="nav-item mb-3">
                        <div class="dropdown d-inline-block w-100">
                            <button class="btn btn-sm btn-outline-info dropdown-toggle w-100" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-calendar-alt"></i>
                                سال مالی: <?php echo e(session('active_academic_year_name', 'انتخاب نشده')); ?>

                            </button>
                            <ul class="dropdown-menu">
                                <?php $__currentLoopData = $academicYears; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $yr): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li>
                                        <a class="dropdown-item <?php echo e($yr->name == session('active_academic_year_name') ? 'active' : ''); ?>"
                                           href="<?php echo e(route('school.set-academic-year', $yr->id)); ?>">
                                            <?php echo e($yr->name); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </li>
                <?php else: ?>
                    <li class="nav-item mb-3">
                        <span class="text-warning">سال مالی تعریف نشده</span>
                    </li>
                <?php endif; ?>

                
                
                
                
                <li class="nav-item">
                    <a href="<?php echo e(route('school.dashboard')); ?>" class="nav-link <?php if(request()->is('school/dashboard')): ?> active <?php endif; ?>">
                        <i class="fas fa-tachometer-alt"></i> داشبورد
                    </a>
                </li>

                
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#studentsMenu" role="button" aria-expanded="<?php echo e(request()->is('school/students*') ? 'true' : 'false'); ?>">
                        <span><i class="fas fa-users"></i> دانش‌آموزان</span>
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse <?php echo e(request()->is('school/students*') ? 'show' : ''); ?>" id="studentsMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="<?php echo e(route('school.students.index')); ?>" class="nav-link <?php if(!request()->query('filter') && !request()->query('status_filter') && !request()->query('financial_filter')): ?> active <?php endif; ?>">
                                    <i class="fas fa-list"></i> لیست دانش‌آموزان
                                    <span class="badge bg-primary rounded-pill ms-auto"><?php echo e($stats['total']); ?></span>
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo e(route('school.students.index', ['filter' => 'senfi'])); ?>" class="nav-link <?php if(request('filter') == 'senfi'): ?> active <?php endif; ?>">
                                    <i class="fas fa-layer-group"></i> صنف‌بندی
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo e(route('school.students.trash')); ?>" class="nav-link">
                                    <i class="fas fa-trash"></i> سطل زباله
                                    <span class="badge bg-warning rounded-pill ms-auto"><?php echo e($stats['trash']); ?></span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#guardiansMenu" role="button" aria-expanded="<?php echo e(request()->is('school/guardians*') ? 'true' : 'false'); ?>">
                        <span><i class="fas fa-user-friends"></i> مدیریت اولیا</span>
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse <?php echo e(request()->is('school/guardians*') ? 'show' : ''); ?>" id="guardiansMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a href="<?php echo e(route('school.guardians.index')); ?>" class="nav-link">لیست اولیا</a></li>
                            <li class="nav-item"><a href="<?php echo e(route('school.guardians.create')); ?>" class="nav-link">ثبت ولی جدید</a></li>
                            <li class="nav-item"><a href="<?php echo e(route('school.guardians.index', ['financial' => 'debtor'])); ?>" class="nav-link">خانواده‌های بدهکار</a></li>
                            <li class="nav-item"><a href="<?php echo e(route('school.guardians.index', ['financial' => 'discount'])); ?>" class="nav-link">خانواده‌های دارای تخفیف</a></li>
                            <li class="nav-item"><a href="<?php echo e(route('school.guardians.index', ['financial' => 'free'])); ?>" class="nav-link">خانواده‌های یتیم</a></li>
                        </ul>
                    </div>
                </li>

                
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#employeesMenu" role="button" aria-expanded="<?php echo e(request()->is('school/employees*') ? 'true' : 'false'); ?>">
                        <span><i class="fas fa-user-tie"></i> کارمندان</span>
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse <?php echo e(request()->is('school/employees*') ? 'show' : ''); ?>" id="employeesMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="<?php echo e(route('school.employees.index')); ?>" class="nav-link <?php if(!request()->query('role_id') && !request()->query('status')): ?> active <?php endif; ?>">
                                    <i class="fas fa-list"></i> لیست کارمندان
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo e(route('school.employees.create')); ?>" class="nav-link">
                                    <i class="fas fa-plus"></i> ثبت کارمند جدید
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#assetsMenu" role="button" aria-expanded="<?php echo e(request()->is('school/assets*') || request()->is('school/asset-categories*') ? 'true' : 'false'); ?>">
                        <span><i class="fas fa-cubes"></i> اموال و تجهیزات</span>
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse <?php echo e(request()->is('school/assets*') || request()->is('school/asset-categories*') ? 'show' : ''); ?>" id="assetsMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item">
                                <a href="<?php echo e(route('school.assets.index')); ?>" class="nav-link <?php if(request()->is('school/assets') && !request()->is('school/assets/*')): ?> active <?php endif; ?>">
                                    لیست تجهیزات
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo e(route('school.assets.create')); ?>" class="nav-link <?php if(request()->is('school/assets/create')): ?> active <?php endif; ?>">
                                    ثبت تجهیز جدید
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="<?php echo e(route('school.asset-categories.index')); ?>" class="nav-link <?php if(request()->is('school/asset-categories*')): ?> active <?php endif; ?>">
                                    دسته‌بندی‌ها
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#financeMenu" role="button" aria-expanded="<?php echo e(request()->is('school/fee-types*') || request()->is('school/student-fees*') || request()->is('school/payments*') || request()->is('school/incomes*') || request()->is('school/income-categories*') || request()->is('school/cashboxes*') || request()->is('school/cashbox-transactions*') || request()->is('school/expenses*') || request()->is('school/expense-categories*') || request()->is('school/salaries*') || request()->is('school/employee-advances*') || request()->is('school/ledger*') || request()->is('school/academic-years*') || request()->is('school/terms*') ? 'true' : 'false'); ?>">
                        <span><i class="fas fa-money-bill-wave"></i> حسابداری</span>
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse <?php echo e(request()->is('school/fee-types*') || request()->is('school/student-fees*') || request()->is('school/payments*') || request()->is('school/incomes*') || request()->is('school/income-categories*') || request()->is('school/cashboxes*') || request()->is('school/cashbox-transactions*') || request()->is('school/expenses*') || request()->is('school/expense-categories*') || request()->is('school/salaries*') || request()->is('school/employee-advances*') || request()->is('school/ledger*') || request()->is('school/academic-years*') || request()->is('school/terms*') ? 'show' : ''); ?>" id="financeMenu">
                        <ul class="nav flex-column">
                            
                            <li class="nav-item">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#incomeSubMenu" role="button" aria-expanded="<?php echo e(request()->is('school/fee-types*') || request()->is('school/student-fees*') || request()->is('school/payments*') || request()->is('school/incomes*') || request()->is('school/income-categories*') ? 'true' : 'false'); ?>">
                                    <span><i class="fas fa-arrow-down"></i> درآمد</span>
                                    <i class="fas fa-chevron-down small"></i>
                                </a>
                                <div class="collapse <?php echo e(request()->is('school/fee-types*') || request()->is('school/student-fees*') || request()->is('school/payments*') || request()->is('school/incomes*') || request()->is('school/income-categories*') ? 'show' : ''); ?>" id="incomeSubMenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item"><a href="<?php echo e(route('school.fee-types.index')); ?>" class="nav-link <?php if(request()->is('school/fee-types*')): ?> active <?php endif; ?>"><i class="fas fa-tags"></i> انواع هزینه‌ها</a></li>
                                        <li class="nav-item"><a href="<?php echo e(route('school.student-fees.index')); ?>" class="nav-link <?php if(request()->is('school/student-fees*')): ?> active <?php endif; ?>"><i class="fas fa-file-invoice-dollar"></i> ثبت شهریه</a></li>
                                        <li class="nav-item"><a href="<?php echo e(route('school.incomes.index')); ?>" class="nav-link <?php if(request()->is('school/incomes*')): ?> active <?php endif; ?>"><i class="fas fa-coins"></i> ثبت درآمد</a></li>
                                        <li class="nav-item"><a href="<?php echo e(route('school.income-categories.index')); ?>" class="nav-link <?php if(request()->is('school/income-categories*')): ?> active <?php endif; ?>"><i class="fas fa-tags"></i> دسته‌بندی درآمد</a></li>
                                    </ul>
                                </div>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#expenseSubMenu" role="button" aria-expanded="<?php echo e(request()->is('school/expenses*') || request()->is('school/expense-categories*') || request()->is('school/salaries*') || request()->is('school/employee-advances*') || request()->is('school/invoices*') ? 'true' : 'false'); ?>">
                                    <span><i class="fas fa-arrow-up"></i> مصرف</span>
                                    <i class="fas fa-chevron-down small"></i>
                                </a>
                                <div class="collapse <?php echo e(request()->is('school/expenses*') || request()->is('school/expense-categories*') || request()->is('school/salaries*') || request()->is('school/employee-advances*') || request()->is('school/invoices*') ? 'show' : ''); ?>" id="expenseSubMenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item"><a href="<?php echo e(route('school.expenses.index')); ?>" class="nav-link <?php if(request()->is('school/expenses')): ?> active <?php endif; ?>"><i class="fas fa-file-invoice"></i> مصارف</a></li>
                                        <li class="nav-item"><a href="<?php echo e(route('school.expense-categories.index')); ?>" class="nav-link <?php if(request()->is('school/expense-categories*')): ?> active <?php endif; ?>"><i class="fas fa-tags"></i> دسته‌بندی مصارف</a></li>
                                        <li class="nav-item"><a href="<?php echo e(route('school.invoices.index')); ?>" class="nav-link <?php if(request()->is('school/invoices*')): ?> active <?php endif; ?>"><i class="fas fa-file-image"></i> فاکتورها</a></li>
                                        
                                        <li class="nav-item">
                                            <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#salariesSubMenu" role="button" aria-expanded="<?php echo e(request()->is('school/salaries*') || request()->is('school/employee-advances*') ? 'true' : 'false'); ?>">
                                                <span><i class="fas fa-money-check-alt"></i> معاشات</span>
                                                <i class="fas fa-chevron-down small"></i>
                                            </a>
                                            <div class="collapse <?php echo e(request()->is('school/salaries*') || request()->is('school/employee-advances*') ? 'show' : ''); ?>" id="salariesSubMenu">
                                                <ul class="nav flex-column">
                                                    <li class="nav-item"><a href="<?php echo e(route('school.salaries.index')); ?>" class="nav-link <?php if(request()->is('school/salaries') && !request()->has('status')): ?> active <?php endif; ?>"><i class="fas fa-list"></i> همه معاشات</a></li>
                                                    <li class="nav-item"><a href="<?php echo e(route('school.salaries.create')); ?>" class="nav-link"><i class="fas fa-plus"></i> ثبت معاش جدید</a></li>
                                                    <li class="nav-item"><a href="<?php echo e(route('school.employee-advances.index')); ?>" class="nav-link <?php if(request()->is('school/employee-advances*')): ?> active <?php endif; ?>"><i class="fas fa-hand-holding-usd"></i> پیش‌پرداخت‌ها</a></li>
                                                    <li class="nav-item"><a href="<?php echo e(route('school.salaries.index', ['status' => 'unpaid'])); ?>" class="nav-link"><i class="fas fa-exclamation-triangle"></i> پرداخت نشده</a></li>
                                                    <li class="nav-item"><a href="<?php echo e(route('school.salaries.index', ['status' => 'paid'])); ?>" class="nav-link"><i class="fas fa-check-circle"></i> پرداخت شده</a></li>
                                                </ul>
                                            </div>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            
                            <li class="nav-item">
                                <a href="<?php echo e(route('school.cashboxes.index')); ?>" class="nav-link <?php if(request()->is('school/cashboxes*') || request()->is('school/cashbox-transactions*')): ?> active <?php endif; ?>">
                                    <i class="fas fa-cash-register"></i> صندوق
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a href="<?php echo e(route('school.ledger.index')); ?>" class="nav-link <?php if(request()->is('school/ledger*')): ?> active <?php endif; ?>">
                                    <i class="fas fa-book"></i> دفتر کل
                                </a>
                            </li>
                            
                            <li class="nav-item">
                                <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#periodsMenu" role="button" aria-expanded="<?php echo e(request()->is('school/academic-years*') || request()->is('school/terms*') ? 'true' : 'false'); ?>">
                                    <span><i class="fas fa-calendar-alt"></i> دوره‌های مالی</span>
                                    <i class="fas fa-chevron-down small"></i>
                                </a>
                                <div class="collapse <?php echo e(request()->is('school/academic-years*') || request()->is('school/terms*') ? 'show' : ''); ?>" id="periodsMenu">
                                    <ul class="nav flex-column">
                                        <li class="nav-item"><a href="<?php echo e(route('school.academic-years.index')); ?>" class="nav-link <?php if(request()->is('school/academic-years*')): ?> active <?php endif; ?>"><i class="fas fa-calendar-alt"></i> سال‌های مالی</a></li>
                                        <li class="nav-item"><a href="<?php echo e(route('school.terms.index')); ?>" class="nav-link <?php if(request()->is('school/terms*')): ?> active <?php endif; ?>"><i class="fas fa-calendar-week"></i> ترم‌ها</a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>

                
                <li class="nav-item">
                    <a class="nav-link d-flex justify-content-between align-items-center" data-bs-toggle="collapse" href="#independentLoanMenu" role="button" aria-expanded="<?php echo e(request()->is('school/loan-fund*') || request()->is('school/loans*') || request()->is('school/installments*') ? 'true' : 'false'); ?>">
                        <span><i class="fas fa-hand-holding-heart"></i> قرض‌الحسنه (صندوق مستقل)</span>
                        <i class="fas fa-chevron-down small"></i>
                    </a>
                    <div class="collapse <?php echo e(request()->is('school/loan-fund*') || request()->is('school/loans*') || request()->is('school/installments*') ? 'show' : ''); ?>" id="independentLoanMenu">
                        <ul class="nav flex-column">
                            <li class="nav-item"><a href="<?php echo e(route('school.loan-fund.index')); ?>" class="nav-link <?php if(request()->is('school/loan-fund*')): ?> active <?php endif; ?>"><i class="fas fa-piggy-bank"></i> صندوق قرض‌الحسنه</a></li>
                            <li class="nav-item"><a href="<?php echo e(route('school.loan-fund.deposit')); ?>" class="nav-link <?php if(request()->is('school/loan-fund/deposit')): ?> active <?php endif; ?>"><i class="fas fa-plus-circle"></i> واریز به صندوق</a></li>
                            <li class="nav-item border-top mt-1 pt-1"><a href="<?php echo e(route('school.loans.index')); ?>" class="nav-link <?php if(request()->is('school/loans') && !request()->is('school/loans/create')): ?> active <?php endif; ?>"><i class="fas fa-list"></i> وام‌های جاری</a></li>
                            <li class="nav-item"><a href="<?php echo e(route('school.loans.create')); ?>" class="nav-link <?php if(request()->is('school/loans/create')): ?> active <?php endif; ?>"><i class="fas fa-plus-circle"></i> ثبت وام جدید</a></li>
                        </ul>
                    </div>
                </li>

                
                <li class="nav-item reports-section">
                    <a href="<?php echo e(route('school.reports.index')); ?>" class="nav-link <?php if(request()->is('school/reports*')): ?> active <?php endif; ?>">
                        <i class="fas fa-chart-bar"></i> گزارشات
                    </a>
                </li>

                
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('school.backup.index')); ?>">
                        <i class="fas fa-database"></i>
                        <span>پشتیبان‌گیری</span>
                    </a>
                </li>

                
                <li class="nav-item mt-3">
                    <form method="POST" action="/logout">
                        <?php echo csrf_field(); ?>
                        <button type="submit" class="btn btn-logout btn-sm w-100">
                            <i class="fas fa-sign-out-alt"></i> خروج
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        <!-- محتوای اصلی -->
        <div id="page-content-wrapper">
            <header class="mb-4 border-bottom pb-2 d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <?php echo e(auth()->user()->name); ?> –
                    <?php $__currentLoopData = auth()->user()->roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <span class="badge bg-primary"><?php echo e($role->name); ?></span>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </h5>
                <small class="text-muted">تاریخ: <?php echo e(now()->format('Y/m/d')); ?></small>
            </header>
            <?php echo $__env->yieldContent('content'); ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\layouts\admin.blade.php ENDPATH**/ ?>