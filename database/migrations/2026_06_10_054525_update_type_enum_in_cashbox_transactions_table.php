<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // تغییر ENUM برای پذیرش مقادیر جدید
        DB::statement("ALTER TABLE cashbox_transactions MODIFY COLUMN type ENUM('income', 'expense', 'deposit', 'withdrawal', 'transfer') NOT NULL");
    }

    public function down(): void
    {
        // برگرداندن به حالت قبلی
        DB::statement("ALTER TABLE cashbox_transactions MODIFY COLUMN type ENUM('income', 'expense') NOT NULL");
    }
};
