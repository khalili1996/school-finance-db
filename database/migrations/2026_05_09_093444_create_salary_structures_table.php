<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('base_salary', 10, 2)->default(0);         // معاش پایه
            $table->decimal('overtime_rate', 10, 2)->default(0);        // نرخ اضافه‌کاری (ساعتی یا روزانه)
            $table->decimal('bonus_default', 10, 2)->default(0);        // پاداش پیش‌فرض
            $table->decimal('deduction_default', 10, 2)->default(0);    // کسر پیش‌فرض
            $table->decimal('tax_percent', 5, 2)->default(0);           // درصد مالیات
            $table->decimal('guarantee_percent', 5, 2)->default(0);     // درصد ضمانت
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique('employee_id');                              // هر کارمند یک ساختار معاش
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_structures');
    }
};
