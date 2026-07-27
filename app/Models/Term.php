<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Term extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'academic_year_id',
        'name',
        'type',
        'start_date',
        'end_date',
        'is_active',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicYear()
    {
        return $this->belongsTo(AcademicYear::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
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
