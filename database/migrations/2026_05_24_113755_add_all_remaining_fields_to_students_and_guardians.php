<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // ========== جدول دانش‌آموزان ==========
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'base_number')) {
                $table->string('base_number', 50)->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('students', 'original_residence')) {
                $table->string('original_residence')->nullable()->after('address');
            }
            if (!Schema::hasColumn('students', 'whatsapp_phone')) {
                $table->string('whatsapp_phone', 20)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('students', 'photo')) {
                $table->string('photo')->nullable();
            }
        });

        // ========== جدول اولیا ==========
        Schema::table('student_guardians', function (Blueprint $table) {
            if (!Schema::hasColumn('student_guardians', 'national_id')) {
                $table->string('national_id', 50)->nullable()->after('phone');
            }
            if (!Schema::hasColumn('student_guardians', 'education')) {
                $table->string('education', 100)->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('student_guardians', 'job')) {
                $table->string('job', 100)->nullable()->after('education');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $columns = ['base_number', 'original_residence', 'whatsapp_phone', 'photo'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('students', $col)) {
                    $table->dropColumn($col);
                }
            }
        });

        Schema::table('student_guardians', function (Blueprint $table) {
            $columns = ['national_id', 'education', 'job'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('student_guardians', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
