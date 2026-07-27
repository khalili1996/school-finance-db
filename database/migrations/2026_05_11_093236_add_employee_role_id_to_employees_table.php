<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->foreignId('employee_role_id')
                  ->nullable()
                  ->after('school_id')
                  ->constrained('employee_roles')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign(['employee_role_id']);
            $table->dropColumn('employee_role_id');
        });
    }
};
