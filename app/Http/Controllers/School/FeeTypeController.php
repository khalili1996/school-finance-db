<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\FeeType;
use Illuminate\Http\Request;

class FeeTypeController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    /**
     * لیست انواع هزینه‌ها
     */
    public function index()
    {
        $feeTypes = FeeType::where('school_id', $this->getSchoolId())
            ->orderBy('name')
            ->get();

        return view('school.fee-types.index', compact('feeTypes'));
    }

    /**
     * فرم ایجاد نوع هزینه جدید
     */
    public function create()
    {
        return view('school.fee-types.create');
    }

    /**
     * ذخیره‌ی نوع هزینه جدید
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'category'     => 'required|in:tuition,one_time,other',
            'is_optional'  => 'nullable|boolean',
            'is_active'    => 'nullable|boolean',
        ]);

        $data['school_id']   = $this->getSchoolId();
        $data['is_optional'] = $request->boolean('is_optional');
        $data['is_active']   = $request->boolean('is_active', true);

        FeeType::create($data);

        return redirect()->route('school.fee-types.index')
            ->with('success', 'نوع هزینه جدید با موفقیت ثبت شد.');
    }

    /**
     * فرم ویرایش نوع هزینه
     */
    public function edit(FeeType $feeType)
    {
        $this->authorizeAccess($feeType);
        return view('school.fee-types.edit', compact('feeType'));
    }

    /**
     * به‌روزرسانی نوع هزینه
     */
    public function update(Request $request, FeeType $feeType)
    {
        $this->authorizeAccess($feeType);

        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'category'     => 'required|in:tuition,one_time,other',
            'is_optional'  => 'nullable|boolean',
            'is_active'    => 'nullable|boolean',
        ]);

        $data['is_optional'] = $request->boolean('is_optional');
        $data['is_active']   = $request->boolean('is_active', true);

        $feeType->update($data);

        return redirect()->route('school.fee-types.index')
            ->with('success', 'نوع هزینه با موفقیت به‌روزرسانی شد.');
    }

    /**
     * حذف نوع هزینه
     */
    public function destroy(FeeType $feeType)
    {
        $this->authorizeAccess($feeType);
        $feeType->delete();

        return redirect()->route('school.fee-types.index')
            ->with('success', 'نوع هزینه با موفقیت حذف شد.');
    }

    private function authorizeAccess(FeeType $feeType)
    {
        if ($feeType->school_id !== $this->getSchoolId()) {
            abort(403, 'دسترسی غیرمجاز');
        }
    }
}
