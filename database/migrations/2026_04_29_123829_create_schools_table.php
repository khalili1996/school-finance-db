<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('schools', function (Blueprint $table) {
        $table->id();

        // اطلاعات اصلی
        $table->string('name');
        $table->string('code', 50)->unique();

        // اطلاعات تماس
        $table->string('phone', 20)->nullable();
        $table->string('email')->nullable();
        $table->text('address')->nullable();

        // تنظیمات
        $table->string('currency', 10)->default('AFN');
        $table->boolean('is_active')->default(true);

        // ایندکس‌ها (برای سرعت)
        $table->index('is_active');

        // زمان‌ها
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schools');
    }
};
