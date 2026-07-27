<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // بازنشانی کش
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // ---------- ایجاد Permission ها (اگر وجود نداشته باشند) ----------
        $permissions = [
            'view_students', 'create_students', 'edit_students', 'delete_students',
            'view_employees', 'create_employees', 'edit_employees', 'delete_employees',
            'view_student_fees', 'create_student_fees', 'edit_student_fees', 'delete_student_fees',
            'view_payments', 'create_payments', 'edit_payments', 'delete_payments',
            'view_expenses', 'create_expenses', 'edit_expenses', 'delete_expenses',
            'view_incomes', 'create_incomes', 'edit_incomes', 'delete_incomes',
            'view_cashbox', 'manage_cashbox',
            'view_reports', 'print_reports',
            'manage_users',
            'manage_schools',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // ---------- ایجاد Role ها (اگر وجود نداشته باشند) ----------
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin']);
        $schoolAdmin = Role::firstOrCreate(['name' => 'School Admin']);
        $accountant = Role::firstOrCreate(['name' => 'Accountant']);
        $viewer = Role::firstOrCreate(['name' => 'Viewer']);

        // ---------- تخصیص Permission ها (بدون خطای تکراری) ----------
        $superAdmin->syncPermissions(Permission::all());

        $schoolAdmin->syncPermissions([
            'view_students', 'create_students', 'edit_students', 'delete_students',
            'view_employees', 'create_employees', 'edit_employees', 'delete_employees',
            'view_student_fees', 'create_student_fees', 'edit_student_fees', 'delete_student_fees',
            'view_payments', 'create_payments', 'edit_payments', 'delete_payments',
            'view_expenses', 'create_expenses', 'edit_expenses', 'delete_expenses',
            'view_incomes', 'create_incomes', 'edit_incomes', 'delete_incomes',
            'view_cashbox', 'manage_cashbox',
            'view_reports', 'print_reports',
        ]);

        $accountant->syncPermissions([
            'view_students',
            'view_student_fees', 'create_student_fees', 'edit_student_fees',
            'view_payments', 'create_payments',
            'view_expenses', 'create_expenses', 'edit_expenses',
            'view_incomes', 'create_incomes', 'edit_incomes',
            'view_cashbox',
            'view_reports', 'print_reports',
        ]);

        $viewer->syncPermissions([
            'view_students',
            'view_employees',
            'view_student_fees',
            'view_payments',
            'view_expenses',
            'view_incomes',
            'view_cashbox',
            'view_reports',
        ]);
    }
}
