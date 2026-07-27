<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\StudentGuardian;
use Illuminate\Http\Request;

class GuardianController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');

        $query = StudentGuardian::where('school_id', $schoolId)
            ->when($activeYearId, fn($q) => $q->where('academic_year_id', $activeYearId))
            ->withCount('students')
            ->with(['students.studentFees', 'students.payments']);

        // جستجو
        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%")
                  ->orWhere('job', 'like', "%{$search}%");
            });
        }

        // فیلتر وضعیت
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        // فیلتر نسبت
        if ($rel = $request->input('relation')) {
            $query->where('relation', $rel);
        }

        // فیلتر تعداد فرزند
        if ($kids = $request->input('kids')) {
            if ($kids === '1') $query->having('students_count', '=', 1);
            elseif ($kids === '2') $query->having('students_count', '=', 2);
            elseif ($kids === '3+') $query->having('students_count', '>=', 3);
        }

        $guardians = $query->orderBy('created_at', 'desc')->get();

        // محاسبه بدهی
        foreach ($guardians as $guardian) {
            $totalFee = 0; $totalPaid = 0; $hasDiscount = false; $hasFree = false;
            foreach ($guardian->students as $student) {
                $fee = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                $paid = $student->payments->sum('amount');
                $totalFee += $fee; $totalPaid += $paid;
                if ($student->studentFees->contains(fn($f) => $f->discount > 0)) $hasDiscount = true;
                if ($student->studentFees->isEmpty() || $fee == 0) $hasFree = true;
            }
            $guardian->total_debt = max($totalFee - $totalPaid, 0);
            $guardian->is_debtor = $guardian->total_debt > 0;
            $guardian->has_discount = $hasDiscount;
            $guardian->has_free = $hasFree;
        }

        // فیلتر مالی
        if ($financial = $request->input('financial')) {
            if ($financial === 'debtor') $guardians = $guardians->filter(fn($g) => $g->is_debtor);
            elseif ($financial === 'settled') $guardians = $guardians->filter(fn($g) => !$g->is_debtor);
            elseif ($financial === 'discount') $guardians = $guardians->filter(fn($g) => $g->has_discount);
            elseif ($financial === 'free') $guardians = $guardians->filter(fn($g) => $g->has_free);
        }

        $page = $request->input('page', 1);
        $perPage = 15;
        $guardians = new \Illuminate\Pagination\LengthAwarePaginator(
            $guardians->forPage($page, $perPage),
            $guardians->count(),
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('school.guardians.index', compact('guardians'));
    }

    public function create()
    {
        return view('school.guardians.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'full_name'        => 'required|string|max:255',
            'relation'         => 'nullable|string|max:50',
            'phone'            => 'nullable|string|max:20',
            'secondary_phone'  => 'nullable|string|max:20',
            'national_id'      => 'nullable|string|max:50',
            'education'        => 'nullable|string|max:100',
            'job'              => 'nullable|string|max:100',
            'address'          => 'nullable|string|max:500',
            'is_active'        => 'nullable|boolean',
        ]);

        $data['school_id'] = $this->getSchoolId();
        $data['academic_year_id'] = session('active_academic_year_id');
        $data['is_active'] = $request->boolean('is_active', true);

        StudentGuardian::create($data);
        return redirect()->route('school.guardians.index')->with('success', 'ولی جدید ثبت شد.');
    }

    public function show(StudentGuardian $guardian)
    {
        $this->authorizeAccess($guardian);
        $guardian->load('students.studentFees', 'students.payments');

        $totalFee = 0; $totalPaid = 0;
        foreach ($guardian->students as $student) {
            $totalFee += $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
            $totalPaid += $student->payments->sum('amount');
        }
        $guardian->total_fee = $totalFee;
        $guardian->total_paid = $totalPaid;
        $guardian->total_debt = max($totalFee - $totalPaid, 0);

        return view('school.guardians.show', compact('guardian'));
    }

    public function edit(StudentGuardian $guardian)
    {
        $this->authorizeAccess($guardian);
        $guardian->load('students');
        return view('school.guardians.edit', compact('guardian'));
    }

    public function update(Request $request, StudentGuardian $guardian)
    {
        $this->authorizeAccess($guardian);
        $data = $request->validate([
            'full_name'        => 'required|string|max:255',
            'relation'         => 'nullable|string|max:50',
            'phone'            => 'nullable|string|max:20',
            'secondary_phone'  => 'nullable|string|max:20',
            'national_id'      => 'nullable|string|max:50',
            'education'        => 'nullable|string|max:100',
            'job'              => 'nullable|string|max:100',
            'address'          => 'nullable|string|max:500',
            'is_active'        => 'nullable|boolean',
        ]);
        $guardian->update($data);
        return redirect()->route('school.guardians.index')->with('success', 'اطلاعات ولی به‌روزرسانی شد.');
    }

    public function destroy(StudentGuardian $guardian)
    {
        $this->authorizeAccess($guardian);
        $guardian->delete();
        return redirect()->route('school.guardians.index')->with('success', 'ولی حذف شد.');
    }

    public function report()
    {
        $schoolId = $this->getSchoolId();
        $guardians = StudentGuardian::where('school_id', $schoolId)->withCount('students')->get();

        $totalGuardians = $guardians->count();
        $totalFathers = $guardians->where('relation', 'father')->count();
        $totalMothers = $guardians->where('relation', 'mother')->count();
        $totalOthers = $guardians->whereNotIn('relation', ['father', 'mother'])->count();

        $debtorFamilies = 0; $discountFamilies = 0; $orphanFamilies = 0;
        foreach ($guardians as $guardian) {
            $guardian->load('students.studentFees', 'students.payments');
            $totalFee = 0; $totalPaid = 0; $hasDiscount = false; $hasOrphan = false;
            foreach ($guardian->students as $student) {
                $fee = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
                $paid = $student->payments->sum('amount');
                $totalFee += $fee; $totalPaid += $paid;
                if ($student->studentFees->contains(fn($f) => $f->discount > 0)) $hasDiscount = true;
                if ($student->status === 'three_piece') $hasOrphan = true;
            }
            if (($totalFee - $totalPaid) > 0) $debtorFamilies++;
            if ($hasDiscount) $discountFamilies++;
            if ($hasOrphan) $orphanFamilies++;
        }

        return view('school.guardians.report', compact(
            'totalGuardians', 'totalFathers', 'totalMothers', 'totalOthers',
            'debtorFamilies', 'discountFamilies', 'orphanFamilies'
        ));
    }
    public function preview(StudentGuardian $guardian)
{
    $this->authorizeAccess($guardian);
    $guardian->load(['students.studentFees.month', 'students.payments', 'students.school']);

    // دریافت همه‌ی ماه‌های تعریف‌شده برای این مدرسه
    $allMonths = \App\Models\Month::where('school_id', $this->getSchoolId())
                    ->orderBy('number')
                    ->get();

    // محاسبه‌ی وضعیت مالی هر فرزند
    foreach ($guardian->students as $student) {
        $student->total_fee = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
        $student->total_paid = $student->payments->sum('amount');
        $student->balance = max($student->total_fee - $student->total_paid, 0);

        // ماه‌های پرداخت‌نشده
        $unpaidMonths = [];
        foreach ($allMonths as $month) {
            $fee = $student->studentFees->firstWhere('month_id', $month->id);
            if ($fee) {
                $paidForMonth = $student->payments->where('fee_id', $fee->id)->sum('amount');
                $remaining = ($fee->amount - $fee->discount) - $paidForMonth;
                if ($remaining > 0) {
                    $unpaidMonths[] = $month->name;
                }
            }
        }
        $student->unpaid_months = $unpaidMonths;
    }

    // مجموع مالی خانواده
    $totalFamilyFee = $guardian->students->sum('total_fee');
    $totalFamilyPaid = $guardian->students->sum('total_paid');
    $totalFamilyDebt = max($totalFamilyFee - $totalFamilyPaid, 0);

    return view('school.guardians.preview', compact('guardian', 'totalFamilyFee', 'totalFamilyPaid', 'totalFamilyDebt'));
}
    private function authorizeAccess(StudentGuardian $guardian)
    {
        if ($guardian->school_id !== $this->getSchoolId()) abort(403, 'دسترسی غیرمجاز');
    }
}
