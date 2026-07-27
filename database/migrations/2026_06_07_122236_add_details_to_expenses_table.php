<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            if (!Schema::hasColumn('expenses', 'quantity')) {
                $table->decimal('quantity', 10, 2)->nullable()->after('description');
            }
            if (!Schema::hasColumn('expenses', 'unit')) {
                $table->string('unit', 30)->nullable()->after('quantity');
            }
            if (!Schema::hasColumn('expenses', 'received_by')) {
                $table->string('received_by', 100)->nullable()->after('expense_date');
            }
            if (!Schema::hasColumn('expenses', 'consumer_name')) {
                $table->string('consumer_name', 100)->nullable()->after('received_by');
            }
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropColumn(['quantity', 'unit', 'received_by', 'consumer_name']);
        });
    }
};
