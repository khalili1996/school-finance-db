<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use Illuminate\Http\Request;
use App\Models\AcademicYear;
class CashboxController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $query = Cashbox::where('school_id', $schoolId);

        if ($search = $request->input('search')) {
            $query->where('name', 'like', "%{$search}%");
        }

        $cashboxes = $query->orderBy('name')->get();
        return view('school.cashboxes.index', compact('cashboxes'));
    }

    public function create()
    {
        return view('school.cashboxes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:cash,bank',
            'initial_balance'=> 'required|numeric|min:0',
            'notes'          => 'nullable|string|max:500',
        ]);

        $data['school_id'] = $this->getSchoolId();
        $data['current_balance'] = $data['initial_balance'];
        $data['is_active'] = true;

        Cashbox::create($data);

        return redirect()->route('school.cashboxes.index')
            ->with('success', 'صندوق با موفقیت ایجاد شد.');
    }

    public function edit(Cashbox $cashbox)
    {
        $this->authorizeAccess($cashbox);
        return view('school.cashboxes.edit', compact('cashbox'));
    }

    public function update(Request $request, Cashbox $cashbox)
    {
        $this->authorizeAccess($cashbox);
        $data = $request->validate([
            'name'           => 'required|string|max:255',
            'type'           => 'required|in:cash,bank',
            'initial_balance'=> 'required|numeric|min:0',
            'is_active'      => 'boolean',
            'notes'          => 'nullable|string|max:500',
        ]);

        $cashbox->update($data);
        if ($cashbox->wasChanged('initial_balance') && $cashbox->transactions()->count() === 0) {
            $cashbox->current_balance = $data['initial_balance'];
            $cashbox->save();
        }

        return redirect()->route('school.cashboxes.index')
            ->with('success', 'صندوق با موفقیت ویرایش شد.');
    }


public function show(Cashbox $cashbox, Request $request)
{
    $this->authorizeAccess($cashbox);
    $schoolId = $this->getSchoolId();

    // ★ دریافت تمام سال‌های مالی برای dropdown
    $academicYears = AcademicYear::where('school_id', $schoolId)
        ->orderBy('start_date', 'desc')
        ->get();

    $query = $cashbox->transactions()->latest();

    // ★ فیلتر بر اساس سال مالی
    if ($yearId = $request->input('academic_year_id')) {
        $year = AcademicYear::find($yearId);
        if ($year && $year->school_id == $schoolId) {
            $query->whereBetween('transaction_date', [$year->start_date, $year->end_date]);
        }
    }

    $transactions = $query->paginate(20);

    return view('school.cashboxes.show', compact('cashbox', 'transactions', 'academicYears'));
}

    public function destroy(Cashbox $cashbox)
    {
        $this->authorizeAccess($cashbox);
        $cashbox->transactions()->delete();
        $cashbox->delete();

        return redirect()->route('school.cashboxes.index')
            ->with('success', 'صندوق و تمام تراکنش‌های آن با موفقیت حذف شد.');
    }

    /**
     * همگام‌سازی تراکنش‌های قدیمی (فقط رکوردهای حذف‌نشده)
     */
    public function syncOldTransactions(Request $request)
    {
        $request->validate(['cashbox_id' => 'required|exists:cashboxes,id']);
        $cashbox = Cashbox::findOrFail($request->cashbox_id);
        $this->authorizeAccess($cashbox);

        $count = 0;

        // همگام‌سازی عواید قدیمی (IncomeReceipt) – فقط رکوردهای حذف‌نشده
        $incomeReceipts = \App\Models\IncomeReceipt::where('school_id', $this->getSchoolId())
            ->whereNull('deleted_at')
            ->whereDoesntHave('cashboxTransactions')
            ->get();
        foreach ($incomeReceipts as $receipt) {
            $cashbox->transactions()->create([
                'school_id'        => $receipt->school_id,
                'type'             => 'deposit',
                'amount'           => $receipt->amount,
                'transaction_date' => $receipt->receipt_date,
                'reference_type'   => \App\Models\IncomeReceipt::class,
                'reference_id'     => $receipt->id,
                'description'      => 'دریافت عواید (همگام‌سازی)',
            ]);
            $cashbox->increment('current_balance', $receipt->amount);
            $count++;
        }

        // همگام‌سازی مصارف قدیمی (ExpensePayment) – فقط رکوردهای حذف‌نشده
        $expensePayments = \App\Models\ExpensePayment::where('school_id', $this->getSchoolId())
            ->whereNull('deleted_at')
            ->whereDoesntHave('cashboxTransactions')
            ->get();
        foreach ($expensePayments as $payment) {
            $cashbox->transactions()->create([
                'school_id'        => $payment->school_id,
                'type'             => 'withdrawal',
                'amount'           => $payment->amount,
                'transaction_date' => $payment->payment_date,
                'reference_type'   => \App\Models\ExpensePayment::class,
                'reference_id'     => $payment->id,
                'description'      => 'پرداخت مصرف (همگام‌سازی)',
            ]);
            $cashbox->decrement('current_balance', $payment->amount);
            $count++;
        }

        // همگام‌سازی پرداخت‌های شهریه قدیمی (Payment) – فقط رکوردهای حذف‌نشده
        $payments = \App\Models\Payment::where('school_id', $this->getSchoolId())
            ->whereNull('deleted_at')
            ->whereDoesntHave('cashboxTransactions')
            ->get();
        foreach ($payments as $payment) {
            $cashbox->transactions()->create([
                'school_id'        => $payment->school_id,
                'type'             => 'deposit',
                'amount'           => $payment->amount,
                'transaction_date' => $payment->payment_date,
                'reference_type'   => \App\Models\Payment::class,
                'reference_id'     => $payment->id,
                'description'      => 'پرداخت شهریه (همگام‌سازی)',
            ]);
            $cashbox->increment('current_balance', $payment->amount);
            $count++;
        }

        return back()->with('success', "{$count} تراکنش قدیمی با صندوق همگام‌سازی شد.");
    }

    /**
     * پاک‌سازی تراکنش‌های یتیم (بدون مرجع یا مرجع حذف‌شده)
     */
   public function cleanOrphanTransactions(Request $request)
{
    $request->validate(['cashbox_id' => 'required|exists:cashboxes,id']);
    $cashbox = Cashbox::findOrFail($request->cashbox_id);
    $this->authorizeAccess($cashbox);

    $cleaned = 0;

    // 1. پاک‌سازی پرداخت‌های یتیم (دانش‌آموزشان حذف شده)
    $orphanPayments = \App\Models\Payment::where('school_id', $cashbox->school_id)
        ->whereDoesntHave('student', function ($q) {
            $q->withTrashed();   // فقط دانش‌آموزانی که وجود ندارند (حتی حذف شده)
        })->get();
    foreach ($orphanPayments as $payment) {
        $payment->delete();      // رویداد deleting خودش اجرا می‌شود و صندوق اصلاح می‌شود
        $cleaned++;
    }

    // 2. پاک‌سازی معاش‌های یتیم (کارمندشان حذف شده)
    $orphanSalaries = \App\Models\Salary::where('school_id', $cashbox->school_id)
        ->whereDoesntHave('employee', function ($q) {
            $q->withTrashed();
        })->get();
    foreach ($orphanSalaries as $salary) {
        $salary->delete();       // رویداد deleting خودش (و payments) اجرا می‌شود
        $cleaned++;
    }

    // 3. پاک‌سازی تراکنش‌های cashbox که reference_id آن‌ها دیگر معتبر نیست
    $orphanTrxs = $cashbox->transactions()
        ->whereNotNull('reference_type')
        ->get()
        ->filter(function ($trx) {
            if (!class_exists($trx->reference_type)) return true;
            $ref = $trx->reference_type::withTrashed()->find($trx->reference_id);
            return !$ref || $ref->trashed();
        });
    foreach ($orphanTrxs as $trx) {
        // اصلاح موجودی صندوق
        if ($trx->type === 'deposit') {
            $cashbox->decrement('current_balance', $trx->amount);
        } else {
            $cashbox->increment('current_balance', $trx->amount);
        }
        $trx->delete();
        $cleaned++;
    }

    return back()->with('success', "{$cleaned} رکورد یتیم پاک‌سازی شد و موجودی صندوق‌ها اصلاح گردید.");
}

    private function authorizeAccess(Cashbox $cashbox)
    {
        if ($cashbox->school_id !== $this->getSchoolId()) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }
    public function destroyTransaction(CashboxTransaction $transaction)
{
    $cashbox = $transaction->cashbox;
    $this->authorizeAccess($cashbox);

    // اگر تراکنش مرجعی دارد (مثلاً Payment یا SalaryPayment)
    if ($transaction->reference_type && class_exists($transaction->reference_type)) {
        $reference = $transaction->reference_type::find($transaction->reference_id);
        if ($reference) {
            // حذف رکورد اصلی (رویدادهای deleting خودش اجرا می‌شود)
            $reference->delete();
            return back()->with('success', 'تراکنش و رکورد مرتبط با موفقیت حذف شدند.');
        }
    }

    // اگر مرجعی وجود نداشت (تراکنش دستی یا یتیم)
    if ($transaction->type === 'deposit') {
        $cashbox->decrement('current_balance', $transaction->amount);
    } else {
        $cashbox->increment('current_balance', $transaction->amount);
    }
    $transaction->delete();

    return back()->with('success', 'تراکنش حذف شد و موجودی صندوق اصلاح گردید.');
}
}
