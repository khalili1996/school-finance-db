<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش عواید</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #e9ecef; margin: 0; padding: 10px; }
        .report-container { max-width: 100%; background: #fff; padding: 15mm; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .filter-form { margin-bottom: 15mm; text-align: center; }
        .filter-form select, .filter-form button { padding: 5px 10px; font-family: 'Vazir'; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .summary { margin-top: 10mm; text-align: right; font-weight: bold; }
        .btn-print { background: #2c3e50; color: #fff; padding: 8px 16px; border: none; cursor: pointer; font-family: 'Vazir'; text-decoration: none; display: inline-block; }
        @media print {
            .btn-print, .filter-form button, .filter-form select { display: none; }
            body { background: #fff; margin: 0; padding: 0; }
            .report-container { box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>
<div class="report-container">
    {{-- ★ هدر یکسان (لوگو + نام مکتب + سال مالی) --}}
    @php
        $subtitle = $selectedMonth ? 'ماه: ' . $selectedMonth->name : '';
    @endphp
    @include('partials.report-header', [
        'title' => 'گزارش عواید',
        'subtitle' => $subtitle
    ])

    <div class="filter-form">
        <form method="GET" action="{{ route('school.incomes.report') }}">
            <label>ماه:</label>
            <select name="month_id">
                <option value="">همه</option>
                @foreach($months as $month)
                    <option value="{{ $month->id }}" {{ request('month_id') == $month->id ? 'selected' : '' }}>{{ $month->name }}</option>
                @endforeach
            </select>

            <label>دسته‌بندی:</label>
            <select name="category_id">
                <option value="">همه</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn-print" style="display:inline-block;">اعمال</button>
            <button type="button" class="btn-print" onclick="window.print()" style="display:inline-block; margin-right:5px;">چاپ / PDF</button>
            <a href="{{ route('school.incomes.index') }}" class="btn-print" style="display:inline-block; background:#777;">بازگشت</a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ردیف</th>
                <th>عنوان</th>
                <th>دسته‌بندی</th>
                <th>مبلغ کل</th>
                <th>دریافتی</th>
                <th>باقی‌مانده</th>
                <th>تاریخ</th>
                <th>ماه</th>
                <th>منبع</th>
                <th>وضعیت</th>
                <th>توضیحات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($incomes as $i => $income)
                @php $remaining = $income->total_amount - $income->received_amount; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $income->title }}</td>
                    <td>{{ $income->category->name ?? '—' }}</td>
                    <td>{{ number_format($income->total_amount) }}</td>
                    <td>{{ number_format($income->received_amount) }}</td>
                    <td>{{ number_format(max($remaining, 0)) }}</td>
                    <td>{{ \App\Helpers\JalaliHelper::toJalali($income->income_date) }}</td>
                    <td>{{ $income->month->name ?? '—' }}</td>
                    <td>{{ $income->source ?? '—' }}</td>
                    <td>{{ $income->status == 'received' ? 'دریافت کامل' : ($income->status == 'partially_received' ? 'دریافت جزئی' : 'دریافت نشده') }}</td>
                    <td>{{ $income->description ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p>تعداد رکوردها: {{ $incomes->count() }}</p>
        <p>مجموع مبالغ کل: {{ number_format($totalAmount) }} ؋</p>
        <p>مجموع دریافتی‌ها: {{ number_format($totalReceived) }} ؋</p>
        <p>مجموع باقی‌مانده: {{ number_format($totalRemaining) }} ؋</p>
    </div>
</div>
</body>
</html>
