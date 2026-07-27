<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // create_asset_categories_table.php
public function up()
{
    Schema::create('asset_categories', function (Blueprint $table) {
        $table->id();
        $table->foreignId('school_id')->constrained('schools')->cascadeOnDelete();
        $table->string('name');
        $table->string('prefix', 5)->nullable(); // برای کد اموال: ELEC, FURN, ...
        $table->timestamps();
        $table->softDeletes();
    });
}
};
