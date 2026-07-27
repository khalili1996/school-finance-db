<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();

            $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();

            $table->foreignId('student_id')->constrained('students')->cascadeOnDelete();

            $table->foreignId('enrollment_id')
                ->nullable()
                ->constrained('enrollments')
                ->nullOnDelete();

            $table->foreignId('fee_type_id')
                ->constrained('fee_types')
                ->cascadeOnDelete();

            $table->foreignId('term_id')
                ->nullable()
                ->constrained('terms')
                ->nullOnDelete();

            $table->foreignId('month_id')
                ->nullable()
                ->constrained('months')
                ->nullOnDelete();

            $table->decimal('amount', 10, 2);

            $table->decimal('discount', 10, 2)->default(0);

            $table->text('notes')->nullable();

            $table->timestamps();

            $table->softDeletes();

            $table->index(
                ['school_id', 'student_id', 'fee_type_id', 'term_id', 'month_id'],
                'fees_lookup_idx'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_fees');
    }
};
