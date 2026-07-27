<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashbox_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('from_cashbox_id')->constrained('cashboxes')->cascadeOnDelete();
            $table->foreignId('to_cashbox_id')->constrained('cashboxes')->cascadeOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('transfer_date');
            $table->string('receipt_number', 50)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashbox_transfers');
    }
};
