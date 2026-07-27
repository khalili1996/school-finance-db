<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش مصارف</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #e9ecef; margin: 0; padding: 10px; }
        .report-container { max-width: 100%; background: #fff; padding: 15mm; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        .filter-form { margin-bottom: 15mm; text-align: center; }
        .filter-form input, .filter-form select, .filter-form button { padding: 5px 10px; font-family: 'Vazir'; }
        table { width: 100%; border-collapse: collapse; font-size: 11px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .summary { margin-top: 10mm; text-align: right; font-weight: bold; }
        .btn-print { background: #2c3e50; color: #fff; padding: 8px 16px; border: none; cursor: pointer; font-family: 'Vazir'; }
        @media print {
            .btn-print, .filter-form button { display: none; }
            body { background: #fff; margin: 0; padding: 0; }
            .report-container { box-shadow: none; padding: 0; }
        }
    </style>
</head>
<body>
<div class="report-container">
    {{-- ★ هدر یکسان (لوگو + نام مکتب + سال مالی) --}}
    @php
        $subtitle = '';
        if ($from && $to) {
            $subtitle = "از تاریخ {$from} تا {$to}";
        }
    @endphp
    @include('partials.report-header', [
        'title' => 'گزارش مصارف',
        'subtitle' => $subtitle
    ])

    <div class="filter-form">
        <form method="GET" action="{{ route('school.expenses.report') }}">
            <label>از تاریخ (شمسی):</label>
            <input type="text" name="date_from" value="{{ $from }}" placeholder="مثلاً ۱۴۰۴/۰۱/۰۱">

            <label>تا تاریخ (شمسی):</label>
            <input type="text" name="date_to" value="{{ $to }}" placeholder="مثلاً ۱۴۰۴/۰۳/۳۰">

            <label>دسته‌بندی:</label>
            <select name="category_id">
                <option value="">همه</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                        {{ $cat->name }}
                    </option>
                @endforeach
            </select>

            <button type="submit" class="btn-print" style="display:inline-block;">اعمال فیلتر</button>
            <button type="button" class="btn-print" onclick="window.print()" style="display:inline-block; margin-right:5px;">چاپ / PDF</button>
            <a href="{{ route('school.expenses.index') }}" class="btn-print" style="display:inline-block; background:#777;">بازگشت</a>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ردیف</th>
                <th>عنوان</th>
                <th>دسته‌بندی</th>
                <th>تعداد/مقدار</th>
                <th>واحد</th>
                <th>مبلغ کل</th>
                <th>پرداختی</th>
                <th>باقی‌مانده</th>
                <th>تاریخ</th>
                <th>شماره فاکتور</th>
                <th>وضعیت</th>
                <th>توضیحات</th>
            </tr>
        </thead>
        <tbody>
            @foreach($expenses as $i => $expense)
                @php $remaining = $expense->total_amount - $expense->paid_amount; @endphp
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $expense->title }}</td>
                    <td>{{ $expense->category->name ?? '—' }}</td>
                    <td>{{ $expense->quantity }}</td>
                    <td>{{ $expense->unit }}</td>
                    <td>{{ number_format($expense->total_amount) }}</td>
                    <td>{{ number_format($expense->paid_amount) }}</td>
                    <td>{{ number_format(max($remaining, 0)) }}</td>
                    <td>{{ \App\Helpers\JalaliHelper::toJalali($expense->expense_date) }}</td>
                    <td>{{ $expense->invoice_number }}</td>
                    <td>{{ $expense->status == 'paid' ? 'پرداخت کامل' : ($expense->status == 'partially_paid' ? 'پرداخت جزئی' : 'پرداخت نشده') }}</td>
                    <td>{{ $expense->description }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="summary">
        <p>تعداد رکوردها: {{ $expenses->count() }}</p>
        <p>مجموع مبالغ کل: {{ number_format($totalAmount) }} ؋</p>
        <p>مجموع پرداختی‌ها: {{ number_format($totalPaid) }} ؋</p>
        <p>مجموع باقی‌مانده: {{ number_format($totalRemaining) }} ؋</p>
    </div>
</div>
</body>
</html>
