<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExpenseCategory extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'description',
        'is_active',
        'academic_year_id'
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
