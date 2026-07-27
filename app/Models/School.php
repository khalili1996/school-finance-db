<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'code',
        'phone',
        'email',
        'address',
        'currency',
        'is_active',
    ];

    // ========== روابط ==========

    public function academicYears()
    {
        return $this->hasMany(AcademicYear::class);
    }

    public function terms()
    {
        return $this->hasMany(Term::class);
    }

    public function months()
    {
        return $this->hasMany(Month::class);
    }

    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function studentGuardians()
    {
        return $this->hasMany(StudentGuardian::class);
    }

    public function classes()
    {
        return $this->hasMany(ClassRoom::class);
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class);
    }

    public function feeTypes()
    {
        return $this->hasMany(FeeType::class);
    }

    public function studentFees()
    {
        return $this->hasMany(StudentFee::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function employeeRoles()
    {
        return $this->hasMany(EmployeeRole::class);
    }

    public function salaryStructures()
    {
        return $this->hasMany(SalaryStructure::class);
    }

    public function salaries()
    {
        return $this->hasMany(Salary::class);
    }

    public function salaryPayments()
    {
        return $this->hasMany(SalaryPayment::class);
    }

    public function expenseCategories()
    {
        return $this->hasMany(ExpenseCategory::class);
    }

    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }

    public function expensePayments()
    {
        return $this->hasMany(ExpensePayment::class);
    }

    public function incomeCategories()
    {
        return $this->hasMany(IncomeCategory::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function incomeReceipts()
    {
        return $this->hasMany(IncomeReceipt::class);
    }

    public function cashboxes()
    {
        return $this->hasMany(Cashbox::class);
    }

    public function cashboxTransactions()
    {
        return $this->hasMany(CashboxTransaction::class);
    }

    public function cashboxTransfers()
    {
        return $this->hasMany(CashboxTransfer::class);
    }

    public function ledgerEntries()
    {
        return $this->hasMany(LedgerEntry::class);
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
