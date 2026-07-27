<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Employee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'employee_role_id',
        'first_name',
        'last_name',
        'father_name',
        'grandfather_name',
        'national_id',
        'birth_date',
        'permanent_address',
        'current_address',
        'address',
        'phone',
        'secondary_phone',
        'position',
        'department',
        'hire_date',
        'contract_type',
        'base_salary',
        'status',
        'employee_code',
        'termination_date',
        'photo',
        'gender',
        'education_level',
        'field_of_study',
        'position_points',       // ← جدید
        'experience_points',     // ← جدید
        'education_points',      // ← جدید
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function employeeRole()
    {
        return $this->belongsTo(EmployeeRole::class);
    }

    public function salaryStructure()
    {
        return $this->hasOne(SalaryStructure::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function loans()
    {
        return $this->hasMany(EmployeeLoan::class);
    }
}
