<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش چاپی تجهیزات مکتب</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4 landscape; margin: 10mm; }
        body {
            font-family: 'Vazir', Tahoma, sans-serif;
            background: #fff; margin: 0; padding: 10px; direction: rtl; color: #333;
        }
        .btn-print { background: #2c3e50; color: #fff; padding: 6px 18px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 10px; }
        @media print { .btn-print { display: none; } }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 11px; }
        th, td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        th { background: #2c3e50; color: #fff; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #555; }
    </style>
</head>
<body>
<button class="btn-print" onclick="window.print()">🖨️ چاپ گزارش</button>

{{-- ★ هدر یکسان (لوگو + نام مکتب + سال مالی) --}}
@include('partials.report-header', [
    'title' => 'گزارش تجهیزات',
    'subtitle' => $selectedCategory ? 'دسته‌بندی: ' . $selectedCategory->name : ''
])

<table>
    <thead>
        <tr>
            <th>کد اموال</th>
            <th>دسته‌بندی</th>
            <th>شرح</th>
            <th>تعداد</th>
            <th>تحویل‌گیرنده</th>
            <th>قیمت واحد</th>
            <th>قیمت کل</th>
            <th>تاریخ خرید</th>
            <th>وضعیت</th>
            <th>توضیحات</th>
        </tr>
    </thead>
    <tbody>
        @forelse($assets as $asset)
        <tr>
            <td>{{ $asset->asset_code }}</td>
            <td>{{ $asset->category->name ?? '—' }}</td>
            <td>{{ $asset->description }}</td>
            <td>{{ $asset->quantity }}</td>
            <td>{{ $asset->custodian ?? '—' }}</td>
            <td>{{ number_format($asset->unit_price) }}</td>
            <td>{{ number_format($asset->total_price) }}</td>
            <td>{{ \App\Helpers\JalaliHelper::toJalali($asset->purchase_date) }}</td>
            <td>
                @switch($asset->status)
                    @case('active') فعال @break
                    @case('transferred') انتقال @break
                    @case('broken') خراب @break
                    @case('scrap') اسقاط @break
                    @default {{ $asset->status }}
                @endswitch
            </td>
            <td>{{ $asset->notes ?? '—' }}</td>
        </tr>
        @empty
        <tr>
            <td colspan="10" class="text-center text-muted py-4">تجهیزاتی با این فیلترها یافت نشد.</td>
        </tr>
        @endforelse
    </tbody>
</table>

<div class="footer">
    تاریخ چاپ: {{ \App\Helpers\JalaliHelper::todayJalali() }} | سامانه مدیریت مالی الزهرا (س)
</div>
</body>
</html>
