<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CashboxTransaction;
use App\Models\LedgerEntry;

class ExpensePayment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'expense_id',
        'amount',
        'payment_date',
        'receipt_number',
        'payment_method',
        'notes',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function expense()
    {
        return $this->belongsTo(Expense::class);
    }

    public function cashboxTransactions()
    {
        return $this->morphMany(CashboxTransaction::class, 'reference');
    }

    public function ledgerEntries()
    {
        return $this->morphMany(LedgerEntry::class, 'reference');
    }
}
