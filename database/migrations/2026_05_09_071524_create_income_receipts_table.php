<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('income_receipts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('income_id')->constrained('incomes')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->date('receipt_date');
            $table->string('receipt_number', 50)->nullable();
            $table->enum('payment_method', ['cash', 'bank', 'other'])->default('cash');
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('income_receipts');
    }
};
