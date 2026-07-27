@extends('layouts.admin')

@section('title', 'فاکتورها')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-file-invoice ms-2"></i> فاکتورهای ثبت‌شده</h1>
        <a href="{{ route('school.invoices.report') }}" class="btn btn-outline-primary">
            <i class="fas fa-print"></i> گزارش چاپی
        </a>
    </div>

    <form method="GET" action="{{ route('school.invoices.index') }}" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="جستجوی شماره فاکتور..." value="{{ request('search') }}">
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> جستجو</button>
            @if(request('search'))
                <a href="{{ route('school.invoices.index') }}" class="btn btn-secondary">حذف</a>
            @endif
        </div>
    </form>

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>شماره فاکتور</th>
                        <th>عنوان هزینه</th>
                        <th>تاریخ</th>
                        <th>فایل</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($invoices as $expense)
                        <tr>
                            <td>{{ $expense->invoice_number }}</td>
                            <td>{{ $expense->title }}</td>
                            <td>{{ $expense->expense_date }}</td>
                            <td>
                                @if($expense->scan_file)
                                    <a href="{{ asset('storage/'.$expense->scan_file) }}" target="_blank">
                                        <img src="{{ asset('storage/'.$expense->scan_file) }}" style="max-height: 50px;" alt="فاکتور">
                                    </a>
                                @else
                                    —
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('school.expenses.edit', $expense->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" class="text-center text-muted py-3">هیچ فاکتوری یافت نشد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $invoices->links() }}</div>
    </div>
</div>
@endsection
