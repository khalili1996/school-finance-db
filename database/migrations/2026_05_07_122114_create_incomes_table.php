<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incomes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('income_category_id')->constrained('income_categories')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->nullable()->constrained('academic_years')->nullOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->foreignId('month_id')->nullable()->constrained('months')->nullOnDelete();
            $table->string('title');                                // عنوان عاید (کمک، دونیشن، ...)
            $table->decimal('total_amount', 10, 2);                 // مبلغ کل
            $table->decimal('received_amount', 10, 2)->default(0);  // مبلغ دریافت‌شده
            $table->date('income_date')->nullable();               // تاریخ عاید
            $table->string('source', 100)->nullable();             // منبع عاید
            $table->text('description')->nullable();
            $table->enum('status', ['due', 'partially_received', 'received', 'cancelled'])->default('due');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('incomes');
    }
};
