@extends('layouts.admin')

@section('title', 'لیست درآمدها')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-coins ms-2"></i> مدیریت درآمد</h1>
        <div>
            <a href="{{ route('school.incomes.report') }}" class="btn btn-outline-primary btn-sm me-2">
                <i class="fas fa-print"></i> گزارش چاپی
            </a>
            <a href="{{ route('school.incomes.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> ثبت درآمد جدید
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    {{-- فیلترها و جستجو --}}
    <form method="GET" action="{{ route('school.incomes.index') }}" class="row g-2 mb-4">
        <div class="col-md-2">
            <input type="text" name="search" class="form-control" placeholder="جستجوی عنوان یا منبع..." value="{{ request('search') }}">
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
                <option value="due" {{ request('status') == 'due' ? 'selected' : '' }}>دریافت نشده</option>
                <option value="partially_received" {{ request('status') == 'partially_received' ? 'selected' : '' }}>دریافت جزئی</option>
                <option value="received" {{ request('status') == 'received' ? 'selected' : '' }}>دریافت کامل</option>
                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>لغو شده</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="month_id" class="form-select" onchange="this.form.submit()">
                <option value="">همه ماه‌ها</option>
                @foreach($months as $month)
                    <option value="{{ $month->id }}" {{ request('month_id') == $month->id ? 'selected' : '' }}>{{ $month->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> اعمال</button>
            @if(request('search') || request('category_id') || request('status') || request('month_id'))
                <a href="{{ route('school.incomes.index') }}" class="btn btn-secondary">حذف فیلترها</a>
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
                            <th>مبلغ کل</th>
                            <th>دریافتی</th>
                            <th>باقی‌مانده</th>
                            <th>تاریخ</th>
                            <th>ماه</th>
                            <th>منبع</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($incomes as $income)
                            @php $remaining = $income->total_amount - $income->received_amount; @endphp
                            <tr>
                                <td>{{ $income->title }}</td>
                                <td>{{ $income->category->name ?? '—' }}</td>
                                <td>{{ number_format($income->total_amount) }} ؋</td>
                                <td>{{ number_format($income->received_amount) }} ؋</td>
                                <td>{{ number_format(max($remaining, 0)) }} ؋</td>
                                {{-- 📅 نمایش تاریخ شمسی --}}
                                <td>{{ $income->income_date ? \App\Helpers\JalaliHelper::toJalali($income->income_date) : '—' }}</td>
                                <td>{{ $income->month->name ?? '—' }}</td>
                                <td>{{ $income->source ?? '—' }}</td>
                                <td>
                                    @switch($income->status)
                                        @case('received') <span class="badge bg-success">دریافت کامل</span> @break
                                        @case('partially_received') <span class="badge bg-warning text-dark">دریافت جزئی</span> @break
                                        @case('due') <span class="badge bg-danger">دریافت نشده</span> @break
                                        @case('cancelled') <span class="badge bg-secondary">لغو شده</span> @break
                                    @endswitch
                                </td>
                                <td>
                                    <a href="{{ route('school.incomes.edit', $income->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('school.incomes.destroy', $income->id) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('آیا از حذف این عاید اطمینان دارید؟')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="10" class="text-center text-muted py-3">هیچ عایدی ثبت نشده است.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($incomes->hasPages())
        <div class="card-footer">{{ $incomes->links() }}</div>
        @endif
    </div>
</div>
@endsection
