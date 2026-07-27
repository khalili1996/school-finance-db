<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>پیش‌نمایش چاپ شهریه – {{ $student->first_name }} {{ $student->last_name }}</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A5; margin: 8mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #fff; margin: 0; padding: 10px; direction: rtl; }
        .container { width: 100%; max-width: 148mm; margin: auto; }
        h2, h3, h4 { text-align: center; margin: 5px 0; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; font-size: 11px; }
        th, td { border: 1px solid #333; padding: 3px 5px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .paid { background: #d4edda; }
        .unpaid { background: #f8d7da; }
        .btn-print { display: inline-block; background: #2c3e50; color: #fff; padding: 4px 12px; border: none; cursor: pointer; font-family: 'Vazir'; margin-bottom: 10px; text-decoration: none; }
        .filter-form { margin-bottom: 10px; }
        .filter-form select, .filter-form .btn-sm { font-family: 'Vazir'; font-size: 12px; padding: 2px 5px; }
        @media print {
            .btn-print, .filter-form { display: none; }
        }
    </style>
</head>
<body>
<div class="container">
    <button class="btn-print" onclick="window.print()">چاپ</button>

    <h2>{{ $student->school->name ?? 'مکتب' }}</h2>
    <h3>گزارش شهریه دانش‌آموز</h3>

    {{-- خلاصه اطلاعات --}}
    <table>
        <tr>
            <td><strong>کد:</strong> {{ $student->student_code }}</td>
            <td><strong>نام:</strong> {{ $student->first_name }} {{ $student->last_name }}</td>
        </tr>
        <tr>
            <td><strong>نام پدر:</strong> {{ $student->father_name }}</td>
            <td><strong>صنف:</strong> {{ $student->class ?? '—' }}</td>
        </tr>
        <tr>
            <td><strong>مجموع شهریه (قابل پرداخت):</strong> {{ number_format($totalFee) }} ؋</td>
            <td><strong>تخفیف کل:</strong>
                {{ number_format(
                    $selectedMonthName
                    ? $student->studentFees->where('month.name', $selectedMonthName)->sum('discount')
                    : $student->studentFees->sum('discount')
                ) }} ؋
            </td>
        </tr>
        <tr>
            <td><strong>پرداختی کل:</strong> {{ number_format($totalPaid) }} ؋</td>
            <td><strong>باقی‌مانده:</strong> {{ number_format($totalRemaining) }} ؋</td>
        </tr>
    </table>

    {{-- فرم فیلتر ماه --}}
    <form method="GET" action="{{ route('school.student-fees.fee-preview', $student->id) }}" class="filter-form">
        <label for="monthFilter">فیلتر ماه:</label>
        <select name="month" id="monthFilter" onchange="this.form.submit()">
            <option value="">همهٔ ماه‌ها</option>
            @foreach($allMonths ?? $monthlyDetails as $item)
                @php
                    $monthName = is_array($item) ? $item['month_name'] : $item->name;
                @endphp
                <option value="{{ $monthName }}" {{ isset($selectedMonthName) && $selectedMonthName == $monthName ? 'selected' : '' }}>
                    {{ $monthName }}
                </option>
            @endforeach
        </select>
        <a href="{{ route('school.student-fees.fee-preview', $student->id) }}" class="btn-sm" style="margin-right:8px;">حذف فیلتر</a>
    </form>

    <h4>وضعیت ماه‌ها</h4>
    <table>
        <thead>
            <tr>
                <th>ماه</th>
                <th>مبلغ</th>
                <th>تخفیف</th>
                <th>قابل پرداخت</th>
                <th>پرداخت شده</th>
                <th>باقی‌مانده</th>
                <th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            @forelse($monthlyDetails as $detail)
            <tr class="{{ $detail['is_paid'] ? 'paid' : 'unpaid' }}">
                <td>{{ $detail['month_name'] }}</td>
                <td>{{ number_format($detail['amount']) }}</td>
                <td>{{ number_format($detail['discount']) }}</td>
                <td>{{ number_format($detail['due']) }}</td>
                <td>{{ number_format($detail['paid']) }}</td>
                <td>{{ number_format($detail['remaining']) }}</td>
                <td>{{ $detail['is_paid'] ? 'پرداخت' : 'بدهکار' }}</td>
            </tr>
            @empty
            <tr>
                <td colspan="7">داده‌ای برای این ماه وجود ندارد.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
</body>
</html>
