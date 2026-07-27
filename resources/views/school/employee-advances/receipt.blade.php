<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>رسید پیش‌پرداخت</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A5; margin: 8mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #e9ecef; margin: 0; padding: 10px; display: flex; justify-content: center; }
        .receipt-container { width: 148mm; min-height: 210mm; background: #fff; box-shadow: 0 0 15px rgba(0,0,0,0.15); border-radius: 6px; padding: 10mm; box-sizing: border-box; margin: 0 auto; color: #333; }
        .btn-group { text-align: center; margin-bottom: 8mm; }
        .btn { padding: 4mm 10mm; margin: 0 3mm; border: none; border-radius: 4px; cursor: pointer; font-size: 12px; text-decoration: none; display: inline-block; font-weight: bold; }
        .btn-print { background: #2c3e50; color: #fff; }
        .header { background: #2c3e50; color: #fff; padding: 6mm; border-radius: 4px; text-align: center; margin-bottom: 8mm; }
        .bismillah { font-size: 20px; margin-bottom: 2mm; }
        .school-name { font-size: 18px; font-weight: bold; }
        .receipt-title { font-size: 16px; margin-top: 2mm; font-weight: bold; }
        .info-table { width: 100%; border-collapse: separate; border-spacing: 0; margin-bottom: 8mm; font-size: 12px; border: 1px solid #adb5bd; border-radius: 6px; overflow: hidden; }
        .info-table td { border: 1px solid #adb5bd; padding: 3mm 4mm; text-align: right; vertical-align: middle; }
        .info-table .label { background-color: #f8f9fa; font-weight: 600; width: 35%; color: #2c3e50; }
        .signature { margin-top: 10mm; display: flex; justify-content: space-between; }
        .signature div { text-align: center; }
        .signature .line { border-top: 1px solid #333; width: 150px; display: inline-block; }
        .footer { text-align: center; margin-top: 10mm; font-size: 9px; color: #888; border-top: 1px solid #ccc; padding-top: 4mm; }
        @media print { .btn-group { display: none; } body { background: #fff; margin: 0; padding: 0; } .receipt-container { box-shadow: none; width: 100%; min-height: auto; padding: 0; } }
    </style>
</head>
<body>
<div class="receipt-container">
    <div class="btn-group">
        <button class="btn btn-print" onclick="window.print()"><i class="fas fa-print"></i> چاپ / ذخیره PDF</button>
    </div>

    @php
        $school = \App\Models\School::find(session('active_school_id', auth()->user()->school_id));
    @endphp
    <div class="header">
        <div class="bismillah">بسمه تعالی</div>
        <div class="school-name">{{ $school->name ?? 'مکتب' }}</div>
        <div class="receipt-title">رسید پیش‌پرداخت (مساعده)</div>
    </div>

    <table class="info-table">
        <tr><td class="label">نام کارمند</td><td class="value">{{ $employeeAdvance->employee->first_name }} {{ $employeeAdvance->employee->last_name }}</td></tr>
        <tr><td class="label">ماه مقصد کسر</td><td class="value">{{ $employeeAdvance->month->name ?? '—' }}</td></tr>
        <tr><td class="label">تاریخ</td><td class="value">{{ \App\Helpers\JalaliHelper::toJalali($employeeAdvance->advance_date) }}</td></tr>
        <tr><td class="label">مبلغ</td><td class="value">{{ number_format($employeeAdvance->amount) }} ؋</td></tr>
        @if($employeeAdvance->notes)
        <tr><td class="label">توضیحات</td><td class="value">{{ $employeeAdvance->notes }}</td></tr>
        @endif
    </table>

    <div class="signature">
        <div>
            <span class="line"></span>
            <p>امضاء دریافت‌کننده</p>
        </div>
        <div>
            <span class="line"></span>
            <p>امضاء مسئول مالی</p>
        </div>
    </div>

    <div class="footer">
        تاریخ چاپ: {{ now()->format('Y/m/d') }} | سامانه مدیریت مالی الزهرا (س)
    </div>
</div>
</body>
</html>
