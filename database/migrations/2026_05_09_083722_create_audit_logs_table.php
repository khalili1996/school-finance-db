<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('action');                              // ایجاد، ویرایش، حذف، پرداخت
            $table->string('model_type')->nullable();              // مدل مورد نظر (Student, Employee, ...)
            $table->unsignedBigInteger('model_id')->nullable();    // شناسه رکورد
            $table->json('old_data')->nullable();                  // داده قبل از تغییر (برای ویرایش/حذف)
            $table->json('new_data')->nullable();                  // داده بعد از تغییر (برای ایجاد/ویرایش)
            $table->text('description')->nullable();               // توضیح عملیات
            $table->ipAddress('ip_address')->nullable();
            $table->timestamps();

            $table->index(['model_type', 'model_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
