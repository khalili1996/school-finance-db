@extends('layouts.admin')

@section('title', 'راه‌اندازی سال مالی جدید')

@section('content')
<div class="container-fluid px-4">
    {{-- لوگو و نام مکتب --}}
    <div class="text-center mb-4">
        @if($logo)
            <img src="{{ asset('storage/' . $logo) }}" alt="لوگو" height="80">
        @endif
        <h4 class="mt-2">{{ $schoolName }}</h4>
        <h5 class="text-primary">سال مالی جدید: {{ $newYear->name }}</h5>
    </div>

    <div class="card shadow">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="fas fa-play-circle me-1"></i> راه‌اندازی سال مالی جدید</h5>
        </div>
        <div class="card-body">
            <form action="{{ route('school.setup-year.start') }}" method="POST">
                @csrf
                <input type="hidden" name="new_year_id" value="{{ $newYear->id }}">

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="transfer_students" id="transferStudents" value="1" checked>
                        <label class="form-check-label" for="transferStudents">
                            <strong>انتقال همه دانش‌آموزان</strong>
                        </label>
                    </div>
                    <small class="text-muted">در صورت انتخاب، تمام دانش‌آموزان سال قبل به‌صورت کپی در سال جدید ایجاد می‌شوند و پایهٔ تحصیلی آن‌ها یک کلاس بالاتر می‌رود.</small>
                </div>

                <hr>

                <div class="mb-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="transfer_employees" id="transferEmployees" value="1" checked>
                        <label class="form-check-label" for="transferEmployees">
                            <strong>انتقال همه کارمندان</strong>
                        </label>
                    </div>
                    <small class="text-muted">در صورت انتخاب، تمام کارمندان سال قبل در سال جدید کپی می‌شوند (بدون تغییر در سمت و حقوق).</small>
                </div>

                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-rocket"></i> شروع انتقال
                    </button>
                    <a href="{{ route('school.dashboard') }}" class="btn btn-secondary btn-lg">بعداً انجام می‌دهم</a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
