<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->nullable()->constrained('schools')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('activity');                            // نوع فعالیت (login, logout, print_report, ...)
            $table->text('description')->nullable();               // شرح فعالیت
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();                // مرورگر یا دستگاه کاربر
            $table->timestamps();

            $table->index('activity');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
    }
};
