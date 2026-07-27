<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\{Student, Payment, Cashbox, CashboxTransaction, LedgerEntry,
    Employee, Salary, Expense, Income,
    Loan, LoanInstallment, LoanFundTransaction,
    Asset, AssetCategory};

class CheckDatabaseLinks extends Command
{
    protected $signature = 'db:check-links';
    protected $description = 'بررسی سلامت لینک‌های دیتابیس';

    public function handle()
    {
        $this->info('🔍 بررسی اتصالات دیتابیس...');

        // 1. دانش‌آموز ← شهریه ← پرداخت ← صندوق
        $this->checkRelation('Student -> StudentFee', \App\Models\StudentFee::class, 'student');
        $this->checkRelation('Payment -> StudentFee', Payment::class, 'studentFee');
        $this->checkRelation('Payment -> CashboxTransaction', CashboxTransaction::class, null, function() {
            return CashboxTransaction::where('reference_type', Payment::class)->exists();
        });

        // 2. کارمند ← معاش ← پرداخت معاش ← صندوق
        $this->checkRelation('Salary -> Employee', Salary::class, 'employee');
        $this->checkRelation('SalaryPayment -> Salary', \App\Models\SalaryPayment::class, 'salary');
        $this->checkRelation('SalaryPayment -> CashboxTransaction', CashboxTransaction::class, null, function() {
            return CashboxTransaction::where('reference_type', \App\Models\SalaryPayment::class)->exists();
        });

        // 3. مصارف ← صندوق
        $this->checkRelation('Expense -> CashboxTransaction', CashboxTransaction::class, null, function() {
            return CashboxTransaction::where('reference_type', Expense::class)->exists();
        });

        // 4. درآمد ← صندوق
        $this->checkRelation('Income -> CashboxTransaction', CashboxTransaction::class, null, function() {
            return CashboxTransaction::where('reference_type', Income::class)->exists();
        });

        // 5. دفتر کل
        $this->checkRelation('LedgerEntry exists', LedgerEntry::class, null, function() {
            return LedgerEntry::exists();
        });

        // 6. قرض‌الحسنه
        $this->checkRelation('Loan -> Installments', LoanInstallment::class, 'loan');
        $this->checkRelation('Loan -> FundTransaction', LoanFundTransaction::class, null, function() {
            return LoanFundTransaction::where('reference_type', Loan::class)->exists();
        });
        $this->checkRelation('Installment -> FundTransaction', LoanFundTransaction::class, null, function() {
            return LoanFundTransaction::where('reference_type', LoanInstallment::class)->exists();
        });

        // 7. تجهیزات
        $this->checkRelation('Asset -> Category', Asset::class, 'category');

        $this->info('✅ بررسی پایان یافت.');
    }

    private function checkRelation($label, $model, $relation = null, $callback = null)
    {
        $this->output->write("🔹 {$label} ... ");
        try {
            if ($callback) {
                $result = $callback();
                $status = $result ? '✅ موجود' : '❌ خالی';
            } elseif ($relation) {
                $instance = app($model)->first();
                if (!$instance) {
                    $status = '⚠️ مدل خالی (رکوردی وجود ندارد)';
                } else {
                    $related = $instance->{$relation}()->exists();
                    $status = $related ? '✅ مرتبط' : '❌ بدون رابطه';
                }
            } else {
                $status = '⚠️ چک دستی';
            }
        } catch (\Exception $e) {
            $status = "❌ خطا: {$e->getMessage()}";
        }
        $this->line($status);
    }
}
