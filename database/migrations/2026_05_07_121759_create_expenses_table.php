<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('expense_category_id')->constrained('expense_categories')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->foreignId('month_id')->nullable()->constrained('months')->nullOnDelete();
            $table->string('title');                                // عنوان مصرف
            $table->decimal('total_amount', 10, 2);                 // مبلغ کل
            $table->decimal('paid_amount', 10, 2)->default(0);      // مبلغ پرداخت‌شده
            $table->date('expense_date')->nullable();               // تاریخ مصرف
            $table->text('description')->nullable();
            $table->string('scan_file')->nullable();                // فایل اسکن فاکتور
            $table->enum('status', ['due', 'partially_paid', 'paid', 'cancelled'])->default('due');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
