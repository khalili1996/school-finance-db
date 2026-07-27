<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Student extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'guardian_id',
        'first_name',
        'last_name',
        'father_name',
        'grandfather_name',
        'birth_year',
        'birth_date',           // ← اضافه شد
        'national_id',
        'base_number',          // ← اضافه شد
        'phone',
        'whatsapp_phone',       // ← اضافه شد
        'class',
        'gender',
        'address',
        'original_residence',   // ← اضافه شد
        'enrollment_date',
        'student_code',
        'status',
        'financial_status',     // ← اضافه شد
        'is_orphan',            // ← اضافه شد
        'photo',                // ← اضافه شد
        'academic_year_id'
        ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function guardian()
    {
        return $this->belongsTo(StudentGuardian::class, 'guardian_id');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}
