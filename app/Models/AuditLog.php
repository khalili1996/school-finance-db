<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'school_id',
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_data',
        'new_data',
        'description',
        'ip_address',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * رابطه‌ی پلی‌مورفیک: این لاگ برای کدام رکورد (دانش‌آموز، هزینه، ...) ثبت شده است.
     */
    public function auditable()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }
}
