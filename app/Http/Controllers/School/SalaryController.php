<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\SalaryPayment;
use App\Models\Employee;
use App\Models\Month;
use App\Models\Cashbox;
use App\Models\AcademicYear;   // ★
use App\Services\AccountingService;
use Illuminate\Http\Request;

class SalaryController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    /**
     * ★ اگر سال مالی انتخاب نشده باشد، آخرین سال را فعال می‌کند.
     */
    private function ensureAcademicYear()
    {
        if (!session('active_academic_year_id')) {
            $lastYear = AcademicYear::where('school_id', $this->getSchoolId())
                ->orderBy('start_date', 'desc')
                ->first();
            if ($lastYear) {
                session([
                    'active_academic_year_id'    => $lastYear->id,
                    'active_academic_year_name'  => $lastYear->name,
                    'active_academic_year_start' => $lastYear->start_date,
                    'active_academic_year_end'   => $lastYear->end_date,
                ]);
            }
        }
    }

    /**
     * نمایش لیست معاشات
     */
  public function index(Request $request)
{
    $schoolId = $this->getSchoolId();
    $activeYearId = session('active_academic_year_id');

    // تمام ماه‌های ممکن
    $allMonths = Month::where('school_id', $schoolId)->orderBy('number')->get();

    // تعیین بازهٔ ماه‌ها
    $monthFrom = $request->input('month_from');
    $monthTo   = $request->input('month_to');

    if ($monthFrom && $monthTo) {
        $from = Month::find($monthFrom);
        $to   = Month::find($monthTo);
        if ($from && $to && $from->number <= $to->number) {
            $months = $allMonths->where('number', '>=', $from->number)
                                ->where('number', '<=', $to->number)
                                ->values();
        } else {
            $months = $allMonths;
        }
    } else {
        // اگر بازه‌ای انتخاب نشده، همهٔ ماه‌ها را نشان بده
        $months = $allMonths;
    }

    // واکشی معاش‌های سال جاری
    $salaries = Salary::where('school_id', $schoolId)
        ->where('academic_year_id', $activeYearId)
        ->with(['employee.employeeRole', 'salaryPayments'])
        ->get();

    // گروه‌بندی بر اساس کارمند
    $grouped = $salaries->groupBy('employee_id');

    $matrix = [];
    foreach ($grouped as $empId => $empSalaries) {
        $employee = $empSalaries->first()->employee;
        $row = [
            'employee'       => $employee,
            'months'         => [],
            'totalAmount'    => 0,
            'totalPaid'      => 0,
            'totalRemaining' => 0,
        ];

        foreach ($months as $month) {
            $salary = $empSalaries->firstWhere('month_id', $month->id);
            $cell = [
                'amount'    => 0,
                'paid'      => 0,
                'remaining' => 0,
                'isPaid'    => false,
                'salary'    => null,
            ];

            if ($salary) {
                $paid = $salary->salaryPayments->sum('amount');
                $remaining = $salary->total_amount - $paid;
                $isPaid = $remaining <= 0;

                $cell = [
                    'amount'    => $salary->total_amount,
                    'paid'      => $paid,
                    'remaining' => $remaining,
                    'isPaid'    => $isPaid,
                    'salary'    => $salary,
                ];

                $row['totalAmount'] += $salary->total_amount;
                $row['totalPaid']   += $paid;
                $row['totalRemaining'] += $remaining;
            }

            $row['months'][$month->id] = $cell;
        }

        // فیلتر وضعیت پرداخت (در صورت انتخاب)
        $paymentStatus = $request->input('payment_status');
        if ($paymentStatus === 'paid' && $row['totalRemaining'] > 0) {
            continue;
        } elseif ($paymentStatus === 'unpaid' && $row['totalRemaining'] <= 0) {
            continue;
        }

        $matrix[] = $row;
    }

    // سایر متغیرهای لازم برای ویو
    $allEmployees = Employee::where('school_id', $schoolId)
        ->where('academic_year_id', $activeYearId)
        ->orderBy('first_name')
        ->get();

    $cashboxes = Cashbox::where('school_id', $schoolId)
        ->where('is_active', true)
        ->get();

    return view('school.salaries.index', compact(
        'matrix', 'months', 'allMonths', 'cashboxes', 'allEmployees'
    ));
}

    public function create()
    {
        $schoolId = $this->getSchoolId();
        $this->ensureAcademicYear();   // ★
        $activeYearId = session('active_academic_year_id');

        $employees = Employee::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)
            ->orderBy('first_name')
            ->get();

        $months = Month::where('school_id', $schoolId)->orderBy('number')->get();
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();
        $terms = \App\Models\Term::where('school_id', $schoolId)->orderBy('name')->get();

        return view('school.salaries.create', compact('employees', 'months', 'academicYears', 'terms'));
    }

   public function store(Request $request)
{
    $this->ensureAcademicYear();

    $data = $request->validate([
        'employee_id'       => 'required|exists:employees,id',
        'month_id'          => 'required|exists:months,id',
        'base_salary'       => 'required|numeric|min:0',
        'overtime_amount'   => 'nullable|numeric|min:0',
        'bonus_amount'      => 'nullable|numeric|min:0',
        'deduction_amount'  => 'nullable|numeric|min:0',
        'tax_amount'        => 'nullable|numeric|min:0',
        'guarantee_amount'  => 'nullable|numeric|min:0',
        'total_amount'      => 'nullable|numeric|min:0',   // ★ nullable
        'notes'             => 'nullable|string|max:500',
    ]);

    // اگر total_amount خالی بود، آن را محاسبه کن
    $total = $data['total_amount'] ?? 0;
    if ($total <= 0) {
        $total = ($data['base_salary'] ?? 0)
               + ($data['overtime_amount'] ?? 0)
               + ($data['bonus_amount'] ?? 0)
               - ($data['deduction_amount'] ?? 0)
               - ($data['tax_amount'] ?? 0)
               - ($data['guarantee_amount'] ?? 0);
    }

    $data['school_id']         = $this->getSchoolId();
    $data['academic_year_id']  = session('active_academic_year_id');
    $data['total_amount']      = max(0, $total);   // ★ اطمینان از مثبت بودن
    $data['paid_amount']       = 0;
    $data['status']            = 'due';

    Salary::create($data);

    return redirect()->route('school.salaries.index')
        ->with('success', 'معاش با موفقیت ثبت شد.');
}
    public function show(Salary $salary)
    {
        $this->authorizeAccess($salary);
        $salary->load(['employee', 'month', 'salaryPayments']);
        return view('school.salaries.show', compact('salary'));
    }

    public function edit(Salary $salary)
    {
        $this->authorizeAccess($salary);
        $this->ensureAcademicYear();   // ★
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');

        $employees = Employee::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)
            ->orderBy('first_name')
            ->get();
        $months = Month::where('school_id', $schoolId)->orderBy('number')->get();
        $academicYears = AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get();
        $terms = \App\Models\Term::where('school_id', $schoolId)->orderBy('name')->get();

        return view('school.salaries.edit', compact('salary', 'employees', 'months', 'academicYears', 'terms'));
    }

    public function update(Request $request, Salary $salary)
    {
        $this->authorizeAccess($salary);
        $data = $request->validate([
            'employee_id'       => 'required|exists:employees,id',
            'month_id'          => 'required|exists:months,id',
            'base_salary'       => 'required|numeric|min:0',
            'overtime_amount'   => 'nullable|numeric|min:0',
            'bonus_amount'      => 'nullable|numeric|min:0',
            'deduction_amount'  => 'nullable|numeric|min:0',
            'tax_amount'        => 'nullable|numeric|min:0',
            'guarantee_amount'  => 'nullable|numeric|min:0',
            'total_amount'      => 'required|numeric|min:0',
            'notes'             => 'nullable|string|max:500',
        ]);

        $salary->update($data);

        return redirect()->route('school.salaries.index')
            ->with('success', 'معاش با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Salary $salary, AccountingService $accounting)
    {
        if ($salary->school_id !== $this->getSchoolId()) {
            abort(403);
        }

        foreach ($salary->salaryPayments as $payment) {
            $accounting->deleteSalaryPayment($payment);
        }

        $salary->delete();

        return redirect()->route('school.salaries.index')
            ->with('success', 'معاش و پرداخت‌های مرتبط با موفقیت حذف شدند.');
    }

    private function authorizeAccess(Salary $salary)
    {
        if ($salary->school_id !== $this->getSchoolId()) {
            abort(403);
        }
    }
}
