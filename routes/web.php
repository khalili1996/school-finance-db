<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\School\AuthController as SchoolAuthController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\SchoolController;
use App\Http\Controllers\School\UserController as SchoolUserController;
use App\Http\Controllers\School\DashboardController as SchoolDashboardController;
use App\Http\Controllers\School\StudentController as SchoolStudentController;
use App\Http\Controllers\School\GuardianController as SchoolGuardianController;
use App\Http\Controllers\School\EmployeeController as SchoolEmployeeController;
use App\Http\Controllers\School\FeeTypeController as SchoolFeeTypeController;
use App\Http\Controllers\School\StudentFeeController as SchoolStudentFeeController;
use App\Http\Controllers\School\PaymentController as SchoolPaymentController;
use App\Http\Controllers\School\ExpenseController as SchoolExpenseController;
use App\Http\Controllers\School\ExpenseCategoryController as SchoolExpenseCategoryController;
use App\Http\Controllers\School\InvoiceController as SchoolInvoiceController;
use App\Http\Controllers\School\IncomeController as SchoolIncomeController;
use App\Http\Controllers\School\IncomeCategoryController as SchoolIncomeCategoryController;
use App\Http\Controllers\School\AcademicYearController as SchoolAcademicYearController;
use App\Http\Controllers\School\SalaryPaymentController;
use App\Http\Controllers\School\BackupController;
use App\Http\Controllers\StudentGuardianController;
use App\Http\Controllers\Api\DateController;
use App\Http\Controllers\School\SetupYearController;

// ==========================================
// ۱. صفحه‌ی اصلی
// ==========================================
Route::get('/', [HomeController::class, 'index'])->name('home');

// ==========================================
// ۲. مسیرهای ورود کاربران مدرسه
// ==========================================
Route::get('/login', [SchoolAuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [SchoolAuthController::class, 'login']);
Route::post('/logout', [SchoolAuthController::class, 'logout'])->name('logout');

// ==========================================
// ۳. مسیرهای پنل سوپر ادمین
// ==========================================
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login']);
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::middleware('auth')->group(function () {
        Route::get('/dashboard', [SchoolController::class, 'index'])->name('dashboard');

        // مدیریت مدارس
        Route::get('/schools/trash', [SchoolController::class, 'trash'])->name('schools.trash');
        Route::post('/schools/{id}/restore', [SchoolController::class, 'restore'])->name('schools.restore');
        Route::delete('/schools/{id}/force-delete', [SchoolController::class, 'forceDelete'])->name('schools.force-delete');
        Route::resource('schools', SchoolController::class);

        Route::get('/schools/{school}/enter', [SchoolController::class, 'enter'])->name('schools.enter');
        Route::get('/exit-school', function () {
            session()->forget('active_school_id');
            return redirect('/admin/dashboard');
        })->name('exit-school');
    });
});

// ==========================================
// ۴. فضای داخلی مدرسه
// ==========================================
Route::middleware('auth')->prefix('school')->name('school.')->group(function () {
    Route::get('/dashboard', [SchoolDashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', SchoolUserController::class);

    // دانش‌آموزان
    Route::get('/students/trash', [SchoolStudentController::class, 'trash'])->name('students.trash');
    Route::post('/students/{id}/restore', [SchoolStudentController::class, 'restore'])->name('students.restore');
    Route::delete('/students/{id}/force-delete', [SchoolStudentController::class, 'forceDelete'])->name('students.force-delete');
    Route::get('/students/{student}/preview', [SchoolStudentController::class, 'preview'])->name('students.preview');
    Route::get('/students/report', [SchoolStudentController::class, 'report'])->name('students.report');
    Route::resource('students', SchoolStudentController::class);

    // انتقال دانش‌آموزان
    Route::post('/students/{student}/transfer', [SchoolStudentController::class, 'transferSingle'])->name('students.transfer-single');
    Route::post('/students/transfer-multiple', [SchoolStudentController::class, 'transferMultiple'])->name('students.transfer-multiple');

    // اولیا
    Route::resource('guardians', SchoolGuardianController::class);
    Route::get('/guardians/{guardian}/preview', [SchoolGuardianController::class, 'preview'])->name('guardians.preview');
    Route::get('/guardians/report', [SchoolGuardianController::class, 'report'])->name('guardians.report');

    // کارمندان
    Route::get('/employees/trash', [SchoolEmployeeController::class, 'trash'])->name('employees.trash');
    Route::post('/employees/{id}/restore', [SchoolEmployeeController::class, 'restore'])->name('employees.restore');
    Route::delete('/employees/{id}/force-delete', [SchoolEmployeeController::class, 'forceDelete'])->name('employees.force-delete');
    Route::get('/employees/{employee}/preview', [SchoolEmployeeController::class, 'preview'])->name('employees.preview');
    Route::get('/employees/report', [SchoolEmployeeController::class, 'report'])->name('employees.report');
    Route::resource('employees', SchoolEmployeeController::class);
    Route::post('/employees/roles/quick-store', [SchoolEmployeeController::class, 'quickStoreRole'])->name('employees.roles.quick-store');
    Route::post('/employees/{employee}/transfer', [SchoolEmployeeController::class, 'transferSingle'])->name('employees.transfer-single');
Route::post('/employees/transfer-multiple', [SchoolEmployeeController::class, 'transferMultiple'])->name('employees.transfer-multiple');
    // انواع هزینه‌ها
    Route::resource('fee-types', SchoolFeeTypeController::class);

    // شهریه دانش‌آموزان
    Route::get('/student-fees/print', [SchoolStudentFeeController::class, 'printReport'])->name('student-fees.print');
    Route::get('/student-fees/{student}/fee-preview', [SchoolStudentFeeController::class, 'feePreview'])->name('student-fees.fee-preview');
    Route::get('/student-fees/{student}/notice', [SchoolStudentFeeController::class, 'feeNotice'])->name('student-fees.notice');
    Route::get('/student-fees/notice-report', [SchoolStudentFeeController::class, 'feeNoticeReport'])->name('student-fees.notice-report');

    Route::delete('/student-fees/delete-by-student/{student}', [SchoolStudentFeeController::class, 'destroyByStudent'])->name('student-fees.delete-student');

    Route::resource('student-fees', SchoolStudentFeeController::class);

    Route::get('/api/students/search', [SchoolPaymentController::class, 'searchStudents'])->name('api.students.search');

    // پرداخت‌ها (شهریه)
    Route::get('/api/students/{student}/fees', [SchoolPaymentController::class, 'feesByStudent'])->name('api.students.fees');
    Route::get('/payments/{payment}/receipt', [SchoolPaymentController::class, 'receipt'])->name('payments.receipt');
    Route::post('/payments/sync-old-to-income', [SchoolPaymentController::class, 'syncOldToIncome'])->name('payments.sync-old');
    Route::post('/payments/sync-to-ledger', [SchoolPaymentController::class, 'syncOldPaymentsToLedger'])->name('payments.sync-to-ledger');
    Route::resource('payments', SchoolPaymentController::class);
    Route::get('/student-fees/payment/{payment}/slip', [SchoolStudentFeeController::class, 'paymentSlip'])->name('student-fees.payment-slip');
   Route::get('/payments/{payment}/slip', [SchoolPaymentController::class, 'paymentSlip'])->name('payments.payment-slip');
    // مصارف
    Route::get('/expenses/report', [SchoolExpenseController::class, 'report'])->name('expenses.report');
    Route::resource('expenses', SchoolExpenseController::class);
    Route::resource('expense-categories', SchoolExpenseCategoryController::class);

    // فاکتورها
    Route::get('/invoices', [SchoolInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('/invoices/report', [SchoolInvoiceController::class, 'report'])->name('invoices.report');

    // عواید
    Route::get('/incomes/report', [SchoolIncomeController::class, 'report'])->name('incomes.report');
    Route::resource('incomes', SchoolIncomeController::class);
    Route::resource('income-categories', SchoolIncomeCategoryController::class);

    Route::get('/api/students/by-class', [SchoolStudentController::class, 'byClass'])->name('api.students.by-class');

    // صندوق (مدرسه)
    Route::post('/cashboxes/sync-old-transactions', [\App\Http\Controllers\School\CashboxController::class, 'syncOldTransactions'])->name('cashboxes.sync-old');
    Route::post('/cashboxes/clean-orphan', [\App\Http\Controllers\School\CashboxController::class, 'cleanOrphanTransactions'])->name('cashboxes.clean-orphan');
    Route::resource('cashboxes', \App\Http\Controllers\School\CashboxController::class);
    Route::resource('cashbox-transactions', \App\Http\Controllers\School\CashboxTransactionController::class)->only(['destroy']);

    // ----------------------------------------------------------------
    // بخش معاشات
    // ----------------------------------------------------------------
    Route::get('/salaries/print-report', [\App\Http\Controllers\School\SalaryController::class, 'printReport'])->name('salaries.print-report');
    Route::get('/salaries/{employee}/preview', [\App\Http\Controllers\School\SalaryController::class, 'preview'])->name('salaries.preview');
    Route::resource('salaries', \App\Http\Controllers\School\SalaryController::class);

    Route::get('/salary-payments/create', [SalaryPaymentController::class, 'create'])->name('salary-payments.create');
    Route::post('/salary-payments', [SalaryPaymentController::class, 'store'])->name('salary-payments.store');
    Route::get('/salary-payments/{payment}/receipt', [SalaryPaymentController::class, 'receipt'])->name('salary-payments.receipt');
    Route::post('/salary-payments/quick', [SalaryPaymentController::class, 'quickStore'])->name('salary-payments.quick-store');
    Route::get('/salary-payments/{salaryPayment}/edit', [SalaryPaymentController::class, 'edit'])->name('salary-payments.edit');
    Route::put('/salary-payments/{salaryPayment}', [SalaryPaymentController::class, 'update'])->name('salary-payments.update');
    Route::delete('/salary-payments/{salaryPayment}', [SalaryPaymentController::class, 'destroy'])->name('salary-payments.destroy');

    // ----------------------------------------------------------------
    // پیش‌پرداخت‌ها (مساعده)
    // ----------------------------------------------------------------
    Route::get('/api/employee-advances/sum', [\App\Http\Controllers\School\EmployeeAdvanceController::class, 'advanceSum'])->name('api.employee-advances.sum');
    Route::get('/employee-advances/{employeeAdvance}/receipt', [\App\Http\Controllers\School\EmployeeAdvanceController::class, 'receipt'])->name('employee-advances.receipt');
    Route::resource('employee-advances', \App\Http\Controllers\School\EmployeeAdvanceController::class);

    // ----------------------------------------------------------------
    // اموال و تجهیزات
    // ----------------------------------------------------------------
    Route::get('/assets/print', [\App\Http\Controllers\School\AssetController::class, 'printReport'])->name('assets.print');
    Route::resource('assets', \App\Http\Controllers\School\AssetController::class);
    Route::resource('asset-categories', \App\Http\Controllers\School\AssetCategoryController::class);

    // ----------------------------------------------------------------
    // گزارشات
    // ----------------------------------------------------------------
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/', [\App\Http\Controllers\School\ReportController::class, 'index'])->name('index');
        Route::get('students', [\App\Http\Controllers\School\ReportController::class, 'students'])->name('students');
        Route::get('guardians', [\App\Http\Controllers\School\ReportController::class, 'guardians'])->name('guardians');
        Route::get('employees', [\App\Http\Controllers\School\ReportController::class, 'employees'])->name('employees');

        Route::prefix('financial')->name('financial.')->group(function () {
            Route::get('incomes', [\App\Http\Controllers\School\ReportController::class, 'financialIncomes'])->name('incomes');
            Route::get('expenses', [\App\Http\Controllers\School\ReportController::class, 'financialExpenses'])->name('expenses');
            Route::get('cashboxes', [\App\Http\Controllers\School\ReportController::class, 'financialCashboxes'])->name('cashboxes');
            Route::get('ledger', [\App\Http\Controllers\School\ReportController::class, 'financialLedger'])->name('ledger');
        });

        Route::get('loans', [\App\Http\Controllers\School\ReportController::class, 'loans'])->name('loans');
        Route::get('assets', [\App\Http\Controllers\School\ReportController::class, 'assetsReport'])->name('assets');
        Route::get('comprehensive', [\App\Http\Controllers\School\ReportController::class, 'comprehensive'])->name('comprehensive');
    });

    // ----------------------------------------------------------------
    // قرض‌الحسنه‌ها (مستقل)
    // ----------------------------------------------------------------
    Route::get('/loan-fund', [\App\Http\Controllers\School\LoanFundController::class, 'index'])->name('loan-fund.index');
    Route::get('/loan-fund/deposit', [\App\Http\Controllers\School\LoanFundController::class, 'createDeposit'])->name('loan-fund.deposit');
    Route::post('/loan-fund/deposit', [\App\Http\Controllers\School\LoanFundController::class, 'storeDeposit'])->name('loan-fund.deposit.store');

    Route::get('/loans/{loan}/installments', [\App\Http\Controllers\School\LoanInstallmentController::class, 'index'])->name('loans.installments');
    Route::get('/installments/{installment}/edit', [\App\Http\Controllers\School\LoanInstallmentController::class, 'edit'])->name('installments.edit');
    Route::put('/installments/{installment}', [\App\Http\Controllers\School\LoanInstallmentController::class, 'update'])->name('installments.update');
    Route::delete('/installments/{installment}', [\App\Http\Controllers\School\LoanInstallmentController::class, 'destroy'])->name('installments.destroy');
    Route::get('/installments/{installment}/receipt', [\App\Http\Controllers\School\LoanInstallmentController::class, 'receipt'])->name('installments.receipt');
    Route::post('/installments/pay', [\App\Http\Controllers\School\LoanInstallmentController::class, 'payInstallment'])->name('installments.pay');
    //Route::get('/loans/{loan}/show', [\App\Http\Controllers\School\LoanController::class, 'show'])->name('loans.show');
    Route::resource('loans', \App\Http\Controllers\School\LoanController::class);

    // ----------------------------------------------------------------
    // اظهارنامه‌ها
    // ----------------------------------------------------------------
    Route::resource('declarations', \App\Http\Controllers\School\DeclarationController::class);

    // دوره‌های مالی
    Route::get('/academic-years/{academicYear}/set', [SchoolAcademicYearController::class, 'setAcademicYear'])->name('set-academic-year');
    Route::resource('academic-years', SchoolAcademicYearController::class);
    Route::resource('terms', \App\Http\Controllers\School\TermController::class);

    // ----------------------------------------------------------------
    // دفتر کل
    // ----------------------------------------------------------------
    Route::delete('/ledger/{ledgerEntry}', [\App\Http\Controllers\School\LedgerController::class, 'destroy'])->name('ledger.destroy');
    Route::get('/ledger', [\App\Http\Controllers\School\LedgerController::class, 'index'])->name('ledger.index');

    // ----------------------------------------------------------------
    // پشتیبان‌گیری (بکاپ)
    // ----------------------------------------------------------------
    Route::get('/backup', [BackupController::class, 'index'])->name('backup.index');
    Route::get('/backup/download', [BackupController::class, 'download'])->name('backup.download');

    // ----------------------------------------------------------------
    // راه‌اندازی سال جدید
    // ----------------------------------------------------------------
    Route::get('/setup-year', [SetupYearController::class, 'index'])->name('setup-year.index');
    Route::post('/setup-year/start', [SetupYearController::class, 'start'])->name('setup-year.start');
});

// ==========================================
// ۵. تنظیم سال تحصیلی (بیرون از prefix school)
// ==========================================
//Route::middleware('auth')->get('/set-academic-year/{academicYear}', function (\App\Models\AcademicYear $academicYear) {
  //  if ($academicYear->school_id != session('active_school_id')) {
    //    abort(403);
    //}
    //session([
      //  'active_academic_year_id'    => $academicYear->id,
        //'active_academic_year_start' => $academicYear->start_date,
        //'active_academic_year_end'   => $academicYear->end_date,
        //'active_academic_year_name'  => $academicYear->name,
    //]);
    // ★ به جای redirect()->back()، مستقیماً به داشبورد بروید
    //return redirect()->route('school.dashboard')->with('success', 'سال مالی فعال شد: ' . $academicYear->name);
//})->name('school.set-academic-year');
// API تاریخ
Route::post('/api/convert-date', [DateController::class, 'convert'])->name('api.convert-date');
Route::get('/api/today-date', [DateController::class, 'today'])->name('api.today-date');
