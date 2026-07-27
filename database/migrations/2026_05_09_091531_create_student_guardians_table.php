<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_guardians', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('full_name');
            $table->string('relation', 30)->nullable();          // نسبت (پدر، مادر، برادر و ...)
            $table->string('phone', 20)->nullable();             // شماره تماس اصلی
            $table->string('secondary_phone', 20)->nullable();   // شماره تماس دوم
            $table->text('address')->nullable();
            $table->boolean('is_primary')->default(false);       // ولی اصلی
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_guardians');
    }
};
