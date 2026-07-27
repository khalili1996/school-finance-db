<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
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

class TestIntegration extends Command
{
    protected $signature = 'project:test-integration';
    protected $description = 'تست یکپارچگی کامل مالی (بدون تغییر در دیتابیس)';

    private AccountingService $accounting;
    private int $schoolId;
    private $cashbox;
    private $school;

    public function __construct()
    {
        parent::__construct();
        $this->accounting = app(AccountingService::class);
    }

    public function handle()
    {
        $this->info('🚀 شروع تست یکپارچگی مالی (ایمن - بدون تغییر داده‌ها)...');

        DB::beginTransaction();
        try {
            // ۱. ساخت مدرسهٔ تست
            $this->school = School::firstOrCreate(
                ['code' => 'INTEGRATION'],
                ['name' => 'مدرسه تست یکپارچگی']
            );
            $this->schoolId = $this->school->id;

            // ۲. ساخت صندوق تست با موجودی اولیه ۱۰۰,۰۰۰
            $this->cashbox = Cashbox::firstOrCreate(
                ['school_id' => $this->schoolId, 'name' => 'صندوق یکپارچگی'],
                ['current_balance' => 100000, 'is_active' => true]
            );
            $this->cashbox->update(['current_balance' => 100000]);
            $initialBalance = $this->cashbox->current_balance;

            // ۳. پیش‌نیازها (ماه، نوع شهریه، دسته‌بندی‌ها، نقش)
            $month = Month::firstOrCreate(
                ['school_id' => $this->schoolId, 'name' => 'حمل', 'number' => 1],
                []
            );
            $feeType = FeeType::firstOrCreate(
                ['school_id' => $this->schoolId, 'name' => 'شهریه ماهانه'],
                ['is_active' => true]
            );
            $expenseCategory = ExpenseCategory::firstOrCreate(
                ['school_id' => $this->schoolId, 'name' => 'هزینه جاری'],
                []
            );
            $incomeCategory = IncomeCategory::firstOrCreate(
                ['school_id' => $this->schoolId, 'name' => 'درآمد متفرقه'],
                []
            );
            $employeeRole = EmployeeRole::firstOrCreate(
                ['school_id' => $this->schoolId, 'name' => 'معلم'],
                []
            );

            // ۴. دانش‌آموز تست
            $student = Student::firstOrCreate(
                [
                    'school_id'   => $this->schoolId,
                    'national_id' => 'INTEG-STU-001',
                ],
                [
                    'first_name'   => 'تست',
                    'last_name'    => 'یکپارچگی',
                    'father_name'  => 'پدر',
                    'gender'       => 'male',
                    'status'       => 'present',
                    'class'        => 'چهارم',
                    'student_code' => 'INT001',
                ]
            );

            // ۵. کارمند تست
            $employee = Employee::firstOrCreate(
                [
                    'school_id'   => $this->schoolId,
                    'national_id' => 'INTEG-EMP-001',
                ],
                [
                    'first_name'       => 'کارمند',
                    'last_name'        => 'تست',
                    'father_name'      => 'پدر',
                    'employee_role_id' => $employeeRole->id,
                    'status'           => 'active',
                    'employee_code'    => 'EMP-INT001',
                    'hire_date'        => now()->format('Y-m-d'),
                ]
            );

            $academicYear = AcademicYear::firstOrCreate(
                ['school_id' => $this->schoolId, 'name' => '۱۴۰۴'],
                ['start_date' => '2025-03-21', 'end_date' => '2026-03-20']
            );

            // ۶. تست شهریه (ثبت + پرداخت)
            $this->info('--- ۱. تست شهریه ---');
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
            $this->accounting->recordPayment($paymentData);
            $this->assertBalance($initialBalance + 1500, 'پرداخت شهریه');

            // ۷. تست درآمد (ثبت درآمد مستقل)
            $this->info('--- ۲. تست درآمد ---');
            $incomeData = [
                'school_id'         => $this->schoolId,
                'income_category_id'=> $incomeCategory->id,
                'title'             => 'تست درآمد یکپارچگی',
                'total_amount'      => 2000,
                'received_amount'   => 2000,
                'income_date'       => now()->format('Y-m-d'),
                'status'            => 'received',
                'cashbox_id'        => $this->cashbox->id,
            ];
            $this->accounting->recordIncome($incomeData);
            $this->assertBalance($initialBalance + 1500 + 2000, 'ثبت درآمد');

            // ۸. تست مصرف (هزینه)
            $this->info('--- ۳. تست مصرف ---');
            $expenseData = [
                'school_id'          => $this->schoolId,
                'expense_category_id'=> $expenseCategory->id,
                'title'              => 'تست مصرف یکپارچگی',
                'total_amount'       => 500,
                'paid_amount'        => 500,
                'expense_date'       => now()->format('Y-m-d'),
                'status'             => 'paid',
                'cashbox_id'         => $this->cashbox->id,
            ];
            $this->accounting->recordExpense($expenseData);
            $this->assertBalance($initialBalance + 1500 + 2000 - 500, 'پرداخت مصرف');

            // ۹. تست معاش (ثبت معاش + پرداخت)
            $this->info('--- ۴. تست معاش ---');
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

            $salaryPaymentData = [
                'school_id'     => $this->schoolId,
                'salary_id'     => $salary->id,
                'employee_id'   => $employee->id,
                'amount'        => 8000,
                'payment_date'  => now()->format('Y-m-d'),
                'cashbox_id'    => $this->cashbox->id,
                'payment_method'=> 'cash',
            ];
            $this->accounting->recordSalaryPayment($salaryPaymentData);
            $this->assertBalance($initialBalance + 1500 + 2000 - 500 - 8000, 'پرداخت معاش');

            // ۱۰. بررسی نهایی
            $expectedFinal = $initialBalance + 1500 + 2000 - 500 - 8000;
            $this->cashbox->refresh();
            if (abs($this->cashbox->current_balance - $expectedFinal) < 0.01) {
                $this->info("✅ تست یکپارچگی کامل موفقیت‌آمیز بود.");
                $this->info('🎉 سیستم مالی کاملاً هماهنگ و یکپارچه است.');
            } else {
                $this->error("❌ خطا در جمع‌بندی نهایی: موجودی {$this->cashbox->current_balance} ولی انتظار {$expectedFinal}");
            }

            // ۱۱. برگشت تمام تغییرات – هیچ داده‌ای در دیتابیس باقی نمی‌ماند
            DB::rollBack();
            $this->info('♻️ تمام داده‌های تست از دیتابیس پاک شدند (Rollback).');

            return 0;
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ تست یکپارچگی با خطا مواجه شد: ' . $e->getMessage());
            return 1;
        }
    }

    private function assertBalance($expected, string $label)
    {
        $this->cashbox->refresh();
        $current = $this->cashbox->current_balance;
        if (abs($current - $expected) < 0.01) {
            $this->info("✅ {$label}: موجودی صندوق صحیح است ({$current}).");
        } else {
            throw new \Exception("خطا در {$label}: موجودی صندوق {$current}، انتظار {$expected}");
        }
    }
}
