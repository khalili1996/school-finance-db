<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش چاپی معاشات</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #fff; margin: 0; padding: 10px; }
        h2 { text-align: center; margin-bottom: 5px; }
        .btn-print { display: inline-block; background: #2c3e50; color: #fff; padding: 4px 12px; border: none; cursor: pointer; margin-bottom: 10px; }
        @media print { .btn-print { display: none; } }

        .employee-block { margin-bottom: 20px; border: 1px solid #ccc; padding: 10px; }
        .employee-header { background: #f8f9fa; padding: 5px 10px; font-weight: bold; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; font-size: 10px; margin-bottom: 10px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .totals { font-weight: bold; background: #e9ecef; }
    </style>
</head>
<body>
<button class="btn-print" onclick="window.print()">چاپ گزارش</button>
<h2>گزارش معاشات کارمندان</h2>
@if(request('employee_id'))
    <p>فیلتر کارمند: {{ \App\Models\Employee::find(request('employee_id'))->first_name ?? '' }}</p>
@endif

@forelse($reportData as $data)
    @php $emp = $data['employee']; @endphp
    <div class="employee-block">
        <div class="employee-header">
            {{ $emp->first_name }} {{ $emp->last_name }} ({{ $emp->father_name ?? '—' }}) – سمت: {{ $emp->position->name ?? $emp->position ?? '—' }}
        </div>
        @if(count($data['salaries']) > 0)
            <table>
                <thead>
                    <tr>
                        <th>ماه</th>
                        <th>حقوق پایه</th>
                        <th>اضافه‌کاری</th>
                        <th>پاداش</th>
                        <th>کسورات</th>
                        <th>مالیات</th>
                        <th>ضمانت</th>
                        <th>پیش‌پرداخت</th>
                        <th>جمع کل</th>
                        <th>پرداخت شده</th>
                        <th>باقی‌مانده</th>
                        <th>تاریخ پرداخت</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($data['salaries'] as $sal)
                    <tr>
                        <td>{{ $sal['month'] }}</td>
                        <td>{{ number_format($sal['base_salary']) }}</td>
                        <td>{{ number_format($sal['overtime_amount']) }}</td>
                        <td>{{ number_format($sal['bonus_amount']) }}</td>
                        <td>{{ number_format($sal['deduction_amount']) }}</td>
                        <td>{{ number_format($sal['tax_amount']) }}</td>
                        <td>{{ number_format($sal['guarantee_amount']) }}</td>
                        <td>{{ number_format($sal['advance_amount']) }}</td>
                        <td>{{ number_format($sal['total_amount']) }}</td>
                        <td>{{ number_format($sal['paid_amount']) }}</td>
                        <td>{{ number_format($sal['remaining']) }}</td>
                        <td>{{ $sal['payment_date'] ?? '—' }}</td>
                    </tr>
                    @endforeach
                    <tr class="totals">
                        <td colspan="7">مجموع</td>
                        <td>{{ number_format($data['total_advance']) }}</td>
                        <td>{{ number_format($data['total_amount']) }}</td>
                        <td>{{ number_format($data['total_paid']) }}</td>
                        <td>{{ number_format($data['total_remaining']) }}</td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        @else
            <p>داده‌ای برای ماه‌های انتخاب‌شده وجود ندارد.</p>
        @endif
    </div>
@empty
    <p>هیچ رکوردی برای نمایش وجود ندارد.</p>
@endforelse
</body>
</html>
