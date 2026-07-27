<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentGuardian extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_id',
        'full_name',
        'relation',
        'phone',
        'secondary_phone',
        'address',
        'education',
        'job',
        'is_primary',
    ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'guardian_id');
    }
}
