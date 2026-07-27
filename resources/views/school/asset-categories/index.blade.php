@extends('layouts.admin')
@section('title', 'دسته‌بندی تجهیزات')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4>دسته‌بندی تجهیزات</h4>
        <a href="{{ route('school.asset-categories.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> دسته جدید
        </a>
    </div>

    <div class="card shadow">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>نام دسته</th>
                        <th>عملیات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $category->name }}</td>
                        <td>
                            <a href="{{ route('school.asset-categories.edit', $category) }}" class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <form action="{{ route('school.asset-categories.destroy', $category) }}" method="POST" class="d-inline" onsubmit="return confirm('آیا مطمئن هستید؟')">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="3" class="text-center">دسته‌بندی وجود ندارد.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">{{ $categories->links() }}</div>
    </div>
</div>
@endsection
