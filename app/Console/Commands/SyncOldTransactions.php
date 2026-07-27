<?php

namespace App\Console\Commands;

use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use App\Models\IncomeReceipt;
use App\Models\ExpensePayment;
use Illuminate\Console\Command;

class SyncOldTransactions extends Command
{
    protected $signature = 'sync:old-transactions {cashbox_id : ID صندوقی که تراکنش‌ها در آن ثبت شوند}';
    protected $description = 'همگام‌سازی دریافت‌ها و پرداخت‌های قدیمی با صندوق';

    public function handle(): void
    {
        $cashboxId = $this->argument('cashbox_id');
        $cashbox = Cashbox::findOrFail($cashboxId);
        $this->info("استفاده از صندوق: {$cashbox->name}");

        // IncomeReceiptهایی که هنوز تراکنش صندوق ندارند
        $incomeReceipts = IncomeReceipt::whereDoesntHave('cashboxTransactions')->get();
        $countIncome = 0;
        foreach ($incomeReceipts as $receipt) {
            CashboxTransaction::create([
                'school_id'        => $receipt->school_id,
                'cashbox_id'       => $cashbox->id,
                'type'             => 'deposit',
                'amount'           => $receipt->amount,
                'transaction_date' => $receipt->receipt_date,
                'reference_type'   => IncomeReceipt::class,
                'reference_id'     => $receipt->id,
                'description'      => 'دریافت عواید (همگام‌سازی)',
            ]);
            $cashbox->increment('current_balance', $receipt->amount);
            $countIncome++;
        }

        $this->info("{$countIncome} دریافت عواید همگام‌سازی شد.");

        // ExpensePaymentهایی که هنوز تراکنش صندوق ندارند
        $expensePayments = ExpensePayment::whereDoesntHave('cashboxTransactions')->get();
        $countExpense = 0;
        foreach ($expensePayments as $payment) {
            CashboxTransaction::create([
                'school_id'        => $payment->school_id,
                'cashbox_id'       => $cashbox->id,
                'type'             => 'withdrawal',
                'amount'           => $payment->amount,
                'transaction_date' => $payment->payment_date,
                'reference_type'   => ExpensePayment::class,
                'reference_id'     => $payment->id,
                'description'      => 'پرداخت مصرف (همگام‌سازی)',
            ]);
            $cashbox->decrement('current_balance', $payment->amount);
            $countExpense++;
        }

        $this->info("{$countExpense} پرداخت مصرف همگام‌سازی شد.");
        $this->info('موجودی صندوق به‌روز شد: ' . number_format($cashbox->fresh()->current_balance) . ' ؋');
    }
}
