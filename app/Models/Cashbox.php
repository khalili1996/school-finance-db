<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cashbox extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'type',
        'initial_balance',
        'current_balance',
        'is_active',
        'notes',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function transactions()
    {
        return $this->hasMany(CashboxTransaction::class);
    }

    public function outgoingTransfers()
    {
        return $this->hasMany(CashboxTransfer::class, 'from_cashbox_id');
    }

    public function incomingTransfers()
    {
        return $this->hasMany(CashboxTransfer::class, 'to_cashbox_id');
    }
}
