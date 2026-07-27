<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('students', function (Blueprint $table) {
        if (!Schema::hasColumn('students', 'birth_date')) {
            $table->date('birth_date')->nullable();
        }
        if (!Schema::hasColumn('students', 'base_number')) {
            $table->string('base_number', 50)->nullable();
        }
        if (!Schema::hasColumn('students', 'whatsapp_phone')) {
            $table->string('whatsapp_phone', 20)->nullable();
        }
        if (!Schema::hasColumn('students', 'original_residence')) {
            $table->string('original_residence', 255)->nullable();
        }
        if (!Schema::hasColumn('students', 'financial_status')) {
            $table->string('financial_status', 20)->nullable();
        }
        if (!Schema::hasColumn('students', 'is_orphan')) {
            $table->boolean('is_orphan')->default(false);
        }
        if (!Schema::hasColumn('students', 'photo')) {
            $table->string('photo')->nullable();
        }
    });
}

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'birth_date',
                'base_number',
                'whatsapp_phone',
                'original_residence',
                'financial_status',
                'is_orphan',
                'photo',
            ]);
        });
    }
};
