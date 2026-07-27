<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('employee_id')->constrained('employees')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->foreignId('month_id')->nullable()->constrained('months')->nullOnDelete();
            $table->decimal('base_salary', 10, 2)->default(0);
            $table->decimal('overtime_amount', 10, 2)->default(0);
            $table->decimal('bonus_amount', 10, 2)->default(0);
            $table->decimal('deduction_amount', 10, 2)->default(0);       // مجموع کسرات
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('guarantee_amount', 10, 2)->default(0);      // ضمانت
            $table->decimal('total_amount', 10, 2)->default(0);
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->enum('status', ['due', 'partially_paid', 'paid', 'closed'])->default('due');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['employee_id', 'month_id', 'academic_year_id'], 'unique_employee_salary');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
