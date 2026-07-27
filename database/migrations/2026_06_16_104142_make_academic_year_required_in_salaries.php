<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            // اگر قبلاً nullable شده، آن را به NOT NULL تغییر می‌دهیم
            $table->unsignedBigInteger('academic_year_id')->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('salaries', function (Blueprint $table) {
            $table->unsignedBigInteger('academic_year_id')->nullable()->change();
        });
    }
};
