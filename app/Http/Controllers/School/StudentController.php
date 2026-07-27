<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentGuardian;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\JalaliHelper;
use App\Models\Payment;
use App\Models\StudentFee;
use App\Services\AccountingService;
use App\Models\AcademicYear;

class StudentController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    private function shareStudentStats()
    {
        $schoolId = $this->getSchoolId();
        $baseQuery = Student::where('school_id', $schoolId);

        $totalActive = (clone $baseQuery)->count();

        $debtorIds = (clone $baseQuery)->whereHas('studentFees', function ($q) {
            $q->selectRaw('student_id, SUM(amount - discount) as total_fee');
        })->withSum('payments', 'amount')
          ->get()
          ->filter(fn ($s) => ($s->studentFees->sum(fn($f) => $f->amount - $f->discount) > $s->payments_sum_amount))
          ->pluck('id');
        $debtorCount = $debtorIds->count();

        $discountIds = \App\Models\StudentFee::whereHas('student', fn($q) => $q->where('school_id', $schoolId))
                        ->where('discount', '>', 0)->pluck('student_id');
        $discountCount = (clone $baseQuery)->whereIn('id', $discountIds)->count();

        $orphanCount = (clone $baseQuery)->where('status', 'three_piece')->count();
        $leftCount = (clone $baseQuery)->whereIn('status', ['graduated', 'transferred'])->count();
        $trashCount = Student::onlyTrashed()->where('school_id', $schoolId)->count();

        $freeStudentIds = \App\Models\Student::where('school_id', $schoolId)
            ->where(function ($query) {
                $query->doesntHave('studentFees')
                      ->orWhereHas('studentFees', function ($q) {
                          $q->select('student_id')
                            ->selectRaw('SUM(amount - discount) as total_fee')
                            ->groupBy('student_id')
                            ->havingRaw('SUM(amount - discount) = 0');
                      });
            })->pluck('id');
        $freeCount = $freeStudentIds->count();

        $fullFeeCount = (clone $baseQuery)
                        ->whereNotIn('id', $discountIds)
                        ->whereNotIn('id', $freeStudentIds)
                        ->count();

        view()->share('studentStats', [
            'total'    => $totalActive,
            'debtor'   => $debtorCount,
            'discount' => $discountCount,
            'orphan'   => $orphanCount,
            'left'     => $leftCount,
            'trash'    => $trashCount,
            'free'     => $freeCount,
            'fullFee'  => $fullFeeCount,
        ]);
    }

   public function index(Request $request)
{
    $this->shareStudentStats();
    $schoolId = $this->getSchoolId();
    $activeYearId = session('active_academic_year_id');

    $query = Student::where('school_id', $schoolId)
        ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
        ->with('guardian');

    if ($status = $request->input('status_filter')) {
        if ($status === 'active') {
            $query->where('status', 'present');
        } elseif ($status === 'inactive') {
            $query->where('status', '!=', 'present');
        }
    }

    if ($financial = $request->input('financial_filter')) {
        if ($financial === 'discount') {
            $query->whereHas('studentFees', function ($q) {
                $q->where('discount', '>', 0);
            });
        } elseif ($financial === 'free') {
            $query->where(function ($q) use ($schoolId) {
                $q->doesntHave('studentFees');
            })->orWhere(function ($q) use ($schoolId) {
                $freeIds = \App\Models\StudentFee::whereHas('student', fn($s) => $s->where('school_id', $schoolId))
                            ->select('student_id')
                            ->selectRaw('SUM(amount - discount) as total_fee')
                            ->groupBy('student_id')
                            ->havingRaw('SUM(amount - discount) = 0')
                            ->pluck('student_id');
                $q->whereIn('id', $freeIds);
            });
        } elseif ($financial === 'full') {
            $discountIds = \App\Models\StudentFee::whereHas('student', fn($s) => $s->where('school_id', $schoolId))
                            ->where('discount', '>', 0)->pluck('student_id');
            $freeIds = \App\Models\StudentFee::whereHas('student', fn($s) => $s->where('school_id', $schoolId))
                        ->select('student_id')
                        ->selectRaw('SUM(amount - discount) as total_fee')
                        ->groupBy('student_id')
                        ->havingRaw('SUM(amount - discount) = 0')
                        ->pluck('student_id');
            $query->whereNotIn('id', $discountIds)->whereNotIn('id', $freeIds);
        }
    }

    if ($filter = $request->input('filter')) {
        switch ($filter) {
            case 'bedehkar':
                $debtorIds = \App\Models\Student::where('school_id', $schoolId)
                    ->whereHas('studentFees')
                    ->withSum('payments', 'amount')
                    ->get()
                    ->filter(function ($student) {
                        $totalFees = $student->studentFees->sum(DB::raw('amount - discount'));
                        return ($totalFees > ($student->payments_sum_amount ?? 0));
                    })
                    ->pluck('id');
                $query->whereIn('id', $debtorIds);
                break;
            case 'takhfif':
                $query->whereHas('studentFees', function ($q) {
                    $q->where('discount', '>', 0);
                });
                break;
            case 'yatim':
                $query->where('status', 'three_piece');
                break;
            case 'faret':
                $query->whereIn('status', ['graduated', 'transferred']);
                break;
            case 'free':
                $query->where(function ($q) use ($schoolId) {
                    $q->doesntHave('studentFees');
                })->orWhere(function ($q) use ($schoolId) {
                    $freeIds = \App\Models\StudentFee::whereHas('student', fn($s) => $s->where('school_id', $schoolId))
                                ->select('student_id')
                                ->selectRaw('SUM(amount - discount) as total_fee')
                                ->groupBy('student_id')
                                ->havingRaw('SUM(amount - discount) = 0')
                                ->pluck('student_id');
                    $q->whereIn('id', $freeIds);
                });
                break;
            case 'present':
                $query->where('status', 'present');
                break;
            case 'absent':
                $query->where('status', '!=', 'present');
                break;
            case 'full_fee':
                $discountIds = \App\Models\StudentFee::whereHas('student', fn($s) => $s->where('school_id', $schoolId))
                                ->where('discount', '>', 0)->pluck('student_id');
                $freeIds = \App\Models\StudentFee::whereHas('student', fn($s) => $s->where('school_id', $schoolId))
                            ->select('student_id')
                            ->selectRaw('SUM(amount - discount) as total_fee')
                            ->groupBy('student_id')
                            ->havingRaw('SUM(amount - discount) = 0')
                            ->pluck('student_id');
                $query->whereNotIn('id', $discountIds)->whereNotIn('id', $freeIds);
                break;
            case 'senfi':
                // اگر فیلتر صنف خاصی انتخاب شده، اعمال کن
                if ($classFilter = $request->input('class_filter')) {
                    $query->where('class', $classFilter);
                }

                $studentsByClass = $query->orderBy('class')->orderBy('first_name')->get()->groupBy('class');

                // لیست کلاس‌ها فقط مربوط به سال تحصیلی جاری
                $classes = Student::where('school_id', $schoolId)
                    ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
                    ->select('class')->distinct()->orderBy('class')->pluck('class');

                return view('school.students.by-class', compact('studentsByClass', 'classes'));
        }
    }

    if ($search = $request->input('search')) {
        $query->where(function ($q) use ($search) {
            $q->where('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%")
              ->orWhere('student_code', 'like', "%{$search}%")
              ->orWhere('national_id', 'like', "%{$search}%")
              ->orWhere('base_number', 'like', "%{$search}%");
        });
    }

    $students = $query->orderBy('created_at', 'desc')->paginate(15);
    $studentsCount = $students->total();

    $nextYears = AcademicYear::where('school_id', $schoolId)
                    ->where('id', '!=', $activeYearId)
                    ->orderBy('start_date')
                    ->get();

    return view('school.students.index', compact('students', 'studentsCount', 'nextYears'));
}

    public function create()
    {
        $guardians = StudentGuardian::where('school_id', $this->getSchoolId())->get();
        return view('school.students.create', compact('guardians'));
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
        'first_name'      => 'required|string|max:100',
        'last_name'       => 'required|string|max:100',
        'father_name'     => 'required|string|max:100',
        'grandfather_name'=> 'nullable|string|max:100',
        'national_id'     => 'required|string|max:50',
        'base_number'     => 'nullable|string|max:50',
        'birth_date'      => 'nullable|string',
        'gender'          => 'required|in:male,female',
        'class'           => 'nullable|string|max:20',
        'original_residence'=> 'nullable|string|max:255',
        'address'         => 'nullable|string|max:500',
        'father_phone'    => 'nullable|string|max:20',
        'whatsapp_phone'  => 'nullable|string|max:20',
        'status'          => 'required|in:present,blocked',
        'financial_status'=> 'nullable|in:full,discount,free',
        'is_orphan'       => 'nullable|boolean',
        'photo'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        'new_guardian_name'     => 'nullable|string|max:255',
        'new_guardian_relation' => 'nullable|string|max:50',
        'guardian_education'    => 'nullable|string|max:100',
        'guardian_job'          => 'nullable|string|max:100',
        'new_guardian_phone'    => 'nullable|string|max:20',
        'new_guardian_address'  => 'nullable|string|max:500',
    ]);

    $photoPath = null;
    if ($request->hasFile('photo')) {
        $photoPath = $request->file('photo')->store('students', 'public');
    }

    if ($request->filled('new_guardian_name')) {
        $guardian = StudentGuardian::create([
            'school_id'        => $this->getSchoolId(),
            'full_name'        => $request->new_guardian_name,
            'relation'         => $request->new_guardian_relation,
            'phone'            => $request->new_guardian_phone,
            'education'        => $request->guardian_education,
            'job'              => $request->guardian_job,
            'address'          => $request->new_guardian_address,
            'secondary_phone'  => null,
            'is_primary'       => true,
            'academic_year_id' => session('active_academic_year_id'),
        ]);
        $data['guardian_id'] = $guardian->id;
    }

    if ($request->boolean('is_orphan')) {
        $data['status'] = 'three_piece';
    }

    if (!empty($data['birth_date'])) {
        $data['birth_date'] = JalaliHelper::toGregorian($data['birth_date'])->format('Y-m-d');
    }
    $enrollmentJalali = $request->input('enrollment_date', JalaliHelper::todayJalali());
    $data['enrollment_date'] = JalaliHelper::toGregorian($enrollmentJalali)->format('Y-m-d');

    $data['school_id']      = $this->getSchoolId();
    $data['academic_year_id'] = session('active_academic_year_id');
    $data['student_code']   = $this->generateStudentCode();
    $data['photo']          = $photoPath;
    $data['phone']          = $request->father_phone;
    $data['financial_status'] = $request->financial_status;
    $data['is_orphan']      = $request->boolean('is_orphan');

    Student::create($data);

    return redirect()->route('school.students.index')->with('success', 'دانش‌آموز با موفقیت ثبت شد.');
}

    public function show(Student $student)
    {
        $this->authorizeSchoolAccess($student);
        $student->load(['guardian', 'enrollments', 'studentFees.feeType', 'studentFees.month', 'payments']);
        return view('school.students.show', compact('student'));
    }

    public function edit(Student $student)
    {
        $this->authorizeSchoolAccess($student);
        $guardians = StudentGuardian::where('school_id', $this->getSchoolId())->get();

        $student->birth_date = $student->birth_date
            ? JalaliHelper::toJalali($student->birth_date, 'Y/m/d')
            : null;
        $student->enrollment_date = $student->enrollment_date
            ? JalaliHelper::toJalali($student->enrollment_date, 'Y/m/d')
            : null;

        return view('school.students.edit', compact('student', 'guardians'));
    }

    public function update(Request $request, Student $student)
    {
        $this->authorizeSchoolAccess($student);
        $data = $request->validate([
            'first_name'      => 'required|string|max:100',
            'last_name'       => 'required|string|max:100',
            'father_name'     => 'required|string|max:100',
            'grandfather_name'=> 'nullable|string|max:100',
            'birth_date'      => 'nullable|string',
            'national_id'     => 'required|string|max:50',
            'base_number'     => 'nullable|string|max:50',
            'phone'           => 'nullable|string|max:20',
            'class'           => 'nullable|string|max:20',
            'gender'          => 'required|in:male,female',
            'address'         => 'nullable|string',
            'enrollment_date' => 'nullable|string',
            'guardian_id'     => 'nullable|exists:student_guardians,id',
            'status'          => 'required|in:present,blocked,temporary,three_piece',
            'financial_status'=> 'nullable|in:full,discount,free',
            'is_orphan'       => 'nullable|boolean',
            'photo'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'new_guardian_name'     => 'nullable|string|max:255',
            'new_guardian_relation' => 'nullable|string|max:50',
            'guardian_education'    => 'nullable|string|max:100',
            'guardian_job'          => 'nullable|string|max:100',
            'new_guardian_phone'    => 'nullable|string|max:20',
            'new_guardian_address'  => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('students', 'public');
        }

        if ($request->filled('new_guardian_name')) {
            $guardian = $student->guardian;
            if ($guardian) {
                $guardian->update([
                    'full_name' => $request->new_guardian_name,
                    'relation'  => $request->new_guardian_relation,
                    'phone'     => $request->new_guardian_phone,
                    'education' => $request->guardian_education,
                    'job'       => $request->guardian_job,
                    'address'   => $request->new_guardian_address,
                ]);
            } else {
                $guardian = StudentGuardian::create([
                    'school_id'        => $this->getSchoolId(),
                    'full_name'        => $request->new_guardian_name,
                    'relation'         => $request->new_guardian_relation,
                    'phone'            => $request->new_guardian_phone,
                    'education'        => $request->guardian_education,
                    'job'              => $request->guardian_job,
                    'address'          => $request->new_guardian_address,
                    'secondary_phone'  => null,
                    'is_primary'       => true,
                ]);
                $data['guardian_id'] = $guardian->id;
            }
        }

        if ($request->boolean('is_orphan')) {
            $data['status'] = 'three_piece';
        }

        if (!empty($data['birth_date'])) {
            $data['birth_date'] = JalaliHelper::toGregorian($data['birth_date'])->format('Y-m-d');
        }
        if (!empty($data['enrollment_date'])) {
            $data['enrollment_date'] = JalaliHelper::toGregorian($data['enrollment_date'])->format('Y-m-d');
        }

        $data['phone'] = $request->father_phone;
        $data['financial_status'] = $request->financial_status;
        $data['is_orphan'] = $request->boolean('is_orphan');

        $student->update($data);
        return redirect()->route('school.students.index')->with('success', 'اطلاعات دانش‌آموز به‌روزرسانی شد.');
    }

    public function destroy(Student $student, AccountingService $accounting)
    {
        $this->authorizeSchoolAccess($student);

        $payments = Payment::where('student_id', $student->id)->get();
        foreach ($payments as $payment) {
            $accounting->deletePayment($payment);
        }

        StudentFee::where('student_id', $student->id)->delete();
        $student->delete();

        return redirect()->route('school.students.index')
            ->with('success', 'دانش‌آموز و تمام داده‌های مالی مرتبط حذف شدند.');
    }

 public function trash()
{
    $this->shareStudentStats();
    $schoolId = $this->getSchoolId();
    $activeYearId = session('active_academic_year_id');

    $students = Student::onlyTrashed()
        ->where('school_id', $schoolId)
        ->when($activeYearId, function ($query) use ($activeYearId) {
            $query->where('academic_year_id', $activeYearId);
        })
        ->when($activeYearId, function ($query) {
            $query->whereNotNull('academic_year_id');
        })
        ->orderBy('deleted_at', 'desc')
        ->paginate(15);

    return view('school.students.trash', compact('students'));
}

    public function restore($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        if ($student->school_id !== $this->getSchoolId()) abort(403);
        $student->restore();
        return redirect()->route('school.students.trash')->with('success', 'دانش‌آموز بازیابی شد.');
    }

    public function forceDelete($id)
    {
        $student = Student::onlyTrashed()->findOrFail($id);
        if ($student->school_id !== $this->getSchoolId()) abort(403);

        $student->forceDelete();
        return redirect()->route('school.students.trash')->with('success', 'دانش‌آموز برای همیشه حذف شد.');
    }

    public function preview(Student $student)
    {
        $this->authorizeSchoolAccess($student);
        $student->load(['guardian', 'school']);
        return view('school.students.preview', compact('student'));
    }

    public function report(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $query = Student::where('school_id', $schoolId)->with(['guardian', 'studentFees.month', 'payments']);

        if ($class = $request->input('class_filter')) {
            $query->where('class', $class);
        }

        $students = $query->orderBy('class')->orderBy('first_name')->get();

        $allMonths = \App\Models\Month::where('school_id', $schoolId)->orderBy('number')->get();

        foreach ($students as $student) {
            $unpaidMonths = [];
            foreach ($allMonths as $month) {
                $fee = $student->studentFees->firstWhere('month_id', $month->id);
                if ($fee) {
                    $paidForMonth = $student->payments->where('fee_id', $fee->id)->sum('amount');
                    $remaining = ($fee->amount - $fee->discount) - $paidForMonth;
                    if ($remaining > 0) {
                        $unpaidMonths[] = $month->name;
                    }
                }
            }
            $student->unpaidMonths = $unpaidMonths;
        }

        $classes = Student::where('school_id', $schoolId)->select('class')->distinct()->orderBy('class')->pluck('class');
        $selectedClass = $class ?? null;

        return view('school.students.report', compact('students', 'classes', 'selectedClass', 'allMonths'));
    }

    // ========== انتقال‌ها ==========

    public function transferSingle(Request $request, Student $student)
    {
        $this->authorizeSchoolAccess($student);
        $targetYearId = $request->input('target_year_id');
        $targetYear = AcademicYear::findOrFail($targetYearId);
        if ($targetYear->school_id != $this->getSchoolId()) abort(403);

        $newStudent = $student->replicate();

        if ($student->guardian_id && $student->guardian) {
            $newGuardian = $student->guardian->replicate();
            $newGuardian->academic_year_id = $targetYearId;
            $newGuardian->created_at = now();
            $newGuardian->updated_at = now();
            $newGuardian->save();
            $newStudent->guardian_id = $newGuardian->id;
        }

        $newStudent->academic_year_id = $targetYearId;
        $newStudent->class = $this->promoteClass($student->class);
        $newStudent->student_code = $this->generateStudentCode();
        $newStudent->created_at = now();
        $newStudent->updated_at = now();
        $newStudent->save();

        return back()->with('success', "دانش‌آموز به سال {$targetYear->name} منتقل شد.");
    }

    public function transferMultiple(Request $request)
    {
        $targetYearId = $request->input('target_year_id');
        $studentIdsRaw = $request->input('student_ids', '[]');
        $studentIds = json_decode($studentIdsRaw, true);

        if (empty($studentIds) || !$targetYearId) {
            return back()->with('error', 'هیچ دانش‌آموزی انتخاب نشده یا سال مقصد نامشخص است.');
        }

        $targetYear = AcademicYear::findOrFail($targetYearId);
        if ($targetYear->school_id != $this->getSchoolId()) abort(403);

        $students = Student::whereIn('id', $studentIds)->get();
        $count = 0;
        foreach ($students as $student) {
            $newStudent = $student->replicate();

            if ($student->guardian_id && $student->guardian) {
                $newGuardian = $student->guardian->replicate();
                $newGuardian->academic_year_id = $targetYearId;
                $newGuardian->created_at = now();
                $newGuardian->updated_at = now();
                $newGuardian->save();
                $newStudent->guardian_id = $newGuardian->id;
            }

            $newStudent->academic_year_id = $targetYearId;
            $newStudent->class = $this->promoteClass($student->class);
            $newStudent->student_code = $this->generateStudentCode();
            $newStudent->created_at = now();
            $newStudent->updated_at = now();
            $newStudent->save();
            $count++;
        }

        return back()->with('success', "{$count} دانش‌آموز به سال {$targetYear->name} منتقل شدند.");
    }

    private function promoteClass(?string $class): ?string
    {
        if (is_numeric($class)) {
            return (string)((int)$class + 1);
        }

        $map = [
            'اول' => 'دوم', 'دوم' => 'سوم', 'سوم' => 'چهارم',
            'چهارم' => 'پنجم', 'پنجم' => 'ششم', 'ششم' => 'هفتم',
            'هفتم' => 'هشتم', 'هشتم' => 'نهم', 'نهم' => 'دهم',
            'دهم' => 'یازدهم', 'یازدهم' => 'دوازدهم',
        ];

        return $map[$class] ?? $class;
    }

    private function authorizeSchoolAccess(Student $student)
    {
        if ($student->school_id !== $this->getSchoolId()) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }

    private function generateStudentCode(): string
    {
        $schoolId = $this->getSchoolId();
        $year = date('Y') - 621;
        $lastStudent = Student::withTrashed()
            ->where('school_id', $schoolId)
            ->where('student_code', 'like', "STU-{$year}-%")
            ->orderBy('student_code', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->student_code, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        $code = sprintf('STU-%d-%04d', $year, $newNumber);

        while (Student::withTrashed()->where('school_id', $schoolId)->where('student_code', $code)->exists()) {
            $newNumber++;
            $code = sprintf('STU-%d-%04d', $year, $newNumber);
        }

        return $code;
    }

    public function byClass(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');
        $class = $request->input('class');

        $students = Student::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->when($class, fn($q) => $q->where('class', $class))
            ->select('id', 'first_name', 'last_name', 'class', 'student_code')
            ->orderBy('first_name')
            ->get();

        return response()->json($students);
    }
}
