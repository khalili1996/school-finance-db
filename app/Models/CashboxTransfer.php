<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CashboxTransfer extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'from_cashbox_id',
        'to_cashbox_id',
        'amount',
        'transfer_date',
        'receipt_number',
        'notes',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function fromCashbox()
    {
        return $this->belongsTo(Cashbox::class, 'from_cashbox_id');
    }

    public function toCashbox()
    {
        return $this->belongsTo(Cashbox::class, 'to_cashbox_id');
    }
}
