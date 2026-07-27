<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;

class ExpenseCategoryController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    public function index()
    {
        $activeYearId = session('active_academic_year_id');
$categories = ExpenseCategory::where('school_id', $this->getSchoolId())
    ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
    ->orderBy('name')
    ->get();
        return view('school.expense-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('school.expense-categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $data['school_id'] = $this->getSchoolId();
$data['academic_year_id'] = session('active_academic_year_id');   // ★
        ExpenseCategory::create($data);

        return redirect()->route('school.expense-categories.index')
            ->with('success', 'دسته‌بندی جدید با موفقیت ثبت شد.');
    }

    public function edit(ExpenseCategory $expenseCategory)
    {
        $this->authorizeAccess($expenseCategory);
        return view('school.expense-categories.edit', compact('expenseCategory'));
    }

    public function update(Request $request, ExpenseCategory $expenseCategory)
    {
        $this->authorizeAccess($expenseCategory);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $expenseCategory->update($data);

        return redirect()->route('school.expense-categories.index')
            ->with('success', 'دسته‌بندی با موفقیت به‌روزرسانی شد.');
    }

    public function destroy(ExpenseCategory $expenseCategory)
    {
        $this->authorizeAccess($expenseCategory);
        $expenseCategory->delete();

        return redirect()->route('school.expense-categories.index')
            ->with('success', 'دسته‌بندی با موفقیت حذف شد.');
    }

    private function authorizeAccess(ExpenseCategory $expenseCategory)
    {
        if ($expenseCategory->school_id !== $this->getSchoolId()) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }
}
