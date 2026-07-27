<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LedgerEntry extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'entry_date',
        'description',
        'debit',
        'credit',
        'reference_type',
        'reference_id',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * رابطه‌ی پلی‌مورفیک برای اتصال به منبع مالی (مثلاً Payment، ExpensePayment و ...)
     */
    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }
}
