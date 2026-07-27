<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class SimplifyAssetCategoriesTable extends Migration
{
    public function up()
    {
        Schema::table('asset_categories', function (Blueprint $table) {
            // حذف ستون‌های اضافی (در صورت وجود)
            $columnsToDrop = ['prefix', 'suffix', 'code', 'description'];
            foreach ($columnsToDrop as $column) {
                if (Schema::hasColumn('asset_categories', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    public function down()
    {
        // در صورت نیاز به بازگردانی، می‌توانید دوباره ستون‌ها را ایجاد کنید
    }
}
