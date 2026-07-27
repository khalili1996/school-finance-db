<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            if (!Schema::hasColumn('student_fees', 'academic_year_id')) {
                $table->unsignedBigInteger('academic_year_id')->nullable()->after('fee_type_id');
                $table->foreign('academic_year_id')->references('id')->on('academic_years')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('student_fees', function (Blueprint $table) {
            $table->dropForeign(['academic_year_id']);
            $table->dropColumn('academic_year_id');
        });
    }
};
