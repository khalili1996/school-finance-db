<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Income;
use App\Models\IncomeCategory;
use App\Models\Month;
use App\Models\Cashbox;
use App\Models\CashboxTransaction;
use App\Models\LedgerEntry;
use Illuminate\Http\Request;
use App\Helpers\JalaliHelper;
use Illuminate\Support\Facades\DB;

class IncomeController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    // ---------- لیست درآمدها (بدون شهریه) ----------
    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $query = Income::where('school_id', $schoolId)->with('category', 'month');
        $activeYearId = session('active_academic_year_id');
$query = $query->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId));
        // مخفی کردن دسته‌بندی "شهریه" (اگر وجود دارد)
        $feeCategoryId = IncomeCategory::where('school_id', $schoolId)
                            ->where('name', 'شهریه')
                            ->value('id');
        if ($feeCategoryId) {
            $query->where('income_category_id', '!=', $feeCategoryId);
        }

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('source', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($catId = $request->input('category_id')) {
            $query->where('income_category_id', $catId);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($monthId = $request->input('month_id')) {
            $query->where('month_id', $monthId);
        }

        $incomes    = $query->orderBy('income_date', 'desc')->paginate(15);
        $categories = IncomeCategory::where('school_id', $schoolId)->orderBy('name')->get();
        $months     = Month::where('school_id', $schoolId)->orderBy('number')->get();

        return view('school.incomes.index', compact('incomes', 'categories', 'months'));
    }

    // ---------- فرم ایجاد درآمد ----------
    public function create()
    {
        $schoolId   = $this->getSchoolId();
        $categories = IncomeCategory::where('school_id', $schoolId)->orderBy('name')->get();
        $months     = Month::where('school_id', $schoolId)->orderBy('number')->get();
        $cashboxes  = Cashbox::where('school_id', $schoolId)->where('is_active', true)->get();

        return view('school.incomes.create', compact('categories', 'months', 'cashboxes'));
    }

    // ---------- ذخیره درآمد جدید (با تراکنش اتمی) ----------
    public function store(Request $request)
    {
        $data = $request->validate([
            'title'              => 'required|string|max:255',
            'income_category_id' => 'required|exists:income_categories,id',
            'total_amount'       => 'required|numeric|min:1',
            'received_amount'    => 'nullable|numeric|min:0',
            'income_date'        => 'required|string',
            'month_id'           => 'nullable|exists:months,id',
            'source'             => 'nullable|string|max:100',
            'description'        => 'nullable|string|max:500',
            'status'             => 'required|in:due,partially_received,received,cancelled',
            'cashbox_id'         => 'required|exists:cashboxes,id',
        ]);

        $data['school_id']       = $this->getSchoolId();
        $data['academic_year_id'] = session('active_academic_year_id');
        $data['received_amount'] = $data['received_amount'] ?? 0;

        if (!empty($data['income_date'])) {
            $data['income_date'] = JalaliHelper::toGregorian($data['income_date'])->format('Y-m-d');
        }

        // تراکنش اتمی
        $income = DB::transaction(function () use ($request, $data) {
            $income = Income::create($data);

            if ($income->received_amount > 0) {
                CashboxTransaction::create([
                    'school_id'        => $this->getSchoolId(),
                    'cashbox_id'       => $request->cashbox_id,
                    'type'             => 'deposit',
                    'amount'           => $income->received_amount,
                    'transaction_date' => $income->income_date,
                    'reference_type'   => Income::class,
                    'reference_id'     => $income->id,
                    'description'      => 'دریافت عاید: ' . $income->title,
                ]);
                Cashbox::where('id', $request->cashbox_id)->increment('current_balance', $income->received_amount);

                LedgerEntry::create([
                    'school_id'      => $this->getSchoolId(),
                    'entry_date'     => $income->income_date,
                    'description'    => 'دریافت عاید: ' . $income->title,
                    'debit'          => $income->received_amount,
                    'credit'         => 0,
                    'reference_type' => Income::class,
                    'reference_id'   => $income->id,
                ]);
            }

            return $income;
        });

        return redirect()->route('school.incomes.index')->with('success', 'عاید جدید با موفقیت ثبت شد.');
    }

    // ---------- فرم ویرایش ----------
    public function edit(Income $income)
    {
        $this->authorizeAccess($income);
        $schoolId   = $this->getSchoolId();
        $categories = IncomeCategory::where('school_id', $schoolId)->orderBy('name')->get();
        $months     = Month::where('school_id', $schoolId)->orderBy('number')->get();
        $cashboxes  = Cashbox::where('school_id', $schoolId)->where('is_active', true)->get();

        $income->income_date = $income->income_date
            ? JalaliHelper::toJalali($income->income_date, 'Y/m/d')
            : null;

        return view('school.incomes.edit', compact('income', 'categories', 'months', 'cashboxes'));
    }

    // ---------- به‌روزرسانی (با اصلاح موجودی صندوق) ----------
    public function update(Request $request, Income $income)
    {
        $this->authorizeAccess($income);
        $data = $request->validate([
            'title'              => 'required|string|max:255',
            'income_category_id' => 'required|exists:income_categories,id',
            'total_amount'       => 'required|numeric|min:1',
            'received_amount'    => 'nullable|numeric|min:0',
            'income_date'        => 'required|string',
            'month_id'           => 'nullable|exists:months,id',
            'source'             => 'nullable|string|max:100',
            'description'        => 'nullable|string|max:500',
            'status'             => 'required|in:due,partially_received,received,cancelled',
            'cashbox_id'         => 'required|exists:cashboxes,id',
        ]);

        $data['received_amount'] = $data['received_amount'] ?? 0;

        if (!empty($data['income_date'])) {
            $data['income_date'] = JalaliHelper::toGregorian($data['income_date'])->format('Y-m-d');
        }

        DB::transaction(function () use ($income, $request, $data) {
            // برگشت موجودی صندوق برای تراکنش‌های قبلی
            $oldTransactions = CashboxTransaction::where('reference_type', Income::class)
                ->where('reference_id', $income->id)
                ->get();
            foreach ($oldTransactions as $trx) {
                if ($trx->cashbox) {
                    $trx->cashbox->decrement('current_balance', $trx->amount);
                }
            }

            // حذف تراکنش‌های قبلی
            CashboxTransaction::where('reference_type', Income::class)
                ->where('reference_id', $income->id)->delete();
            LedgerEntry::where('reference_type', Income::class)
                ->where('reference_id', $income->id)->delete();

            // به‌روزرسانی خود Income
            $income->update($data);

            // ثبت تراکنش جدید
            if ($income->received_amount > 0) {
                CashboxTransaction::create([
                    'school_id'        => $this->getSchoolId(),
                    'cashbox_id'       => $request->cashbox_id,
                    'type'             => 'deposit',
                    'amount'           => $income->received_amount,
                    'transaction_date' => $income->income_date,
                    'reference_type'   => Income::class,
                    'reference_id'     => $income->id,
                    'description'      => 'دریافت عاید: ' . $income->title,
                ]);
                Cashbox::where('id', $request->cashbox_id)->increment('current_balance', $income->received_amount);

                LedgerEntry::create([
                    'school_id'      => $this->getSchoolId(),
                    'entry_date'     => $income->income_date,
                    'description'    => 'دریافت عاید: ' . $income->title,
                    'debit'          => $income->received_amount,
                    'credit'         => 0,
                    'reference_type' => Income::class,
                    'reference_id'   => $income->id,
                ]);
            }
        });

        return redirect()->route('school.incomes.index')->with('success', 'عاید با موفقیت به‌روزرسانی شد.');
    }

    // ---------- حذف (با اصلاح موجودی صندوق) ----------
    public function destroy(Income $income)
    {
        $this->authorizeAccess($income);

        DB::transaction(function () use ($income) {
            // برگشت موجودی صندوق
            $transactions = CashboxTransaction::where('reference_type', Income::class)
                ->where('reference_id', $income->id)
                ->get();
            foreach ($transactions as $trx) {
                if ($trx->cashbox) {
                    $trx->cashbox->decrement('current_balance', $trx->amount);
                }
            }

            // حذف تراکنش‌ها
            CashboxTransaction::where('reference_type', Income::class)
                ->where('reference_id', $income->id)->delete();
            LedgerEntry::where('reference_type', Income::class)
                ->where('reference_id', $income->id)->delete();

            $income->delete();
        });

        return redirect()->route('school.incomes.index')->with('success', 'عاید حذف شد.');
    }

    // ---------- گزارش ----------
    public function report(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $query = Income::where('school_id', $schoolId)->with('category', 'month');
        $activeYearId = session('active_academic_year_id');
$query = $query->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId));
        if ($monthId = $request->input('month_id')) {
            $query->where('month_id', $monthId);
        }
        if ($catId = $request->input('category_id')) {
            $query->where('income_category_id', $catId);
        }
        $incomes = $query->orderBy('income_date', 'desc')->get();
        $months = Month::where('school_id', $schoolId)->orderBy('number')->get();
        $categories = IncomeCategory::where('school_id', $schoolId)->orderBy('name')->get();
        $selectedMonth = $monthId ? Month::find($monthId) : null;
        $school = \App\Models\School::find($schoolId);
        $totalAmount = $incomes->sum('total_amount');
        $totalReceived = $incomes->sum('received_amount');
        $totalRemaining = $incomes->sum(fn($i) => $i->total_amount - $i->received_amount);
        return view('school.incomes.report', compact('incomes', 'months', 'categories', 'selectedMonth', 'school', 'totalAmount', 'totalReceived', 'totalRemaining'));
    }

    private function authorizeAccess(Income $income)
    {
        if ($income->school_id !== $this->getSchoolId()) abort(403);
    }
}
