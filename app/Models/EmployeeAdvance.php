<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeAdvance extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'employee_id',
        'month_id',       // جدید
        'amount',
        'advance_date',
        'notes',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function month()      // جدید
    {
        return $this->belongsTo(Month::class);
    }
}
