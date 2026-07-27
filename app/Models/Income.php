<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CashboxTransaction;
use App\Models\LedgerEntry;

class Income extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'income_category_id',
        'academic_year_id',
        'term_id',
        'month_id',
        'title',
        'total_amount',
        'received_amount',
        'income_date',
        'source',
        'description',
        'status',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function category()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }

    public function incomeCategory()
    {
        return $this->belongsTo(IncomeCategory::class, 'income_category_id');
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function incomeReceipts()
    {
        return $this->hasMany(IncomeReceipt::class);
    }

    public function cashboxTransactions()
    {
        return $this->morphMany(CashboxTransaction::class, 'reference');
    }

    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, 'reference');
    }

    // ========== پاک‌سازی خودکار صندوق و دفتر کل هنگام حذف ==========
    protected static function booted()
    {
        static::deleting(function ($income) {
            // ۱. حذف تراکنش‌های صندوق مرتبط (با اصلاح موجودی)
            $transactions = CashboxTransaction::where('reference_type', self::class)
                ->where('reference_id', $income->id)
                ->get();

            foreach ($transactions as $trx) {
                if ($trx->cashbox) {
                    // اگر واریز بوده → موجودی کم شود (برگشت واریز)
                    if ($trx->type === 'deposit') {
                        $trx->cashbox->decrement('current_balance', $trx->amount);
                    } elseif ($trx->type === 'withdrawal') {
                        $trx->cashbox->increment('current_balance', $trx->amount);
                    }
                }
                $trx->delete();
            }

            // ۲. حذف اسناد دفتر کل مرتبط
            LedgerEntry::where('reference_type', self::class)
                ->where('reference_id', $income->id)
                ->delete();
        });
    }
}
