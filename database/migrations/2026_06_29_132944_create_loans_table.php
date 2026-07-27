<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('employee_id')->nullable()->constrained('employees')->nullOnDelete();
            // اطلاعات قرض‌گیرنده (در صورت نبود کارمند)
            $table->string('borrower_name', 200)->nullable();
            $table->string('borrower_father_name', 100)->nullable();
            $table->string('borrower_national_id', 50)->nullable();
            $table->string('borrower_phone', 20)->nullable();
            $table->string('borrower_address', 500)->nullable();
            $table->string('borrower_photo')->nullable(); // مسیر فایل عکس

            // اطلاعات ضامن
            $table->string('guarantor_name', 200);
            $table->string('guarantor_father_name', 100)->nullable();
            $table->string('guarantor_national_id', 50)->nullable();
            $table->string('guarantor_phone', 20)->nullable();
            $table->string('guarantor_address', 500)->nullable();
            $table->string('guarantor_photo')->nullable();

            // جزئیات وام
            $table->decimal('amount', 12, 2);          // اصل مبلغ
            $table->tinyInteger('duration_months');    // تعداد اقساط
            $table->decimal('installment_amount', 10, 2); // مبلغ هر قسط
            $table->date('start_date');                // تاریخ شروع (میلادی)
            $table->date('end_date')->nullable();      // تاریخ پایان (اختیاری)
            $table->string('status', 20)->default('active'); // active, completed
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
