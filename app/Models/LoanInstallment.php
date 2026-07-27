<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanInstallment extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'loan_id',
        'amount',
        'due_date',
        'paid_date',
        'paid_amount',
        'status',
        'cashbox_id',
        'cashbox_transaction_id',
        'ledger_entry_id',
        'notes',
    ];

    public function loan()
    {
        return $this->belongsTo(Loan::class);
    }

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }

    public function cashboxTransaction()
    {
        return $this->belongsTo(CashboxTransaction::class);
    }

    public function ledgerEntry()
    {
        return $this->belongsTo(LedgerEntry::class);
    }
}
