<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('academic_years', function (Blueprint $table) {
            $table->index(['school_id', 'is_active']);
        });

        Schema::table('terms', function (Blueprint $table) {
            $table->index(['school_id', 'type', 'is_active']);
        });

        Schema::table('months', function (Blueprint $table) {
            $table->index(['school_id', 'type']);
        });

        Schema::table('students', function (Blueprint $table) {
            $table->index(['school_id', 'status', 'last_name', 'first_name'], 'students_search_idx');
        });

        Schema::table('student_guardians', function (Blueprint $table) {
            $table->index(['school_id', 'phone']);
        });

        Schema::table('classes', function (Blueprint $table) {
            $table->index(['school_id', 'is_active']);
        });

        // enrollments_status_idx قبلاً در create_enrollments_table ساخته شده ← حذف شد

        Schema::table('fee_types', function (Blueprint $table) {
            $table->index(['school_id', 'is_active']);
        });

        // fees_lookup_idx قبلاً در create_student_fees_table ساخته شده ← حذف شد

        // payments_student_date_idx قبلاً در create_payments_table ساخته شده ← حذف شد

        Schema::table('employees', function (Blueprint $table) {
            $table->index(['school_id', 'status', 'department'], 'employees_status_dept_idx');
        });

        Schema::table('employee_roles', function (Blueprint $table) {
            $table->index(['school_id', 'is_active']);
        });

        // salary_structures شاخص school_id, employee_id قبلاً ساخته شده ← حذف شد

        // salaries_lookup_idx قبلاً در create_salaries_table ساخته شده ← حذف شد

        Schema::table('salary_payments', function (Blueprint $table) {
            $table->index(['school_id', 'employee_id', 'payment_date'], 'salary_payments_date_idx');
        });

        Schema::table('expense_categories', function (Blueprint $table) {
            $table->index(['school_id', 'is_active']);
        });

        // expenses_status_idx قبلاً در create_expenses_table ساخته شده ← حذف شد

        Schema::table('expense_payments', function (Blueprint $table) {
            $table->index(['school_id', 'expense_id', 'payment_date']);
        });

        Schema::table('income_categories', function (Blueprint $table) {
            $table->index(['school_id', 'is_active']);
        });

        // incomes_status_idx قبلاً در create_incomes_table ساخته شده ← حذف شد

        Schema::table('income_receipts', function (Blueprint $table) {
            $table->index(['school_id', 'income_id', 'receipt_date']);
        });

        Schema::table('cashboxes', function (Blueprint $table) {
            $table->index(['school_id', 'is_active']);
        });

        Schema::table('cashbox_transactions', function (Blueprint $table) {
            $table->index(['school_id', 'cashbox_id', 'transaction_date'], 'cashbox_txn_date_idx');
        });

        Schema::table('cashbox_transfers', function (Blueprint $table) {
            $table->index(['school_id', 'from_cashbox_id', 'to_cashbox_id']);
        });

        Schema::table('ledger_entries', function (Blueprint $table) {
            $table->index(['school_id', 'entry_date'], 'ledger_school_date_idx');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->index(['school_id', 'role_id', 'is_active']);
        });

        Schema::table('roles', function (Blueprint $table) {
            $table->index(['school_id', 'is_active']);
        });

        Schema::table('audit_logs', function (Blueprint $table) {
            $table->index(['school_id', 'user_id', 'created_at'], 'audit_logs_search_idx');
        });

        Schema::table('activity_logs', function (Blueprint $table) {
            $table->index(['school_id', 'user_id', 'created_at'], 'activity_logs_search_idx');
        });
    }

    public function down(): void
    {
        Schema::table('academic_years', fn($t) => $t->dropIndex(['school_id', 'is_active']));
        Schema::table('terms', fn($t) => $t->dropIndex(['school_id', 'type', 'is_active']));
        Schema::table('months', fn($t) => $t->dropIndex(['school_id', 'type']));
        Schema::table('students', fn($t) => $t->dropIndex('students_search_idx'));
        Schema::table('student_guardians', fn($t) => $t->dropIndex(['school_id', 'phone']));
        Schema::table('classes', fn($t) => $t->dropIndex(['school_id', 'is_active']));
        Schema::table('fee_types', fn($t) => $t->dropIndex(['school_id', 'is_active']));
        Schema::table('employees', fn($t) => $t->dropIndex('employees_status_dept_idx'));
        Schema::table('employee_roles', fn($t) => $t->dropIndex(['school_id', 'is_active']));
        Schema::table('salary_payments', fn($t) => $t->dropIndex('salary_payments_date_idx'));
        Schema::table('expense_categories', fn($t) => $t->dropIndex(['school_id', 'is_active']));
        Schema::table('expense_payments', fn($t) => $t->dropIndex(['school_id', 'expense_id', 'payment_date']));
        Schema::table('income_categories', fn($t) => $t->dropIndex(['school_id', 'is_active']));
        Schema::table('income_receipts', fn($t) => $t->dropIndex(['school_id', 'income_id', 'receipt_date']));
        Schema::table('cashboxes', fn($t) => $t->dropIndex(['school_id', 'is_active']));
        Schema::table('cashbox_transactions', fn($t) => $t->dropIndex('cashbox_txn_date_idx'));
        Schema::table('cashbox_transfers', fn($t) => $t->dropIndex(['school_id', 'from_cashbox_id', 'to_cashbox_id']));
        Schema::table('ledger_entries', fn($t) => $t->dropIndex('ledger_school_date_idx'));
        Schema::table('users', fn($t) => $t->dropIndex(['school_id', 'role_id', 'is_active']));
        Schema::table('roles', fn($t) => $t->dropIndex(['school_id', 'is_active']));
        Schema::table('audit_logs', fn($t) => $t->dropIndex('audit_logs_search_idx'));
        Schema::table('activity_logs', fn($t) => $t->dropIndex('activity_logs_search_idx'));
    }
};
