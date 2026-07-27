<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\EmployeeAdvance;
use App\Models\Employee;
use App\Models\Month;
use App\Helpers\JalaliHelper;
use Illuminate\Http\Request;

class EmployeeAdvanceController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    // لیست پیش‌پرداخت‌ها
    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $query = EmployeeAdvance::where('school_id', $schoolId)
                  ->with(['employee', 'month']);

        if ($employeeId = $request->input('employee_id')) {
            $query->where('employee_id', $employeeId);
        }

        $advances = $query->orderBy('advance_date', 'desc')->paginate(15);
        $employees = Employee::where('school_id', $schoolId)
                      ->orderBy('first_name')
                      ->get();

        return view('school.employee-advances.index', compact('advances', 'employees'));
    }

    // فرم ایجاد پیش‌پرداخت جدید
    public function create()
    {
        $schoolId = $this->getSchoolId();
        $employees = Employee::where('school_id', $schoolId)
                      ->orderBy('first_name')
                      ->get();
        $months = Month::where('school_id', $schoolId)
                  ->orderBy('number')
                  ->get();

        return view('school.employee-advances.create', compact('employees', 'months'));
    }

    // ذخیره پیش‌پرداخت
    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id'  => 'required|exists:employees,id',
            'month_id'     => 'required|exists:months,id',
            'amount'       => 'required|numeric|min:1',
            'advance_date' => 'required|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        // تبدیل تاریخ شمسی به میلادی
        $gregorian = JalaliHelper::toGregorian($data['advance_date']);
        $data['advance_date'] = $gregorian->format('Y-m-d');
        $data['school_id'] = $this->getSchoolId();

        EmployeeAdvance::create($data);

        return redirect()->route('school.employee-advances.index')
            ->with('success', 'پیش‌پرداخت با موفقیت ثبت شد.');
    }

    // فرم ویرایش
    public function edit(EmployeeAdvance $employeeAdvance)
    {
        $this->authorizeAccess($employeeAdvance);
        $schoolId = $this->getSchoolId();
        $employees = Employee::where('school_id', $schoolId)
                      ->orderBy('first_name')
                      ->get();
        $months = Month::where('school_id', $schoolId)
                  ->orderBy('number')
                  ->get();

        // تبدیل تاریخ میلادی به شمسی برای نمایش
        $employeeAdvance->advance_date = JalaliHelper::toJalali($employeeAdvance->advance_date, 'Y/m/d');

        return view('school.employee-advances.edit', compact('employeeAdvance', 'employees', 'months'));
    }

    // به‌روزرسانی
    public function update(Request $request, EmployeeAdvance $employeeAdvance)
    {
        $this->authorizeAccess($employeeAdvance);
        $data = $request->validate([
            'employee_id'  => 'required|exists:employees,id',
            'month_id'     => 'required|exists:months,id',
            'amount'       => 'required|numeric|min:1',
            'advance_date' => 'required|date',
            'notes'        => 'nullable|string|max:500',
        ]);

        $gregorian = JalaliHelper::toGregorian($data['advance_date']);
        $data['advance_date'] = $gregorian->format('Y-m-d');

        $employeeAdvance->update($data);

        return redirect()->route('school.employee-advances.index')
            ->with('success', 'پیش‌پرداخت به‌روزرسانی شد.');
    }

    // حذف
    public function destroy(EmployeeAdvance $employeeAdvance)
    {
        $this->authorizeAccess($employeeAdvance);
        $employeeAdvance->delete();
        return redirect()->route('school.employee-advances.index')
            ->with('success', 'پیش‌پرداخت حذف شد.');
    }

    // رسید چاپی
    public function receipt(EmployeeAdvance $employeeAdvance)
    {
        $this->authorizeAccess($employeeAdvance);
        $employeeAdvance->load(['employee', 'month']);
        return view('school.employee-advances.receipt', compact('employeeAdvance'));
    }

    // بررسی دسترسی
    private function authorizeAccess(EmployeeAdvance $advance)
    {
        if ($advance->school_id !== $this->getSchoolId()) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }
    public function advanceSum(Request $request)
{
    $request->validate([
        'employee_id' => 'required|exists:employees,id',
        'month_id'    => 'required|exists:months,id',
    ]);

    $sum = EmployeeAdvance::where('employee_id', $request->employee_id)
            ->where('month_id', $request->month_id)
            ->sum('amount');

    return response()->json(['sum' => $sum]);
}
}
