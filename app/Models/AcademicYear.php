<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicYear extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'start_date',
        'end_date',
        'is_active',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
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
    // در App\Models\AcademicYear
public function getNameAttribute($value)
{
    // اگر نام خالی باشد و تاریخ شروع موجود باشد، نام شمسی تولید کن
    if (empty($value) && $this->start_date) {
        $startYear = \App\Helpers\JalaliHelper::toJalali($this->start_date, 'Y');
        $endYear = $this->end_date ? \App\Helpers\JalaliHelper::toJalali($this->end_date, 'Y') : $startYear;
        return $startYear . '-' . $endYear;
    }
    return $value;
}
}
