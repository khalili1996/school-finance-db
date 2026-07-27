<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->string('name');                         // مثال: شهریه، لباس، کتاب، ...
            $table->text('description')->nullable();
            $table->boolean('is_optional')->default(false); // اختیاری بودن
            $table->boolean('is_active')->default(true);
            $table->enum('category', ['tuition', 'one_time', 'other'])->default('other');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_types');
    }
};
