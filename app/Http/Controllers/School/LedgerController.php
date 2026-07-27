<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\LedgerEntry;
use App\Models\CashboxTransaction;
use App\Models\AcademicYear;             // ★ اضافه شد
use App\Helpers\JalaliHelper;
use Illuminate\Http\Request;

class LedgerController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();

        // ★ دریافت سال‌های مالی برای dropdown
        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->orderBy('start_date', 'desc')
            ->get();

        $query = LedgerEntry::where('school_id', $schoolId)
            ->with([
                'reference' => function ($morphTo) {
                    $morphTo->morphWith([
                        \App\Models\SalaryPayment::class => ['salary.employee', 'salary.month'],
                    ]);
                }
            ]);

        // ★ فیلتر بر اساس سال مالی (در صورت انتخاب)
        if ($yearId = $request->input('academic_year_id')) {
            $year = AcademicYear::find($yearId);
            if ($year && $year->school_id == $schoolId) {
                $query->whereBetween('entry_date', [$year->start_date, $year->end_date]);
            }
        }

        // فیلتر تاریخ دستی (شمسی)
        $from = $request->input('from_date');
        $to   = $request->input('to_date');

        if ($from) {
            $fromGregorian = JalaliHelper::toGregorian($from)->format('Y-m-d');
            $query->whereDate('entry_date', '>=', $fromGregorian);
        }
        if ($to) {
            $toGregorian = JalaliHelper::toGregorian($to)->format('Y-m-d');
            $query->whereDate('entry_date', '<=', $toGregorian);
        }

        $entries = $query->orderBy('entry_date', 'desc')->orderBy('id', 'desc')->paginate(30);

        $incomeEntries  = $entries->where('debit', '>', 0);
        $expenseEntries = $entries->where('credit', '>', 0);

        $totalIncome  = $incomeEntries->sum('debit');
        $totalExpense = $expenseEntries->sum('credit');
        $balance = $totalIncome - $totalExpense;

        return view('school.ledger.index', compact(
            'entries', 'incomeEntries', 'expenseEntries',
            'totalIncome', 'totalExpense', 'balance',
            'academicYears'   // ★
        ));
    }

    /**
     * حذف یک سند دفتر کل (و تراکنش صندوق مرتبط)
     */
    public function destroy(LedgerEntry $ledgerEntry)
    {
        $schoolId = $this->getSchoolId();
        if ($ledgerEntry->school_id !== $schoolId) {
            abort(403);
        }

        $cashboxTrx = CashboxTransaction::where('reference_type', LedgerEntry::class)
                        ->where('reference_id', $ledgerEntry->id)
                        ->first();
        if ($cashboxTrx) {
            $cashbox = $cashboxTrx->cashbox;
            if ($cashbox) {
                if ($cashboxTrx->type === 'deposit') {
                    $cashbox->decrement('current_balance', $cashboxTrx->amount);
                } elseif ($cashboxTrx->type === 'withdrawal') {
                    $cashbox->increment('current_balance', $cashboxTrx->amount);
                }
            }
            $cashboxTrx->delete();
        }

        $ledgerEntry->delete();

        return back()->with('success', 'سند دفتر کل و تراکنش صندوق مرتبط حذف شدند.');
    }
}
