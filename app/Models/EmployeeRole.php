<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EmployeeRole extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'name',
        'description',
        'is_active',
    ];

    // ========== روابط ==========

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function employees()
    {
        // این رابطه هنوز کامل نیست چون ستون employee_role_id در جدول employees وجود ندارد
        // بلافاصله بعد از ذخیره‌ی این مدل، یک مایگریشن برای افزودن آن می‌سازیم.
        return $this->hasMany(Employee::class);
    }
}
