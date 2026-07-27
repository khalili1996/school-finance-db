<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\School;
use App\Models\Student;
use App\Models\Employee;
use App\Models\StudentGuardian;
use App\Models\StudentFee;
use App\Models\Payment;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Salary;
use App\Models\Cashbox;
use App\Models\AcademicYear;

class CleanupProject extends Command
{
    protected $signature = 'project:cleanup';
    protected $description = 'پاک‌سازی داده‌های تستی و معیوب (با تأیید)';

    public function handle()
    {
        $this->info('🔍 جستجوی داده‌های تستی و معیوب...');

        // ۱. یافتن مدرسه‌های تستی (با code = TEST یا نام شامل "تست")
        $testSchools = School::where('code', 'TEST')
            ->orWhere('name', 'like', '%تست%')
            ->get();

        if ($testSchools->isNotEmpty()) {
            $this->warn('مدارس تستی یافت شده:');
            foreach ($testSchools as $s) {
                $this->line(" - [{$s->id}] {$s->name} (code: {$s->code})");
            }
            if ($this->confirm('آیا این مدارس و تمام داده‌های مرتبط حذف شوند؟', true)) {
                foreach ($testSchools as $school) {
                    $this->deleteSchoolData($school);
                    $school->delete();
                    $this->info("✅ مدرسه {$school->name} و داده‌هایش حذف شد.");
                }
            }
        } else {
            $this->line('✅ مدرسه تستی یافت نشد.');
        }

        // ۲. رکوردهای فاقد سال تحصیلی
        $this->info('--- رکوردهای فاقد academic_year_id ---');
        $this->fixNullAcademicYear('student_guardians');
        $this->fixNullAcademicYear('student_fees');
        $this->fixNullAcademicYear('incomes');
        $this->fixNullAcademicYear('expenses');

        // ۳. پرداخت بی‌صاحب (بدون شهریه)
        $this->info('--- پرداخت بی‌صاحب ---');
        $orphanPayment = Payment::whereDoesntHave('studentFee')->first();
        if ($orphanPayment) {
            $this->warn("پرداخت بی‌صاحب با id={$orphanPayment->id} مبلغ {$orphanPayment->amount}");
            if ($this->confirm('حذف شود؟', true)) {
                $orphanPayment->delete();
                $this->info('✅ حذف شد.');
            }
        } else {
            $this->line('✅ پرداخت بی‌صاحب یافت نشد.');
        }

        // ۴. صندوق‌های تستی (نام شامل "تست")
        $this->info('--- صندوق‌های تستی ---');
        $testCashboxes = Cashbox::where('name', 'like', '%تست%')->get();
        if ($testCashboxes->isNotEmpty()) {
            $this->warn('صندوق‌های تست:');
            foreach ($testCashboxes as $c) {
                $this->line(" - [{$c->id}] {$c->name}");
            }
            if ($this->confirm('حذف شوند؟', true)) {
                foreach ($testCashboxes as $c) {
                    $c->delete();
                }
                $this->info('✅ حذف شدند.');
            }
        } else {
            $this->line('✅ صندوق تستی یافت نشد.');
        }

        // ۵. همگام‌سازی موجودی صندوق‌های واقعی (اختیاری)
        $this->info('--- همگام‌سازی موجودی صندوق‌ها ---');
        $realCashboxes = Cashbox::where('name', 'not like', '%تست%')->get();
        foreach ($realCashboxes as $box) {
            $calculated = DB::table('cashbox_transactions')
                ->where('cashbox_id', $box->id)
                ->sum('amount');
            if (abs($calculated - $box->current_balance) > 0.01) {
                $this->warn("صندوق [{$box->id}] {$box->name}: موجودی فعلی {$box->current_balance}، مجموع تراکنش‌ها {$calculated}");
                if ($this->confirm("موجودی این صندوق به {$calculated} تغییر کند؟", false)) {
                    $box->update(['current_balance' => $calculated]);
                    $this->info('✅ به‌روز شد.');
                }
            }
        }

        $this->info('🎉 پاک‌سازی پایان یافت. اکنون می‌توانید health-check را اجرا کنید.');
    }

    private function fixNullAcademicYear($table)
    {
        $count = DB::table($table)->whereNull('academic_year_id')->whereNull('deleted_at')->count();
        if ($count > 0) {
            $this->warn("{$count} رکورد در {$table} فاقد سال تحصیلی.");
            $yearId = $this->ask('سال تحصیلی پیش‌فرض برای این رکوردها (شناسه عددی):', '1');
            if (is_numeric($yearId)) {
                DB::table($table)->whereNull('academic_year_id')->whereNull('deleted_at')
                    ->update(['academic_year_id' => (int)$yearId]);
                $this->info("✅ به سال {$yearId} منتقل شدند.");
            }
        } else {
            $this->line("✅ {$table}: صفر");
        }
    }

    private function deleteSchoolData($school)
    {
        // حذف تمام داده‌های وابسته به این مدرسه
        $sid = $school->id;
        Payment::where('school_id', $sid)->delete();
        StudentFee::where('school_id', $sid)->delete();
        StudentGuardian::where('school_id', $sid)->delete();
        Student::where('school_id', $sid)->delete();
        Employee::where('school_id', $sid)->delete();
        Income::where('school_id', $sid)->delete();
        Expense::where('school_id', $sid)->delete();
        Salary::where('school_id', $sid)->delete();
        Cashbox::where('school_id', $sid)->delete();
        AcademicYear::where('school_id', $sid)->delete();
        // سایر جداول در صورت نیاز...
        DB::table('cashbox_transactions')->where('school_id', $sid)->delete();
        DB::table('cashbox_transfers')->where('school_id', $sid)->delete();
    }
}
