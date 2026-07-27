<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enrollments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();
            $table->foreignId('academic_year_id')->constrained('academic_years')->cascadeOnDelete();
            $table->foreignId('term_id')->nullable()->constrained('terms')->nullOnDelete();
            $table->foreignId('class_id')->nullable()->constrained('classes')->nullOnDelete();
            $table->string('class', 20)->nullable();               // صنف در این ثبت‌نام
            $table->enum('status', ['active', 'transferred', 'graduated'])->default('active');
            $table->date('enrollment_date')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['student_id', 'academic_year_id'], 'unique_enrollment');
            $table->index(['school_id', 'academic_year_id', 'status'], 'enrollments_status_idx');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enrollments');
    }
};
