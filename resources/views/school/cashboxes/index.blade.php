@extends('layouts.admin')

@section('title', 'صندوق‌ها')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">💰 مدیریت صندوق‌ها</h4>
        <a href="{{ route('school.cashboxes.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle me-1"></i> ایجاد صندوق جدید
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    @if($cashboxes->isEmpty())
        <div class="alert alert-info text-center">
            هنوز هیچ صندوقی ایجاد نشده است. لطفاً یک صندوق جدید بسازید.
        </div>
    @else
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>نام صندوق</th>
                            <th>نوع</th>
                            <th>موجودی اولیه</th>
                            <th>مجموع درآمد</th>
                            <th>مجموع مصرف</th>
                            <th>موجودی فعلی</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cashboxes as $box)
                            @php
                                $totalIncome   = $box->transactions()->where('type', 'deposit')->sum('amount');
                                $totalExpense  = $box->transactions()->where('type', 'withdrawal')->sum('amount');
                                $currentBalance = $box->initial_balance + $totalIncome - $totalExpense;
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $box->name }}</td>
                                <td>
                                    <span class="badge bg-{{ $box->type === 'bank' ? 'info' : 'success' }}">
                                        {{ $box->type === 'bank' ? 'بانکی' : 'نقدی' }}
                                    </span>
                                </td>
                                <td>{{ number_format($box->initial_balance, 0) }} ؋</td>
                                <td class="text-success">{{ number_format($totalIncome, 0) }} ؋</td>
                                <td class="text-danger">{{ number_format($totalExpense, 0) }} ؋</td>
                                <td class="fw-bold {{ $currentBalance >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($currentBalance, 0) }} ؋
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm">
                                        <a href="{{ route('school.cashboxes.show', $box) }}" class="btn btn-outline-secondary" title="جزئیات">
                                            <i class="fa fa-eye"></i>
                                        </a>
                                        <a href="{{ route('school.cashboxes.edit', $box) }}" class="btn btn-outline-warning" title="ویرایش">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                        <form action="{{ route('school.cashboxes.destroy', $box) }}" method="POST" class="d-inline">
                                            @csrf @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="حذف"
                                                onclick="return confirm('آیا از حذف این صندوق اطمینان دارید؟')">
                                                <i class="fa fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
@endsection
