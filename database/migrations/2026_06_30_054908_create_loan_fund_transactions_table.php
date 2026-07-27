<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::create('loan_fund_transactions', function (Blueprint $table) {
        $table->id();
        $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
        $table->string('type', 30); // 'deposit', 'withdrawal_loan', 'repayment_installment'
        $table->decimal('amount', 12, 2);
        $table->date('transaction_date');
        $table->nullableMorphs('reference'); // برای loan یا installment
        $table->text('description')->nullable();
        $table->timestamps();
        $table->softDeletes();
    });
}
};
