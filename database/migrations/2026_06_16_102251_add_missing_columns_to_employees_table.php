<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            // افزودن ستون‌ها فقط در صورتی که وجود نداشته باشند
            if (!Schema::hasColumn('employees', 'gender')) {
                $table->string('gender')->nullable();
            }
            if (!Schema::hasColumn('employees', 'education_level')) {
                $table->string('education_level')->nullable();
            }
            if (!Schema::hasColumn('employees', 'field_of_study')) {
                $table->string('field_of_study')->nullable();
            }
            if (!Schema::hasColumn('employees', 'photo')) {
                $table->string('photo')->nullable();
            }
            if (!Schema::hasColumn('employees', 'position_points')) {
                $table->integer('position_points')->nullable();
            }
            if (!Schema::hasColumn('employees', 'experience_points')) {
                $table->integer('experience_points')->nullable();
            }
            if (!Schema::hasColumn('employees', 'education_points')) {
                $table->integer('education_points')->nullable();
            }
        });
    }

    public function down(): void
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropColumn([
                'gender',
                'education_level',
                'field_of_study',
                'photo',
                'position_points',
                'experience_points',
                'education_points',
            ]);
        });
    }
};
