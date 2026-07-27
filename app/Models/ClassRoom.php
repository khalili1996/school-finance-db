<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRoom extends Model
{
    use SoftDeletes;

    // تعیین نام دقیق جدول در دیتابیس
    protected $table = 'classes';

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

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'class_id');
    }
}
