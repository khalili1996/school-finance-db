<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentFee extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'student_id',
        'enrollment_id',
        'fee_type_id',
        'term_id',
        'month_id',
        'amount',
        'discount',
        'notes',
        'academic_year_id'
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function enrollment()
    {
        return $this->belongsTo(Enrollment::class);
    }

    public function feeType()
    {
        return $this->belongsTo(FeeType::class);
    }

    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    public function month()
    {
        return $this->belongsTo(Month::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'fee_id');
    }
}
