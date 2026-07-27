<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\LoanFundTransaction;
use App\Helpers\JalaliHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanFundController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    /**
     * داشبورد صندوق قرض‌الحسنه (موجودی، درآمد، مصرف و لیست تراکنش‌ها)
     */
    public function index()
    {
        $schoolId = $this->getSchoolId();

        $totalDeposits = LoanFundTransaction::where('school_id', $schoolId)
            ->where('type', 'deposit')->sum('amount');
        $totalRepayments = LoanFundTransaction::where('school_id', $schoolId)
            ->where('type', 'repayment_installment')->sum('amount');
        $totalWithdrawals = LoanFundTransaction::where('school_id', $schoolId)
            ->where('type', 'withdrawal_loan')->sum('amount');

        $balance = $totalDeposits + $totalRepayments - $totalWithdrawals;

        $transactions = LoanFundTransaction::where('school_id', $schoolId)
            ->latest('transaction_date')
            ->paginate(20);

        return view('school.loan-fund.index', compact(
            'totalDeposits', 'totalRepayments', 'totalWithdrawals', 'balance', 'transactions'
        ));
    }

    /**
     * فرم واریز دستی به صندوق
     */
    public function createDeposit()
    {
        return view('school.loan-fund.deposit');
    }

    /**
     * ذخیره واریز
     */
    public function storeDeposit(Request $request)
    {
        $data = $request->validate([
            'amount'           => 'required|numeric|min:1',
            'transaction_date' => 'required|string', // شمسی
            'description'      => 'nullable|string|max:500',
        ]);

        $gregorian = JalaliHelper::toGregorian($data['transaction_date']);

        LoanFundTransaction::create([
            'school_id'        => $this->getSchoolId(),
            'type'             => 'deposit',
            'amount'           => $data['amount'],
            'transaction_date' => $gregorian->format('Y-m-d'),
            'description'      => $data['description'] ?? 'واریز دستی',
        ]);

        return redirect()->route('school.loan-fund.index')
            ->with('success', 'مبلغ با موفقیت به صندوق قرض‌الحسنه واریز شد.');
    }
}
