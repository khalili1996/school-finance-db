<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Term;
use Illuminate\Http\Request;
use App\Helpers\JalaliHelper;

class TermController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    public function index(Request $request)
    {
        $academicYearId = $request->input('academic_year_id');
        $query = Term::where('school_id', $this->getSchoolId())->with('academicYear');

        if ($academicYearId) {
            $query->where('academic_year_id', $academicYearId);
        }

        $terms = $query->orderBy('start_date', 'desc')->paginate(20);
        $academicYears = AcademicYear::where('school_id', $this->getSchoolId())->orderBy('start_date', 'desc')->get();

        return view('school.terms.index', compact('terms', 'academicYears', 'academicYearId'));
    }

    public function create()
    {
        $academicYears = AcademicYear::where('school_id', $this->getSchoolId())->orderBy('start_date', 'desc')->get();
        return view('school.terms.create', compact('academicYears'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => 'required|string|max:255',
            'type'             => 'nullable|in:spring,fall,winter,summer',
            'start_date'       => 'required|string',
            'end_date'         => 'required|string',
            'is_active'        => 'boolean',
        ]);

        $data['start_date'] = JalaliHelper::toGregorian($request->start_date)->format('Y-m-d');
        $data['end_date']   = JalaliHelper::toGregorian($request->end_date)->format('Y-m-d');
        $data['school_id']  = $this->getSchoolId();

        Term::create($data);

        return redirect()->route('school.terms.index', ['academic_year_id' => $data['academic_year_id']])
            ->with('success', 'ترم جدید با موفقیت ایجاد شد.');
    }

    public function edit(Term $term)
    {
        $this->authorizeAccess($term);
        $academicYears = AcademicYear::where('school_id', $this->getSchoolId())->orderBy('start_date', 'desc')->get();
        return view('school.terms.edit', compact('term', 'academicYears'));
    }

    public function update(Request $request, Term $term)
    {
        $this->authorizeAccess($term);

        $data = $request->validate([
            'academic_year_id' => 'required|exists:academic_years,id',
            'name'             => 'required|string|max:255',
            'type'             => 'nullable|in:spring,fall,winter,summer',
            'start_date'       => 'required|string',
            'end_date'         => 'required|string',
            'is_active'        => 'boolean',
        ]);

        $data['start_date'] = JalaliHelper::toGregorian($request->start_date)->format('Y-m-d');
        $data['end_date']   = JalaliHelper::toGregorian($request->end_date)->format('Y-m-d');

        $term->update($data);

        return redirect()->route('school.terms.index', ['academic_year_id' => $data['academic_year_id']])
            ->with('success', 'ترم به‌روزرسانی شد.');
    }

    public function destroy(Term $term)
    {
        $this->authorizeAccess($term);
        $academicYearId = $term->academic_year_id;
        $term->delete();

        return redirect()->route('school.terms.index', ['academic_year_id' => $academicYearId])
            ->with('success', 'ترم حذف شد.');
    }

    private function authorizeAccess(Term $term)
    {
        if ($term->school_id !== $this->getSchoolId()) {
            abort(403);
        }
    }
}
