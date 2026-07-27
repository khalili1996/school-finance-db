<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\School;

class SchoolSeeder extends Seeder
{
    public function run(): void
    {
        School::updateOrCreate(
            ['code' => 'MZS-1401'], // شرط یکتایی: اگر کد وجود داشت، فقط به‌روزرسانی کن
            [
                'name'       => 'مکتب خصوصی منجی الزهرا (س)',
                'phone'      => '0093766725207',
                'email'      => 'monji3497@gmail.com',
                'address'    => 'افغانستان، کابل، ناحیه 13، شهرک عرفانی',
                'currency'   => 'AFN',
                'is_active'  => true,
            ]
        );
    }
}
