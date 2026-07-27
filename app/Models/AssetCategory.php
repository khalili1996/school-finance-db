<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssetCategory extends Model
{
    use SoftDeletes;

    protected $fillable = ['school_id', 'name',];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function assets()
    {
        return $this->hasMany(Asset::class, 'category_id');
    }
}
