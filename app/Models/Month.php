<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Month extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'number',
        'type',
        'is_active',
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

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
