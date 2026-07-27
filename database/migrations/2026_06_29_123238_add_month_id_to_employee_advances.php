<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employee_advances', function (Blueprint $table) {
            $table->foreignId('month_id')
                  ->nullable()
                  ->after('employee_id')
                  ->constrained('months')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employee_advances', function (Blueprint $table) {
            $table->dropForeign(['month_id']);
            $table->dropColumn('month_id');
        });
    }
};
