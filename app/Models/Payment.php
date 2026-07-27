<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'student_id',
        'fee_id',
        'term_id',
        'month_id',
        'amount',
        'payment_date',
        'date_type',          // جدید
        'receipt_number',
        'payment_method',
        'notes',
        'academic_year_id'
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function studentFee()
    {
        return $this->belongsTo(StudentFee::class, 'fee_id');
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function cashboxTransactions()
    {
        return $this->morphMany(CashboxTransaction::class, 'reference');
    }

    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, 'reference');
    }
    protected static function booted()
{
    static::deleting(function ($payment) {
        // حذف تراکنش صندوق
        $cashboxTransaction = \App\Models\CashboxTransaction::where('reference_type', self::class)
            ->where('reference_id', $payment->id)
            ->first();
        if ($cashboxTransaction) {
            // اصلاح موجودی صندوق (برگشت واریز)
            \App\Models\Cashbox::where('id', $cashboxTransaction->cashbox_id)
                ->decrement('current_balance', $cashboxTransaction->amount);
            $cashboxTransaction->delete();
        }

        // حذف سند دفتر کل
        \App\Models\LedgerEntry::where('reference_type', self::class)
            ->where('reference_id', $payment->id)
            ->delete();
    });
}

}
