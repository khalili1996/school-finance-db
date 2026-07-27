<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\School;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $schoolId = session('active_school_id', auth()->user()->school_id);
        $school = School::findOrFail($schoolId);
        $activeYearId = session('active_academic_year_id');

        // ====== آمار پایه ======
        $totalStudents = \App\Models\Student::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->count();

        $totalEmployees = \App\Models\Employee::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->count();

        // ====== درآمد امروز ======
        $todayIncome = \App\Models\Payment::where('school_id', $schoolId)
            ->whereDate('payment_date', today())
            ->when($activeYearId, fn($q) => $q->whereHas('studentFee', fn($q) => $q->where('academic_year_id', $activeYearId)))
            ->sum('amount');

        // ====== مصارف کل ======
        $totalExpenses = 0;
        try {
            $expenseAmount = \App\Models\ExpensePayment::where('school_id', $schoolId)
                ->when($activeYearId, fn($q) => $q->whereHas('expense', fn($q) => $q->where('academic_year_id', $activeYearId)))
                ->sum('amount');
            $salaryAmount = \App\Models\SalaryPayment::where('school_id', $schoolId)
                ->when($activeYearId, fn($q) => $q->whereHas('salary', fn($q) => $q->where('academic_year_id', $activeYearId)))
                ->sum('amount');
            $totalExpenses = $expenseAmount + $salaryAmount;
        } catch (\Exception $e) {}

        // ====== موجودی صندوق ======
        $cashboxBalance = \App\Models\Cashbox::where('school_id', $schoolId)->sum('current_balance');

        // ====== بدهی کل شاگردان ======
        $totalFees = \App\Models\StudentFee::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->sum(\DB::raw('amount - discount'));
        $totalPaid = \App\Models\Payment::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->whereHas('studentFee', fn($q) => $q->where('academic_year_id', $activeYearId)))
            ->sum('amount');
        $totalDebt = max($totalFees - $totalPaid, 0);

        // ====== شاگردان بدهکار (بالای ۵۰۰ افغانی) ======
        $debtorStudents = \App\Models\Student::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->whereHas('studentFees', function ($q) use ($activeYearId) {
                if ($activeYearId) $q->where('academic_year_id', $activeYearId);
            })
            ->withSum('payments as total_paid', 'amount')
            ->get()
            ->filter(function ($student) {
                $totalFee = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                return ($totalFee - $student->total_paid) > 500;
            });

        // ====== معاشات عقب‌افتاده ======
        $unpaidSalaries = \App\Models\Salary::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->whereIn('status', ['due', 'partially_paid'])
            ->count();

        // ====== صندوق منفی ======
        $negativeCashboxes = \App\Models\Cashbox::where('school_id', $schoolId)
            ->where('current_balance', '<', 0)
            ->count();

        // ====== آخرین پرداخت‌های امروز ======
        $todayPayments = \App\Models\Payment::where('school_id', $schoolId)
            ->whereDate('payment_date', today())
            ->when($activeYearId, fn($q) => $q->whereHas('studentFee', fn($q) => $q->where('academic_year_id', $activeYearId)))
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        // ====== آخرین تراکنش‌های کلی ======
        $recentTransactions = \App\Models\Payment::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->whereHas('studentFee', fn($q) => $q->where('academic_year_id', $activeYearId)))
            ->with('student')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        $academicYears = \App\Models\AcademicYear::orderBy('name', 'desc')->get();

        return view('school.dashboard', compact(
            'school',
            'totalStudents',
            'totalEmployees',
            'todayIncome',
            'totalExpenses',
            'cashboxBalance',
            'totalDebt',
            'debtorStudents',
            'unpaidSalaries',
            'negativeCashboxes',
            'todayPayments',
            'recentTransactions',
            'academicYears'
        ));
    }
}
