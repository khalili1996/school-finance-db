<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\IncomeCategory;
use Illuminate\Http\Request;

class IncomeCategoryController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    /**
     * لیست دسته‌بندی‌ها
     */
 public function index()
{
    $activeYearId = session('active_academic_year_id');

    $categories = IncomeCategory::where('school_id', $this->getSchoolId())
        ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
        ->orderBy('name')
        ->get();

    return view('school.income-categories.index', compact('categories'));
}

    /**
     * فرم ایجاد دسته‌بندی جدید
     */
    public function create()
    {
        return view('school.income-categories.create');
    }

    /**
     * ذخیره‌ی دسته‌بندی جدید
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $data['school_id'] = $this->getSchoolId();
$data['academic_year_id'] = session('active_academic_year_id');   // ★
        IncomeCategory::create($data);

        return redirect()->route('school.income-categories.index')
            ->with('success', 'دسته‌بندی جدید با موفقیت ثبت شد.');
    }

    /**
     * فرم ویرایش
     */
    public function edit(IncomeCategory $incomeCategory)
    {
        $this->authorizeAccess($incomeCategory);
        return view('school.income-categories.edit', compact('incomeCategory'));
    }

    /**
     * به‌روزرسانی
     */
    public function update(Request $request, IncomeCategory $incomeCategory)
    {
        $this->authorizeAccess($incomeCategory);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
        ]);

        $incomeCategory->update($data);

        return redirect()->route('school.income-categories.index')
            ->with('success', 'دسته‌بندی با موفقیت به‌روزرسانی شد.');
    }

    /**
     * حذف
     */
    public function destroy(IncomeCategory $incomeCategory)
    {
        $this->authorizeAccess($incomeCategory);
        $incomeCategory->delete();

        return redirect()->route('school.income-categories.index')
            ->with('success', 'دسته‌بندی با موفقیت حذف شد.');
    }

    private function authorizeAccess(IncomeCategory $incomeCategory)
    {
        if ($incomeCategory->school_id !== $this->getSchoolId()) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }
}
