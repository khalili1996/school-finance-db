@extends('layouts.admin')
@section('title', 'مدیریت اولیا')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-user-friends ms-2"></i> مدیریت اولیا</h1>
        <a href="{{ route('school.guardians.create') }}" class="btn btn-success"><i class="fas fa-plus"></i> ثبت ولی جدید</a>
    </div>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif

    <form method="GET" class="row g-2 mb-4">
        <div class="col-md-3"><input type="text" name="search" class="form-control" placeholder="جستجوی نام، تذکره، شغل..." value="{{ request('search') }}"></div>
        <div class="col-md-2">
            <select name="status" class="form-select" onchange="this.form.submit()">
                <option value="">همه وضعیت‌ها</option>
                <option value="active" {{ request('status')=='active'?'selected':'' }}>فعال</option>
                <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>غیرفعال</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="relation" class="form-select" onchange="this.form.submit()">
                <option value="">همه نسبت‌ها</option>
                <option value="father" {{ request('relation')=='father'?'selected':'' }}>پدر</option>
                <option value="mother" {{ request('relation')=='mother'?'selected':'' }}>مادر</option>
                <option value="brother" {{ request('relation')=='brother'?'selected':'' }}>برادر</option>
                <option value="uncle" {{ request('relation')=='uncle'?'selected':'' }}>کاکا/ماما</option>
                <option value="other" {{ request('relation')=='other'?'selected':'' }}>سایر</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="kids" class="form-select" onchange="this.form.submit()">
                <option value="">تعداد فرزندان</option>
                <option value="1" {{ request('kids')=='1'?'selected':'' }}>1 فرزند</option>
                <option value="2" {{ request('kids')=='2'?'selected':'' }}>2 فرزند</option>
                <option value="3+" {{ request('kids')=='3+'?'selected':'' }}>3 و بیشتر</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="financial" class="form-select" onchange="this.form.submit()">
                <option value="">همه وضعیت مالی</option>
                <option value="debtor" {{ request('financial')=='debtor'?'selected':'' }}>بدهکار</option>
                <option value="settled" {{ request('financial')=='settled'?'selected':'' }}>تسویه شده</option>
                <option value="discount" {{ request('financial')=='discount'?'selected':'' }}>دارای تخفیف</option>
                <option value="free" {{ request('financial')=='free'?'selected':'' }}>رایگان</option>
            </select>
        </div>
        <div class="col-auto">
            <button type="submit" class="btn btn-primary"><i class="fas fa-search"></i> اعمال</button>
            <a href="{{ route('school.guardians.index') }}" class="btn btn-secondary">حذف فیلترها</a>
        </div>
    </form>

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr><th>نام کامل</th><th>نسبت</th><th>شماره تماس</th><th>شغل</th><th>تعداد فرزندان</th><th>بدهی کل</th><th>وضعیت</th><th>عملیات</th></tr>
                </thead>
                <tbody>
                    @forelse($guardians as $guardian)
                        <tr>
                            <td>{{ $guardian->full_name }}</td>
                            <td>
                                @switch($guardian->relation)
                                @case('father') پدر @break
                                @case('mother') مادر @break
                                @case('brother') برادر @break
                                @case('uncle') کاکا / ماما @break
                                @case('other') سایر @break
                                @default {{ $guardian->relation ?? '—' }}
                                @endswitch
                                </td>
                            <td>
                                @if($guardian->phone)
                                    <a href="tel:{{ $guardian->phone }}"><i class="fas fa-phone text-success"></i></a>
                                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $guardian->phone) }}" target="_blank"><i class="fab fa-whatsapp text-success"></i></a>
                                    {{ $guardian->phone }}
                                @else — @endif
                            </td>
                            <td>{{ $guardian->job ?? '—' }}</td>
                            <td>
                                <span class="badge bg-primary">{{ $guardian->students_count }}</span>
                                @if($guardian->students_count > 0)
                                    <a href="#family-{{ $guardian->id }}" data-bs-toggle="collapse" class="btn btn-sm btn-outline-info ms-1"><i class="fas fa-users"></i></a>
                                @endif
                            </td>
                            <td>@if($guardian->total_debt > 0)<span class="text-danger">{{ number_format($guardian->total_debt) }} ؋</span>@else <span class="text-success">تسویه</span> @endif</td>
                            <td><span class="badge bg-{{ $guardian->is_active?'success':'danger' }}">{{ $guardian->is_active?'فعال':'غیرفعال' }}</span></td>
                            <td>
                                <a href="{{ route('school.guardians.show', $guardian->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                <a href="{{ route('school.guardians.edit', $guardian->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('school.guardians.destroy', $guardian->id) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف شود؟')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @if($guardian->students_count > 0)
                        <tr class="collapse" id="family-{{ $guardian->id }}">
                            <td colspan="8">
                                <div class="p-3 bg-light">
                                    <h6>فرزندان {{ $guardian->full_name }}</h6>
                                    <table class="table table-sm table-bordered bg-white">
                                        <thead class="table-secondary"><tr><th>نام</th><th>نام پدر</th><th>پدرکلان</th><th>صنف</th><th>عملیات</th></tr></thead>
                                        <tbody>
                                            @foreach($guardian->students as $student)
                                            <tr>
                                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                                <td>{{ $student->father_name }}</td>
                                                <td>{{ $student->grandfather_name ?? '—' }}</td>
                                                <td>{{ $student->class ?? '—' }}</td>
                                                <td>
                                                    <a href="{{ route('school.students.show', $student->id) }}" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                                    <a href="{{ route('school.students.edit', $student->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                                    <form action="{{ route('school.students.destroy', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('حذف دانش‌آموز؟')">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        @endif
                    @empty
                        <tr><td colspan="8" class="text-center text-muted py-3">هیچ ولی‌ای ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $guardians->links() }}</div>
    </div>
</div>
@endsection
