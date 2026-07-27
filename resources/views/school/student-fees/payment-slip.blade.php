<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رسید پرداخت شهریه</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A5; margin: 8mm; }
        body {
            font-family: 'Vazir', Tahoma, sans-serif;
            background: #fff; margin: 0; padding: 10px; direction: rtl; color: #333;
        }
        .btn-print { background: #2c3e50; color: #fff; padding: 6px 18px; border: none; border-radius: 5px; cursor: pointer; margin-bottom: 10px; }
        @media print { .btn-print { display: none; } }
        table { width: 100%; border-collapse: collapse; margin: 15px 0; font-size: 12px; }
        th, td { border: 1px solid #333; padding: 6px 8px; text-align: right; }
        th { background: #2c3e50; color: #fff; text-align: center; }
        .label { font-weight: bold; background-color: #f8f9fa; width: 30%; }
        .value { width: 70%; }
        .signature { margin-top: 10mm; display: flex; justify-content: space-between; }
        .signature div { text-align: center; }
        .signature .line { border-top: 1px solid #333; width: 150px; display: inline-block; }
        .footer { text-align: center; margin-top: 10mm; font-size: 9px; color: #888; border-top: 1px solid #ccc; padding-top: 4mm; }
    </style>
</head>
<body>
<button class="btn-print" onclick="window.print()">🖨️ چاپ رسید</button>

@include('partials.report-header', [
    'title' => 'رسید پرداخت شهریه',
    'subtitle' => 'شماره رسید: ' . ($payment->receipt_number ?? $payment->id)
])

<table>
    <tr><td class="label">کد دانش‌آموز</td><td class="value">{{ $payment->student->student_code }}</td></tr>
    <tr><td class="label">نام دانش‌آموز</td><td class="value">{{ $payment->student->first_name }} {{ $payment->student->last_name }}</td></tr>
    <tr><td class="label">نام پدر</td><td class="value">{{ $payment->student->father_name }}</td></tr>
    <tr><td class="label">صنف</td><td class="value">{{ $payment->student->class ?? '—' }}</td></tr>
    @if($payment->studentFee && $payment->studentFee->month)
    <tr><td class="label">ماه</td><td class="value">{{ $payment->studentFee->month->name }}</td></tr>
    @endif
    <tr><td class="label">مبلغ شهریه (این ماه)</td><td class="value">{{ number_format($payment->studentFee->amount ?? 0) }} ؋</td></tr>
    <tr><td class="label">مبلغ پرداختی</td><td class="value">{{ number_format($payment->amount) }} ؋</td></tr>
    @php
        $remaining = 0;
        if ($payment->studentFee) {
            $totalPaid = \App\Models\Payment::where('student_id', $payment->student_id)
                ->where('month_id', $payment->month_id)
                ->sum('amount');
            $remaining = ($payment->studentFee->amount - $payment->studentFee->discount) - $totalPaid;
        }
    @endphp
    <tr><td class="label">باقی‌مانده</td><td class="value">{{ number_format(max($remaining, 0)) }} ؋</td></tr>
    <tr><td class="label">تاریخ پرداخت</td><td class="value">{{ \App\Helpers\JalaliHelper::toJalali($payment->payment_date) }}</td></tr>
</table>

{{-- امضاها --}}
<div class="signature">
    <div>
        <span class="line"></span>
        <p>امضاء مسئول مالی</p>
    </div>
    <div>
        <span class="line"></span>
        <p>امضاء دریافت‌کننده</p>
    </div>
</div>

<div class="footer">
    تاریخ چاپ: {{ \App\Helpers\JalaliHelper::todayJalali() }} | سامانه مدیریت مالی الزهرا (س)
</div>
</body>
</html>
