<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\Employee;
use App\Models\Student;
use App\Models\Setting;
use App\Models\IncomeCategory;        // ★ اضافه شد
use App\Models\ExpenseCategory;       // ★ اضافه شد
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SetupYearController extends Controller
{
    public function index(Request $request)
    {
        $newYearId = $request->input('new_year_id');
        $newYear = AcademicYear::findOrFail($newYearId);

        $schoolId = session('active_school_id');
        $schoolName = Setting::get('school_name', 'مکتب', $schoolId);
        $logo = Setting::get('logo', null, $schoolId);

        return view('school.setup-year', compact('newYear', 'schoolName', 'logo'));
    }

    /**
     * شروع انتقال واقعی
     */
    public function start(Request $request)
    {
        $newYearId = $request->input('new_year_id');
        $transferStudents = $request->boolean('transfer_students');
        $transferEmployees = $request->boolean('transfer_employees');

        $schoolId = session('active_school_id');

        $newYear = AcademicYear::findOrFail($newYearId);
        if ($newYear->school_id != $schoolId) {
            abort(403);
        }

        $stats = [
            'students'     => 0,
            'employees'    => 0,
            'income_cats'  => 0,
            'expense_cats' => 0,
        ];

        DB::transaction(function () use ($schoolId, $newYear, $transferStudents, $transferEmployees, &$stats) {
            // ──── ۱. انتقال دانش‌آموزان (با کپی ولی) ────
            if ($transferStudents) {
                $oldStudents = Student::where('school_id', $schoolId)
                    ->where('academic_year_id', session('active_academic_year_id'))
                    ->get();

                foreach ($oldStudents as $old) {
                    $newStudent = $old->replicate();

                    // کپی ولی
                    if ($old->guardian_id && $old->guardian) {
                        $newGuardian = $old->guardian->replicate();
                        $newGuardian->academic_year_id = $newYear->id;
                        $newGuardian->created_at = now();
                        $newGuardian->updated_at = now();
                        $newGuardian->save();
                        $newStudent->guardian_id = $newGuardian->id;
                    }

                    $newStudent->academic_year_id = $newYear->id;
                    $newStudent->class = $this->promoteClass($old->class);
                    $newStudent->student_code = $this->generateStudentCode($schoolId);
                    $newStudent->created_at = now();
                    $newStudent->updated_at = now();
                    $newStudent->save();

                    $stats['students']++;
                }
            }

            // ──── ۲. انتقال کارمندان ────
            if ($transferEmployees) {
                $oldEmployees = Employee::where('school_id', $schoolId)
                    ->where('academic_year_id', session('active_academic_year_id'))
                    ->get();

                foreach ($oldEmployees as $old) {
                    $newEmployee = $old->replicate();
                    $newEmployee->academic_year_id = $newYear->id;
                    $newEmployee->employee_code = $this->generateEmployeeCode($schoolId);
                    $newEmployee->created_at = now();
                    $newEmployee->updated_at = now();
                    $newEmployee->save();

                    $stats['employees']++;
                }
            }

            // ★ ۳. کپی دسته‌بندی‌های درآمد
            $oldIncomeCategories = IncomeCategory::where('school_id', $schoolId)
                ->where('academic_year_id', session('active_academic_year_id'))
                ->get();

            foreach ($oldIncomeCategories as $cat) {
                $newCat = $cat->replicate();
                $newCat->academic_year_id = $newYear->id;
                $newCat->created_at = now();
                $newCat->updated_at = now();
                $newCat->save();
                $stats['income_cats']++;
            }

            // ★ ۴. کپی دسته‌بندی‌های مصارف
            $oldExpenseCategories = ExpenseCategory::where('school_id', $schoolId)
                ->where('academic_year_id', session('active_academic_year_id'))
                ->get();

            foreach ($oldExpenseCategories as $cat) {
                $newCat = $cat->replicate();
                $newCat->academic_year_id = $newYear->id;
                $newCat->created_at = now();
                $newCat->updated_at = now();
                $newCat->save();
                $stats['expense_cats']++;
            }

            // ──── ۵. فعال‌سازی سال جدید ────
            session([
                'active_academic_year_id'    => $newYear->id,
                'active_academic_year_start' => $newYear->start_date,
                'active_academic_year_end'   => $newYear->end_date,
                'active_academic_year_name'  => $newYear->name,
            ]);
        });

        $message = "سال مالی {$newYear->name} با موفقیت راه‌اندازی شد.";
        if ($transferStudents) {
            $message .= " {$stats['students']} دانش‌آموز منتقل شدند.";
        }
        if ($transferEmployees) {
            $message .= " {$stats['employees']} کارمند منتقل شدند.";
        }
        $message .= " {$stats['income_cats']} دسته‌بندی درآمد و {$stats['expense_cats']} دسته‌بندی مصرف منتقل شدند.";

        return redirect()->route('school.dashboard')->with('success', $message);
    }

    // ──── متدهای کمکی ────

    private function promoteClass(?string $class): ?string
    {
        if (is_numeric($class)) {
            return (string)((int)$class + 1);
        }

        $map = [
            'اول' => 'دوم', 'دوم' => 'سوم', 'سوم' => 'چهارم',
            'چهارم' => 'پنجم', 'پنجم' => 'ششم', 'ششم' => 'هفتم',
            'هفتم' => 'هشتم', 'هشتم' => 'نهم', 'نهم' => 'دهم',
            'دهم' => 'یازدهم', 'یازدهم' => 'دوازدهم',
        ];

        return $map[$class] ?? $class;
    }

    private function generateStudentCode($schoolId): string
    {
        $year = date('Y') - 621;
        $last = Student::withTrashed()
            ->where('school_id', $schoolId)
            ->where('student_code', 'like', "STU-{$year}-%")
            ->orderBy('student_code', 'desc')
            ->first();

        $number = $last ? (int)substr($last->student_code, -4) + 1 : 1;

        do {
            $code = sprintf('STU-%d-%04d', $year, $number);
            $exists = Student::withTrashed()
                ->where('school_id', $schoolId)
                ->where('student_code', $code)
                ->exists();
            if ($exists) $number++;
        } while ($exists);

        return $code;
    }

    private function generateEmployeeCode($schoolId): string
    {
        $year = date('Y') - 621;
        $last = Employee::withTrashed()
            ->where('school_id', $schoolId)
            ->where('employee_code', 'like', "EMP-{$year}-%")
            ->orderBy('employee_code', 'desc')
            ->first();

        $number = $last ? (int)substr($last->employee_code, -4) + 1 : 1;

        do {
            $code = sprintf('EMP-%d-%04d', $year, $number);
            $exists = Employee::withTrashed()
                ->where('school_id', $schoolId)
                ->where('employee_code', $code)
                ->exists();
            if ($exists) $number++;
        } while ($exists);

        return $code;
    }
}
