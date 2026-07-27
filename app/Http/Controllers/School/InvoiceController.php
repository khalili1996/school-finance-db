<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Expense;
use App\Models\Month;
use App\Models\School;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    /**
     * لیست فاکتورها
     */
    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');   // ★

        $query = Expense::where('school_id', $schoolId)
            ->whereNotNull('invoice_number')
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))   // ★
            ->orderBy('expense_date', 'desc');

        if ($search = $request->input('search')) {
            $query->where('invoice_number', 'like', "%{$search}%");
        }

        $invoices = $query->paginate(15);

        return view('school.invoices.index', compact('invoices'));
    }

    /**
     * گزارش چاپی فاکتورها با فیلتر ماه
     */
    public function report(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');   // ★

        $query = Expense::where('school_id', $schoolId)
            ->whereNotNull('invoice_number')
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))   // ★
            ->with('month');

        if ($monthId = $request->input('month_id')) {
            $query->where('month_id', $monthId);
        }

        $invoices = $query->orderBy('expense_date', 'desc')->get();
        $months = Month::where('school_id', $schoolId)->orderBy('number')->get();
        $selectedMonth = $monthId ? Month::find($monthId) : null;
        $school = School::find($schoolId);

        return view('school.invoices.report', compact('invoices', 'months', 'selectedMonth', 'school'));
    }
}
