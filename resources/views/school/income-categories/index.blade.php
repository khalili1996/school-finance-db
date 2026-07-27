@extends('layouts.admin')

@section('title', 'دسته‌بندی عواید')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1><i class="fas fa-tags ms-2"></i> دسته‌بندی عواید</h1>
        <a href="{{ route('school.income-categories.create') }}" class="btn btn-success">
            <i class="fas fa-plus"></i> ثبت دسته‌بندی جدید
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="card shadow">
        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>نام</th>
                        <th>توضیحات</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $cat)
                        <tr>
                            <td>{{ $cat->name }}</td>
                            <td>{{ $cat->description ?? '—' }}</td>
                            <td>
                                <a href="{{ route('school.income-categories.edit', $cat->id) }}" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <form action="{{ route('school.income-categories.destroy', $cat->id) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('آیا از حذف این دسته‌بندی اطمینان دارید؟')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="3" class="text-center text-muted py-3">هیچ دسته‌بندی ثبت نشده است.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
