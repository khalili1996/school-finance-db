<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            if (!Schema::hasColumn('incomes', 'month_id')) {
                $table->foreignId('month_id')->nullable()->after('income_date')->constrained('months')->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('incomes', function (Blueprint $table) {
            $table->dropForeign(['month_id']);
            $table->dropColumn('month_id');
        });
    }
};
