<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            if (!Schema::hasColumn('students', 'financial_status')) {
                $table->enum('financial_status', ['full', 'discount', 'free'])->nullable()->after('status');
            }
            if (!Schema::hasColumn('students', 'is_orphan')) {
                $table->boolean('is_orphan')->default(false)->after('financial_status');
            }
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn(['financial_status', 'is_orphan']);
        });
    }
};
