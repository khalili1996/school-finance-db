<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SalaryStructure extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'employee_id',
        'base_salary',
        'overtime_rate',
        'bonus_default',
        'deduction_default',
        'tax_percent',
        'guarantee_percent',
        'is_active',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
