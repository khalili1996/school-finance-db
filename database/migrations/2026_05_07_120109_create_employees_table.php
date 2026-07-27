<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('father_name');
            $table->string('grandfather_name')->nullable();
            $table->string('national_id', 50)->nullable();            // نمبر تذکره
            $table->string('permanent_address')->nullable();          // سکونت اصلی
            $table->string('current_address')->nullable();            // سکونت فعلی
            $table->string('phone', 20)->nullable();
            $table->string('position');                               // سمت / وظیفه
            $table->string('department')->nullable();                 // بخش (آموزشی، اداری، مالی)
            $table->date('hire_date')->nullable();                    // تاریخ استخدام
            $table->enum('contract_type', ['permanent', 'temporary'])->default('temporary');
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('employee_code', 30)->unique();            // کد یکتای کارمند
            $table->date('termination_date')->nullable();             // تاریخ ختم وظیفه
            $table->timestamps();

            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
