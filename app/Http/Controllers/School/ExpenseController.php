<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Month;
use App\Models\Cashbox;
use Illuminate\Http\Request;
use App\Helpers\JalaliHelper;
use App\Services\AccountingService;

class ExpenseController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');   // ★

        $query = Expense::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))   // ★
            ->with(['category', 'month']);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('consumer_name', 'like', "%{$search}%")
                  ->orWhere('received_by', 'like', "%{$search}%")
                  ->orWhere('invoice_number', 'like', "%{$search}%");
            });
        }
        if ($catId = $request->input('category_id')) {
            $query->where('expense_category_id', $catId);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(15);
        $categories = ExpenseCategory::where('school_id', $schoolId)->orderBy('name')->get();

        return view('school.expenses.index', compact('expenses', 'categories'));
    }

    public function create()
    {
        $schoolId = $this->getSchoolId();
        $categories = ExpenseCategory::where('school_id', $schoolId)->orderBy('name')->get();
        $months     = Month::where('school_id', $schoolId)->orderBy('number')->get();
        $cashboxes  = Cashbox::where('school_id', $schoolId)->where('is_active', true)->get();

        return view('school.expenses.create', compact('categories', 'months', 'cashboxes'));
    }

    public function store(Request $request, AccountingService $accounting)
    {
        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'total_amount'        => 'required|numeric|min:1',
            'paid_amount'         => 'nullable|numeric|min:0',
            'expense_date'        => 'required|string',
            'description'         => 'nullable|string|max:500',
            'quantity'            => 'nullable|numeric|min:0',
            'unit'                => 'nullable|string|max:30',
            'received_by'         => 'nullable|string|max:100',
            'consumer_name'       => 'nullable|string|max:100',
            'invoice_number'      => 'nullable|string|max:50',
            'scan_file'           => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
            'month_id'            => 'nullable|exists:months,id',
            'status'              => 'required|in:due,partially_paid,paid,cancelled',
            'cashbox_id'          => 'required|exists:cashboxes,id',
        ]);

        $data['school_id']   = $this->getSchoolId();
        $data['academic_year_id'] = session('active_academic_year_id');   // ★
        $data['paid_amount'] = $data['paid_amount'] ?? 0;

        if (!empty($data['expense_date'])) {
            $data['expense_date'] = JalaliHelper::toGregorian($data['expense_date'])->format('Y-m-d');
        }

        if ($request->hasFile('scan_file')) {
            $data['scan_file'] = $request->file('scan_file')->store('invoices', 'public');
        }

        $accounting->recordExpense($data);

        return redirect()->route('school.expenses.index')->with('success', 'مصرف جدید با موفقیت ثبت شد.');
    }

    public function edit(Expense $expense)
    {
        $this->authorizeAccess($expense);
        $schoolId = $this->getSchoolId();
        $categories = ExpenseCategory::where('school_id', $schoolId)->orderBy('name')->get();
        $months     = Month::where('school_id', $schoolId)->orderBy('number')->get();
        $cashboxes  = Cashbox::where('school_id', $schoolId)->where('is_active', true)->get();

        $expense->expense_date = $expense->expense_date
            ? JalaliHelper::toJalali($expense->expense_date, 'Y/m/d')
            : null;

        return view('school.expenses.edit', compact('expense', 'categories', 'months', 'cashboxes'));
    }

    public function update(Request $request, Expense $expense, AccountingService $accounting)
    {
        $this->authorizeAccess($expense);
        $data = $request->validate([
            'title'               => 'required|string|max:255',
            'expense_category_id' => 'required|exists:expense_categories,id',
            'total_amount'        => 'required|numeric|min:1',
            'paid_amount'         => 'nullable|numeric|min:0',
            'expense_date'        => 'required|string',
            'description'         => 'nullable|string|max:500',
            'quantity'            => 'nullable|numeric|min:0',
            'unit'                => 'nullable|string|max:30',
            'received_by'         => 'nullable|string|max:100',
            'consumer_name'       => 'nullable|string|max:100',
            'invoice_number'      => 'nullable|string|max:50',
            'scan_file'           => 'nullable|image|mimes:jpeg,png,jpg,pdf|max:2048',
            'month_id'            => 'nullable|exists:months,id',
            'status'              => 'required|in:due,partially_paid,paid,cancelled',
            'cashbox_id'          => 'required|exists:cashboxes,id',
        ]);

        $data['school_id']   = $this->getSchoolId();
        $data['paid_amount'] = $data['paid_amount'] ?? 0;

        if (!empty($data['expense_date'])) {
            $data['expense_date'] = JalaliHelper::toGregorian($data['expense_date'])->format('Y-m-d');
        }

        if ($request->hasFile('scan_file')) {
            $data['scan_file'] = $request->file('scan_file')->store('invoices', 'public');
        }

        $accounting->updateExpense($expense, $data);

        return redirect()->route('school.expenses.index')->with('success', 'مصرف با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(Expense $expense, AccountingService $accounting)
    {
        if ($expense->school_id !== $this->getSchoolId()) {
            abort(403);
        }

        $accounting->deleteExpense($expense);

        return redirect()->route('school.expenses.index')
            ->with('success', 'مصرف و تمام تراکنش‌های مرتبط حذف شدند.');
    }

    public function report(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');   // ★
        $query = Expense::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))   // ★
            ->with(['category', 'month']);
        $from = $request->input('date_from');
        $to   = $request->input('date_to');

        $fromGregorian = $from ? JalaliHelper::toGregorian($from)->format('Y-m-d') : null;
        $toGregorian   = $to   ? JalaliHelper::toGregorian($to)->format('Y-m-d') : null;

        if ($fromGregorian) $query->where('expense_date', '>=', $fromGregorian);
        if ($toGregorian)   $query->where('expense_date', '<=', $toGregorian);

        if ($catId = $request->input('category_id')) {
            $query->where('expense_category_id', $catId);
        }
        $expenses = $query->orderBy('expense_date', 'desc')->get();
        $categories = ExpenseCategory::where('school_id', $schoolId)->orderBy('name')->get();
        $totalAmount    = $expenses->sum('total_amount');
        $totalPaid      = $expenses->sum('paid_amount');
        $totalRemaining = $expenses->sum(fn($e) => $e->total_amount - $e->paid_amount);

        $school = \App\Models\School::find($schoolId);

        return view('school.expenses.report', compact(
            'expenses', 'totalAmount', 'totalPaid', 'totalRemaining',
            'from', 'to', 'categories', 'school'
        ));
    }

    private function authorizeAccess(Expense $expense)
    {
        if ($expense->school_id !== $this->getSchoolId()) abort(403);
    }
}
