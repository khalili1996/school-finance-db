<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique('students_student_code_unique'); // نام ایندکس پیش‌فرض لاراول
            $table->unique(['school_id', 'student_code'], 'unique_student_code_per_school');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropUnique('unique_student_code_per_school');
            $table->unique('student_code');
        });
    }
};
