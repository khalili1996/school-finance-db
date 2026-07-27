<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>پیش‌نمایش چاپ معاش – {{ $employee->first_name }} {{ $employee->last_name }}</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4; margin: 10mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #fff; margin: 0; padding: 10px; direction: rtl; color: #333; }
        .container { max-width: 190mm; margin: auto; }
        h2, h3 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 11px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .info-table td { text-align: right; }
        .info-table .label { background: #f8f9fa; font-weight: bold; width: 35%; }
        .btn-print { display: inline-block; background: #2c3e50; color: #fff; padding: 5px 15px; border: none; cursor: pointer; font-family: 'Vazir'; margin-bottom: 10px; }
        @media print { .btn-print { display: none; } }
        .totals { font-weight: bold; background: #e9ecef; }
    </style>
</head>
<body>
<div class="container">
    <button class="btn-print" onclick="window.print()">چاپ</button>
    <h2>{{ $employee->school->name ?? 'مکتب' }}</h2>
    <h3>گزارش کامل معاش کارمند</h3>

    <table class="info-table">
        <tr><td class="label">نام:</td><td>{{ $employee->first_name }} {{ $employee->last_name }}</td></tr>
        <tr><td class="label">نام پدر:</td><td>{{ $employee->father_name ?? '—' }}</td></tr>
        <tr><td class="label">سمت:</td><td>{{ $employee->position->name ?? $employee->position ?? '—' }}</td></tr>
        <tr><td class="label">مجموع معاش:</td><td>{{ number_format(array_sum(array_column($monthlyDetails, 'total_amount'))) }} ؋</td></tr>
        <tr><td class="label">مجموع پیش‌پرداخت:</td><td>{{ number_format(array_sum(array_column($monthlyDetails, 'advance_amount'))) }} ؋</td></tr>
        <tr><td class="label">مجموع پرداختی:</td><td>{{ number_format(array_sum(array_column($monthlyDetails, 'paid'))) }} ؋</td></tr>
        <tr><td class="label">مجموع بدهی:</td><td>{{ number_format(array_sum(array_column($monthlyDetails, 'remaining'))) }} ؋</td></tr>
    </table>

    @if(count($monthlyDetails) > 0)
        <h4>جزئیات ماهانه</h4>
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
                @foreach($monthlyDetails as $detail)
                <tr>
                    <td>{{ $detail['month_name'] }}</td>
                    <td>{{ number_format($detail['base_salary']) }}</td>
                    <td>{{ number_format($detail['overtime_amount']) }}</td>
                    <td>{{ number_format($detail['bonus_amount']) }}</td>
                    <td>{{ number_format($detail['deduction_amount']) }}</td>
                    <td>{{ number_format($detail['tax_amount']) }}</td>
                    <td>{{ number_format($detail['guarantee_amount']) }}</td>
                    <td>{{ number_format($detail['advance_amount']) }}</td>
                    <td>{{ number_format($detail['total_amount']) }}</td>
                    <td>{{ number_format($detail['paid']) }}</td>
                    <td>{{ number_format($detail['remaining']) }}</td>
                    <td>{{ $detail['payment_date'] ?? '—' }}</td>
                </tr>
                @endforeach
                <tr class="totals">
                    <td colspan="7">مجموع</td>
                    <td>{{ number_format(array_sum(array_column($monthlyDetails, 'advance_amount'))) }}</td>
                    <td>{{ number_format(array_sum(array_column($monthlyDetails, 'total_amount'))) }}</td>
                    <td>{{ number_format(array_sum(array_column($monthlyDetails, 'paid'))) }}</td>
                    <td>{{ number_format(array_sum(array_column($monthlyDetails, 'remaining'))) }}</td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    @else
        <p class="text-center">هیچ اطلاعاتی برای ماه‌های انتخاب‌شده وجود ندارد.</p>
    @endif
</div>
</body>
</html>
