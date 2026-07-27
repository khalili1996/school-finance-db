<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('father_name');
            $table->string('grandfather_name')->nullable();
            $table->year('birth_year')->nullable();
            $table->string('national_id', 50)->nullable();         // نمبر اساس
            $table->string('phone', 20)->nullable();              // شماره تماس ولی
            $table->string('class', 20)->nullable();              // صنف / کلاس
            $table->enum('gender', ['male', 'female']);
            $table->text('address')->nullable();
            $table->date('enrollment_date')->nullable();          // تاریخ ثبت‌نام
            $table->string('student_code', 30)->unique();         // کد یکتای دانش‌آموز
            $table->enum('status', ['active', 'blocked', 'graduated', 'transferred'])->default('active');
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
