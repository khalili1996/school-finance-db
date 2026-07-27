@extends('layouts.admin')

@section('title', 'تراکنش‌های صندوق')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">📊 تراکنش‌های صندوق</h4>
        <a href="{{ route('school.cashbox-transactions.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle me-1"></i> تراکنش جدید
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- فیلترها --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('school.cashbox-transactions.index') }}" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label for="academic_year_id" class="form-label">سال مالی</label>
                    <select name="academic_year_id" id="academic_year_id" class="form-select" onchange="this.form.submit()">
                        <option value="">همه سال‌ها</option>
                        @foreach($academicYears as $yr)
                            <option value="{{ $yr->id }}" {{ request('academic_year_id') == $yr->id ? 'selected' : '' }}>
                                {{ $yr->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="type" class="form-label">نوع تراکنش</label>
                    <select name="type" id="type" class="form-select">
                        <option value="">همه</option>
                        <option value="deposit" {{ request('type') === 'deposit' ? 'selected' : '' }}>واریز</option>
                        <option value="withdrawal" {{ request('type') === 'withdrawal' ? 'selected' : '' }}>برداشت</option>
                        <option value="transfer" {{ request('type') === 'transfer' ? 'selected' : '' }}>انتقال</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="cashbox_id" class="form-label">صندوق</label>
                    <select name="cashbox_id" id="cashbox_id" class="form-select">
                        <option value="">همه</option>
                        @foreach($cashboxes as $cb)
                            <option value="{{ $cb->id }}" {{ request('cashbox_id') == $cb->id ? 'selected' : '' }}>
                                {{ $cb->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="from_date" class="form-label">از تاریخ</label>
                    <input type="date" name="from_date" id="from_date" class="form-control" value="{{ request('from_date') }}">
                </div>
                <div class="col-md-2">
                    <label for="to_date" class="form-label">تا تاریخ</label>
                    <input type="date" name="to_date" id="to_date" class="form-control" value="{{ request('to_date') }}">
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-outline-secondary w-100">
                        <i class="fas fa-search me-1"></i> فیلتر
                    </button>
                    <a href="{{ route('school.cashbox-transactions.index') }}" class="btn btn-outline-danger w-100">
                        <i class="fas fa-times"></i>
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- جدول تراکنش‌ها --}}
    @if($transactions->isEmpty())
        <div class="alert alert-info text-center">هیچ تراکنشی یافت نشد.</div>
    @else
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>تاریخ</th>
                            <th>صندوق</th>
                            <th>نوع</th>
                            <th>مبلغ (افغانی)</th>
                            <th>شرح</th>
                            <th>منبع</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($transactions as $trx)
                        <tr>
                            <td>{{ $trx->transaction_date }}</td>
                            <td>{{ $trx->cashbox->name ?? '—' }}</td>
                            <td>
                                @if($trx->type === 'deposit')
                                    <span class="badge bg-success">واریز</span>
                                @elseif($trx->type === 'withdrawal')
                                    <span class="badge bg-danger">برداشت</span>
                                @else
                                    <span class="badge bg-warning text-dark">انتقال</span>
                                @endif
                            </td>
                            <td class="{{ $trx->type === 'deposit' ? 'text-success' : 'text-danger' }}">
                                {{ number_format($trx->amount, 0) }}
                            </td>
                            <td>{{ $trx->description ?: '—' }}</td>
                            <td>
                                @if($trx->reference)
                                    {{ class_basename($trx->reference_type) }} #{{ $trx->reference_id }}
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('school.cashbox-transactions.show', $trx) }}" class="btn btn-outline-secondary" title="جزئیات">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <a href="{{ route('school.cashbox-transactions.receipt', $trx) }}" class="btn btn-outline-info" title="رسید چاپی">
                                        <i class="fa fa-print"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                {{ $transactions->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
