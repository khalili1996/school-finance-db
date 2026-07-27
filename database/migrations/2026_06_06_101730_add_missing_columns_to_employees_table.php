<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            if (!Schema::hasColumn('employees', 'birth_date')) {
                $table->date('birth_date')->nullable()->after('national_id');
            }
            if (!Schema::hasColumn('employees', 'address')) {
                $table->text('address')->nullable()->after('current_address'); // یا 'phone' در صورت نبود current_address
            }
            if (!Schema::hasColumn('employees', 'base_salary')) {
                $table->decimal('base_salary', 10, 2)->nullable()->default(0)->after('contract_type');
            }
            if (!Schema::hasColumn('employees', 'secondary_phone')) {
                $table->string('secondary_phone', 20)->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn(['birth_date', 'address', 'base_salary', 'secondary_phone']);
        });
    }
};
