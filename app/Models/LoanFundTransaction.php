<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanFundTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id', 'type', 'amount', 'transaction_date',
        'reference_type', 'reference_id', 'description'
    ];

    public function reference()
    {
        return $this->morphTo();
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
