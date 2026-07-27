@extends('layouts.admin')
@section('title', 'واریز به صندوق قرض‌الحسنه')
@section('content')
<div class="container-fluid px-4">
    <div class="card shadow">
        <div class="card-header bg-primary text-white"><h5>واریز پول به صندوق</h5></div>
        <div class="card-body">
            <form method="POST" action="{{ route('school.loan-fund.deposit.store') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">مبلغ (افغانی)</label>
                    <input type="number" name="amount" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">تاریخ (شمسی)</label>
                    <input type="text" name="transaction_date" class="form-control" value="{{ \App\Helpers\JalaliHelper::todayJalali() }}" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">شرح</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-success">ثبت واریز</button>
            </form>
        </div>
    </div>
</div>
@endsection
