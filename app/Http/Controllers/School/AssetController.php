<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Helpers\JalaliHelper;
use Illuminate\Http\Request;

class AssetController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    /**
     * لیست تجهیزات
     */
    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $query = Asset::where('school_id', $schoolId)
            ->with('category')
            ->orderBy('asset_code');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('custodian', 'like', "%{$search}%");
            });
        }

        $assets = $query->paginate(15);
        $categories = AssetCategory::where('school_id', $schoolId)->orderBy('name')->get();

        return view('school.assets.index', compact('assets', 'categories'));
    }

    /**
     * فرم ایجاد تجهیز جدید
     */
    public function create()
    {
        $schoolId = $this->getSchoolId();
        $categories = AssetCategory::where('school_id', $schoolId)->orderBy('name')->get();
        return view('school.assets.create', compact('categories'));
    }

    /**
     * ذخیره تجهیز جدید
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'asset_code'    => 'required|string|max:30|unique:assets,asset_code',
            'category_id'   => 'required|exists:asset_categories,id',
            'description'   => 'required|string|max:500',
            'quantity'      => 'required|integer|min:1',
            'custodian'     => 'nullable|string|max:200',
            'unit_price'    => 'required|numeric|min:0',
            'purchase_date' => 'required|string', // تاریخ شمسی
            'status'        => 'required|in:active,transferred,broken,scrap',
            'notes'         => 'nullable|string|max:500',
        ]);

        $data['school_id']    = $this->getSchoolId();
        $data['total_price']  = $data['unit_price'] * $data['quantity'];
        $data['purchase_date'] = JalaliHelper::toGregorian($data['purchase_date'])->format('Y-m-d');

        Asset::create($data);

        return redirect()->route('school.assets.index')
            ->with('success', 'تجهیزات با موفقیت ثبت شد.');
    }

    /**
     * نمایش جزئیات (پیش‌نمایش)
     */
    public function show(Asset $asset)
    {
        $this->authorizeAccess($asset);
        return view('school.assets.show', compact('asset'));
    }

    /**
     * فرم ویرایش تجهیز
     */
    public function edit(Asset $asset)
    {
        $this->authorizeAccess($asset);
        $schoolId = $this->getSchoolId();
        $categories = AssetCategory::where('school_id', $schoolId)->orderBy('name')->get();
        // تبدیل تاریخ میلادی به شمسی برای نمایش
        $asset->purchase_date = JalaliHelper::toJalali($asset->purchase_date, 'Y/m/d');
        return view('school.assets.edit', compact('asset', 'categories'));
    }

    /**
     * به‌روزرسانی تجهیز
     */
    public function update(Request $request, Asset $asset)
    {
        $this->authorizeAccess($asset);

        $data = $request->validate([
            'asset_code'    => 'required|string|max:30|unique:assets,asset_code,' . $asset->id,
            'category_id'   => 'required|exists:asset_categories,id',
            'description'   => 'required|string|max:500',
            'quantity'      => 'required|integer|min:1',
            'custodian'     => 'nullable|string|max:200',
            'unit_price'    => 'required|numeric|min:0',
            'purchase_date' => 'required|string',
            'status'        => 'required|in:active,transferred,broken,scrap',
            'notes'         => 'nullable|string|max:500',
        ]);

        $data['total_price']   = $data['unit_price'] * $data['quantity'];
        $data['purchase_date'] = JalaliHelper::toGregorian($data['purchase_date'])->format('Y-m-d');

        $asset->update($data);

        return redirect()->route('school.assets.index')
            ->with('success', 'تجهیزات به‌روزرسانی شد.');
    }

    /**
     * حذف تجهیز
     */
    public function destroy(Asset $asset)
    {
        $this->authorizeAccess($asset);
        $asset->delete();
        return redirect()->route('school.assets.index')
            ->with('success', 'تجهیزات حذف شد.');
    }

    /**
     * گزارش چاپی
     */
    public function printReport(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $query = Asset::where('school_id', $schoolId)
            ->with('category')
            ->orderBy('asset_code');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }
        if ($categoryId = $request->input('category_id')) {
            $query->where('category_id', $categoryId);
        }
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('description', 'like', "%{$search}%")
                  ->orWhere('asset_code', 'like', "%{$search}%")
                  ->orWhere('custodian', 'like', "%{$search}%");
            });
        }

        $assets = $query->get();

        $selectedCategory = null;
        if ($request->input('category_id')) {
            $selectedCategory = AssetCategory::find($request->input('category_id'));
        }

        return view('school.assets.print', compact('assets', 'selectedCategory'));
    }

    private function authorizeAccess(Asset $asset)
    {
        if ($asset->school_id !== $this->getSchoolId()) {
            abort(403);
        }
    }
}
