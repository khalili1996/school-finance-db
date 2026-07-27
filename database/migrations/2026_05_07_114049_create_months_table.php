<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('months', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name', 20);                // نام ماه: حمل، ثور، جوزا و ...
            $table->tinyInteger('number');              // شماره ماه: 1 تا 12
            $table->enum('type', ['spring', 'winter']); // بهاری یا زمستانی
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['school_id', 'number']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('months');
    }
};
