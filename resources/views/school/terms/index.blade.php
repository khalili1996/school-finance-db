@extends('layouts.admin')

@section('title', 'ترم‌ها')

@section('content')
<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">📆 ترم‌های تحصیلی</h4>
        <a href="{{ route('school.terms.create') }}" class="btn btn-success">
            <i class="fas fa-plus-circle me-1"></i> ایجاد ترم جدید
        </a>
    </div>

    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('school.terms.index') }}" class="row g-2">
                <div class="col-md-4">
                    <select name="academic_year_id" class="form-select">
                        <option value="">همه سال‌ها</option>
                        @foreach($academicYears as $ay)
                            <option value="{{ $ay->id }}" {{ request('academic_year_id') == $ay->id ? 'selected' : '' }}>{{ $ay->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-secondary">فیلتر</button>
                </div>
            </form>
        </div>
    </div>

    @if($terms->isEmpty())
        <div class="alert alert-info">هیچ ترمی یافت نشد.</div>
    @else
        <div class="card shadow-sm border-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>سال مالی</th>
                            <th>نام</th>
                            <th>نوع</th>
                            <th>شروع</th>
                            <th>پایان</th>
                            <th>وضعیت</th>
                            <th>عملیات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($terms as $term)
                        <tr>
                            <td>{{ $term->academicYear->name ?? '—' }}</td>
                            <td>{{ $term->name }}</td>
                            <td>{{ $term->type ?: '—' }}</td>
                            <td>@jalali($term->start_date)</td>
                            <td>@jalali($term->end_date)</td>
                            <td>
                                @if($term->is_active)
                                    <span class="badge bg-success">فعال</span>
                                @else
                                    <span class="badge bg-secondary">غیرفعال</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('school.terms.edit', $term) }}" class="btn btn-outline-warning"><i class="fa fa-pencil"></i></a>
                                    <form action="{{ route('school.terms.destroy', $term) }}" method="POST" class="d-inline">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-outline-danger" onclick="return confirm('حذف شود؟')"><i class="fa fa-trash"></i></button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-white">
                {{ $terms->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
