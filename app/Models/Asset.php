<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asset extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id', 'category_id', 'asset_code', 'description',
        'quantity', 'custodian', 'unit_price', 'total_price',
        'purchase_date', 'status', 'notes',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class);
    }
}
