<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // تغییر نوع ستون از year به smallInteger برای سازگاری بهتر
            $table->unsignedSmallInteger('birth_year')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // بازگشت به نوع year (در صورت نیاز به عقبگرد)
            $table->year('birth_year')->nullable()->change();
        });
    }
};
