@extends('layouts.admin')

@section('title', 'سطل زباله دانش‌آموزان')

@section('content')
<div class="container-fluid">
    <h1><i class="fas fa-trash ms-2"></i> دانش‌آموزان حذف‌شده</h1>
    <table class="table table-hover">
        <thead><tr><th>کد</th><th>نام</th><th>تاریخ حذف</th><th>عملیات</th></tr></thead>
        <tbody>
            @forelse($students as $student)
            <tr>
                <td>{{ $student->student_code }}</td>
                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                <td>{{ $student->deleted_at->format('Y/m/d H:i') }}</td>
                <td>
                    <form action="{{ route('school.students.restore', $student->id) }}" method="POST" class="d-inline">
                        @csrf
                        <button class="btn btn-sm btn-success"><i class="fas fa-undo"></i> بازیابی</button>
                    </form>
                    <form action="{{ route('school.students.force-delete', $student->id) }}" method="POST" class="d-inline" onsubmit="return confirm('کاملاً حذف شود؟')">
                        @csrf @method('DELETE')
                        <button class="btn btn-sm btn-danger"><i class="fas fa-times"></i> حذف دائمی</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-muted">سطل زباله خالی است</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
