<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ledger_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->date('entry_date');                              // تاریخ سند
            $table->text('description')->nullable();                 // شرح
            $table->decimal('debit', 12, 2)->default(0);             // بدهکار
            $table->decimal('credit', 12, 2)->default(0);            // بستانکار
            $table->string('reference_type')->nullable();            // نوع رکورد مرجع (مثلاً StudentPayment, SalaryPayment)
            $table->unsignedBigInteger('reference_id')->nullable();  // شناسه‌ی رکورد مرجع
            $table->timestamps();

            $table->index(['reference_type', 'reference_id']);
            $table->index('entry_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ledger_entries');
    }
};
