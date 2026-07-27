@extends('layouts.admin')

@section('title', 'لیست مصارف')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-file-invoice ms-2"></i> مدیریت مصارف</h1>
        <div>
            <a href="{{ route('school.expenses.report') }}" class="btn btn-outline-primary btn-sm me-2">
                <i class="fas fa-print"></i> گزارش چاپی
            </a>
            <a href="{{ route('school.expenses.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> ثبت مصرف جدید
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- فیلترها و جستجو --}}
    <form method="GET" action="{{ route('school.expenses.index') }}" class="row g-2 mb-4">
        <div class="col-md-3">
            <input type="text" name="search" class="form-control" placeholder="جستجوی عنوان، توضیحات، شماره فاکتور..." value="{{ request('search') }}">
        </div>
        <div class="col-md-2">
            <select name="category_id" class="form-select" onchange="this.form.submit()">
                <option value="">همه دسته‌بندی‌ها</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">همه وضعیت‌ها</option>
                <option value="due" {{ request('status') == 'due' ? 'selected' : '' }}>پرداخت نشده</option>
                <option value="partially_paid" {{ request('status') == 'partially_paid' ? 'selected' : '' }}>پرداخت جزئی</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>پرداخت کامل</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>لغو شده</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> اعمال</button>
            @if(request('search') || request('category_id') || request('status'))
                <a href="{{ route('school.expenses.index') }}" class="btn btn-secondary">حذف فیلترها</a>
            @endif
        </div>
    </form>

    {{-- جدول --}}
    <div class="card shadow">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>عنوان</th>
                            <th>دسته‌بندی</th>
                            <th>ماه</th>
                            <th>مبلغ کل</th>
                            <th>پرداختی</th>
                            <th>باقی‌مانده</th>
                            <th>تاریخ</th>
                            <th>شماره فاکتور</th>
                            <th>فاکتور</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $expense)
                            @php $remaining = $expense->total_amount - $expense->paid_amount; @endphp
                            <tr>
                                <td>{{ $expense->title }}</td>
                                <td>{{ $expense->category->name ?? '—' }}</td>
                                <td>{{ $expense->month->name ?? '—' }}</td>
                                <td>{{ number_format($expense->total_amount) }} ؋</td>
                                <td>{{ number_format($expense->paid_amount) }} ؋</td>
                                <td>{{ number_format(max($remaining, 0)) }} ؋</td>
                                {{-- 📅 نمایش تاریخ شمسی --}}
                                <td>{{ $expense->expense_date ? \App\Helpers\JalaliHelper::toJalali($expense->expense_date) : '—' }}</td>
                                <td>{{ $expense->invoice_number ?? '—' }}</td>
                                <td>
                                    @if($expense->scan_file)
                                        <a href="{{ asset('storage/'.$expense->scan_file) }}" target="_blank" class="btn btn-sm btn-outline-info">
                                            <i class="fas fa-file-image"></i>
                                        </a>
                                    @else
                                        —
                                    @endif
                                </td>
                                <td>
                                    @switch($expense->status)
                                        @case('paid') <span class="badge bg-success">پرداخت کامل</span> @break
                                        @case('partially_paid') <span class="badge bg-warning text-dark">پرداخت جزئی</span> @break
                                        @case('due') <span class="badge bg-danger">پرداخت نشده</span> @break
                                        @case('cancelled') <span class="badge bg-secondary">لغو شده</span> @break
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('school.expenses.edit', $expense->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('school.expenses.destroy', $expense->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('آیا از حذف این مصرف اطمینان دارید؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="11" class="text-center text-muted py-3">هیچ مصرفی ثبت نشده است.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($expenses->hasPages())
        <div class="card-footer">{{ $expenses->links() }}</div>
        @endif
    </div>
</div>
@endsection
