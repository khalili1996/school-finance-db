<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Cashbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Helpers\JalaliHelper;
use App\Models\Month;
use App\Services\AccountingService;

class PaymentController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');
        $query = Payment::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->with(['student', 'studentFee.feeType', 'month']);

        if ($studentId = $request->input('student_id')) {
            $query->where('student_id', $studentId);
        }

        $payments = $query->orderBy('payment_date', 'desc')->paginate(15);
        $students = Student::where('school_id', $schoolId)
    ->when(session('active_academic_year_id'), fn($q) => $q->where('academic_year_id', session('active_academic_year_id')))
    ->orderBy('first_name')
    ->get();

        return view('school.payments.index', compact('payments', 'students'));
    }

    public function create()
    {
        $schoolId = $this->getSchoolId();
        $students = Student::where('school_id', $schoolId)->orderBy('first_name')->get();
        $cashboxes = Cashbox::where('school_id', $schoolId)->where('is_active', true)->get();
        $months = \App\Models\Month::where('school_id', $schoolId)->orderBy('number')->get();

        return view('school.payments.create', compact('students', 'cashboxes', 'months'));
    }

    public function store(Request $request, AccountingService $accounting)
    {
        $data = $request->validate([
            'student_id'      => 'required|exists:students,id',
            'fee_id'          => 'nullable|exists:student_fees,id',
            'amount'          => 'required|numeric|min:1',
            'month_id'        => 'nullable|exists:months,id',
            'payment_method'  => 'required|in:cash,bank,other',
            'receipt_number'  => 'nullable|string|max:50',
            'notes'           => 'nullable|string|max:500',
            'cashbox_id'      => 'required|exists:cashboxes,id',
            'payment_date'    => 'required|string',
        ]);

        $data['school_id']    = $this->getSchoolId();
        $data['academic_year_id'] = session('active_academic_year_id');
        $data['date_type']    = 'jalali';
        $data['payment_date'] = JalaliHelper::toGregorian($request->payment_date)->format('Y-m-d');

        $payment = $accounting->recordPayment($data);

        $schoolId = $this->getSchoolId();
        $allMonthIds = Month::where('school_id', $schoolId)->orderBy('number')->pluck('id')->toArray();
        $existingMonthIds = StudentFee::where('student_id', $data['student_id'])
            ->where('school_id', $schoolId)
            ->pluck('month_id')
            ->toArray();
        $missingMonthIds = array_diff($allMonthIds, $existingMonthIds);

        $feeTypeId = null;
        if (!empty($data['fee_id'])) {
            $currentFee = StudentFee::find($data['fee_id']);
            $feeTypeId = $currentFee ? $currentFee->fee_type_id : null;
        }
        if (!$feeTypeId) {
            $firstFeeType = \App\Models\FeeType::where('school_id', $schoolId)->where('is_active', true)->first();
            $feeTypeId = $firstFeeType ? $firstFeeType->id : null;
        }

        $activeYearId = session('active_academic_year_id');
        foreach ($missingMonthIds as $monthId) {
            StudentFee::create([
                'school_id'        => $schoolId,
                'student_id'       => $data['student_id'],
                'fee_type_id'      => $feeTypeId,
                'month_id'         => $monthId,
                'amount'           => $data['amount'],
                'discount'         => 0,
                'academic_year_id' => $activeYearId,   // ★ اضافه شد
            ]);
        }

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'پرداخت با موفقیت ثبت شد.']);
        }

        return redirect()->route('school.payments.index')
            ->with('success', 'پرداخت با موفقیت ثبت شد و شهریه‌های بعدی ایجاد گردید.');
    }

    public function edit(Payment $payment)
    {
        $this->authorizeAccess($payment);
        $schoolId = $this->getSchoolId();
        $students = Student::where('school_id', $schoolId)->orderBy('first_name')->get();
        $fees = StudentFee::where('student_id', $payment->student_id)->with('feeType')->get();
        $cashboxes = Cashbox::where('school_id', $schoolId)->where('is_active', true)->get();
        $months = \App\Models\Month::where('school_id', $schoolId)->orderBy('number')->get();

        $payment->payment_date = $payment->payment_date
            ? JalaliHelper::toJalali($payment->payment_date, 'Y/m/d')
            : null;

        return view('school.payments.edit', compact('payment', 'students', 'fees', 'cashboxes', 'months'));
    }

    public function update(Request $request, Payment $payment, AccountingService $accounting)
    {
        $this->authorizeAccess($payment);
        $data = $request->validate([
            'student_id'      => 'required|exists:students,id',
            'fee_id'          => 'nullable|exists:student_fees,id',
            'amount'          => 'required|numeric|min:1',
            'payment_method'  => 'required|in:cash,bank,other',
            'receipt_number'  => 'nullable|string|max:50',
            'notes'           => 'nullable|string|max:500',
            'cashbox_id'      => 'required|exists:cashboxes,id',
            'payment_date'    => 'required|string',
        ]);

        $data['payment_date'] = JalaliHelper::toGregorian($request->payment_date)->format('Y-m-d');
        $data['date_type']    = 'jalali';
        $data['school_id']    = $this->getSchoolId();

        $accounting->updatePayment($payment, $data);

        return redirect()->route('school.payments.index')
            ->with('success', 'پرداخت با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Payment $payment, AccountingService $accounting)
    {
        $this->authorizeAccess($payment);
        $accounting->deletePayment($payment);

        return redirect()->route('school.payments.index')
            ->with('success', 'پرداخت و تراکنش‌های مرتبط حذف شدند.');
    }

    public function feesByStudent($studentId)
    {
        // بدون تغییر
    }

    public function searchStudents(Request $request)
    {
        $query = $request->input('q');
        if (empty($query)) {
            return response()->json([]);
        }

        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');

        $students = Student::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->where(function ($q) use ($query) {
                $q->where('first_name', 'like', "%{$query}%")
                  ->orWhere('last_name', 'like', "%{$query}%")
                  ->orWhere('student_code', 'like', "%{$query}%")
                  ->orWhere('national_id', 'like', "%{$query}%");
            })
            ->select('id', 'first_name', 'last_name', 'student_code')
            ->take(10)
            ->get();

        return response()->json($students);
    }

    public function receipt(Payment $payment)
    {
        $this->authorizeAccess($payment);
        $payment->load('student', 'studentFee.month', 'school');

        $totalFee = $payment->studentFee
            ? $payment->student->studentFees->sum(fn($f) => $f->amount - $f->discount)
            : 0;
        $totalPaid = $payment->student->payments->sum('amount');
        $balance = max($totalFee - $totalPaid, 0);

        return view('school.payments.receipt', compact('payment', 'totalFee', 'balance'));
    }

    public function syncOldPaymentsToLedger()
    {
        $schoolId = $this->getSchoolId();

        $payments = Payment::where('school_id', $schoolId)
            ->whereDoesntHave('ledgerEntries', function ($q) {
                $q->where('debit', '>', 0);
            })
            ->get();

        $count = 0;
        foreach ($payments as $payment) {
            \App\Models\LedgerEntry::create([
                'school_id'      => $schoolId,
                'entry_date'     => $payment->payment_date,
                'description'    => 'دریافت شهریه - ' . ($payment->student->first_name ?? '') . ' ' . ($payment->student->last_name ?? ''),
                'debit'          => $payment->amount,
                'credit'         => 0,
                'reference_type' => Payment::class,
                'reference_id'   => $payment->id,
            ]);
            $count++;
        }

        return back()->with('success', "{$count} پرداخت قدیمی با دفتر کل همگام‌سازی شد.");
    }
 public function paymentSlip(Payment $payment)
{
    $this->authorizeAccess($payment);

    // بارگذاری روابط
    $payment->load(['student', 'studentFee.month']);

    // اگر fee_id نال است، بر اساس student_id و month_id یک StudentFee پیدا کن
    if (!$payment->studentFee && $payment->month_id) {
        $fee = \App\Models\StudentFee::where('student_id', $payment->student_id)
            ->where('month_id', $payment->month_id)
            ->first();
        if ($fee) {
            $payment->setRelation('studentFee', $fee);
        }
    }

    return view('school.student-fees.payment-slip', compact('payment'));
}

    private function authorizeAccess(Payment $payment)
    {
        if ($payment->school_id !== $this->getSchoolId()) abort(403);
    }
}
