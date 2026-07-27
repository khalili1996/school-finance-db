<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Month;
use App\Models\School;

class MonthsSeeder extends Seeder
{
    public function run(): void
    {
        $months = [
            ['number' => 1,  'name' => 'حمل',   'type' => 'spring'],
            ['number' => 2,  'name' => 'ثور',   'type' => 'spring'],
            ['number' => 3,  'name' => 'جوزا',  'type' => 'spring'],
            ['number' => 4,  'name' => 'سرطان', 'type' => 'spring'],
            ['number' => 5,  'name' => 'اسد',   'type' => 'spring'],
            ['number' => 6,  'name' => 'سنبله', 'type' => 'spring'],
            ['number' => 7,  'name' => 'میزان', 'type' => 'spring'],
            ['number' => 8,  'name' => 'عقرب',  'type' => 'spring'],
            ['number' => 9,  'name' => 'قوس',   'type' => 'spring'],
            ['number' => 10, 'name' => 'جدی',   'type' => 'winter'],
            ['number' => 11, 'name' => 'دلو',   'type' => 'winter'],
            ['number' => 12, 'name' => 'حوت',   'type' => 'winter'],
        ];

        // فرض می‌کنیم اولین مدرسه (ID=1) وجود دارد.
        $schoolId = School::first()->id ?? 1;

        foreach ($months as $month) {
            Month::updateOrCreate(
                [
                    'school_id' => $schoolId,
                    'number'    => $month['number'],
                ],
                [
                    'name'       => $month['name'],
                    'type'       => $month['type'],
                    'is_active'  => true,
                ]
            );
        }
    }
}
