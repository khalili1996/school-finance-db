<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\CashboxTransaction;
use App\Models\LedgerEntry;

class IncomeReceipt extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'income_id',
        'payment_id',
        'amount',
        'receipt_date',
        'receipt_number',
        'payment_method',
        'notes',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function income()
    {
        return $this->belongsTo(Income::class);
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
