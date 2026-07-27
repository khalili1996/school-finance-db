<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashbox_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('cashbox_id')->constrained('cashboxes')->cascadeOnDelete();
            $table->enum('type', ['income', 'expense']);                 // ورود یا خروج
            $table->decimal('amount', 12, 2);
            $table->date('transaction_date');
            $table->string('reference_type')->nullable();                // مثال: 'student', 'employee', 'expense', 'income'
            $table->unsignedBigInteger('reference_id')->nullable();      // کلید خارجی به رکورد مرجع
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashbox_transactions');
    }
};
