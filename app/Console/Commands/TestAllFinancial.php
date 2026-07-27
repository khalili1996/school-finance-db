<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Cashbox;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\FeeType;
use App\Models\Salary;
use App\Models\SalaryPayment;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Models\Month;
use App\Models\School;
use App\Models\AcademicYear;
use App\Services\AccountingService;

class TestAllFinancial extends Command
{
    protected $signature = 'project:test-all';
    protected $description = 'تست جامع تمام عملیات مالی (مصرف، درآمد، شهریه، معاش)';

    private AccountingService $accounting;
    private int $schoolId;
    private $cashbox;

    public function __construct()
    {
        parent::__construct();
        $this->accounting = app(AccountingService::class);
    }

    public function handle()
    {
        $this->info('🚀 شروع تست جامع مالی...');
// حذف کامل مدرسهٔ تستی که ممکن است soft-delete شده باشد
School::withTrashed()->where('code', 'TEST')->forceDelete();
        // ۱. انتخاب یا ایجاد مدرسه تست
        $school = School::firstOrCreate(
            ['code' => 'TEST'],
            ['name' => 'مدرسه تست']
        );
        $this->schoolId = $school->id;

        // ۲. ایجاد صندوق تست با موجودی اولیه ۱۰۰,۰۰۰
        $this->cashbox = Cashbox::firstOrCreate(
            ['school_id' => $this->schoolId, 'name' => 'صندوق تست'],
            ['current_balance' => 100000, 'is_active' => true]
        );
        $this->cashbox->update(['current_balance' => 100000]);

        // ۳. اجرای تست‌ها
        $this->testExpense();
        $this->testIncome();
        $this->testPayment();
        $this->testSalary();

        $this->info('🎉 تست جامع با موفقیت به پایان رسید.');
        return 0;
    }

    // -------------------------------------------------
    // ۱. تست مصارف
    // -------------------------------------------------
    private function testExpense()
    {
        $this->info('--- ۱. تست مصرف ---');
        $category = ExpenseCategory::firstOrCreate(
            ['school_id' => $this->schoolId, 'name' => 'تست سلامت'],
            []
        );

        $expenseData = [
            'school_id'          => $this->schoolId,
            'expense_category_id'=> $category->id,
            'title'              => 'تست مصرف',
            'total_amount'       => 5000,
            'paid_amount'        => 5000,
            'expense_date'       => now()->format('Y-m-d'),
            'status'             => 'paid',
            'cashbox_id'         => $this->cashbox->id,
        ];

        $expense = $this->accounting->recordExpense($expenseData);
        $this->assertBalance(95000, 'ثبت مصرف');

        $this->accounting->updateExpense($expense, array_merge($expenseData, ['paid_amount' => 3000]));
        $this->assertBalance(97000, 'ویرایش مصرف');

        $this->accounting->deleteExpense($expense);
        $this->assertBalance(100000, 'حذف مصرف');
    }

    // -------------------------------------------------
    // ۲. تست درآمدها
    // -------------------------------------------------
    private function testIncome()
    {
        $this->info('--- ۲. تست درآمد ---');
        $incCategory = IncomeCategory::firstOrCreate(
            ['school_id' => $this->schoolId, 'name' => 'تست سلامت'],
            []
        );

        $incomeData = [
            'school_id'         => $this->schoolId,
            'income_category_id'=> $incCategory->id,
            'title'             => 'تست درآمد',
            'total_amount'      => 10000,
            'received_amount'   => 10000,
            'income_date'       => now()->format('Y-m-d'),
            'status'            => 'received',
            'cashbox_id'        => $this->cashbox->id,
        ];

        $income = $this->accounting->recordIncome($incomeData);
        $this->assertBalance(110000, 'ثبت درآمد');

        $this->accounting->updateIncome($income, array_merge($incomeData, ['received_amount' => 7000]));
        $this->assertBalance(107000, 'ویرایش درآمد');

        $this->accounting->deleteIncome($income);
        $this->assertBalance(100000, 'حذف درآمد');
    }

    // -------------------------------------------------
    // ۳. تست شهریه
    // -------------------------------------------------
    private function testPayment()
    {
        $this->info('--- ۳. تست شهریه ---');
        $student = Student::firstOrCreate(
            [
                'school_id'    => $this->schoolId,
                'first_name'   => 'دانش‌آموز',
                'last_name'    => 'تست',
                'national_id'  => '1234567890',
            ],
            [
                'father_name'  => 'پدر',
                'gender'       => 'male',
                'status'       => 'present',
                'class'        => 'اول',
                'student_code' => 'TEST001',
            ]
        );

        $feeType = FeeType::firstOrCreate(
            ['school_id' => $this->schoolId, 'name' => 'شهریه ماهانه'],
            ['is_active' => true]
        );
        $month = Month::firstOrCreate(
            ['school_id' => $this->schoolId, 'name' => 'حمل', 'number' => 1],
            []
        );
        $fee = StudentFee::firstOrCreate(
            [
                'school_id'  => $this->schoolId,
                'student_id' => $student->id,
                'month_id'   => $month->id,
            ],
            [
                'fee_type_id' => $feeType->id,
                'amount'      => 1500,
                'discount'    => 0,
            ]
        );

        $paymentData = [
            'school_id'      => $this->schoolId,
            'student_id'     => $student->id,
            'fee_id'         => $fee->id,
            'amount'         => 1500,
            'month_id'       => $month->id,
            'payment_date'   => now()->format('Y-m-d'),
            'payment_method' => 'cash',
            'date_type'      => 'gregorian',
            'cashbox_id'     => $this->cashbox->id,
        ];

        $payment = $this->accounting->recordPayment($paymentData);
        $this->assertBalance(101500, 'ثبت پرداخت شهریه');

        $this->accounting->updatePayment($payment, array_merge($paymentData, ['amount' => 1200]));
        $this->assertBalance(101200, 'ویرایش پرداخت شهریه');

        $this->accounting->deletePayment($payment);
        $this->assertBalance(100000, 'حذف پرداخت شهریه');
    }

    // -------------------------------------------------
    // ۴. تست معاش
    // -------------------------------------------------
    private function testSalary()
    {
        $this->info('--- ۴. تست معاش ---');
        $role = EmployeeRole::firstOrCreate(
            ['school_id' => $this->schoolId, 'name' => 'معلم'],
            []
        );

        $employeeCode = 'EMP-' . rand(1000, 9999);
        $employee = Employee::create([
            'school_id'        => $this->schoolId,
            'first_name'       => 'کارمند',
            'last_name'        => 'تست',
            'father_name'      => 'پدر',
            'national_id'      => '987654321',
            'employee_role_id' => $role->id,
            'status'           => 'active',
            'employee_code'    => $employeeCode,
        ]);

        $month = Month::firstOrCreate(
            ['school_id' => $this->schoolId, 'name' => 'حمل', 'number' => 1],
            []
        );

        $academicYear = AcademicYear::firstOrCreate(
            ['school_id' => $this->schoolId, 'name' => '۱۴۰۴'],
            ['start_date' => '2025-03-21', 'end_date' => '2026-03-20']
        );

        $salary = Salary::create([
            'school_id'        => $this->schoolId,
            'employee_id'      => $employee->id,
            'month_id'         => $month->id,
            'academic_year_id' => $academicYear->id,
            'base_salary'      => 8000,
            'total_amount'     => 8000,
            'paid_amount'      => 0,
            'status'           => 'due',
        ]);

        $paymentData = [
            'school_id'     => $this->schoolId,
            'salary_id'     => $salary->id,
            'employee_id'   => $employee->id,
            'amount'        => 8000,
            'payment_date'  => now()->format('Y-m-d'),
            'cashbox_id'    => $this->cashbox->id,
            'payment_method'=> 'cash',
        ];

        $payment = $this->accounting->recordSalaryPayment($paymentData);
        $this->assertBalance(92000, 'ثبت پرداخت معاش');

        $this->accounting->updateSalaryPayment($payment, array_merge($paymentData, ['amount' => 7000]));
        $this->assertBalance(93000, 'ویرایش پرداخت معاش');

        $this->accounting->deleteSalaryPayment($payment);
        $this->assertBalance(100000, 'حذف پرداخت معاش');

        // پاکسازی داده‌های تستی
        $salary->delete();
        $employee->forceDelete();
        $academicYear->delete();
        $month->delete();
        $role->delete();
    }

    // -------------------------------------------------
    // بررسی موجودی صندوق
    // -------------------------------------------------
    private function assertBalance($expected, string $label)
    {
        $this->cashbox->refresh();
        $current = $this->cashbox->current_balance;
        if (abs($current - $expected) < 0.01) {
            $this->info("✅ پس از {$label}: موجودی صندوق صحیح است ({$current}).");
        } else {
            $this->error("❌ پس از {$label}: موجودی صندوق {$current}، انتظار {$expected} بود!");
        }
    }
}
