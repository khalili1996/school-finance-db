<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Salary extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'employee_id',
        'academic_year_id',
        'term_id',
        'month_id',
        'base_salary',
        'overtime_amount',
        'bonus_amount',
        'deduction_amount',
        'tax_amount',
        'tax_percent',
        'guarantee_amount',
        'total_amount',
        'paid_amount',
        'status',
        'notes',
    ];

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    // ★ این رابطه باید وجود داشته باشد
    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class);
    }
}
