<?php

namespace App\Http\Controllers;

use App\Models\StudentGuardian;
use Illuminate\Http\Request;

class StudentGuardianController extends Controller
{
    /**
     * نمایش فرم ویرایش ولی
     */
    public function edit(StudentGuardian $guardian)
    {
        // بررسی دسترسی: ولی باید متعلق به مدرسه‌ی کاربر باشد
        if ($guardian->school_id !== auth()->user()->school_id) {
            abort(403, 'دسترسی غیرمجاز');
        }

        return view('guardians.edit', compact('guardian'));
    }

    /**
     * به‌روزرسانی اطلاعات ولی
     */
    public function update(Request $request, StudentGuardian $guardian)
    {
        if ($guardian->school_id !== auth()->user()->school_id) {
            abort(403, 'دسترسی غیرمجاز');
        }

        $data = $request->validate([
            'full_name'        => 'required|string|max:255',
            'relation'         => 'nullable|string|max:50',
            'phone'            => 'nullable|string|max:20',
            'secondary_phone'  => 'nullable|string|max:20',
            'address'          => 'nullable|string|max:500',
            'is_primary'       => 'nullable|boolean',
        ]);

        $guardian->update($data);

        return redirect()->back()->with('success', 'اطلاعات ولی با موفقیت به‌روزرسانی شد.');
    }
}
