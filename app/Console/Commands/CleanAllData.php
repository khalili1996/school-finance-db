<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanAllData extends Command
{
    protected $signature = 'db:clean-data';
    protected $description = 'حذف تمام داده‌های ثبت‌شده (بدون تغییر ساختار جداول)';

    public function handle()
    {
        if ($this->confirm('آیا از حذف تمام داده‌ها اطمینان دارید؟ این عملیات غیرقابل بازگشت است.')) {
            $this->info('در حال پاک‌سازی...');

            // غیرفعال کردن بررسی کلیدهای خارجی (برای truncate)
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');

            // ۱. جداول وابسته (تراکنش‌ها، لاگ‌ها و ...)
            \App\Models\CashboxTransaction::truncate();
            \App\Models\LedgerEntry::truncate();
            \App\Models\AuditLog::truncate();
            \App\Models\ActivityLog::truncate();

            // ۲. پرداخت‌ها (شهریه، معاش، اقساط و ...)
            \App\Models\Payment::truncate();
            \App\Models\SalaryPayment::truncate();
            \App\Models\LoanInstallment::truncate();
            \App\Models\Loan::truncate();
            \App\Models\LoanFundTransaction::truncate();
            \App\Models\EmployeeAdvance::truncate();

            // ۳. مصارف و درآمدها
            \App\Models\Expense::truncate();
            \App\Models\Income::truncate();
            \App\Models\IncomeReceipt::truncate();

            // ۴. شهریه و حقوق
            \App\Models\StudentFee::truncate();
            \App\Models\Salary::truncate();

            // ۵. دارایی‌ها
            \App\Models\Asset::truncate();
            \App\Models\AssetCategory::truncate();

            // ۶. دانش‌آموزان و اولیا
            \App\Models\Enrollment::truncate();
            \App\Models\Student::truncate();
            \App\Models\StudentGuardian::truncate();

            // ۷. کارمندان و نقش‌ها
            \App\Models\Employee::truncate();
            \App\Models\EmployeeRole::truncate();
            \App\Models\SalaryStructure::truncate();

            // ۸. صندوق‌ها (موجودی را هم صفر کن)
            \App\Models\Cashbox::query()->update(['current_balance' => 0]);

            // ۹. تنظیمات مدرسه (اختیاری – در صورت تمایل کامنت شود)
            \App\Models\Setting::truncate();

            // ۱۰. سال‌های تحصیلی، ترم‌ها، ماه‌ها و کلاس‌ها (معمولاً ثابت نگه داشته می‌شوند)
            // \App\Models\Month::truncate();
            // \App\Models\Term::truncate();
            // \App\Models\AcademicYear::truncate();

            // ۱۱. کاربران (بجز کاربر فعلی یا ادمین)
            // \App\Models\User::where('id', '!=', 1)->delete();

            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('✅ تمام داده‌ها با موفقیت پاک شدند.');
        } else {
            $this->info('عملیات لغو شد.');
        }

        return 0;
    }
}
