<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\StudentFee;
use App\Models\Student;
use App\Models\FeeType;
use App\Models\Month;
use App\Models\Cashbox;
use App\Models\Payment;
use Illuminate\Http\Request;
use App\Services\AccountingService;

class StudentFeeController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    // ========== لیست شهریه‌ها (نمای ماتریسی) ==========
    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');  // ★

        $classFilter   = $request->input('class_filter');
        $monthFrom     = $request->input('month_from');
        $monthTo       = $request->input('month_to');
        $paymentStatus = $request->input('payment_status');

        // اگر هیچ فیلتری ارسال نشده باشد، از آخرین بازهٔ ذخیره‌شده در session استفاده کن
        if (!$request->hasAny(['class_filter', 'month_from', 'month_to', 'payment_status'])) {
            $monthFrom = session('last_fee_month_from');
            $monthTo   = session('last_fee_month_to');
        }

        $allMonths = Month::where('school_id', $schoolId)->orderBy('number')->get();

        // تعیین ماه‌های قابل نمایش
        if ($monthFrom && $monthTo) {
            $fromMonth = Month::find($monthFrom);
            $toMonth   = Month::find($monthTo);
            if ($fromMonth && $toMonth && $fromMonth->number <= $toMonth->number) {
                $months = $allMonths->where('number', '>=', $fromMonth->number)
                                    ->where('number', '<=', $toMonth->number)
                                    ->values();
            } else {
                $months = $allMonths;
            }
        } else {
            $existingMonthIds = StudentFee::where('school_id', $schoolId)
                ->where('academic_year_id', $activeYearId)  // ★
                ->pluck('month_id')->unique()->filter()->toArray();
            $months = $allMonths->whereIn('id', $existingMonthIds)->values();
        }

        // ★ دانش‌آموزان (فیلتر سال)
        $studentsQuery = Student::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)  // ★
            ->when($classFilter, fn($q) => $q->where('class', $classFilter))
            ->with(['studentFees', 'payments'])
            ->orderBy('class')
            ->orderBy('first_name');

        // فیلتر وضعیت پرداخت
        if ($paymentStatus && $months->isNotEmpty()) {
            $monthIds = $months->pluck('id')->toArray();
            $studentsQuery->where(function ($q) use ($monthIds, $paymentStatus) {
                if ($paymentStatus === 'unpaid') {
                    $q->whereHas('studentFees', fn($f) => $f->whereIn('month_id', $monthIds))
                      ->whereRaw('(select COALESCE(SUM(payments.amount),0) from payments where payments.student_id = students.id and payments.month_id in (' . implode(',', $monthIds) . ')) < (select SUM(amount - discount) from student_fees where student_fees.student_id = students.id and student_fees.month_id in (' . implode(',', $monthIds) . ') and student_fees.deleted_at is null)');
                } elseif ($paymentStatus === 'paid') {
                    $q->whereHas('studentFees', fn($f) => $f->whereIn('month_id', $monthIds))
                      ->whereRaw('(select COALESCE(SUM(payments.amount),0) from payments where payments.student_id = students.id and payments.month_id in (' . implode(',', $monthIds) . ')) >= (select SUM(amount - discount) from student_fees where student_fees.student_id = students.id and student_fees.month_id in (' . implode(',', $monthIds) . ') and student_fees.deleted_at is null)');
                }
            });
        }

        $students = $studentsQuery->paginate(15);

        $cashboxes = Cashbox::where('school_id', $schoolId)->where('is_active', true)->get();

        // ★ کلاس‌ها (فیلتر سال)
        $classes = Student::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)  // ★
            ->select('class')->distinct()->orderBy('class')->pluck('class');

        return view('school.student-fees.index', compact(
            'students', 'months', 'allMonths', 'cashboxes', 'classes',
            'monthFrom', 'monthTo', 'paymentStatus'
        ));
    }

    // ========== فرم ایجاد شهریه جدید ==========
    public function create(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = (int) session('active_academic_year_id');

        if (!$activeYearId) {
            return redirect()->route('school.dashboard')
                ->with('error', 'لطفاً ابتدا سال مالی را انتخاب کنید.');
        }

        $students = Student::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)
            ->orderBy('first_name')
            ->get();

        if ($students->isEmpty()) {
            session()->flash('warning', 'هیچ دانش‌آموزی در سال مالی جاری ثبت نشده است.');
        }

        $feeTypes  = FeeType::where('school_id', $schoolId)->where('is_active', true)->orderBy('name')->get();
        $months    = Month::where('school_id', $schoolId)->orderBy('number')->get();
        $monthPreset = $request->input('month_preset');

        return view('school.student-fees.create', compact('students', 'feeTypes', 'months', 'monthPreset'));
    }

    // ========== ذخیرهٔ شهریه (با بازهٔ ماه) ==========
    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id'   => 'required|exists:students,id',
            'fee_type_id'  => 'required|exists:fee_types,id',
            'amount'       => 'required|numeric|min:0',
            'discount'     => 'nullable|numeric|min:0',
            'notes'        => 'nullable|string|max:500',
            'month_preset' => 'nullable|in:3_winter,9_regular,12_all,custom',
            'month_ids'    => 'nullable|array',
            'month_ids.*'  => 'exists:months,id',
            'month_id'     => 'nullable|exists:months,id',
        ]);

        $schoolId = $this->getSchoolId();
        $discount = $data['discount'] ?? 0;
        $monthIds = [];

        if (!empty($data['month_preset']) && $data['month_preset'] !== 'custom') {
            $allMonths = Month::where('school_id', $schoolId)->orderBy('number')->get();
            if ($data['month_preset'] === '3_winter') { $from = 10; $to = 12; }
            elseif ($data['month_preset'] === '9_regular') { $from = 1; $to = 9; }
            elseif ($data['month_preset'] === '12_all') { $from = 1; $to = 12; }
            else { $from = 1; $to = 12; }
            $monthIds = $allMonths->where('number', '>=', $from)->where('number', '<=', $to)->pluck('id')->toArray();
        } elseif (!empty($data['month_preset']) && $data['month_preset'] === 'custom') {
            $monthIds = $data['month_ids'] ?? [];
        } elseif (!empty($data['month_id'])) {
            $monthIds = [$data['month_id']];
        }

        if (empty($monthIds)) {
            return back()->withErrors(['month_preset' => 'حداقل یک ماه باید انتخاب شود.'])->withInput();
        }

        foreach ($monthIds as $monthId) {
            StudentFee::create([
                'school_id'        => $schoolId,
                'student_id'       => $data['student_id'],
                'fee_type_id'      => $data['fee_type_id'],
                'month_id'         => $monthId,
                'amount'           => $data['amount'],
                'discount'         => $discount,
                'notes'            => $data['notes'] ?? null,
                'academic_year_id' => session('active_academic_year_id'),  // ★
            ]);
        }

        $minMonthId = min($monthIds);
        $maxMonthId = max($monthIds);

        session([
            'last_fee_preset'     => $data['month_preset'] ?? null,
            'last_fee_month_from' => $minMonthId,
            'last_fee_month_to'   => $maxMonthId,
        ]);

        return redirect()->route('school.student-fees.index', [
            'month_from' => $minMonthId,
            'month_to'   => $maxMonthId,
        ])->with('success', 'شهریه برای ' . count($monthIds) . ' ماه با موفقیت ثبت شد.');
    }

    // ========== فرم ویرایش ==========
    public function edit(StudentFee $studentFee)
    {
        $this->authorizeAccess($studentFee);
        $schoolId = $this->getSchoolId();
        $activeYearId = (int) session('active_academic_year_id');

        if (!$activeYearId) {
            return redirect()->route('school.dashboard')
                ->with('error', 'لطفاً ابتدا سال مالی را انتخاب کنید.');
        }

        $students = Student::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)
            ->orderBy('first_name')
            ->get();

        $feeTypes = FeeType::where('school_id', $schoolId)->where('is_active', true)->orderBy('name')->get();
        $months   = Month::where('school_id', $schoolId)->orderBy('number')->get();

        return view('school.student-fees.edit', compact('studentFee', 'students', 'feeTypes', 'months'));
    }

    // ========== به‌روزرسانی ==========
    public function update(Request $request, StudentFee $studentFee)
    {
        $this->authorizeAccess($studentFee);

        $data = $request->validate([
            'student_id'  => 'required|exists:students,id',
            'fee_type_id' => 'required|exists:fee_types,id',
            'month_id'    => 'required|exists:months,id',
            'amount'      => 'required|numeric|min:0',
            'discount'    => 'nullable|numeric|min:0',
            'notes'       => 'nullable|string|max:500',
        ]);

        $data['discount'] = $data['discount'] ?? 0;
        $studentFee->update($data);

        return redirect()->route('school.student-fees.index')
            ->with('success', 'شهریه با موفقیت به‌روزرسانی شد.');
    }

    // ========== حذف یک شهریه + پرداخت‌های مرتبط (با سرویس) ==========
    public function destroy(StudentFee $studentFee, AccountingService $accounting)
    {
        $this->authorizeAccess($studentFee);

        $payments = Payment::where('student_id', $studentFee->student_id)
            ->where('month_id', $studentFee->month_id)
            ->get();

        foreach ($payments as $payment) {
            $accounting->deletePayment($payment);
        }

        $studentFee->delete();

        return redirect()->route('school.student-fees.index')
            ->with('success', 'شهریه و پرداخت‌های این ماه حذف شدند.');
    }

    // ========== حذف تمام شهریه‌ها و پرداخت‌های یک دانش‌آموز ==========
    public function destroyByStudent($studentId, AccountingService $accounting)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');  // ★
        $student = Student::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)      // ★
            ->findOrFail($studentId);

        $payments = Payment::where('student_id', $student->id)->get();

        foreach ($payments as $payment) {
            $accounting->deletePayment($payment);
        }

        StudentFee::where('student_id', $student->id)
            ->where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)      // ★
            ->delete();

        return redirect()->route('school.student-fees.index')
            ->with('success', 'تمام شهریه‌ها و پرداخت‌های این دانش‌آموز حذف شدند.');
    }

    // ========== پاک‌سازی پرداخت‌های بی‌صاحب (اختیاری) ==========
    public function cleanOrphanPayments()
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');  // ★
        $studentIds = Student::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)      // ★
            ->pluck('id');

        $orphanIds = Payment::whereIn('student_id', $studentIds)
            ->whereNotIn('id', function ($query) {
                $query->select('payments.id')
                    ->from('payments')
                    ->join('student_fees', function ($join) {
                        $join->on('payments.student_id', '=', 'student_fees.student_id')
                             ->on('payments.month_id', '=', 'student_fees.month_id')
                             ->whereNull('student_fees.deleted_at');
                    });
            })->pluck('id');

        Payment::whereIn('id', $orphanIds)->delete();

        return redirect()->route('school.student-fees.index')
            ->with('success', count($orphanIds) . ' پرداخت اضافی پاک‌سازی شد.');
    }

    // ========== گزارش چاپی کلی ==========
    public function printReport(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');  // ★
        $classFilter   = $request->input('class_filter');
        $monthFrom     = $request->input('month_from');
        $monthTo       = $request->input('month_to');
        $paymentStatus = $request->input('payment_status');

        $allMonths = Month::where('school_id', $schoolId)->orderBy('number')->get();

        if ($monthFrom && $monthTo) {
            $from = Month::find($monthFrom)->number ?? 1;
            $to   = Month::find($monthTo)->number ?? 12;
            $months = $allMonths->where('number', '>=', $from)->where('number', '<=', $to)->values();
        } else {
            $months = $allMonths;
        }

        $studentsQuery = Student::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)      // ★
            ->when($classFilter, fn($q) => $q->where('class', $classFilter))
            ->with(['studentFees', 'payments'])
            ->orderBy('class')
            ->orderBy('first_name');

        if ($paymentStatus && $months->isNotEmpty()) {
            $monthIds = $months->pluck('id')->toArray();
            $studentsQuery->where(function ($q) use ($monthIds, $paymentStatus) {
                if ($paymentStatus === 'unpaid') {
                    $q->whereHas('studentFees', fn($f) => $f->whereIn('month_id', $monthIds))
                      ->whereRaw('(select COALESCE(SUM(payments.amount),0) from payments where payments.student_id = students.id and payments.month_id in (' . implode(',', $monthIds) . ')) < (select SUM(amount - discount) from student_fees where student_fees.student_id = students.id and student_fees.month_id in (' . implode(',', $monthIds) . ') and student_fees.deleted_at is null)');
                } elseif ($paymentStatus === 'paid') {
                    $q->whereHas('studentFees', fn($f) => $f->whereIn('month_id', $monthIds))
                      ->whereRaw('(select COALESCE(SUM(payments.amount),0) from payments where payments.student_id = students.id and payments.month_id in (' . implode(',', $monthIds) . ')) >= (select SUM(amount - discount) from student_fees where student_fees.student_id = students.id and student_fees.month_id in (' . implode(',', $monthIds) . ') and student_fees.deleted_at is null)');
                }
            });
        }

        $students = $studentsQuery->get();

        return view('school.student-fees.print', compact('students', 'months', 'classFilter'));
    }

    // ========== پیش‌نمایش چاپ شهریه یک دانش‌آموز ==========
    public function feePreview(Request $request, $id)   // <-- Request را هم تزریق کن
{
    $activeYearId = session('active_academic_year_id');
    $student = Student::where('academic_year_id', $activeYearId)
                ->with(['studentFees.month', 'payments'])
                ->findOrFail($id);

    $months = Month::where('school_id', $student->school_id)->orderBy('number')->get();

    // دریافت ماه انتخاب‌شده از Query String
    $selectedMonthName = $request->input('month');

    $monthlyDetails = [];
    foreach ($months as $month) {
        // اگر فیلتر ماه فعال باشد و این ماه انتخاب نشده، رد شود
        if ($selectedMonthName && $month->name !== $selectedMonthName) {
            continue;
        }

        $fee  = $student->studentFees->firstWhere('month_id', $month->id);
        $paid = $student->payments->where('month_id', $month->id)->sum('amount');

        if ($fee || $paid > 0) {
            $monthlyDetails[] = [
                'month_name' => $month->name,
                'amount'     => $fee ? $fee->amount : 0,
                'discount'   => $fee ? $fee->discount : 0,
                'due'        => $fee ? ($fee->amount - $fee->discount) : 0,
                'paid'       => $paid,
                'remaining'  => $fee ? max(($fee->amount - $fee->discount) - $paid, 0) : 0,
                'is_paid'    => $fee ? ($paid >= ($fee->amount - $fee->discount)) : false,
            ];
        }
    }

    // محاسبه‌ی مجموع‌ها فقط برای ماه‌های انتخاب‌شده
    $filteredStudentFees = $student->studentFees->filter(function($fee) use ($months, $selectedMonthName) {
        if (!$selectedMonthName) return true;
        $month = $months->firstWhere('id', $fee->month_id);
        return $month && $month->name === $selectedMonthName;
    });

    $filteredPayments = $student->payments->filter(function($payment) use ($months, $selectedMonthName) {
        if (!$selectedMonthName) return true;
        $month = $months->firstWhere('id', $payment->month_id);
        return $month && $month->name === $selectedMonthName;
    });

    $totalFee      = $filteredStudentFees->sum(fn($f) => $f->amount - $f->discount);
    $totalPaid     = $filteredPayments->sum('amount');
    $totalRemaining = max($totalFee - $totalPaid, 0);

    return view('school.student-fees.fee-preview', compact(
        'student', 'monthlyDetails', 'totalPaid', 'totalFee', 'totalRemaining', 'selectedMonthName'
    ));
}
    // ========== گزارش اطلاعیه بدهی ==========
    public function feeNoticeReport(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');  // ★

        $classFilter   = $request->input('class_filter');
        $monthFrom     = $request->input('month_from');
        $monthTo       = $request->input('month_to');
        $paymentStatus = $request->input('payment_status');

        $allMonths = Month::where('school_id', $schoolId)->orderBy('number')->get();

        if ($monthFrom && $monthTo) {
            $fromMonth = Month::find($monthFrom);
            $toMonth   = Month::find($monthTo);
            if ($fromMonth && $toMonth && $fromMonth->number <= $toMonth->number) {
                $months = $allMonths->where('number', '>=', $fromMonth->number)
                                    ->where('number', '<=', $toMonth->number)
                                    ->values();
            } else {
                $months = $allMonths;
            }
        } else {
            $existingMonthIds = StudentFee::where('school_id', $schoolId)
                ->where('academic_year_id', $activeYearId)  // ★
                ->pluck('month_id')->unique()->filter()->toArray();
            $months = $allMonths->whereIn('id', $existingMonthIds)->values();
        }

        $studentsQuery = Student::where('school_id', $schoolId)
            ->where('academic_year_id', $activeYearId)      // ★
            ->when($classFilter, fn($q) => $q->where('class', $classFilter))
            ->with(['studentFees', 'payments', 'school'])
            ->orderBy('class')
            ->orderBy('first_name');

        if ($paymentStatus && $months->isNotEmpty()) {
            $monthIds = $months->pluck('id')->toArray();
            $studentsQuery->where(function ($q) use ($monthIds, $paymentStatus) {
                if ($paymentStatus === 'unpaid') {
                    $q->whereHas('studentFees', fn($f) => $f->whereIn('month_id', $monthIds))
                      ->whereRaw('(select COALESCE(SUM(payments.amount),0) from payments where payments.student_id = students.id and payments.month_id in (' . implode(',', $monthIds) . ')) < (select SUM(amount - discount) from student_fees where student_fees.student_id = students.id and student_fees.month_id in (' . implode(',', $monthIds) . ') and student_fees.deleted_at is null)');
                } elseif ($paymentStatus === 'paid') {
                    $q->whereHas('studentFees', fn($f) => $f->whereIn('month_id', $monthIds))
                      ->whereRaw('(select COALESCE(SUM(payments.amount),0) from payments where payments.student_id = students.id and payments.month_id in (' . implode(',', $monthIds) . ')) >= (select SUM(amount - discount) from student_fees where student_fees.student_id = students.id and student_fees.month_id in (' . implode(',', $monthIds) . ') and student_fees.deleted_at is null)');
                }
            });
        }

        $students = $studentsQuery->get();

        $unpaidStudents = [];
        foreach ($students as $student) {
            $unpaidMonths = [];
            foreach ($months as $month) {
                $fee = $student->studentFees->firstWhere('month_id', $month->id);
                if (!$fee) continue;

                $paid = $student->payments->where('month_id', $month->id)->sum('amount');
                $due = $fee->amount - $fee->discount;
                $remaining = $due - $paid;

                if ($remaining > 0) {
                    $unpaidMonths[] = [
                        'month'     => $month,
                        'fee'       => $fee,
                        'due'       => $due,
                        'paid'      => $paid,
                        'remaining' => $remaining,
                    ];
                }
            }

            if (count($unpaidMonths) > 0) {
                $unpaidStudents[] = [
                    'student'      => $student,
                    'unpaidMonths' => $unpaidMonths,
                ];
            }
        }

        return view('school.student-fees.fee-notice-report', compact('unpaidStudents', 'months', 'classFilter'));
    }

public function paymentSlip(Payment $payment)
{
    $this->authorizeAccess($payment->studentFee);
    return view('school.student-fees.payment-slip', compact('payment'));
}

    // ========== دسترسی ==========
    private function authorizeAccess(StudentFee $studentFee)
    {
        if ($studentFee->school_id !== $this->getSchoolId()) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }
}
