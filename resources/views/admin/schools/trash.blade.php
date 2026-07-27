@extends('layouts.admin')

@section('title', 'مدارس غیرفعال')

@section('content')
<div class="container-fluid px-4">
    <h4><i class="fas fa-trash-alt ms-2"></i> مدارس غیرفعال</h4>
    <a href="{{ route('admin.schools.index') }}" class="btn btn-primary mb-3">
        <i class="fas fa-arrow-right"></i> بازگشت به مدارس فعال
    </a>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>نام مکتب</th>
                <th>کد</th>
                <th>عملیات</th>
            </tr>
        </thead>
        <tbody>
            @forelse($schools as $school)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $school->name }}</td>
                <td>{{ $school->code }}</td>
                <td>
                    {{-- بازیابی --}}
                    <form action="{{ route('admin.schools.restore', $school->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-success" title="فعال‌سازی مجدد">
                            <i class="fas fa-undo"></i>
                        </button>
                    </form>
                    {{-- حذف دائمی --}}
                    <form action="{{ route('admin.schools.force-delete', $school->id) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('آیا مطمئن هستید؟ این مدرسه برای همیشه حذف خواهد شد و قابل بازگشت نیست.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" title="حذف دائمی">
                            <i class="fas fa-times-circle"></i>
                        </button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4">هیچ مدرسه غیرفعالی وجود ندارد.</td></tr>
            @endforelse
        </tbody>
    </table>
    {{ $schools->links() }}
</div>
@endsection
