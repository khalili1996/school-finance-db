<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Salary;
use App\Models\SalaryPayment;
use App\Models\Cashbox;
use Illuminate\Http\Request;
use App\Services\AccountingService;
use App\Helpers\JalaliHelper;

class SalaryPaymentController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    public function create(Request $request)
    {
        $salaryId = $request->input('salary_id');
        $salary = Salary::with('employee')->findOrFail($salaryId);
        $this->authorizeAccess($salary);
        $cashboxes = Cashbox::where('school_id', $this->getSchoolId())->where('is_active', true)->get();
        $remaining = $salary->total_amount - $salary->paid_amount;
        return view('school.salary_payments.create', compact('salary', 'cashboxes', 'remaining'));
    }

    public function store(Request $request, AccountingService $accounting)
    {
        $data = $request->validate([
            'salary_id'      => 'required|exists:salaries,id',
            'amount'         => 'required|numeric|min:1',
            'payment_date'   => 'required|string',
            'cashbox_id'     => 'required|exists:cashboxes,id',
            'receipt_number' => 'nullable|string|max:50',
            'notes'          => 'nullable|string|max:500',
        ]);

        $salary = Salary::findOrFail($data['salary_id']);
        $this->authorizeAccess($salary);

        if ($data['amount'] > ($salary->total_amount - $salary->paid_amount)) {
            return back()->withErrors(['amount' => 'مبلغ پرداختی بیش از مانده معاش است.'])->withInput();
        }

        $data['payment_date']    = JalaliHelper::toGregorian($data['payment_date'])->format('Y-m-d');
        $data['school_id']       = $this->getSchoolId();
        $data['employee_id']     = $salary->employee_id;
        $data['payment_method']  = 'cash';
        $data['academic_year_id']= session('active_academic_year_id');   // ★

        $accounting->recordSalaryPayment($data);

        $salary->increment('paid_amount', $data['amount']);
        $salary->refresh();
        if ($salary->paid_amount >= $salary->total_amount) {
            $salary->update(['status' => 'paid']);
        } elseif ($salary->paid_amount > 0) {
            $salary->update(['status' => 'partially_paid']);
        }

        return redirect()->route('school.salaries.index')->with('success', 'پرداخت معاش با موفقیت ثبت شد.');
    }

    public function quickStore(Request $request, AccountingService $accounting)
    {
        $data = $request->validate([
            'employee_id'    => 'required|exists:employees,id',
            'month_id'       => 'required|exists:months,id',
            'amount'         => 'required|numeric|min:1',
            'payment_date'   => 'required|string',
            'cashbox_id'     => 'required|exists:cashboxes,id',
            'receipt_number' => 'nullable|string|max:50',
            'notes'          => 'nullable|string|max:500',
        ]);

        $schoolId = $this->getSchoolId();
        $activeYearId = session('active_academic_year_id');   // ★

        $salary = Salary::where('school_id', $schoolId)
                    ->where('employee_id', $data['employee_id'])
                    ->where('month_id', $data['month_id'])
                    ->where('academic_year_id', $activeYearId)  // ★
                    ->first();

        if (!$salary) {
            return response()->json(['success' => false, 'message' => 'معاشی برای این کارمند و ماه ثبت نشده است.'], 422);
        }

        if ($data['amount'] > ($salary->total_amount - $salary->paid_amount)) {
            return response()->json(['success' => false, 'message' => 'مبلغ پرداختی بیش از مانده معاش است.'], 422);
        }

        $data['payment_date']    = JalaliHelper::toGregorian($data['payment_date'])->format('Y-m-d');
        $data['school_id']       = $schoolId;
        $data['salary_id']       = $salary->id;
        $data['employee_id']     = $data['employee_id'];
        $data['payment_method']  = 'cash';
        $data['academic_year_id']= $activeYearId;   // ★

        $accounting->recordSalaryPayment($data);

        $salary->increment('paid_amount', $data['amount']);
        $salary->refresh();
        if ($salary->paid_amount >= $salary->total_amount) {
            $salary->update(['status' => 'paid']);
        } elseif ($salary->paid_amount > 0) {
            $salary->update(['status' => 'partially_paid']);
        }

        return response()->json(['success' => true, 'message' => 'پرداخت با موفقیت ثبت شد.']);
    }

    public function edit(SalaryPayment $salaryPayment)
    {
        $this->authorizeAccess($salaryPayment->salary);
        $cashboxes = Cashbox::where('school_id', $this->getSchoolId())->where('is_active', true)->get();
        $remaining = $salaryPayment->salary->total_amount - $salaryPayment->salary->paid_amount + $salaryPayment->amount;
        $salaryPayment->payment_date = $salaryPayment->payment_date
            ? JalaliHelper::toJalali($salaryPayment->payment_date, 'Y/m/d')
            : null;

        return view('school.salary_payments.edit', compact('salaryPayment', 'cashboxes', 'remaining'));
    }

    public function update(Request $request, SalaryPayment $salaryPayment, AccountingService $accounting)
    {
        $this->authorizeAccess($salaryPayment->salary);

        $data = $request->validate([
            'amount'         => 'required|numeric|min:1',
            'payment_date'   => 'required|string',
            'cashbox_id'     => 'required|exists:cashboxes,id',
            'receipt_number' => 'nullable|string|max:50',
            'notes'          => 'nullable|string|max:500',
        ]);

        $salary = $salaryPayment->salary;
        $available = $salary->total_amount - $salary->paid_amount + $salaryPayment->amount;
        if ($data['amount'] > $available) {
            return back()->withErrors(['amount' => 'مبلغ پرداختی بیش از مانده معاش است.'])->withInput();
        }

        $data['payment_date']    = JalaliHelper::toGregorian($data['payment_date'])->format('Y-m-d');
        $data['school_id']       = $this->getSchoolId();
        $data['employee_id']     = $salary->employee_id;
        $data['payment_method']  = $salaryPayment->payment_method ?? 'cash';
        $data['academic_year_id']= session('active_academic_year_id');   // ★

        $accounting->updateSalaryPayment($salaryPayment, $data);

        $salary->paid_amount = SalaryPayment::where('salary_id', $salary->id)->sum('amount');
        $salary->save();
        $salary->refresh();
        if ($salary->paid_amount >= $salary->total_amount) {
            $salary->update(['status' => 'paid']);
        } elseif ($salary->paid_amount > 0) {
            $salary->update(['status' => 'partially_paid']);
        } else {
            $salary->update(['status' => 'due']);
        }

        return redirect()->route('school.salaries.index')->with('success', 'پرداخت معاش به‌روزرسانی شد.');
    }

    public function destroy(SalaryPayment $salaryPayment, AccountingService $accounting)
    {
        $this->authorizeAccess($salaryPayment->salary);

        $salary = $salaryPayment->salary;

        $accounting->deleteSalaryPayment($salaryPayment);

        $salary->paid_amount = SalaryPayment::where('salary_id', $salary->id)->sum('amount');
        $salary->save();
        $salary->refresh();
        if ($salary->paid_amount >= $salary->total_amount) {
            $salary->update(['status' => 'paid']);
        } elseif ($salary->paid_amount > 0) {
            $salary->update(['status' => 'partially_paid']);
        } else {
            $salary->update(['status' => 'due']);
        }

        return redirect()->route('school.salaries.index')->with('success', 'پرداخت معاش حذف شد.');
    }

    private function authorizeAccess(Salary $salary)
    {
        if ($salary->school_id !== $this->getSchoolId()) abort(403);
    }
}
