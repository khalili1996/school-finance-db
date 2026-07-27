<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Expense extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'expense_category_id',
        'academic_year_id',
        'term_id',
        'month_id',
        'title',
        'total_amount',
        'paid_amount',
        'expense_date',
        'description',
        'scan_file',
        'status',
        'quantity',
        'unit',
        'received_by',
        'consumer_name',
        'invoice_number',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
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

    public function expensePayments()
    {
        return $this->hasMany(ExpensePayment::class);
    }

    public function cashboxTransactions()
    {
        return $this->morphMany(CashboxTransaction::class, 'reference');
    }

    // ★ اضافه شده: پاک‌سازی خودکار صندوق و دفتر کل هنگام حذف
    protected static function booted()
    {
        static::deleting(function ($expense) {
            // ۱. تراکنش‌های صندوق را پیدا کن و موجودی را برگردان
            $transactions = CashboxTransaction::where('reference_type', self::class)
                ->where('reference_id', $expense->id)
                ->get();

            foreach ($transactions as $trx) {
                // برگشت موجودی به صندوق (چون برداشت بوده)
                if ($trx->cashbox) {
                    if ($trx->type === 'withdrawal') {
                        $trx->cashbox->increment('current_balance', $trx->amount);
                    } elseif ($trx->type === 'deposit') {
                        $trx->cashbox->decrement('current_balance', $trx->amount);
                    }
                }
                $trx->delete();
            }

            // ۲. حذف اسناد دفتر کل مرتبط
            LedgerEntry::where('reference_type', self::class)
                ->where('reference_id', $expense->id)
                ->delete();
        });
    }
}
