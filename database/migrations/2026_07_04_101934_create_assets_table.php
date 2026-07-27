<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   // create_assets_table.php
public function up()
{
    Schema::create('assets', function (Blueprint $table) {
        $table->id();
        $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
        $table->foreignId('category_id')->nullable()->constrained('asset_categories')->nullOnDelete();
        $table->string('asset_code', 30)->unique();      // کد اموال
        $table->string('description');                   // شرح
        $table->integer('quantity')->default(1);         // تعداد
        $table->string('custodian')->nullable();         // موقعیت / تحویل‌گیرنده
        $table->decimal('unit_price', 12, 2);            // قیمت واحد
        $table->decimal('total_price', 12, 2);           // قیمت کل (محاسبه‌شده)
        $table->date('purchase_date');                   // تاریخ خرید (میلادی)
        $table->string('status', 20)->default('active'); // وضعیت
        $table->text('notes')->nullable();               // توضیحات
        $table->timestamps();
        $table->softDeletes();
    });
}
};
