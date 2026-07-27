<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Income;
use App\Models\Payment;
use App\Models\SalaryPayment;
use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use App\Models\LedgerEntry;
use Illuminate\Support\Facades\DB;

class AccountingService
{
    // =================================================================
    //  EXPENSE (مصارف)
    // =================================================================

    public function recordExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {
            $expense = Expense::create($data);

            if (!empty($data['paid_amount']) && $data['paid_amount'] > 0) {
                $this->createCashboxTransaction(
                    $data['school_id'],
                    $data['cashbox_id'],
                    'withdrawal',
                    $data['paid_amount'],
                    $data['expense_date'],
                    Expense::class,
                    $expense->id,
                    'پرداخت مصرف: ' . ($data['title'] ?? '')
                );

                Cashbox::where('id', $data['cashbox_id'])->decrement('current_balance', $data['paid_amount']);

                $this->createLedgerEntry(
                    $data['school_id'],
                    $data['expense_date'],
                    'پرداخت مصرف: ' . ($data['title'] ?? ''),
                    0,
                    $data['paid_amount'],
                    Expense::class,
                    $expense->id
                );
            }

            return $expense;
        });
    }

    public function updateExpense(Expense $expense, array $data): Expense
    {
        return DB::transaction(function () use ($expense, $data) {
            $oldTransactions = CashboxTransaction::where('reference_type', Expense::class)
                ->where('reference_id', $expense->id)
                ->get();

            foreach ($oldTransactions as $trx) {
                if ($trx->cashbox) {
                    if ($trx->type === 'withdrawal') {
                        $trx->cashbox->increment('current_balance', $trx->amount);
                    } elseif ($trx->type === 'deposit') {
                        $trx->cashbox->decrement('current_balance', $trx->amount);
                    }
                }
            }

            CashboxTransaction::where('reference_type', Expense::class)
                ->where('reference_id', $expense->id)->delete();
            LedgerEntry::where('reference_type', Expense::class)
                ->where('reference_id', $expense->id)->delete();

            $expense->update($data);

            if (!empty($data['paid_amount']) && $data['paid_amount'] > 0) {
                $this->createCashboxTransaction(
                    $data['school_id'],
                    $data['cashbox_id'],
                    'withdrawal',
                    $data['paid_amount'],
                    $data['expense_date'],
                    Expense::class,
                    $expense->id,
                    'پرداخت مصرف: ' . ($data['title'] ?? '')
                );

                Cashbox::where('id', $data['cashbox_id'])->decrement('current_balance', $data['paid_amount']);

                $this->createLedgerEntry(
                    $data['school_id'],
                    $data['expense_date'],
                    'پرداخت مصرف: ' . ($data['title'] ?? ''),
                    0,
                    $data['paid_amount'],
                    Expense::class,
                    $expense->id
                );
            }

            return $expense;
        });
    }

    public function deleteExpense(Expense $expense): void
    {
        DB::transaction(function () use ($expense) {
            $expense->delete();
        });
    }

    // =================================================================
    //  INCOME (درآمدها)
    // =================================================================

    public function recordIncome(array $data): Income
    {
        return DB::transaction(function () use ($data) {
            $income = Income::create($data);

            if (!empty($data['received_amount']) && $data['received_amount'] > 0) {
                $this->createCashboxTransaction(
                    $data['school_id'],
                    $data['cashbox_id'],
                    'deposit',
                    $data['received_amount'],
                    $data['income_date'],
                    Income::class,
                    $income->id,
                    'دریافت درآمد: ' . ($data['title'] ?? '')
                );

                Cashbox::where('id', $data['cashbox_id'])->increment('current_balance', $data['received_amount']);

                $this->createLedgerEntry(
                    $data['school_id'],
                    $data['income_date'],
                    'دریافت درآمد: ' . ($data['title'] ?? ''),
                    $data['received_amount'],
                    0,
                    Income::class,
                    $income->id
                );
            }

            return $income;
        });
    }

    public function updateIncome(Income $income, array $data): Income
    {
        return DB::transaction(function () use ($income, $data) {
            $oldTransactions = CashboxTransaction::where('reference_type', Income::class)
                ->where('reference_id', $income->id)
                ->get();

            foreach ($oldTransactions as $trx) {
                if ($trx->cashbox) {
                    if ($trx->type === 'deposit') {
                        $trx->cashbox->decrement('current_balance', $trx->amount);
                    } elseif ($trx->type === 'withdrawal') {
                        $trx->cashbox->increment('current_balance', $trx->amount);
                    }
                }
            }

            CashboxTransaction::where('reference_type', Income::class)
                ->where('reference_id', $income->id)->delete();
            LedgerEntry::where('reference_type', Income::class)
                ->where('reference_id', $income->id)->delete();

            $income->update($data);

            if (!empty($data['received_amount']) && $data['received_amount'] > 0) {
                $this->createCashboxTransaction(
                    $data['school_id'],
                    $data['cashbox_id'],
                    'deposit',
                    $data['received_amount'],
                    $data['income_date'],
                    Income::class,
                    $income->id,
                    'دریافت درآمد: ' . ($data['title'] ?? '')
                );

                Cashbox::where('id', $data['cashbox_id'])->increment('current_balance', $data['received_amount']);

                $this->createLedgerEntry(
                    $data['school_id'],
                    $data['income_date'],
                    'دریافت درآمد: ' . ($data['title'] ?? ''),
                    $data['received_amount'],
                    0,
                    Income::class,
                    $income->id
                );
            }

            return $income;
        });
    }

    public function deleteIncome(Income $income): void
    {
        DB::transaction(function () use ($income) {
            $income->delete();
        });
    }

    // =================================================================
    //  PAYMENT (پرداخت شهریه)
    // =================================================================

    public function recordPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {
            $payment = Payment::create($data);

            $this->createCashboxTransaction(
                $data['school_id'],
                $data['cashbox_id'],
                'deposit',
                $data['amount'],
                $data['payment_date'],
                Payment::class,
                $payment->id,
                'دریافت شهریه - دانش‌آموز #' . ($data['student_id'] ?? '')
            );

            Cashbox::where('id', $data['cashbox_id'])->increment('current_balance', $data['amount']);

            $this->createLedgerEntry(
                $data['school_id'],
                $data['payment_date'],
                'دریافت شهریه - دانش‌آموز #' . ($data['student_id'] ?? ''),
                $data['amount'],
                0,
                Payment::class,
                $payment->id
            );

            return $payment;
        });
    }

    public function updatePayment(Payment $payment, array $data): Payment
    {
        return DB::transaction(function () use ($payment, $data) {
            $oldTransactions = CashboxTransaction::where('reference_type', Payment::class)
                ->where('reference_id', $payment->id)
                ->get();
            foreach ($oldTransactions as $trx) {
                if ($trx->cashbox) {
                    $trx->cashbox->decrement('current_balance', $trx->amount);
                }
            }

            CashboxTransaction::where('reference_type', Payment::class)
                ->where('reference_id', $payment->id)->delete();
            LedgerEntry::where('reference_type', Payment::class)
                ->where('reference_id', $payment->id)->delete();

            $payment->update($data);

            if ($data['amount'] > 0) {
                $this->createCashboxTransaction(
                    $data['school_id'],
                    $data['cashbox_id'],
                    'deposit',
                    $data['amount'],
                    $data['payment_date'],
                    Payment::class,
                    $payment->id,
                    'دریافت شهریه (ویرایش)'
                );
                Cashbox::where('id', $data['cashbox_id'])->increment('current_balance', $data['amount']);

                $this->createLedgerEntry(
                    $data['school_id'],
                    $data['payment_date'],
                    'دریافت شهریه (ویرایش)',
                    $data['amount'],
                    0,
                    Payment::class,
                    $payment->id
                );
            }

            return $payment;
        });
    }

    public function deletePayment(Payment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $transactions = CashboxTransaction::where('reference_type', Payment::class)
                ->where('reference_id', $payment->id)
                ->get();
            foreach ($transactions as $trx) {
                if ($trx->cashbox) {
                    $trx->cashbox->decrement('current_balance', $trx->amount);
                }
            }

            CashboxTransaction::where('reference_type', Payment::class)
                ->where('reference_id', $payment->id)->delete();
            LedgerEntry::where('reference_type', Payment::class)
                ->where('reference_id', $payment->id)->delete();

            $payment->delete();
        });
    }

    // =================================================================
    //  SALARY PAYMENT (پرداخت معاش)
    // =================================================================

    /**
     * ثبت پرداخت معاش (تراکنش مالی کامل)
     */
    public function recordSalaryPayment(array $data): SalaryPayment
    {
        return DB::transaction(function () use ($data) {
            $payment = SalaryPayment::create($data);

            $this->createCashboxTransaction(
                $data['school_id'],
                $data['cashbox_id'],
                'withdrawal',
                $data['amount'],
                $data['payment_date'],
                SalaryPayment::class,
                $payment->id,
                'پرداخت معاش - ' . ($data['employee_id'] ?? '')
            );

            Cashbox::where('id', $data['cashbox_id'])->decrement('current_balance', $data['amount']);

            $this->createLedgerEntry(
                $data['school_id'],
                $data['payment_date'],
                'پرداخت معاش - ' . ($data['employee_id'] ?? ''),
                0,
                $data['amount'],
                SalaryPayment::class,
                $payment->id
            );

            return $payment;
        });
    }

    /**
     * ویرایش پرداخت معاش (برگشت مبلغ قبلی + ثبت جدید)
     */
    public function updateSalaryPayment(SalaryPayment $payment, array $data): SalaryPayment
    {
        return DB::transaction(function () use ($payment, $data) {
            $oldTransactions = CashboxTransaction::where('reference_type', SalaryPayment::class)
                ->where('reference_id', $payment->id)
                ->get();
            foreach ($oldTransactions as $trx) {
                if ($trx->cashbox) {
                    if ($trx->type === 'withdrawal') {
                        $trx->cashbox->increment('current_balance', $trx->amount);
                    } elseif ($trx->type === 'deposit') {
                        $trx->cashbox->decrement('current_balance', $trx->amount);
                    }
                }
            }

            CashboxTransaction::where('reference_type', SalaryPayment::class)
                ->where('reference_id', $payment->id)->delete();
            LedgerEntry::where('reference_type', SalaryPayment::class)
                ->where('reference_id', $payment->id)->delete();

            $payment->update($data);

            if ($data['amount'] > 0) {
                $this->createCashboxTransaction(
                    $data['school_id'],
                    $data['cashbox_id'],
                    'withdrawal',
                    $data['amount'],
                    $data['payment_date'],
                    SalaryPayment::class,
                    $payment->id,
                    'پرداخت معاش (ویرایش)'
                );

                Cashbox::where('id', $data['cashbox_id'])->decrement('current_balance', $data['amount']);

                $this->createLedgerEntry(
                    $data['school_id'],
                    $data['payment_date'],
                    'پرداخت معاش (ویرایش)',
                    0,
                    $data['amount'],
                    SalaryPayment::class,
                    $payment->id
                );
            }

            return $payment;
        });
    }

    /**
     * حذف پرداخت معاش (برگشت موجودی و حذف تراکنش‌ها)
     */
    public function deleteSalaryPayment(SalaryPayment $payment): void
    {
        DB::transaction(function () use ($payment) {
            $transactions = CashboxTransaction::where('reference_type', SalaryPayment::class)
                ->where('reference_id', $payment->id)
                ->get();
            foreach ($transactions as $trx) {
                if ($trx->cashbox) {
                    $trx->cashbox->increment('current_balance', $trx->amount);
                }
            }

            CashboxTransaction::where('reference_type', SalaryPayment::class)
                ->where('reference_id', $payment->id)->delete();
            LedgerEntry::where('reference_type', SalaryPayment::class)
                ->where('reference_id', $payment->id)->delete();

            $payment->delete();
        });
    }

    // =================================================================
    //  متدهای کمکی خصوصی
    // =================================================================

    private function createCashboxTransaction(
        int $schoolId,
        int $cashboxId,
        string $type,
        float $amount,
        string $date,
        string $referenceType,
        int $referenceId,
        string $description
    ): CashboxTransaction {
        return CashboxTransaction::create([
            'school_id'        => $schoolId,
            'cashbox_id'       => $cashboxId,
            'type'             => $type,
            'amount'           => $amount,
            'transaction_date' => $date,
            'reference_type'   => $referenceType,
            'reference_id'     => $referenceId,
            'description'      => $description,
        ]);
    }

    private function createLedgerEntry(
        int $schoolId,
        string $date,
        string $description,
        float $debit,
        float $credit,
        string $referenceType,
        int $referenceId
    ): LedgerEntry {
        return LedgerEntry::create([
            'school_id'      => $schoolId,
            'entry_date'     => $date,
            'description'    => $description,
            'debit'          => $debit,
            'credit'         => $credit,
            'reference_type' => $referenceType,
            'reference_id'   => $referenceId,
        ]);
    }
}
