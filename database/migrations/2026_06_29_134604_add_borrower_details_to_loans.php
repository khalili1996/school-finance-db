<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            // اصلاح نام ستون‌های موجود (اگر نیاز باشد)
            // $table->renameColumn('borrower_name', 'borrower_first_name'); // در صورت نیاز

            // اضافه کردن فیلدهای جدید
            $table->string('borrower_last_name', 100)->nullable()->after('borrower_name');
            $table->string('borrower_grandfather_name', 100)->nullable()->after('borrower_father_name');
            $table->string('borrower_relative_phone', 20)->nullable()->after('borrower_phone');
            $table->date('borrower_birth_date')->nullable()->after('borrower_national_id');
            $table->string('borrower_original_province', 100)->nullable()->after('borrower_address');
            $table->string('borrower_original_district', 100)->nullable()->after('borrower_original_province');
            $table->string('borrower_original_village', 150)->nullable()->after('borrower_original_district');
            // آدرس فعلی کامل (جایگزین borrower_address قبلی در صورت نیاز)
            // ما borrower_address را به عنوان آدرس فعلی نگه می‌داریم
            // اگر borrower_address از قبل وجود دارد، می‌توانید آن را به current_address تغییر نام دهید
            // $table->renameColumn('borrower_address', 'borrower_current_address');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn([
                'borrower_last_name',
                'borrower_grandfather_name',
                'borrower_relative_phone',
                'borrower_birth_date',
                'borrower_original_province',
                'borrower_original_district',
                'borrower_original_village',
            ]);
        });
    }
};
