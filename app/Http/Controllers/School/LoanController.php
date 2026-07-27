<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\Employee;
use App\Models\LoanFundTransaction;
use App\Helpers\JalaliHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    private function getSchoolId()
    {
        return session('active_school_id', auth()->user()->school_id);
    }

    /**
     * لیست قرض‌الحسنه‌ها
     */
    public function index(Request $request)
    {
        $schoolId = $this->getSchoolId();
        $query = Loan::where('school_id', $schoolId)->with('employee');

        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        $loans = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('school.loans.index', compact('loans'));
    }

    /**
     * فرم ایجاد قرض‌الحسنه جدید
     */
    public function create()
    {
        $schoolId = $this->getSchoolId();
        $employees = Employee::where('school_id', $schoolId)->orderBy('first_name')->get();
        // حذف کامل ارسال $cashboxes – بخش قرض‌الحسنه مستقل از صندوق اصلی است

        return view('school.loans.create', compact('employees'));
    }

    /**
     * ذخیره قرض‌الحسنه جدید
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'employee_id'               => 'nullable|exists:employees,id',
            'borrower_name'             => 'required_without:employee_id|string|max:200',
            'borrower_last_name'        => 'nullable|string|max:100',
            'borrower_father_name'      => 'nullable|string|max:100',
            'borrower_grandfather_name' => 'nullable|string|max:100',
            'borrower_national_id'      => 'nullable|string|max:50',
            'borrower_phone'            => 'nullable|string|max:20',
            'borrower_relative_phone'   => 'nullable|string|max:20',
            'borrower_birth_date'       => 'nullable|string|max:20',
            'borrower_original_province' => 'nullable|string|max:100',
            'borrower_original_district' => 'nullable|string|max:100',
            'borrower_original_village'  => 'nullable|string|max:150',
            'borrower_address'          => 'nullable|string|max:500',
            'borrower_photo'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            'guarantor_name'            => 'required|string|max:200',
            'guarantor_father_name'     => 'nullable|string|max:100',
            'guarantor_national_id'     => 'nullable|string|max:50',
            'guarantor_phone'           => 'nullable|string|max:20',
            'guarantor_address'         => 'nullable|string|max:500',
            'guarantor_photo'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            'loan_provider'      => 'nullable|string|max:200',
            'amount'             => 'required|numeric|min:1',
            'duration_months'    => 'required|integer|min:1|max:60',
            'installment_amount' => 'required|numeric|min:1',
            'start_date'         => 'required|string',
            'notes'              => 'nullable|string|max:500',
            // 'cashbox_id' کاملاً حذف شده است
        ]);

        $schoolId = $this->getSchoolId();

        if (!empty($data['employee_id'])) {
            $employee = Employee::findOrFail($data['employee_id']);
            $data['borrower_name'] = $data['borrower_name'] ?: $employee->first_name;
            $data['borrower_last_name'] = $data['borrower_last_name'] ?: $employee->last_name;
            $data['borrower_father_name'] = $data['borrower_father_name'] ?: $employee->father_name;
            $data['borrower_national_id'] = $data['borrower_national_id'] ?: $employee->national_id;
            $data['borrower_phone'] = $data['borrower_phone'] ?: $employee->phone;
            $data['borrower_address'] = $data['borrower_address'] ?: $employee->address;
        }

        if ($request->hasFile('borrower_photo')) {
            $data['borrower_photo'] = $request->file('borrower_photo')->store('loans/borrowers', 'public');
        }
        if ($request->hasFile('guarantor_photo')) {
            $data['guarantor_photo'] = $request->file('guarantor_photo')->store('loans/guarantors', 'public');
        }

        $gregorianStart = JalaliHelper::toGregorian($data['start_date']);
        $data['start_date'] = $gregorianStart->format('Y-m-d');
        if (!empty($data['borrower_birth_date'])) {
            $data['borrower_birth_date'] = JalaliHelper::toGregorian($data['borrower_birth_date'])->format('Y-m-d');
        }

        $data['amount'] = (float) $data['amount'];
        $data['duration_months'] = (int) $data['duration_months'];
        $data['installment_amount'] = (float) $data['installment_amount'];

        $data['school_id'] = $schoolId;
        $data['status'] = 'active';
        $data['end_date'] = $gregorianStart->copy()->addMonths($data['duration_months'])->format('Y-m-d');

        DB::transaction(function () use ($data) {
            $loan = Loan::create($data);

            $start = \Carbon\Carbon::parse($loan->start_date);
            for ($i = 0; $i < $loan->duration_months; $i++) {
                $dueDate = $start->copy()->addMonths($i);
                LoanInstallment::create([
                    'loan_id' => $loan->id,
                    'amount'  => $loan->installment_amount,
                    'due_date'=> $dueDate->format('Y-m-d'),
                    'status'  => 'pending',
                ]);
            }

            LoanFundTransaction::create([
                'school_id'        => $loan->school_id,
                'type'             => 'withdrawal_loan',
                'amount'           => $loan->amount,
                'transaction_date' => $loan->start_date,
                'reference_type'   => Loan::class,
                'reference_id'     => $loan->id,
                'description'      => 'پرداخت وام به ' . ($loan->employee->full_name ?? $loan->borrower_name),
            ]);
        });

        return redirect()->route('school.loans.index')
            ->with('success', 'قرض‌الحسنه با موفقیت ثبت شد و اقساط ایجاد گردید.');
    }

    /**
     * نمایش جزئیات (پیش‌نمایش چاپ)
     */
    public function show(Loan $loan)
    {
        $this->authorizeAccess($loan);
        $loan->load(['employee', 'installments']);
        return view('school.loans.show', compact('loan'));
    }

    /**
     * ویرایش قرض‌الحسنه
     */
    public function edit(Loan $loan)
    {
        $this->authorizeAccess($loan);
        $schoolId = $this->getSchoolId();
        $employees = Employee::where('school_id', $schoolId)->orderBy('first_name')->get();
        // $cashboxes حذف شد

        // تبدیل تاریخ‌ها به شمسی
        $loan->start_date = JalaliHelper::toJalali($loan->start_date, 'Y/m/d');
        if ($loan->borrower_birth_date) {
            $loan->borrower_birth_date = JalaliHelper::toJalali($loan->borrower_birth_date, 'Y/m/d');
        }

        return view('school.loans.edit', compact('loan', 'employees'));
    }

    /**
     * به‌روزرسانی قرض‌الحسنه
     */
    public function update(Request $request, Loan $loan)
    {
        $this->authorizeAccess($loan);
        $schoolId = $this->getSchoolId();

        $rules = [
            'borrower_name'             => 'required|string|max:200',
            'borrower_last_name'        => 'nullable|string|max:100',
            'borrower_father_name'      => 'nullable|string|max:100',
            'borrower_grandfather_name' => 'nullable|string|max:100',
            'borrower_national_id'      => 'nullable|string|max:50',
            'borrower_birth_date'       => 'nullable|string|max:20',
            'borrower_phone'            => 'nullable|string|max:20',
            'borrower_relative_phone'   => 'nullable|string|max:20',
            'borrower_original_province' => 'nullable|string|max:100',
            'borrower_original_district' => 'nullable|string|max:100',
            'borrower_original_village'  => 'nullable|string|max:150',
            'borrower_address'          => 'nullable|string|max:500',
            'borrower_photo'            => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            'guarantor_name'            => 'required|string|max:200',
            'guarantor_father_name'     => 'nullable|string|max:100',
            'guarantor_national_id'     => 'nullable|string|max:50',
            'guarantor_phone'           => 'nullable|string|max:20',
            'guarantor_address'         => 'nullable|string|max:500',
            'guarantor_photo'           => 'nullable|image|mimes:jpeg,png,jpg|max:2048',

            'loan_provider'     => 'nullable|string|max:200',
            'amount'            => 'nullable|numeric|min:1',
            'duration_months'   => 'nullable|integer|min:1|max:60',
            'installment_amount'=> 'nullable|numeric|min:1',
            'start_date'        => 'nullable|string',
            'notes'             => 'nullable|string|max:500',
        ];

        $data = $request->validate($rules);

        if ($request->hasFile('borrower_photo')) {
            $data['borrower_photo'] = $request->file('borrower_photo')->store('loans/borrowers', 'public');
        }
        if ($request->hasFile('guarantor_photo')) {
            $data['guarantor_photo'] = $request->file('guarantor_photo')->store('loans/guarantors', 'public');
        }

        if (!empty($data['start_date'])) {
            $data['start_date'] = JalaliHelper::toGregorian($data['start_date'])->format('Y-m-d');
        }
        if (!empty($data['borrower_birth_date'])) {
            $data['borrower_birth_date'] = JalaliHelper::toGregorian($data['borrower_birth_date'])->format('Y-m-d');
        }

        $hasPaidInstallments = $loan->installments()->where('status', 'paid')->exists();

        if ($hasPaidInstallments) {
            $allowedFields = [
                'borrower_name', 'borrower_last_name', 'borrower_father_name',
                'borrower_grandfather_name', 'borrower_national_id', 'borrower_birth_date',
                'borrower_phone', 'borrower_relative_phone',
                'borrower_original_province', 'borrower_original_district',
                'borrower_original_village', 'borrower_address', 'borrower_photo',
                'guarantor_name', 'guarantor_father_name', 'guarantor_national_id',
                'guarantor_phone', 'guarantor_address', 'guarantor_photo',
                'loan_provider', 'notes',
            ];
            $updateData = array_intersect_key($data, array_flip($allowedFields));
            $loan->update($updateData);

            return redirect()->route('school.loans.index')
                ->with('success', 'اطلاعات قرض‌الحسنه به‌روزرسانی شد. (جزئیات مالی به دلیل پرداخت اقساط تغییر نکرد.)');
        }

        $data['amount'] = (float) ($data['amount'] ?? $loan->amount);
        $data['duration_months'] = (int) ($data['duration_months'] ?? $loan->duration_months);
        $data['installment_amount'] = (float) ($data['installment_amount'] ?? $loan->installment_amount);
        $data['start_date'] = $data['start_date'] ?? $loan->start_date;
        $data['end_date'] = \Carbon\Carbon::parse($data['start_date'])->addMonths($data['duration_months'])->format('Y-m-d');

        DB::transaction(function () use ($loan, $data) {
            LoanFundTransaction::where('reference_type', Loan::class)
                ->where('reference_id', $loan->id)
                ->delete();

            $loan->update($data);

            LoanFundTransaction::create([
                'school_id'        => $loan->school_id,
                'type'             => 'withdrawal_loan',
                'amount'           => $loan->amount,
                'transaction_date' => $loan->start_date,
                'reference_type'   => Loan::class,
                'reference_id'     => $loan->id,
                'description'      => 'پرداخت وام (ویرایش شده) به ' . ($loan->employee->full_name ?? $loan->borrower_name),
            ]);

            $loan->installments()->delete();
            $start = \Carbon\Carbon::parse($loan->start_date);
            for ($i = 0; $i < $loan->duration_months; $i++) {
                $dueDate = $start->copy()->addMonths($i);
                LoanInstallment::create([
                    'loan_id' => $loan->id,
                    'amount'  => $loan->installment_amount,
                    'due_date'=> $dueDate->format('Y-m-d'),
                    'status'  => 'pending',
                ]);
            }
        });

        return redirect()->route('school.loans.index')
            ->with('success', 'قرض‌الحسنه و اقساط با موفقیت به‌روزرسانی شدند.');
    }

    /**
     * حذف قرض‌الحسنه
     */
    public function destroy(Loan $loan)
    {
        $this->authorizeAccess($loan);
        DB::transaction(function () use ($loan) {
            LoanFundTransaction::where('reference_type', Loan::class)
                ->where('reference_id', $loan->id)
                ->delete();
            LoanFundTransaction::whereIn('reference_id', $loan->installments->pluck('id'))
                ->where('reference_type', LoanInstallment::class)
                ->delete();
            $loan->delete();
        });
        return redirect()->route('school.loans.index')
            ->with('success', 'قرض‌الحسنه حذف شد.');
    }

    private function authorizeAccess(Loan $loan)
    {
        if ($loan->school_id !== $this->getSchoolId()) {
            abort(403);
        }
    }
}
