<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolController extends Controller
{
    // ==========================================
    // index (اصلاح‌شده نهایی)
    // ==========================================
   public function index(Request $request)
    {
        $yearFilter   = $request->input('year_filter');
        $schoolFilter = $request->input('school_filter');

        // ──────────── ۱. پایه‌های کوئری ────────────
        $studentBase = \App\Models\Student::query()
            ->when($schoolFilter, fn($q) => $q->where('school_id', $schoolFilter))
            ->when($yearFilter,   fn($q) => $q->where('academic_year_id', $yearFilter));

        $employeeBase = \App\Models\Employee::query()
            ->when($schoolFilter, fn($q) => $q->where('school_id', $schoolFilter))
            ->when($yearFilter,   fn($q) => $q->where('academic_year_id', $yearFilter));

        $paymentBase = \App\Models\Payment::query()
            ->when($schoolFilter, fn($q) => $q->where('school_id', $schoolFilter))
            ->when($yearFilter,   fn($q) => $q->where('academic_year_id', $yearFilter));

        $incomeReceiptBase = \App\Models\IncomeReceipt::query()
            ->when($schoolFilter, fn($q) => $q->where('school_id', $schoolFilter))
            ->when($yearFilter,   fn($q) => $q->whereHas('income', fn($q) => $q->where('academic_year_id', $yearFilter)));

        $expensePaymentBase = \App\Models\ExpensePayment::query()
            ->when($schoolFilter, fn($q) => $q->where('school_id', $schoolFilter))
            ->when($yearFilter,   fn($q) => $q->whereHas('expense', fn($q) => $q->where('academic_year_id', $yearFilter)));

        $salaryPaymentBase = \App\Models\SalaryPayment::query()
            ->when($schoolFilter, fn($q) => $q->where('school_id', $schoolFilter))
            ->when($yearFilter,   fn($q) => $q->whereHas('salary', fn($q) => $q->where('academic_year_id', $yearFilter)));

        // ──────────── ۲. کارت‌های آماری ────────────
        $totalSchools    = School::count();
        $activeSchools   = School::where('is_active', true)->count();
        $inactiveSchools = School::where('is_active', false)->count();
        $totalStudents   = (clone $studentBase)->count();
        $totalEmployees  = (clone $employeeBase)->count();

        $totalIncome   = (clone $paymentBase)->sum('amount')
                       + (clone $incomeReceiptBase)->sum('amount');
        $totalExpenses = (clone $expensePaymentBase)->sum('amount')
                       + (clone $salaryPaymentBase)->sum('amount');

        $totalCashbox = \App\Models\Cashbox::when($schoolFilter, fn($q) => $q->where('school_id', $schoolFilter))
                        ->sum('current_balance');

        $totalFees  = \App\Models\StudentFee::when($yearFilter, fn($q) => $q->where('academic_year_id', $yearFilter))
                        ->when($schoolFilter, fn($q) => $q->whereHas('student', fn($q) => $q->where('school_id', $schoolFilter)))
                        ->sum(DB::raw('amount - discount'));
        $totalPaid  = (clone $paymentBase)->sum('amount');
        $totalDebt  = max($totalFees - $totalPaid, 0);

        // ──────────── ۳. وضعیت‌های دانش‌آموزان ────────────
        $studentStatuses = \App\Models\Student::select('status')->distinct()->orderBy('status')->pluck('status');

        // ──────────── ۴. دانش‌آموزان (با eager load فیلترشده) ────────────
        $studentQuery = (clone $studentBase)->with([
            'school',
            'guardian',
            'studentFees' => fn($q) => $yearFilter ? $q->where('academic_year_id', $yearFilter) : $q,
            'payments'    => fn($q) => $yearFilter ? $q->where('academic_year_id', $yearFilter) : $q,
        ]);

        if ($request->filled('student_status')) {
            $studentQuery->where('status', $request->input('student_status'));
        }
        if ($financial = $request->input('student_financial')) {
            if ($financial === 'discount') {
                $ids = \App\Models\StudentFee::where('discount', '>', 0)->pluck('student_id');
                $studentQuery->whereIn('id', $ids);
            } elseif ($financial === 'free') {
                $ids = \App\Models\Student::whereHas('studentFees', function ($q) {
                    $q->selectRaw('student_id, SUM(amount - discount) as total_fee');
                })->get()->filter(fn($s) => $s->studentFees->sum(fn($f) => $f->amount - $f->discount) == 0)->pluck('id');
                $studentQuery->whereIn('id', $ids);
            }
        }
        if ($search = $request->input('student_search')) {
            $studentQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('student_code', 'like', "%{$search}%");
            });
        }
        $students = $studentQuery->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

        // ──────────── ۵. کارمندان ────────────
        $employeeRoles = \App\Models\EmployeeRole::withCount('employees')->orderBy('name')->get();
        $employeeQuery = (clone $employeeBase)->with(['employeeRole', 'school']);
        if ($roleId = $request->input('employee_role')) {
            $employeeQuery->where('employee_role_id', $roleId);
        }
        if ($search = $request->input('employee_search')) {
            $employeeQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%");
            });
        }
        $employees = $employeeQuery->orderBy('created_at', 'desc')->paginate(10)->appends($request->query());

        // ──────────── ۶. خلاصهٔ دانش‌آموزان (برای تب) ────────────
        $studentSummaryQuery = (clone $studentBase);
        if ($request->filled('student_status')) { $studentSummaryQuery->where('status', $request->input('student_status')); }
        if ($search = $request->input('student_search')) {
            $studentSummaryQuery->where(function ($q) use ($search) {
                $q->where('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%");
            });
        }
        $filteredStudentIds = $studentSummaryQuery->pluck('id');
        $studentTotalFee    = \App\Models\StudentFee::whereIn('student_id', $filteredStudentIds)->sum(DB::raw('amount - discount'));
        $studentTotalPaid   = (clone $paymentBase)->whereIn('student_id', $filteredStudentIds)->sum('amount');
        $studentTotalDebt   = $studentTotalFee - $studentTotalPaid;
        $studentCount       = $filteredStudentIds->count();

        // ──────────── ۷. لیست مکاتب (فیلترشده) ────────────
        $schoolQuery = School::query();
        if ($schoolFilter) {
            $schoolQuery->where('id', $schoolFilter);
        }
        $schools = $schoolQuery->orderBy('created_at', 'desc')->paginate(15)->appends($request->query());

        foreach ($schools as $school) {
            $school->total_income    = (clone $paymentBase)->where('school_id', $school->id)->sum('amount')
                                    + (clone $incomeReceiptBase)->where('school_id', $school->id)->sum('amount');
            $school->total_expense   = (clone $expensePaymentBase)->where('school_id', $school->id)->sum('amount')
                                    + (clone $salaryPaymentBase)->where('school_id', $school->id)->sum('amount');
            $school->cashbox_balance = \App\Models\Cashbox::where('school_id', $school->id)->sum('current_balance');
            $school->debt_ratio      = ($school->total_income > 0) ? ($school->total_expense / $school->total_income) * 100 : 0;
        }

        // ──────────── ۸. داده‌های جانبی ────────────
        $allSchools    = School::orderBy('name')->get();
        $academicYears = \App\Models\AcademicYear::orderBy('name', 'desc')->get();

        // ──────────── ۹. آخرین فعالیت‌ها (فیلترشده) ────────────
        $activityQuery = \App\Models\ActivityLog::with('user', 'school')
            ->orderBy('created_at', 'desc');
        if ($schoolFilter) {
            $activityQuery->where('school_id', $schoolFilter);
        }
        $recentActivities = $activityQuery->take(5)->get();

        return view('admin.dashboard', compact(
            'totalSchools', 'activeSchools', 'inactiveSchools',
            'totalStudents', 'totalEmployees',
            'totalIncome', 'totalExpenses', 'totalCashbox', 'totalDebt',
            'students', 'employeeRoles', 'employees',
            'studentTotalFee', 'studentTotalPaid', 'studentTotalDebt', 'studentCount',
            'allSchools', 'academicYears', 'schools',
            'studentStatuses', 'recentActivities'
        ));
    }
    public function create()
    {
        return view('admin.schools.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'school_name'    => 'required|string|max:255',
            'school_code'    => 'required|string|max:50|unique:schools,code',
            'school_phone'   => 'nullable|string|max:20',
            'school_email'   => 'nullable|email|max:255',
            'school_address' => 'nullable|string',
            'admin_name'     => 'required|string|max:255',
            'admin_email'    => 'required|email|max:255|unique:users,email',
            'admin_password' => 'required|string|min:6',
        ]);

        $school = School::create([
            'name'    => $data['school_name'],
            'code'    => $data['school_code'],
            'phone'   => $data['school_phone'] ?? null,
            'email'   => $data['school_email'] ?? null,
            'address' => $data['school_address'] ?? null,
            'is_active' => true,
        ]);

        $admin = User::create([
            'school_id' => $school->id,
            'name'      => $data['admin_name'],
            'email'     => $data['admin_email'],
            'password'  => Hash::make($data['admin_password']),
            'phone'     => $data['school_phone'] ?? null,
            'is_active' => true,
        ]);

        $admin->assignRole('School Admin');

        return redirect()->route('admin.schools.index')->with('success', 'مکتب جدید با موفقیت ایجاد شد و مدیر آن ثبت گردید.');
    }

    // ==========================================
    // enter (بدون تغییر)
    // ==========================================
    public function enter(School $school)
    {
        session(['active_school_id' => $school->id]);

        $academicYears = \App\Models\AcademicYear::where('school_id', $school->id)
            ->orderBy('start_date', 'desc')
            ->get();

        if ($academicYears->isEmpty()) {
            return redirect()->route('school.academic-years.create')
                ->with('info', 'لطفاً ابتدا یک سال تحصیلی برای این مکتب ایجاد کنید.');
        }

        return view('school.select_academic_year', compact('academicYears', 'school'));
    }

    // ==========================================
    // edit (بدون تغییر)
    // ==========================================
    public function edit(School $school)
    {
        $schoolId = $school->id;
        $settings = [
            'phone'   => Setting::get('phone', '', $schoolId),
            'email'   => Setting::get('email', '', $schoolId),
            'address' => Setting::get('address', '', $schoolId),
            'logo'    => Setting::get('logo', '', $schoolId),
        ];

        $adminUser = User::where('school_id', $schoolId)
                        ->whereHas('roles', function ($q) {
                            $q->where('name', 'admin');
                        })
                        ->first();

        if (!$adminUser) {
            $adminUser = User::where('school_id', $schoolId)->first();
        }

        return view('admin.schools.edit', compact('school', 'settings', 'adminUser'));
    }

    // ==========================================
    // update (بدون تغییر)
    // ==========================================
    public function update(Request $request, School $school)
    {
        $data = $request->validate([
            'name'            => 'required|string|max:255',
            'phone'           => 'nullable|string|max:20',
            'contact_email'   => 'nullable|email|max:100',
            'address'         => 'nullable|string|max:500',
            'logo'            => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'admin_email'     => 'required|email',
            'admin_password'  => 'nullable|min:6',
        ]);

        $school->update(['name' => $data['name']]);

        Setting::set('phone', $data['phone'] ?? '', $school->id);
        Setting::set('email', $data['contact_email'] ?? '', $school->id);
        Setting::set('address', $data['address'] ?? '', $school->id);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            Setting::set('logo', $path, $school->id);
        }

        $adminUser = User::where('school_id', $school->id)
                        ->whereHas('roles', function ($q) {
                            $q->where('name', 'admin');
                        })
                        ->first();

        if (!$adminUser) {
            $adminUser = User::where('school_id', $school->id)->first();
        }

        if ($adminUser) {
            $adminUser->email = $data['admin_email'];
            if (!empty($data['admin_password'])) {
                $adminUser->password = Hash::make($data['admin_password']);
            }
            $adminUser->save();
        } else {
            User::create([
                'name'     => 'مدیر ' . $school->name,
                'email'    => $data['admin_email'],
                'password' => Hash::make($data['admin_password'] ?? '123456'),
                'school_id'=> $school->id,
            ]);
        }

        return redirect()->route('admin.schools.index')
            ->with('success', 'اطلاعات مدرسه با موفقیت به‌روزرسانی شد.');
    }

    // ==========================================
    // destroy, trash, restore, forceDelete (بدون تغییر)
    // ==========================================
    public function destroy(School $school)
    {
        $school->delete();
        return redirect()->route('admin.schools.index')
            ->with('success', 'مدرسه غیرفعال شد.');
    }

    public function trash()
    {
        $schools = School::onlyTrashed()->paginate(15);
        return view('admin.schools.trash', compact('schools'));
    }

    public function restore($id)
    {
        $school = School::onlyTrashed()->findOrFail($id);
        $school->restore();
        return redirect()->route('admin.schools.index')
            ->with('success', 'مدرسه دوباره فعال شد.');
    }

    public function forceDelete($id)
    {
        $school = School::onlyTrashed()->findOrFail($id);
        $school->forceDelete();

        return redirect()->route('admin.schools.trash')
            ->with('success', 'مدرسه برای همیشه حذف شد.');
    }
}
