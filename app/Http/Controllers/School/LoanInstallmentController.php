<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\LoanFundTransaction;
use App\Helpers\JalaliHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanInstallmentController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    /**
     * لیست اقساط یک وام (مدیریت پرداخت‌ها)
     */
    public function index(Loan $loan)
    {
        $this->authorizeAccess($loan);
        $loan->load('installments', 'employee');

        return view('school.loans.installments', compact('loan'));
    }

    /**
     * فرم ویرایش پرداخت قسط
     */
    public function edit(LoanInstallment $installment)
    {
        $this->authorizeInstallmentAccess($installment);
        $installment->paid_date = $installment->paid_date
            ? JalaliHelper::toJalali($installment->paid_date, 'Y/m/d')
            : null;

        return view('school.loans.installment-edit', compact('installment'));
    }

    /**
     * به‌روزرسانی پرداخت قسط
     */
    public function update(Request $request, LoanInstallment $installment)
    {
        $this->authorizeInstallmentAccess($installment);

        $data = $request->validate([
            'paid_amount' => 'required|numeric|min:1|max:' . $installment->amount,
            'paid_date'   => 'required|string', // تاریخ شمسی
            'notes'       => 'nullable|string|max:500',
        ]);

        $schoolId = $this->getSchoolId();
        $oldPaidAmount = $installment->paid_amount ?? 0;

        $paidDateGregorian = JalaliHelper::toGregorian($data['paid_date'])->format('Y-m-d');

        DB::transaction(function () use ($installment, $data, $paidDateGregorian, $schoolId, $oldPaidAmount) {
            // حذف تراکنش قبلی
            LoanFundTransaction::where('reference_type', LoanInstallment::class)
                ->where('reference_id', $installment->id)
                ->delete();

            // اگر مبلغ بزرگتر از صفر باشد تراکنش جدید ثبت کن
            if ($data['paid_amount'] > 0) {
                LoanFundTransaction::create([
                    'school_id'        => $schoolId,
                    'type'             => 'repayment_installment',
                    'amount'           => $data['paid_amount'],
                    'transaction_date' => $paidDateGregorian,
                    'reference_type'   => LoanInstallment::class,
                    'reference_id'     => $installment->id,
                    'description'      => 'پرداخت قسط (ویرایش) - ' . ($installment->loan->employee->full_name ?? $installment->loan->borrower_name),
                ]);
            }

            $installment->update([
                'paid_date'   => $paidDateGregorian,
                'paid_amount' => $data['paid_amount'],
                'status'      => $data['paid_amount'] > 0 ? 'paid' : 'pending',
                'notes'       => $data['notes'] ?? $installment->notes,
            ]);

            // به‌روزرسانی وضعیت وام
            if ($installment->loan->installments()->where('status', 'pending')->doesntExist()) {
                $installment->loan->update(['status' => 'completed']);
            } else {
                $installment->loan->update(['status' => 'active']);
            }
        });

        return redirect()->route('school.loans.installments', $installment->loan)
            ->with('success', 'پرداخت قسط با موفقیت ویرایش شد.');
    }

    /**
     * حذف پرداخت قسط (برگرداندن به حالت pending)
     */
    public function destroy(LoanInstallment $installment)
    {
        $this->authorizeInstallmentAccess($installment);

        DB::transaction(function () use ($installment) {
            LoanFundTransaction::where('reference_type', LoanInstallment::class)
                ->where('reference_id', $installment->id)
                ->delete();

            $installment->update([
                'status'      => 'pending',
                'paid_date'   => null,
                'paid_amount' => null,
                'notes'       => null,
            ]);

            $installment->loan->update(['status' => 'active']);
        });

        return back()->with('success', 'پرداخت قسط حذف شد و قسط به حالت معوق برگشت.');
    }

    /**
     * رسید چاپی پرداخت قسط
     */
    public function receipt(LoanInstallment $installment)
    {
        $this->authorizeInstallmentAccess($installment);
        $installment->load('loan.employee');

        return view('school.loans.installment-receipt', compact('installment'));
    }

    /**
     * پرداخت سریع قسط (AJAX)
     */
    public function payInstallment(Request $request)
    {
        $data = $request->validate([
            'installment_id' => 'required|exists:loan_installments,id',
            'paid_date'      => 'required|string',  // تاریخ شمسی
            'notes'          => 'nullable|string|max:500',
        ]);

        $installment = LoanInstallment::findOrFail($data['installment_id']);
        $loan = $installment->loan;
        $schoolId = $this->getSchoolId();

        if ($loan->school_id != $schoolId) {
            return response()->json(['success' => false, 'message' => 'دسترسی غیرمجاز'], 403);
        }

        if ($installment->status === 'paid') {
            return response()->json(['success' => false, 'message' => 'این قسط قبلاً پرداخت شده است.'], 422);
        }

        $paidDateGregorian = JalaliHelper::toGregorian($data['paid_date'])->format('Y-m-d');

        DB::transaction(function () use ($installment, $data, $paidDateGregorian, $schoolId) {
            // تراکنش صندوق (بازپرداخت)
            LoanFundTransaction::create([
                'school_id'        => $schoolId,
                'type'             => 'repayment_installment',
                'amount'           => $installment->amount,
                'transaction_date' => $paidDateGregorian,
                'reference_type'   => LoanInstallment::class,
                'reference_id'     => $installment->id,
                'description'      => 'پرداخت قسط توسط ' . ($installment->loan->employee->full_name ?? $installment->loan->borrower_name),
            ]);

            $installment->update([
                'paid_date'   => $paidDateGregorian,
                'paid_amount' => $installment->amount,
                'status'      => 'paid',
                'notes'       => $data['notes'] ?? null,
            ]);

            if ($installment->loan->installments()->where('status', 'pending')->doesntExist()) {
                $installment->loan->update(['status' => 'completed']);
            }
        });

        return response()->json(['success' => true, 'message' => 'قسط با موفقیت پرداخت شد.']);
    }

    private function authorizeAccess(Loan $loan)
    {
        if ($loan->school_id !== $this->getSchoolId()) {
            abort(403);
        }
    }

    private function authorizeInstallmentAccess(LoanInstallment $installment)
    {
        if ($installment->loan->school_id !== $this->getSchoolId()) {
            abort(403);
        }
    }
}
