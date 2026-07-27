<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Loan extends Model
{
    use SoftDeletes;

   protected $fillable = [
    'school_id',
    'employee_id',
    'borrower_name',            // نام
    'borrower_last_name',       // تخلص
    'borrower_father_name',     // نام پدر
    'borrower_grandfather_name',// نام پدرکلان
    'borrower_national_id',     // شماره تذکره
    'borrower_phone',           // شماره تماس
    'borrower_relative_phone',  // شماره تماس اقارب
    'borrower_birth_date',      // تاریخ تولد
    'borrower_original_province',  // ولایت
    'borrower_original_district',  // ولسوالی
    'borrower_original_village',   // قریه
    'borrower_address',         // آدرس فعلی کامل
    'borrower_photo',
    'guarantor_name',
    'guarantor_father_name',
    'guarantor_national_id',
    'guarantor_phone',
    'guarantor_address',
    'guarantor_photo',
    'amount',
    'duration_months',
    'installment_amount',
    'start_date',
    'end_date',
    'status',
    'notes',
    'loan_provider',       // 🆕

];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function installments()
    {
        return $this->hasMany(LoanInstallment::class);
    }
}
