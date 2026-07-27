<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // افزودن deleted_at به جداول اصلی که نیاز به حذف نرم دارند
        $tables = [
            'schools',
            'academic_years',
            'terms',
            'months',
            'students',
            'student_guardians',
            'classes',
            'enrollments',
            'fee_types',
            'student_fees',
            'payments',
            'employees',
            'employee_roles',
            'salary_structures',
            'salaries',
            'salary_payments',
            'expense_categories',
            'expenses',
            'expense_payments',
            'income_categories',
            'incomes',
            'income_receipts',
            'cashboxes',
            'cashbox_transfers',
            // cashbox_transactions را حذف نرم نمی‌دهیم (ثبت رویداد است)
            'ledger_entries',
            'users',
            'roles',
            'permissions',
        ];

        foreach ($tables as $table) {
            if (!Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'schools',
            'academic_years',
            'terms',
            'months',
            'students',
            'student_guardians',
            'classes',
            'enrollments',
            'fee_types',
            'student_fees',
            'payments',
            'employees',
            'employee_roles',
            'salary_structures',
            'salaries',
            'salary_payments',
            'expense_categories',
            'expenses',
            'expense_payments',
            'income_categories',
            'incomes',
            'income_receipts',
            'cashboxes',
            'cashbox_transfers',
            'ledger_entries',
            'users',
            'roles',
            'permissions',
        ];

        foreach ($tables as $table) {
            if (Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
