<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeLoan extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'employee_id',
        'total_amount',
        'paid_amount',
        'monthly_installment',
        'loan_date',
        'notes',
        'status',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
