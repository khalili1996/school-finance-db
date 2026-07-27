<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AssetCategory;
use Illuminate\Http\Request;

class AssetCategoryController extends Controller
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
        $schoolId = $this->getSchoolId();
        $categories = AssetCategory::where('school_id', $schoolId)
            ->orderBy('name')
            ->paginate(15);

        return view('school.asset-categories.index', compact('categories'));
    }

    /**
     * فرم ایجاد
     */
    public function create()
    {
        return view('school.asset-categories.create');
    }

    /**
     * ذخیره
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255|unique:asset_categories,name,NULL,id,school_id,' . $this->getSchoolId(),
        ]);

        $data['school_id'] = $this->getSchoolId();
        AssetCategory::create($data);

        return redirect()->route('school.asset-categories.index')
            ->with('success', 'دسته‌بندی جدید با موفقیت ثبت شد.');
    }

    /**
     * فرم ویرایش
     */
    public function edit(AssetCategory $assetCategory)
    {
        $this->authorizeAccess($assetCategory);
        return view('school.asset-categories.edit', compact('assetCategory'));
    }

    /**
     * به‌روزرسانی
     */
    public function update(Request $request, AssetCategory $assetCategory)
    {
        $this->authorizeAccess($assetCategory);

        $data = $request->validate([
            'name' => 'required|string|max:255|unique:asset_categories,name,' . $assetCategory->id . ',id,school_id,' . $this->getSchoolId(),
        ]);

        $assetCategory->update($data);

        return redirect()->route('school.asset-categories.index')
            ->with('success', 'دسته‌بندی با موفقیت به‌روزرسانی شد.');
    }

    /**
     * حذف
     */
    public function destroy(AssetCategory $assetCategory)
    {
        $this->authorizeAccess($assetCategory);
        $assetCategory->delete();

        return redirect()->route('school.asset-categories.index')
            ->with('success', 'دسته‌بندی با موفقیت حذف شد.');
    }

    private function authorizeAccess(AssetCategory $category)
    {
        if ($category->school_id !== $this->getSchoolId()) {
            abort(403);
        }
    }
}
