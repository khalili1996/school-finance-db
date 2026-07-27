<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;   // ← این خط باید باشد

class User extends Authenticatable
{
    use Notifiable, HasRoles;             // ← HasRoles باید اینجا باشد

    protected $fillable = [
        'name',
        'email',
        'password',
        'school_id',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
