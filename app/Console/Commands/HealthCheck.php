<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class HealthCheck extends Command
{
    protected $signature = 'project:health-check';
    protected $description = 'بررسی سلامت داده‌ها و یکپارچگی مالی (فقط خواندنی، بدون تغییر دیتابیس)';

    private int $errors = 0;
    private int $warnings = 0;

    public function handle()
    {
        $this->info('🔍 شروع بررسی سلامت داده‌ها...');
        $this->line('این فرمان فقط داده‌ها را می‌خواند و هیچ تغییری اعمال نمی‌کند.');
        $this->line('');

        $this->checkOrphanRecords();
        $this->checkNullAcademicYears();
        $this->checkFinancialConsistency();
        $this->checkCashboxBalances();

        $this->line('');
        if ($this->errors === 0 && $this->warnings === 0) {
            $this->info('✅ سیستم کاملاً سالم است. هیچ خطا یا هشداری یافت نشد.');
        } else {
            $this->error("❌ {$this->errors} خطا و {$this->warnings} هشدار یافت شد. لطفاً موارد بالا را بررسی کنید.");
        }

        return $this->errors > 0 ? 1 : 0;
    }

    private function checkOrphanRecords(): void
    {
        $this->info('--- ۱. بررسی رکوردهای بی‌صاحب (Orphan) ---');

        // شهریه بدون دانش‌آموز
        $orphanFees = DB::table('student_fees')
            ->leftJoin('students', 'student_fees.student_id', '=', 'students.id')
            ->whereNull('students.id')
            ->whereNull('student_fees.deleted_at')
            ->count();
        $this->assertZero($orphanFees, 'شهریه‌های بدون دانش‌آموز');

        // پرداخت بدون دانش‌آموز
        $orphanPayments = DB::table('payments')
            ->leftJoin('students', 'payments.student_id', '=', 'students.id')
            ->whereNull('students.id')
            ->whereNull('payments.deleted_at')
            ->count();
        $this->assertZero($orphanPayments, 'پرداخت‌های بدون دانش‌آموز');

        // پرداخت بدون شهریه (fee_id)
        $orphanPaymentsNoFee = DB::table('payments')
            ->leftJoin('student_fees', 'payments.fee_id', '=', 'student_fees.id')
            ->whereNull('student_fees.id')
            ->whereNull('payments.deleted_at')
            ->count();
        $this->assertZero($orphanPaymentsNoFee, 'پرداخت‌های بدون شهریه');

        // هزینه بدون دسته‌بندی
        $orphanExpenses = DB::table('expenses')
            ->leftJoin('expense_categories', 'expenses.expense_category_id', '=', 'expense_categories.id')
            ->whereNull('expense_categories.id')
            ->whereNull('expenses.deleted_at')
            ->count();
        $this->assertZero($orphanExpenses, 'هزینه‌های بدون دسته‌بندی');

        // پرداخت هزینه بدون expense
        $orphanExpPayments = DB::table('expense_payments')
            ->leftJoin('expenses', 'expense_payments.expense_id', '=', 'expenses.id')
            ->whereNull('expenses.id')
            ->whereNull('expense_payments.deleted_at')
            ->count();
        $this->assertZero($orphanExpPayments, 'پرداخت‌های هزینه بدون رکورد هزینه');

        // پرداخت معاش بدون salary
        $orphanSalPayments = DB::table('salary_payments')
            ->leftJoin('salaries', 'salary_payments.salary_id', '=', 'salaries.id')
            ->whereNull('salaries.id')
            ->whereNull('salary_payments.deleted_at')
            ->count();
        $this->assertZero($orphanSalPayments, 'پرداخت‌های معاش بدون رکورد معاش');

        // کارمند بدون نقش
        $employeesWithoutRole = DB::table('employees')
            ->leftJoin('employee_roles', 'employees.employee_role_id', '=', 'employee_roles.id')
            ->whereNull('employee_roles.id')
            ->whereNull('employees.deleted_at')
            ->count();
        $this->assertZero($employeesWithoutRole, 'کارمندان بدون نقش');
    }

    private function checkNullAcademicYears(): void
    {
        $this->info('--- ۲. بررسی رکوردهای فاقد سال تحصیلی ---');

        $tables = ['students', 'employees', 'student_guardians', 'student_fees', 'payments', 'incomes', 'expenses', 'salaries'];
        foreach ($tables as $table) {
            $count = DB::table($table)
                ->whereNull('academic_year_id')
                ->whereNull("{$table}.deleted_at")
                ->count();
            if ($count > 0) {
                $this->warn("⚠️ {$count} رکورد در جدول {$table} فاقد academic_year_id هستند.");
                $this->warnings++;
            }
        }
    }

    private function checkFinancialConsistency(): void
    {
        $this->info('--- ۳. بررسی یکپارچگی مالی ---');

        // دریافت تمام مدارس
        $schools = DB::table('schools')->pluck('id');

        foreach ($schools as $schoolId) {
            $years = DB::table('academic_years')->where('school_id', $schoolId)->pluck('id');

            foreach ($years as $yearId) {
                // مجموع شهریه‌های قابل پرداخت (amount - discount)
                $totalFee = DB::table('student_fees')
                    ->where('school_id', $schoolId)
                    ->where('academic_year_id', $yearId)
                    ->whereNull('deleted_at')
                    ->sum(DB::raw('amount - discount'));

                // مجموع پرداخت‌های شهریه
                $totalPayments = DB::table('payments')
                    ->where('school_id', $schoolId)
                    ->where('academic_year_id', $yearId)
                    ->whereNull('deleted_at')
                    ->sum('amount');

                // بدهی کل نباید منفی باشد
                $debt = $totalFee - $totalPayments;
                if ($debt < 0) {
                    $this->error("❌ مدرسه {$schoolId} سال {$yearId}: مجموع پرداختی‌ها از کل شهریه بیشتر است!");
                    $this->errors++;
                }

                // مجموع درآمدهای مستقل (income_receipts)
                $totalIncome = DB::table('income_receipts')
                    ->where('school_id', $schoolId)
                    ->whereExists(function ($query) use ($yearId) {
                        $query->select(DB::raw(1))
                              ->from('incomes')
                              ->whereColumn('incomes.id', 'income_receipts.income_id')
                              ->where('incomes.academic_year_id', $yearId)
                              ->whereNull('incomes.deleted_at');
                    })
                    ->whereNull('deleted_at')
                    ->sum('amount');

                // مجموع هزینه‌ها (expense_payments)
                $totalExpenses = DB::table('expense_payments')
                    ->where('school_id', $schoolId)
                    ->whereExists(function ($query) use ($yearId) {
                        $query->select(DB::raw(1))
                              ->from('expenses')
                              ->whereColumn('expenses.id', 'expense_payments.expense_id')
                              ->where('expenses.academic_year_id', $yearId)
                              ->whereNull('expenses.deleted_at');
                    })
                    ->whereNull('deleted_at')
                    ->sum('amount');

                // مجموع پرداخت معاش (salary_payments)
                $totalSalaries = DB::table('salary_payments')
                    ->where('school_id', $schoolId)
                    ->whereExists(function ($query) use ($yearId) {
                        $query->select(DB::raw(1))
                              ->from('salaries')
                              ->whereColumn('salaries.id', 'salary_payments.salary_id')
                              ->where('salaries.academic_year_id', $yearId)
                              ->whereNull('salaries.deleted_at');
                    })
                    ->whereNull('deleted_at')
                    ->sum('amount');

                // خالص: (پرداختی شهریه + درآمدها) - (هزینه‌ها + معاش‌ها)
                $netFlow = ($totalPayments + $totalIncome) - ($totalExpenses + $totalSalaries);

                // مقایسه با تغییرات صندوق (در صورت امکان)
                $cashboxIds = DB::table('cashboxes')->where('school_id', $schoolId)->pluck('id');
                $cashboxChange = 0;
                foreach ($cashboxIds as $cid) {
                    $deposits = DB::table('cashbox_transactions')
                        ->where('cashbox_id', $cid)
                        ->whereIn('type', ['payment', 'income', 'sale', 'loan_return', 'initial'])
                        ->sum('amount');
                    $withdrawals = DB::table('cashbox_transactions')
                        ->where('cashbox_id', $cid)
                        ->whereIn('type', ['expense', 'salary', 'loan', 'withdraw'])
                        ->sum('amount');
                    $cashboxChange += ($deposits - $withdrawals);
                }

                // مقایسه تقریبی
                if (abs($netFlow - $cashboxChange) > 1) {
                    $this->warn("⚠️ مدرسه {$schoolId} سال {$yearId}: اختلاف در محاسبات صندوق ({$netFlow} در مقابل {$cashboxChange}) - ممکن است به دلیل تراکنش‌های خارج از سال باشد.");
                    $this->warnings++;
                }
            }
        }
    }

    private function checkCashboxBalances(): void
{
    $this->info('--- ۴. بررسی موجودی صندوق‌ها ---');

    $cashboxes = DB::table('cashboxes')->whereNull('deleted_at')->get();
    foreach ($cashboxes as $box) {
        $calculated = DB::table('cashbox_transactions')
            ->where('cashbox_id', $box->id)
            ->sum('amount');

        if (abs($calculated - $box->current_balance) > 0.01) {
            $this->error("❌ صندوق {$box->name} (id={$box->id}): موجودی ثبت‌شده {$box->current_balance}، اما مجموع تراکنش‌ها {$calculated} است.");
            $this->errors++;
        }
    }
}

    private function assertZero(int $count, string $label): void
    {
        if ($count > 0) {
            $this->error("❌ {$count} {$label} یافت شد.");
            $this->errors++;
        } else {
            $this->line("✅ {$label}: صفر");
        }
    }
}
