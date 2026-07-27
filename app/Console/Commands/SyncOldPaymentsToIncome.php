<?php

namespace App\Console\Commands;

use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\IncomeReceipt;
use App\Models\Payment;
use Illuminate\Console\Command;

class SyncOldPaymentsToIncome extends Command
{
    protected $signature = 'sync:old-payments-to-income';
    protected $description = 'همگام‌سازی پرداخت‌های قدیمی با عواید (فقط یک‌بار اجرا شود)';

    public function handle(): void
    {
        $payments = Payment::all();
        $category = IncomeCategory::firstOrCreate(
            ['name' => 'شهریه'],
            ['is_active' => true]
        );

        $count = 0;
        foreach ($payments as $payment) {
            if (IncomeReceipt::where('payment_id', $payment->id)->exists()) {
                continue;
            }

            $student = $payment->student;
            $income = Income::create([
                'school_id'         => $payment->school_id,
                'income_category_id'=> $category->id,
                'title'             => 'شهریه - ' . ($student ? $student->first_name . ' ' . $student->last_name : 'دانش‌آموز'),
                'total_amount'      => $payment->amount,
                'received_amount'   => $payment->amount,
                'income_date'       => $payment->payment_date,
                'source'            => 'پرداخت شهریه',
                'status'            => 'received',
            ]);

            IncomeReceipt::create([
                'school_id'      => $payment->school_id,
                'income_id'      => $income->id,
                'payment_id'     => $payment->id,
                'amount'         => $payment->amount,
                'receipt_date'   => $payment->payment_date,
                'payment_method' => $payment->payment_method,
                'receipt_number' => $payment->receipt_number,
            ]);

            $count++;
        }

        $this->info("{$count} پرداخت قدیمی با موفقیت به عواید اضافه شد.");
    }
}
