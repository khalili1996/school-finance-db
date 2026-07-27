<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use App\Helpers\JalaliHelper;

class AcademicYearController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    public function index()
    {
        $schoolId = $this->getSchoolId();
        $academicYears = AcademicYear::where('school_id', $schoolId)
            ->orderBy('start_date', 'desc')
            ->get();

        return view('school.academic-years.index', compact('academicYears'));
    }

    public function create()
    {
        return view('school.academic-years.create');
    }

    public function store(Request $request)
    {
        $schoolId = $this->getSchoolId();

        $data = $request->validate([
            'name'       => 'required|string|max:50|unique:academic_years,name,NULL,id,school_id,' . $schoolId,
            'start_date' => 'required|string',
            'end_date'   => 'required|string',
        ]);

        $data['start_date'] = JalaliHelper::toGregorian($data['start_date'])->format('Y-m-d');
        $data['end_date']   = JalaliHelper::toGregorian($data['end_date'])->format('Y-m-d');

        $data['school_id'] = $schoolId;
        $academicYear = AcademicYear::create($data);

        // اگر اولین سال مالی این مدرسه است
        $isFirstYear = AcademicYear::where('school_id', $schoolId)->count() === 1;

        if ($isFirstYear) {
            session([
                'active_academic_year_id'    => $academicYear->id,
                'active_academic_year_start' => $academicYear->start_date,
                'active_academic_year_end'   => $academicYear->end_date,
                'active_academic_year_name'  => $academicYear->name,
            ]);
            return redirect()->route('school.dashboard')->with('success', 'سال مالی جدید ایجاد و فعال شد.');
        }

        return redirect()->route('school.setup-year.index', ['new_year_id' => $academicYear->id])
            ->with('success', 'سال مالی جدید ایجاد شد. لطفاً تنظیمات انتقال را انجام دهید.');
    }

    // ★★★ این متد کلیدی است ★★★
    public function setAcademicYear(AcademicYear $academicYear)
    {
        if ($academicYear->school_id != $this->getSchoolId()) {
            abort(403);
        }

        // تست: آیا اصلاً وارد این متد می‌شویم؟
        // dd('ورود به setAcademicYear برای سال: ' . $academicYear->name);

        session([
            'active_academic_year_id'    => $academicYear->id,
            'active_academic_year_start' => $academicYear->start_date,
            'active_academic_year_end'   => $academicYear->end_date,
            'active_academic_year_name'  => $academicYear->name,
        ]);
        session()->save();

        return redirect()->route('school.dashboard', ['t' => time()])
                         ->with('success', 'سال مالی فعال شد: ' . $academicYear->name);
    }

    // سایر متدهای resource (show, edit, update, destroy) در صورت نیاز
}
