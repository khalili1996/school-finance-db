<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>گزارش اطلاعیه‌های شهریه {{ $classFilter ? '– صنف '.$classFilter : '' }}</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4; margin: 8mm; }
        body {
            font-family: 'Vazir', Tahoma, sans-serif;
            background: #fff;
            margin: 0;
            padding: 0;
            direction: rtl;
            color: #000;
        }
        .btn-print {
            display: inline-block;
            background: #2c3e50;
            color: #fff;
            padding: 4px 10px;
            border: none;
            cursor: pointer;
            font-family: 'Vazir';
            margin: 5px 0;
            font-size: 12px;
        }
        .grid-container {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            padding: 5px;
            max-width: 100%;
        }
        .notice-card {
            border: 1px solid #999;
            padding: 8px;
            box-shadow: 0 0 3px rgba(0,0,0,0.1);
            background: #fff;
            font-size: 10px;
            line-height: 1.5;
            page-break-inside: avoid;
            break-inside: avoid;
        }
        .header { text-align: center; margin-bottom: 8px; }
        .header .bismillah { font-size: 14px; color: #555; margin-bottom: 3px; }
        .header h2 { font-size: 14px; margin: 3px 0; }
        .header h3 { font-size: 13px; margin: 3px 0; }
        .body-text { margin-bottom: 8px; text-align: justify; }
        table { width: 100%; border-collapse: collapse; margin: 8px 0; font-size: 9px; }
        th, td { border: 1px solid #333; padding: 2px 3px; text-align: center; }
        th { background: #2c3e50; color: #fff; font-size: 9px; }
        .total-row { font-weight: bold; background: #f9f9f9; }
        .footer { margin-top: 8px; text-align: left; font-size: 10px; }
        .signature { margin-top: 10px; text-align: left; font-weight: bold; font-size: 10px; }

        @media print {
            .btn-print { display: none; }
            body { margin: 0; padding: 0; }
            .grid-container { gap: 6px; padding: 0; }
            .notice-card { border: 0.5px solid #ccc; box-shadow: none; }
        }
    </style>
</head>
<body>

@if(count($unpaidStudents) > 0)
    <button class="btn-print" onclick="window.print()">🖨️ چاپ همه اطلاعیه‌ها</button>

    <div class="grid-container">
        @foreach($unpaidStudents as $item)
            @php
                $student = $item['student'];
                $unpaidMonths = $item['unpaidMonths'];
            @endphp

            <div class="notice-card">
                <div class="header">
                    <div class="bismillah">بسمه تعالی</div>
                    <h2>{{ $student->school->name ?? 'آموزشگاه' }}</h2>
                    <h3>اطلاعیه پرداخت شهریه</h3>
                </div>

                <div class="body-text">
                    <p style="margin:0 0 5px 0;">اولیای محترم <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                    فرزند <strong>{{ $student->father_name }}</strong>
                    (صنف <strong>{{ $student->class ?? '—' }}</strong>)، سلام علیکم</p>
                    <p style="margin:0 0 5px 0;">شهریه ماه‌های زیر پرداخت نشده است:</p>
                </div>

                @if(count($unpaidMonths) > 0)
                    <table>
                        <thead>
                            <tr>
                                <th>ماه</th>
                                <th>مبلغ</th>
                                <th>تخفیف</th>
                                <th>قابل پرداخت</th>
                                <th>پرداخت شده</th>
                                <th>باقی‌مانده</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $totalRemaining = 0; @endphp
                            @foreach($unpaidMonths as $um)
                                @php $totalRemaining += $um['remaining']; @endphp
                                <tr>
                                    <td>{{ $um['month']->name }}</td>
                                    <td>{{ number_format($um['fee']->amount) }}</td>
                                    <td>{{ number_format($um['fee']->discount) }}</td>
                                    <td>{{ number_format($um['due']) }}</td>
                                    <td>{{ number_format($um['paid']) }}</td>
                                    <td><strong>{{ number_format($um['remaining']) }}</strong></td>
                                </tr>
                            @endforeach
                            <tr class="total-row">
                                <td colspan="5">جمع</td>
                                <td><strong>{{ number_format($totalRemaining) }} ؋</strong></td>
                            </tr>
                        </tbody>
                    </table>
                    <p style="margin:5px 0;">
                        مجموع معوقه <strong>{{ number_format($totalRemaining) }} افغانی</strong>
                    </p>
                @endif

                <p style="margin:5px 0;">لطفاً در اولین فرصت پرداخت فرمایید.</p>
                <p style="margin:5px 0;">با تشکر</p>
                <div class="signature">
                    <p>مدیریت آموزشگاه</p>
                </div>
            </div>
        @endforeach
    </div>
@else
    <p style="text-align:center; margin-top:50px;">هیچ دانش‌آموز بدهکاری با این فیلترها یافت نشد.</p>
@endif

</body>
</html>
