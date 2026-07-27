<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AcademicYear;


class CashboxTransactionController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }


public function index(Request $request)
{
    $schoolId = $this->getSchoolId();

    // ★ دریافت تمام سال‌های مالی برای dropdown
    $academicYears = AcademicYear::where('school_id', $schoolId)
        ->orderBy('start_date', 'desc')
        ->get();

    $query = CashboxTransaction::where('school_id', $schoolId)
        ->with(['cashbox', 'reference']);

    // ★ فیلتر بر اساس سال مالی
    if ($yearId = $request->input('academic_year_id')) {
        $year = AcademicYear::find($yearId);
        if ($year && $year->school_id == $schoolId) {
            $query->whereBetween('transaction_date', [$year->start_date, $year->end_date]);
        }
    }

    // سایر فیلترها
    if ($type = $request->input('type')) {
        $query->where('type', $type);
    }
    if ($cashboxId = $request->input('cashbox_id')) {
        $query->where('cashbox_id', $cashboxId);
    }
    if ($from = $request->input('from_date')) {
        $query->whereDate('transaction_date', '>=', $from);
    }
    if ($to = $request->input('to_date')) {
        $query->whereDate('transaction_date', '<=', $to);
    }

    $transactions = $query->orderBy('transaction_date', 'desc')->paginate(20);
    $cashboxes = Cashbox::where('school_id', $schoolId)->get();

    return view('school.cashbox_transactions.index', compact('transactions', 'cashboxes', 'academicYears'));
}
    public function create()
    {
        $schoolId = $this->getSchoolId();
        $cashboxes = Cashbox::where('school_id', $schoolId)->where('is_active', true)->get();
        return view('school.cashbox_transactions.create', compact('cashboxes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'cashbox_id'       => 'required|exists:cashboxes,id',
            'type'             => 'required|in:deposit,withdrawal,transfer',
            'amount'           => 'required|numeric|min:1',
            'transaction_date' => 'required|date',
            'description'      => 'nullable|string|max:500',
            'to_cashbox_id'    => 'nullable|required_if:type,transfer|exists:cashboxes,id|different:cashbox_id',
        ]);

        $data['school_id'] = $this->getSchoolId();

        DB::transaction(function () use ($data) {
            $cashbox = Cashbox::findOrFail($data['cashbox_id']);
            if ($cashbox->school_id !== $this->getSchoolId()) abort(403);

            if ($data['type'] === 'transfer') {
                $toCashbox = Cashbox::findOrFail($data['to_cashbox_id']);
                if ($toCashbox->school_id !== $this->getSchoolId()) abort(403);

                // برداشت از مبدأ
                CashboxTransaction::create([
                    'school_id'        => $this->getSchoolId(),
                    'cashbox_id'       => $data['cashbox_id'],
                    'type'             => 'withdrawal',
                    'amount'           => $data['amount'],
                    'transaction_date' => $data['transaction_date'],
                    'description'      => 'انتقال به صندوق ' . $toCashbox->name . ' - ' . $data['description'],
                ]);
                $cashbox->decrement('current_balance', $data['amount']);

                // واریز به مقصد
                CashboxTransaction::create([
                    'school_id'        => $this->getSchoolId(),
                    'cashbox_id'       => $data['to_cashbox_id'],
                    'type'             => 'deposit',
                    'amount'           => $data['amount'],
                    'transaction_date' => $data['transaction_date'],
                    'description'      => 'انتقال از صندوق ' . $cashbox->name . ' - ' . $data['description'],
                ]);
                $toCashbox->increment('current_balance', $data['amount']);
            } else {
                if ($data['type'] === 'withdrawal') {
                    if ($cashbox->current_balance < $data['amount']) {
                        throw new \Exception('موجودی صندوق کافی نیست.');
                    }
                    $cashbox->decrement('current_balance', $data['amount']);
                } elseif ($data['type'] === 'deposit') {
                    $cashbox->increment('current_balance', $data['amount']);
                }
                CashboxTransaction::create($data);
            }
        });

        return redirect()->route('school.cashbox-transactions.index')
            ->with('success', 'تراکنش با موفقیت ثبت شد.');
    }

    public function show(CashboxTransaction $transaction)
    {
        $this->authorizeAccess($transaction);
        $transaction->load('cashbox', 'reference');
        return view('school.cashbox_transactions.show', compact('transaction'));
    }

    public function receipt(CashboxTransaction $transaction)
    {
        $this->authorizeAccess($transaction);
        $transaction->load('cashbox');
        return view('school.cashbox_transactions.receipt', compact('transaction'));
    }

    /**
     * حذف تراکنش (با اصلاح موجودی صندوق)
     */
    public function destroy(CashboxTransaction $cashboxTransaction)
    {
        $this->authorizeAccess($cashboxTransaction);
        $cashbox = $cashboxTransaction->cashbox;

        // اگر تراکنش مرجع معتبری دارد، ابتدا رکورد اصلی را حذف کن
        if ($cashboxTransaction->reference_type && class_exists($cashboxTransaction->reference_type)) {
            $reference = $cashboxTransaction->reference_type::find($cashboxTransaction->reference_id);
            if ($reference) {
                // با حذف رکورد اصلی، رویدادهای deleting خودش اجرا می‌شود
                $reference->delete();
                return redirect()->route('school.cashboxes.show', $cashbox->id)
                    ->with('success', 'تراکنش و رکورد مرتبط با موفقیت حذف شدند.');
            }
        }

        // اگر مرجعی وجود نداشت، خود تراکنش را حذف و موجودی صندوق را اصلاح کن
        if ($cashboxTransaction->type === 'deposit') {
            $cashbox->decrement('current_balance', $cashboxTransaction->amount);
        } elseif ($cashboxTransaction->type === 'withdrawal') {
            $cashbox->increment('current_balance', $cashboxTransaction->amount);
        }
        $cashboxTransaction->delete();

        return redirect()->route('school.cashboxes.show', $cashbox->id)
            ->with('success', 'تراکنش حذف و موجودی صندوق اصلاح شد.');
    }

    private function authorizeAccess(CashboxTransaction $transaction)
    {
        if ($transaction->school_id !== $this->getSchoolId()) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }
}
