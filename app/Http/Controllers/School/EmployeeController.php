<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Models\EmployeeRole;
use App\Helpers\JalaliHelper;
use Illuminate\Http\Request;
use App\Models\AcademicYear;   // در بالای فایل اضافه کنید (اگر نیست)

class EmployeeController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

   public function index(Request $request)
{
    $schoolId = $this->getSchoolId();
    $activeYearId = session('active_academic_year_id');

    $query = Employee::where('school_id', $schoolId)
        ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
        ->with('employeeRole');

    if ($search = $request->input('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('employee_code', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    if ($role = $request->input('role_id')) {
        $query->where('employee_role_id', $role);
    }

    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    $employees = $query->orderBy('created_at', 'desc')->paginate(15);
    $roles = EmployeeRole::where('school_id', $schoolId)->orderBy('name')->get();

    // سال‌های مالی بعدی برای انتقال
    $activeYearId = session('active_academic_year_id');
    $nextYears = AcademicYear::where('school_id', $schoolId)
                    ->where('id', '!=', $activeYearId)
                    ->orderBy('start_date')
                    ->get();

    // ★ فقط یک return – همهٔ متغیرها در compact باشند
    return view('school.employees.index', compact('employees', 'roles', 'nextYears'));
}

    public function create()
    {
        $schoolId = $this->getSchoolId();
        $roles = EmployeeRole::where('school_id', $schoolId)->orderBy('name')->get();
        return view('school.employees.create', compact('roles'));
    }

   public function store(Request $request)
{
    // ⚠️ بررسی انتخاب سال مالی
    if (!session('active_academic_year_id')) {
        return redirect()->back()
            ->withInput()
            ->with('error', 'لطفاً ابتدا یک سال مالی را از بخش «سال‌های مالی» انتخاب کنید.');
    }

    $data = $request->validate([
        'first_name'        => 'required|string|max:100',
        'last_name'         => 'required|string|max:100',
        'father_name'       => 'required|string|max:100',
        'grandfather_name'  => 'nullable|string|max:100',
        'national_id'       => 'nullable|string|max:50',
        'birth_date'        => 'nullable|string',
        'gender'            => 'required|in:male,female',
        'phone'             => 'nullable|string|max:20',
        'secondary_phone'   => 'nullable|string|max:20',
        'address'           => 'nullable|string|max:500',
        'education_level'   => 'nullable|string|max:100',
        'field_of_study'    => 'nullable|string|max:100',
        'photo'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'employee_role_id'  => 'required|exists:employee_roles,id',
        'department'        => 'nullable|string|max:50',
        'hire_date'         => 'nullable|string',
        'contract_type'     => 'required|in:permanent,temporary',
        'base_salary'       => 'nullable|numeric|min:0',
        'status'            => 'required|in:active,inactive',
        'position_points'   => 'nullable|integer|min:0',
        'experience_points' => 'nullable|integer|min:0',
        'education_points'  => 'nullable|integer|min:0',
    ]);

    $data['school_id']        = $this->getSchoolId();
    $data['employee_code']    = $this->generateEmployeeCode();
    $data['academic_year_id'] = session('active_academic_year_id');

    if ($request->hasFile('photo')) {
        $data['photo'] = $request->file('photo')->store('employees', 'public');
    }

    if ($request->filled('employee_role_id')) {
        $role = EmployeeRole::find($request->employee_role_id);
        $data['position'] = $role ? $role->name : null;
    }

    if (!empty($data['birth_date'])) {
        $data['birth_date'] = JalaliHelper::toGregorian($data['birth_date'])->format('Y-m-d');
    }
    if (!empty($data['hire_date'])) {
        $data['hire_date'] = JalaliHelper::toGregorian($data['hire_date'])->format('Y-m-d');
    }

    Employee::create($data);

    return redirect()->route('school.employees.index')->with('success', 'کارمند جدید با موفقیت ثبت شد.');
}

    // ── سایر متدها (show, edit, update, destroy, preview, report, trash, ...) ──
    // دقیقاً همان‌طور که فرستادید، بدون تغییر باقی می‌مانند.
    // (من برای جلوگیری از طولانی شدن پاسخ فقط بخش‌های تغییر‌یافته را نوشتم.
    //  شما باید فایل کامل خود را نگه دارید و فقط index و store را با نسخه بالا جایگزین کنید.)




    public function show(Employee $employee)
    {
        $this->authorizeAccess($employee);
        $employee->load(['employeeRole', 'salaryStructure', 'salaries', 'salaryPayments', 'loans']);

        $baseSalary     = $employee->salaries->sum('base_salary');
        $overtimeAmount = $employee->salaries->sum('overtime_amount');
        $bonusAmount    = $employee->salaries->sum('bonus_amount');
        $deductionAmount= $employee->salaries->sum('deduction_amount');
        $taxAmount      = $employee->salaries->sum('tax_amount');
        $totalAmount    = $employee->salaries->sum('total_amount');
        $paidAmount     = $employee->salaryPayments->sum('amount');
        $balance        = max($totalAmount - $paidAmount, 0);

        $loans = $employee->loans;

        return view('school.employees.show', compact(
            'employee', 'baseSalary', 'overtimeAmount', 'bonusAmount',
            'deductionAmount', 'taxAmount', 'totalAmount', 'paidAmount', 'balance', 'loans'
        ));
    }

    public function edit(Employee $employee)
    {
        $this->authorizeAccess($employee);
        $roles = EmployeeRole::where('school_id', $this->getSchoolId())->orderBy('name')->get();

        // ← تبدیل تاریخ‌های میلادی به شمسی برای نمایش در فرم اضافه شد
        $employee->birth_date = $employee->birth_date ? JalaliHelper::toJalali($employee->birth_date, 'Y/m/d') : null;
        $employee->hire_date  = $employee->hire_date  ? JalaliHelper::toJalali($employee->hire_date, 'Y/m/d') : null;

        return view('school.employees.edit', compact('employee', 'roles'));
    }

    public function update(Request $request, Employee $employee)
    {
        $this->authorizeAccess($employee);

        $data = $request->validate([
            'first_name'        => 'required|string|max:100',
            'last_name'         => 'required|string|max:100',
            'father_name'       => 'required|string|max:100',
            'grandfather_name'  => 'nullable|string|max:100',
            'national_id'       => 'nullable|string|max:50',
            'birth_date'        => 'nullable|string',          // ← date به string تغییر کرد
            'gender'            => 'required|in:male,female',
            'phone'             => 'nullable|string|max:20',
            'secondary_phone'   => 'nullable|string|max:20',
            'address'           => 'nullable|string|max:500',
            'education_level'   => 'nullable|string|max:100',
            'field_of_study'    => 'nullable|string|max:100',
            'photo'             => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'employee_role_id'  => 'required|exists:employee_roles,id',
            'department'        => 'nullable|string|max:50',
            'hire_date'         => 'nullable|string',          // ← date به string تغییر کرد
            'contract_type'     => 'required|in:permanent,temporary',
            'base_salary'       => 'nullable|numeric|min:0',
            'status'            => 'required|in:active,inactive',
            'position_points'   => 'nullable|integer|min:0',
            'experience_points' => 'nullable|integer|min:0',
            'education_points'  => 'nullable|integer|min:0',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('employees', 'public');
        }

        if ($request->filled('employee_role_id')) {
            $role = EmployeeRole::find($request->employee_role_id);
            $data['position'] = $role ? $role->name : null;
        }

        // ← تبدیل تاریخ‌های شمسی به میلادی اضافه شد
        if (!empty($data['birth_date'])) {
            $data['birth_date'] = JalaliHelper::toGregorian($data['birth_date'])->format('Y-m-d');
        }
        if (!empty($data['hire_date'])) {
            $data['hire_date'] = JalaliHelper::toGregorian($data['hire_date'])->format('Y-m-d');
        }

        $data['gender'] = $request->input('gender');

        $employee->update($data);

        return redirect()->route('school.employees.index')->with('success', 'اطلاعات کارمند به‌روزرسانی شد.');
    }

    public function destroy(Employee $employee)
{
    $this->authorizeAccess($employee);

    // ۱. حذف تمام معاش‌های این کارمند (که خودش رویدادهای Salary و SalaryPayment را اجرا می‌کند)
    $employee->salaries()->delete();

    // ۲. حالا خود کارمند را حذف کن
    $employee->delete();

    return redirect()->route('school.employees.index')->with('success', 'کارمند و تمام معاش‌های مرتبط حذف شدند.');
}

    public function preview(Employee $employee)
    {
        $this->authorizeAccess($employee);
        $employee->load(['employeeRole', 'salaryStructure', 'salaries', 'salaryPayments', 'loans', 'school']);

        $baseSalary     = $employee->salaries->sum('base_salary');
        $overtimeAmount = $employee->salaries->sum('overtime_amount');
        $bonusAmount    = $employee->salaries->sum('bonus_amount');
        $deductionAmount= $employee->salaries->sum('deduction_amount');
        $taxAmount      = $employee->salaries->sum('tax_amount');
        $totalAmount    = $employee->salaries->sum('total_amount');
        $paidAmount     = $employee->salaryPayments->sum('amount');
        $balance        = max($totalAmount - $paidAmount, 0);

        $totalPoints = ($employee->position_points ?? 0)
                     + ($employee->experience_points ?? 0)
                     + ($employee->education_points ?? 0);

        $lastSalary = $employee->salaries()->latest()->first();
        $lastTaxAmount  = $lastSalary->tax_amount ?? 0;
        $lastGuarantee  = $lastSalary->guarantee_amount ?? 0;

        $calculatedNet = ($employee->base_salary ?? 0) + $totalPoints - $lastTaxAmount - $lastGuarantee;

        $loans = $employee->loans;

        $activeYear = null;
        if ($yearId = session('active_academic_year_id')) {
            $activeYear = \App\Models\AcademicYear::find($yearId);
        }

        return view('school.employees.preview', compact(
            'employee', 'baseSalary', 'overtimeAmount', 'bonusAmount',
            'deductionAmount', 'taxAmount', 'totalAmount', 'paidAmount', 'balance',
            'totalPoints', 'lastTaxAmount', 'lastGuarantee', 'calculatedNet',
            'loans', 'activeYear'
        ));
    }

    public function report()
    {
        $schoolId = $this->getSchoolId();
        $employees = Employee::where('school_id', $schoolId)->with('employeeRole')->get();

        $totalEmployees = $employees->count();
        $managers       = $employees->filter(fn($e) => $e->department === 'management' || stripos($e->employeeRole->name ?? '', 'مدیر') !== false)->count();
        $teachers       = $employees->filter(fn($e) => $e->department === 'teaching' || stripos($e->employeeRole->name ?? '', 'استاد') !== false)->count();
        $administrative = $employees->filter(fn($e) => $e->department === 'administrative' || stripos($e->employeeRole->name ?? '', 'حسابدار') !== false || stripos($e->employeeRole->name ?? '', 'اداری') !== false)->count();
        $service        = $employees->filter(fn($e) => $e->department === 'service' || stripos($e->employeeRole->name ?? '', 'خدمات') !== false)->count();
        $totalMonthlySalary = $employees->sum('base_salary');
        $totalOvertime      = \App\Models\Salary::where('school_id', $schoolId)->sum('overtime_amount');
        $totalDeductions    = \App\Models\Salary::where('school_id', $schoolId)->sum('deduction_amount');
        $totalPaid          = \App\Models\SalaryPayment::where('school_id', $schoolId)->sum('amount');

        return view('school.employees.report', compact(
            'totalEmployees', 'managers', 'teachers', 'administrative', 'service',
            'totalMonthlySalary', 'totalOvertime', 'totalDeductions', 'totalPaid'
        ));
    }

   public function trash()
{
    $schoolId = $this->getSchoolId();
    $activeYearId = session('active_academic_year_id');

    $employees = Employee::onlyTrashed()
        ->where('school_id', $schoolId)
        ->when($activeYearId, function ($query) use ($activeYearId) {
            $query->where('academic_year_id', $activeYearId);
        })
        ->when($activeYearId, function ($query) {
            $query->whereNotNull('academic_year_id');
        })
        ->with('employeeRole')
        ->orderBy('deleted_at', 'desc')
        ->paginate(15);

    return view('school.employees.trash', compact('employees'));
}
    public function restore($id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        if ($employee->school_id !== $this->getSchoolId()) abort(403);
        $employee->restore();

        return redirect()->route('school.employees.trash')->with('success', 'کارمند بازیابی شد.');
    }

    public function forceDelete($id)
    {
        $employee = Employee::onlyTrashed()->findOrFail($id);
        if ($employee->school_id !== $this->getSchoolId()) abort(403);
        $employee->forceDelete();

        return redirect()->route('school.employees.trash')->with('success', 'کارمند برای همیشه حذف شد.');
    }

    public function quickStoreRole(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        $role = EmployeeRole::create([
            'school_id' => $this->getSchoolId(),
            'name'      => $request->name,
            'is_active' => true,
        ]);

        return response()->json([
            'id'   => $role->id,
            'name' => $role->name,
        ]);
    }

/**
 * انتقال تکی کارمند
 */
public function transferSingle(Request $request, Employee $employee)
{
    $this->authorizeAccess($employee);
    $targetYearId = $request->input('target_year_id');
    $targetYear = AcademicYear::findOrFail($targetYearId);
    if ($targetYear->school_id != $this->getSchoolId()) abort(403);

    $newEmployee = $employee->replicate();
    $newEmployee->academic_year_id = $targetYearId;
    $newEmployee->employee_code = $this->generateEmployeeCode();
    $newEmployee->created_at = now();
    $newEmployee->updated_at = now();
    $newEmployee->save();

    return back()->with('success', "کارمند به سال {$targetYear->name} منتقل شد.");
}

/**
 * انتقال گروهی کارمندان
 */
public function transferMultiple(Request $request)
{
    $targetYearId = $request->input('target_year_id');
    $employeeIdsRaw = $request->input('employee_ids', '[]');

    // تبدیل JSON string به آرایه
    $employeeIds = json_decode($employeeIdsRaw, true);

    if (empty($employeeIds) || !$targetYearId) {
        return back()->with('error', 'هیچ کارمندی انتخاب نشده یا سال مقصد نامشخص است.');
    }

    $targetYear = AcademicYear::findOrFail($targetYearId);
    if ($targetYear->school_id != $this->getSchoolId()) abort(403);

    $employees = Employee::whereIn('id', $employeeIds)->get();
    $count = 0;
    foreach ($employees as $employee) {
        $newEmployee = $employee->replicate();
        $newEmployee->academic_year_id = $targetYearId;
        $newEmployee->employee_code = $this->generateEmployeeCode();
        $newEmployee->created_at = now();
        $newEmployee->updated_at = now();
        $newEmployee->save();
        $count++;
    }

    return back()->with('success', "{$count} کارمند به سال {$targetYear->name} منتقل شدند.");
}
    private function authorizeAccess(Employee $employee)
    {
        if ($employee->school_id !== $this->getSchoolId()) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }

    private function generateEmployeeCode(): string
    {
        $lastEmployee = Employee::withTrashed()
            ->orderBy('employee_code', 'desc')
            ->first();

        if ($lastEmployee && $lastEmployee->employee_code) {
            $lastNumber = (int) substr($lastEmployee->employee_code, 4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return sprintf('EMP-%03d', $newNumber);
    }
}
