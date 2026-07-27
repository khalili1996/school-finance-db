<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryPayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'salary_id',
        'employee_id',
        'amount',
        'payment_date',
        'cashbox_id',
        'receipt_number',
        'payment_method',
        'notes',
        'academic_year_id',   // ★ اضافه شد
    ];

    public function salary()
    {
        return $this->belongsTo(Salary::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }

    protected static function booted()
    {
        static::deleting(function ($payment) {
            $transactions = \App\Models\CashboxTransaction::where('reference_type', self::class)
                ->where('reference_id', $payment->id)
                ->get();

            foreach ($transactions as $trx) {
                if ($trx->cashbox) {
                    $trx->cashbox->increment('current_balance', $trx->amount);
                }
                $trx->delete();
            }

            \App\Models\LedgerEntry::where('reference_type', self::class)
                ->where('reference_id', $payment->id)
                ->delete();
        });
    }
}
