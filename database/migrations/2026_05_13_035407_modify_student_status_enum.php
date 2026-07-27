<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ۱. افزودن مقادیر جدید به ENUM (بدون حذف قدیمی‌ها)
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('active','blocked','graduated','transferred','present','temporary','three_piece') NOT NULL DEFAULT 'present'");

        // ۲. تبدیل داده‌های قدیمی به مقادیر جدید
        DB::statement("UPDATE students SET status = 'present' WHERE status = 'active'");
        DB::statement("UPDATE students SET status = 'blocked' WHERE status = 'graduated'");
        DB::statement("UPDATE students SET status = 'temporary' WHERE status = 'transferred'");
        // 'blocked' قبلاً وجود داشت و معادل 'محروم' است، تغییری نمی‌کند.

        // ۳. حذف مقادیر قدیمی از ENUM
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('present','blocked','temporary','three_piece') NOT NULL DEFAULT 'present'");
    }

    public function down(): void
    {
        // بازگشت به حالت قبلی
        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('active','blocked','graduated','transferred','present','temporary','three_piece') NOT NULL DEFAULT 'active'");

        DB::statement("UPDATE students SET status = 'active' WHERE status = 'present'");
        DB::statement("UPDATE students SET status = 'graduated' WHERE status = 'temporary'");
        DB::statement("UPDATE students SET status = 'transferred' WHERE status = 'three_piece'");

        DB::statement("ALTER TABLE students MODIFY COLUMN status ENUM('active','blocked','graduated','transferred') NOT NULL DEFAULT 'active'");
    }
};
