<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $superAdmin = User::updateOrCreate(
            ['email' => 'admin@munjizahra.edu.af'],
            [
                'name'      => 'Super Admin',
                'password'  => bcrypt('Munjii@1401'),
                'school_id' => 1,
                'phone'     => '0093766725207',
                'is_active' => true,
            ]
        );
        $superAdmin->assignRole('Super Admin');
    }
}
