<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $schools = DB::table('schools')->pluck('id');
        foreach ($schools as $schoolId) {
            $defaultYear = DB::table('academic_years')
                ->where('school_id', $schoolId)
                ->orderBy('start_date')
                ->value('id');
            if ($defaultYear) {
                DB::table('students')
                    ->where('school_id', $schoolId)
                    ->whereNull('academic_year_id')
                    ->update(['academic_year_id' => $defaultYear]);
                DB::table('employees')
                    ->where('school_id', $schoolId)
                    ->whereNull('academic_year_id')
                    ->update(['academic_year_id' => $defaultYear]);
            }
        }
    }

    public function down(): void
    {
        // هیچ تغییری در rollback انجام نمی‌شود
    }
};
