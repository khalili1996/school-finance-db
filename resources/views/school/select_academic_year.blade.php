<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>انتخاب سال تحصیلی – {{ $school->name }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .card { max-width: 500px; margin: 10vh auto; }
    </style>
</head>
<body>
    <div class="card shadow">
        <div class="card-header bg-primary text-white text-center">
            <h4 class="mb-0"><i class="fas fa-calendar-alt ms-2"></i> انتخاب سال تحصیلی</h4>
        </div>
        <div class="card-body">
            <p class="text-muted text-center">مکتب: <strong>{{ $school->name }}</strong></p>
            <div class="list-group">
                @foreach($academicYears as $year)
                    <a href="{{ route('school.set-academic-year', $year->id) }}" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        {{ $year->name }}
                        @if($year->is_active)
                            <span class="badge bg-success">فعال</span>
                        @endif
                        <i class="fas fa-chevron-left"></i>
                    </a>
                @endforeach
            </div>
            <div class="mt-3 text-center">
                <a href="{{ route('school.academic-years.create') }}" class="btn btn-sm btn-success">
                    <i class="fas fa-plus"></i> ایجاد سال جدید
                </a>
            </div>
        </div>
    </div>
</body>
</html>
