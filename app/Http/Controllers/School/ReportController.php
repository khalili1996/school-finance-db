<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Guardian;
use App\Models\Employee;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Salary;
use App\Models\StudentFee;
use App\Models\Payment;
use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use App\Models\LedgerEntry;
use App\Models\Loan;
use App\Models\LoanFundTransaction;
use App\Helpers\JalaliHelper;
use Illuminate\Http\Request;
use App\Models\StudentGuardian;
use App\Models\IncomeCategory;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\AcademicYear;

class ReportController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    /**
     * دریافت سال مالی فعال (در صورت انتخاب)
     */
    private function getActiveYearFilter($query, $tableColumn = 'academic_year_id')
    {
        $activeYearId = session('active_academic_year_id');
        if ($activeYearId) {
            return $query->where($tableColumn, $activeYearId);
        }
        return $query;
    }

    /**
     * صفحه اصلی گزارشات (انتخاب نوع گزارش)
     */
    public function index()
    {
        return view('school.reports.index');
    }

    // ==========================================
    // گزارش دانش‌آموزان
    // ==========================================
    public function students(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');

        $query = Student::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->with('guardian');

        if ($class = $request->input('class_filter')) {
            $query->where('class', $class);
        }
        if ($status = $request->input('status_filter')) {
            $query->where('status', $status);
        }
        if ($financial = $request->input('financial_filter')) {
            if ($financial === 'debtor') {
                $query->whereHas('studentFees', function ($q) {
                    $q->select('student_id')->groupBy('student_id')
                      ->havingRaw('SUM(amount - discount) > (SELECT COALESCE(SUM(payments.amount),0) FROM payments WHERE payments.student_id = student_fees.student_id)');
                });
            } elseif ($financial === 'discount') {
                $query->whereHas('studentFees', fn($q) => $q->where('discount', '>', 0));
            } elseif ($financial === 'free') {
                $query->where('financial_status', 'free');
            } elseif ($financial === 'orphan') {
                $query->where('status', 'three_piece');
            }
        }

        $students = $query->orderBy('first_name')->paginate(20);
        $classes = Student::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->select('class')->distinct()->orderBy('class')->pluck('class');

        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();

        return view('school.reports.students', compact('students', 'classes', 'academicYears'));
    }

    // ==========================================
    // گزارش اولیا
    // ==========================================
    public function guardians(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');

        $query = StudentGuardian::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->withCount('students');

        if ($search = $request->input('search')) {
            $query->where('full_name', 'like', "%{$search}%");
        }

        $guardians = $query->paginate(20);
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();

        return view('school.reports.guardians', compact('guardians', 'academicYears'));
    }

    // ==========================================
    // گزارش کارمندان
    // ==========================================
    public function employees(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');

        $query = Employee::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId));

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $employees = $query->orderBy('first_name')->get();
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();

        return view('school.reports.employees', compact('employees', 'academicYears'));
    }

    // ==========================================
    // گزارش درآمدها (شهریه گروه‌بندی‌شده + سایر درآمدها)
    // ==========================================
    public function financialIncomes(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');

        $fromDate = $request->input('from_date');
        $toDate   = $request->input('to_date');
        $fromDateGregorian = $fromDate ? JalaliHelper::toGregorian($fromDate)->format('Y-m-d') : null;
        $toDateGregorian   = $toDate   ? JalaliHelper::toGregorian($toDate)->format('Y-m-d') : null;

        // ---- شهریه‌های پرداختی (گروه‌بندی بر اساس صنف) ----
        $feePaymentsByClass = collect();
        $totalFeeIncome = 0;
        try {
            $feeQuery = Payment::where('school_id', $schoolId)
                ->whereHas('student')
                ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
                ->when($fromDateGregorian, fn($q) => $q->where('payment_date', '>=', $fromDateGregorian))
                ->when($toDateGregorian,   fn($q) => $q->where('payment_date', '<=', $toDateGregorian));

            if ($class = $request->input('class_filter')) {
                $feeQuery->whereHas('student', fn($q) => $q->where('class', $class));
            }
            if ($monthId = $request->input('month_filter')) {
                $feeQuery->whereHas('studentFee', fn($q) => $q->where('month_id', $monthId));
            }

            $feePaymentsByClass = $feeQuery->with('student')
                ->get()
                ->groupBy('student.class')
                ->map(function ($payments, $class) {
                    return [
                        'class' => $class,
                        'count' => $payments->pluck('student_id')->unique()->count(),
                        'total' => $payments->sum('amount'),
                    ];
                })
                ->sortBy('class')
                ->values();

            $totalFeeIncome = $feePaymentsByClass->sum('total');
        } catch (\Exception $e) {}

        // ---- سایر درآمدها (بدون شهریه) ----
        $feeCategoryId = IncomeCategory::where('school_id', $schoolId)
                            ->where('name', 'شهریه')
                            ->value('id');

        $otherIncomesQuery = Income::where('school_id', $schoolId)
            ->with('incomeCategory')
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->when($feeCategoryId, fn($q) => $q->where('income_category_id', '!=', $feeCategoryId))
            ->when($fromDateGregorian, fn($q) => $q->where('income_date', '>=', $fromDateGregorian))
            ->when($toDateGregorian,   fn($q) => $q->where('income_date', '<=', $toDateGregorian));

        if ($categoryId = $request->input('category_filter')) {
            $otherIncomesQuery->where('income_category_id', $categoryId);
        }

        $otherIncomes = $otherIncomesQuery->orderBy('income_date', 'desc')->paginate(10, ['*'], 'income_page');
        $totalOtherIncomes = 0;
        try {
            $totalOtherIncomes = $otherIncomesQuery->sum('received_amount');
        } catch (\Exception $e) {}

        $classes   = Student::where('school_id', $schoolId)->distinct()->pluck('class')->filter();
        $months    = \App\Models\Month::where('school_id', $schoolId)->orderBy('number')->get();
        $categories = IncomeCategory::where('school_id', $schoolId)->get();
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();

        $grandTotalIncome = $totalFeeIncome + $totalOtherIncomes;

        return view('school.reports.financial-incomes', compact(
            'feePaymentsByClass', 'totalFeeIncome',
            'otherIncomes', 'totalOtherIncomes',
            'grandTotalIncome',
            'fromDate', 'toDate', 'classes', 'months', 'categories', 'academicYears'
        ));
    }

    // ==========================================
    // گزارش مصارف (هزینه‌های روزمره + معاشات)
    // ==========================================
    public function financialExpenses(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');

        $fromDate = $request->input('from_date');
        $toDate   = $request->input('to_date');
        $fromDateGregorian = $fromDate ? JalaliHelper::toGregorian($fromDate)->format('Y-m-d') : null;
        $toDateGregorian   = $toDate   ? JalaliHelper::toGregorian($toDate)->format('Y-m-d') : null;

        // ===== مصارف روزمره (گروه‌بندی‌شده) =====
        $expensesGrouped = collect();
        $totalExpenses = 0;
        try {
            $expensesQuery = Expense::where('school_id', $schoolId)
                ->with('category')
                ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
                ->when($fromDateGregorian, fn($q) => $q->where('expense_date', '>=', $fromDateGregorian))
                ->when($toDateGregorian,   fn($q) => $q->where('expense_date', '<=', $toDateGregorian));

            if ($categoryId = $request->input('expense_category_filter')) {
                $expensesQuery->where('expense_category_id', $categoryId);
            }

            $expenses = $expensesQuery->orderBy('expense_date')->get();
            $expensesGrouped = $expenses->groupBy('expense_category_id')->map(function ($items) {
                $catName = $items->first()->category->name ?? 'متفرقه';
                $total   = $items->sum('total_amount');
                return [
                    'category' => $catName,
                    'items'    => $items,
                    'total'    => $total,
                ];
            })->values();
            $totalExpenses = $expensesGrouped->sum('total');
        } catch (\Exception $e) {}

        // ===== حقوق (با جزئیات کامل) =====
        $salariesQuery = collect();
        $totalSalaries = 0;
        try {
            $salariesQuery = Salary::where('school_id', $schoolId)
                ->with(['employee', 'month'])
                ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
                ->when($fromDateGregorian, fn($q) => $q->where('created_at', '>=', $fromDateGregorian))
                ->when($toDateGregorian,   fn($q) => $q->where('created_at', '<=', $toDateGregorian))
                ->when($monthId = $request->input('month_filter'), fn($q) => $q->where('month_id', $monthId))
                ->get();
            $totalSalaries = $salariesQuery->sum('paid_amount');
        } catch (\Exception $e) {}

        $expenseCategories = \App\Models\ExpenseCategory::where('school_id', $schoolId)->get();
        $months = \App\Models\Month::where('school_id', $schoolId)->orderBy('number')->get();
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();

        return view('school.reports.financial-expenses', compact(
            'expensesGrouped', 'totalExpenses',
            'salariesQuery', 'totalSalaries',
            'fromDate', 'toDate', 'expenseCategories', 'months', 'academicYears'
        ));
    }

    // ==========================================
    // گزارش صندوق
    // ==========================================
    public function financialCashboxes(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');

        $fromDate = $request->input('from_date');
        $toDate   = $request->input('to_date');
        $fromDateGregorian = $fromDate ? JalaliHelper::toGregorian($fromDate)->format('Y-m-d') : null;
        $toDateGregorian   = $toDate   ? JalaliHelper::toGregorian($toDate)->format('Y-m-d') : null;

        $cashboxes = Cashbox::where('school_id', $schoolId)->get();

        $transactionsQuery = CashboxTransaction::where('school_id', $schoolId)
            ->with('cashbox')
            ->when($fromDateGregorian, fn($q) => $q->where('transaction_date', '>=', $fromDateGregorian))
            ->when($toDateGregorian,   fn($q) => $q->where('transaction_date', '<=', $toDateGregorian));

        if ($activeYearId) {
            $year = AcademicYear::find($activeYearId);
            if ($year && $year->school_id == $schoolId) {
                $transactionsQuery->whereBetween('transaction_date', [$year->start_date, $year->end_date]);
            }
        }

        if ($type = $request->input('type_filter')) {
            $transactionsQuery->where('type', $type);
        }

        $transactions = $transactionsQuery->orderBy('transaction_date', 'desc')->paginate(15);
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();

        return view('school.reports.financial-cashboxes', compact(
            'cashboxes', 'transactions', 'fromDate', 'toDate', 'academicYears'
        ));
    }

    // ==========================================
    // گزارش دفتر کل
    // ==========================================
    public function financialLedger(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');

        $fromDate = $request->input('from_date');
        $toDate   = $request->input('to_date');
        $fromDateGregorian = $fromDate ? JalaliHelper::toGregorian($fromDate)->format('Y-m-d') : null;
        $toDateGregorian   = $toDate   ? JalaliHelper::toGregorian($toDate)->format('Y-m-d') : null;

        $incomeQuery = LedgerEntry::where('school_id', $schoolId)
            ->where('debit', '>', 0)
            ->when($fromDateGregorian, fn($q) => $q->where('entry_date', '>=', $fromDateGregorian))
            ->when($toDateGregorian,   fn($q) => $q->where('entry_date', '<=', $toDateGregorian));

        $expenseQuery = LedgerEntry::where('school_id', $schoolId)
            ->where('credit', '>', 0)
            ->when($fromDateGregorian, fn($q) => $q->where('entry_date', '>=', $fromDateGregorian))
            ->when($toDateGregorian,   fn($q) => $q->where('entry_date', '<=', $toDateGregorian));

        if ($activeYearId) {
            $year = AcademicYear::find($activeYearId);
            if ($year && $year->school_id == $schoolId) {
                $incomeQuery->whereBetween('entry_date', [$year->start_date, $year->end_date]);
                $expenseQuery->whereBetween('entry_date', [$year->start_date, $year->end_date]);
            }
        }

        $incomes = $incomeQuery->orderBy('entry_date', 'desc')->paginate(15, ['*'], 'income_page');
        $totalIncome = 0;
        try { $totalIncome = $incomeQuery->sum('debit'); } catch (\Exception $e) {}

        $expenses = $expenseQuery->orderBy('entry_date', 'desc')->paginate(15, ['*'], 'expense_page');
        $totalExpense = 0;
        try { $totalExpense = $expenseQuery->sum('credit'); } catch (\Exception $e) {}

        $balance = $totalIncome - $totalExpense;
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();

        return view('school.reports.financial-ledger', compact(
            'incomes', 'expenses',
            'totalIncome', 'totalExpense', 'balance',
            'fromDate', 'toDate', 'academicYears'
        ));
    }

    // ==========================================
    // گزارش قرض‌الحسنه (بدون تغییر – مستقل)
    // ==========================================
    public function loans(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $query = Loan::where('school_id', $schoolId)->with('employee');
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        $loans = $query->orderBy('created_at', 'desc')->paginate(15);

        $totalDeposits    = LoanFundTransaction::where('school_id', $schoolId)->where('type', 'deposit')->sum('amount');
        $totalLoansGiven  = LoanFundTransaction::where('school_id', $schoolId)->where('type', 'withdrawal_loan')->sum('amount');
        $totalRepayments  = LoanFundTransaction::where('school_id', $schoolId)->where('type', 'repayment_installment')->sum('amount');
        $fundBalance      = $totalDeposits + $totalRepayments - $totalLoansGiven;

        return view('school.reports.loans', compact(
            'loans', 'fundBalance',
            'totalDeposits', 'totalLoansGiven', 'totalRepayments'
        ));
    }

    // ==========================================
    // گزارش جامع
    // ==========================================
    public function comprehensive(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');

        $monthId = $request->input('month_id');
        $selectedMonth = null;
        if ($monthId) {
            $selectedMonth = \App\Models\Month::find($monthId);
        }

        // --- ۱. درآمد شهریه (گروه‌بندی‌شده) ---
        $feePaymentsGrouped = collect();
        $totalFeeIncome = 0;
        try {
            $feeQuery = Payment::where('school_id', $schoolId)
                ->whereHas('studentFee')
                ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId));

            if ($monthId) {
                $feeQuery->whereHas('studentFee', fn($q) => $q->where('month_id', $monthId));
            }

            $feePaymentsGrouped = $feeQuery->get()
                ->groupBy('amount')
                ->map(fn($group) => [
                    'count'  => $group->count(),
                    'amount' => $group->first()->amount,
                    'total'  => $group->sum('amount'),
                ])->sortBy('amount')->values();

            $totalFeeIncome = $feePaymentsGrouped->sum('total');
        } catch (\Exception $e) {}

        // --- ۲. درآمدهای غیرشهریه (تفکیک مستقل و کمک) ---
        $independentIncome = 0;
        $grants = 0;
        try {
            $incomeQuery = Income::where('school_id', $schoolId)
                ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId));

            if ($monthId) {
                $incomeQuery->where('month_id', $monthId);
            }

            $grantCategoryIds = [];
            if (count($grantCategoryIds) > 0) {
                $independentIncome = (clone $incomeQuery)
                    ->whereNotIn('income_category_id', $grantCategoryIds)
                    ->sum('received_amount');
                $grants = (clone $incomeQuery)
                    ->whereIn('income_category_id', $grantCategoryIds)
                    ->sum('received_amount');
            } else {
                $independentIncome = $incomeQuery->sum('received_amount');
                $grants = 0;
            }
        } catch (\Exception $e) {}

        $otherIncomes = $totalFeeIncome + $independentIncome;
        $totalIncome = $otherIncomes + $grants;

        // --- ۳. مصارف (گروه‌بندی‌شده) ---
        $expensesGrouped = collect();
        $totalExpenses = 0;
        try {
            $expQuery = Expense::where('school_id', $schoolId)
                ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId));
            if ($monthId) {
                $expQuery->where('month_id', $monthId);
            }
            $expenses = $expQuery->with('category')->get();
            $expensesGrouped = $expenses->groupBy('expense_category_id')->map(function ($items) {
                $catName = $items->first()->category->name ?? 'متفرقه';
                $total = $items->sum('total_amount');
                return ['category' => $catName, 'items' => $items, 'total' => $total];
            })->values();
            $totalExpenses = $expensesGrouped->sum('total');
        } catch (\Exception $e) {}

        // --- ۴. معاشات ---
        $salariesQuery = collect();
        $totalSalaries = 0;
        try {
            $salariesQuery = Salary::where('school_id', $schoolId)
                ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
                ->when($monthId, fn($q) => $q->where('month_id', $monthId))
                ->with(['employee', 'month'])
                ->get();
            $totalSalaries = $salariesQuery->sum('paid_amount');
        } catch (\Exception $e) {}

        // --- ۵. موجودی صندوق و بدهی ---
        $cashboxBalance = 0;
        try { $cashboxBalance = Cashbox::where('school_id', $schoolId)->sum('current_balance'); } catch (\Exception $e) {}

        $totalDebt = 0;
        try {
            $totalDebt = StudentFee::where('school_id', $schoolId)
                            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
                            ->sum(\DB::raw('amount - discount'))
                        - Payment::where('school_id', $schoolId)
                            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
                            ->sum('amount');
        } catch (\Exception $e) {}

        $months = \App\Models\Month::where('school_id', $schoolId)->orderBy('number')->get();
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();

        return view('school.reports.comprehensive-report', compact(
            'feePaymentsGrouped', 'totalFeeIncome',
            'otherIncomes', 'grants', 'totalIncome',
            'expensesGrouped', 'totalExpenses',
            'salariesQuery', 'totalSalaries',
            'cashboxBalance', 'totalDebt',
            'months', 'selectedMonth', 'monthId', 'academicYears'
        ));
    }

    // ==========================================
    // گزارش دارایی‌ها (اموال)
    // ==========================================
    public function assetsReport(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $query = Asset::where('school_id', $schoolId)
            ->with('category')
            ->orderBy('asset_code');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('custodian', 'like', "%{$search}%");
            });
        }

        $assets = $query->paginate(15);
        $categories = AssetCategory::where('school_id', $schoolId)->orderBy('name')->get();

        return view('school.reports.assets', compact('assets', 'categories'));
    }
}
