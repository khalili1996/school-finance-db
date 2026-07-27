<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FeeType extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'description',
        'is_optional',
        'is_active',
        'category',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }
}
