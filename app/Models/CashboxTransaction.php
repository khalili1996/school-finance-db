<?php

namespace App\Models;

//use App\Observers\CashboxTransactionObserver;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

//#[ObservedBy([CashboxTransactionObserver::class])]
class CashboxTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'cashbox_id',
        'type',
        'amount',
        'transaction_date',
        'reference_type',
        'reference_id',
        'description',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function cashbox()
    {
        return $this->belongsTo(Cashbox::class);
    }

    /**
     * رابطه‌ی پلی‌مورفیک برای اتصال به هر نوع مدل مرجع
     */
    public function reference()
    {
        return $this->morphTo('reference', 'reference_type', 'reference_id');
    }
}
