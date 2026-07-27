<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_installments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);           // مبلغ قسط
            $table->date('due_date');                   // تاریخ سررسید (میلادی)
            $table->date('paid_date')->nullable();      // تاریخ پرداخت (میلادی)
            $table->decimal('paid_amount', 10, 2)->nullable();
            $table->string('status', 20)->default('pending'); // pending, paid
            $table->foreignId('cashbox_id')->nullable()->constrained('cashboxes')->nullOnDelete();
            $table->foreignId('cashbox_transaction_id')->nullable()->constrained('cashbox_transactions')->nullOnDelete();
            $table->foreignId('ledger_entry_id')->nullable()->constrained('ledger_entries')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_installments');
    }
};
