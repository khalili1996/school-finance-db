@extends('layouts.admin')

@section('title', 'صندوق قرض‌الحسنه')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4><i class="fas fa-piggy-bank ms-2 text-success"></i> صندوق قرض‌الحسنه</h4>
        <a href="{{ route('school.loan-fund.deposit') }}" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> واریز به صندوق
        </a>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card bg-success text-white"><div class="card-body">
                <h5>موجودی فعلی</h5>
                <h2>{{ number_format($balance) }} ؋</h2>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card bg-info text-white"><div class="card-body">
                <h5>کل واریزی‌ها</h5>
                <h2>{{ number_format($totalDeposits + $totalRepayments) }} ؋</h2>
            </div></div>
        </div>
        <div class="col-md-4">
            <div class="card bg-danger text-white"><div class="card-body">
                <h5>کل برداشت‌ها (وام‌ها)</h5>
                <h2>{{ number_format($totalWithdrawals) }} ؋</h2>
            </div></div>
        </div>
    </div>

    <h5>تراکنش‌های اخیر</h5>
    <table class="table table-bordered">
        <thead><tr><th>تاریخ</th><th>نوع</th><th>مبلغ</th><th>شرح</th></tr></thead>
        <tbody>
            @foreach($transactions as $tr)
            <tr>
                <td>{{ \App\Helpers\JalaliHelper::toJalali($tr->transaction_date) }}</td>
                <td>
                    @if($tr->type == 'deposit')
                        <span class="badge bg-primary">واریز دستی</span>
                    @elseif($tr->type == 'withdrawal_loan')
                        <span class="badge bg-danger">پرداخت وام</span>
                    @else
                        <span class="badge bg-success">پرداخت قسط</span>
                    @endif
                </td>
                <td>{{ number_format($tr->amount) }}</td>
                <td>{{ $tr->description }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $transactions->links() }}
</div>
@endsection
