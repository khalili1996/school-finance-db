<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>{{ $student->first_name }} {{ $student->last_name }}</title>
    <style>
        /* جاسازی فونت وزیر با base64 برای mPDF */
        @font-face {
            font-family: 'Vazir';
            src: url('data:font/truetype;charset=utf-8;base64, {{ base64_encode(file_get_contents(storage_path('fonts/Vazir.ttf'))) }}') format('truetype');
            font-weight: normal;
            font-style: normal;
        }

        body {
            font-family: 'Vazir', Tahoma, sans-serif;
            direction: rtl;
            padding: 15px;
        }

        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 2px solid #b8860b;
        }
        .bismillah { font-size: 22px; color: #1e3a5f; margin-bottom: 5px; }
        .school-name { font-size: 19px; font-weight: bold; color: #1e3a5f; }
        .form-title {
            font-size: 15px;
            margin: 15px 0 8px;
            padding-bottom: 6px;
            border-bottom: 1px dashed #b8860b;
            font-weight: bold;
            color: #2c3e50;
        }

        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 10px;
            font-size: 13px;
            border: 1px solid #adb5bd;
            border-radius: 8px;
            overflow: hidden;
        }
        .info-table td {
            border: 1px solid #adb5bd;
            padding: 6px 10px;
            text-align: right;
            vertical-align: middle;
        }
        .info-table .label {
            background-color: #f8f9fa;
            font-weight: 600;
            width: 22%;
            color: #2c3e50;
        }
        .info-table .value {
            width: 28%;
            color: #333;
        }
        .info-table tr:nth-child(even) td {
            background-color: #fcfcfc;
        }

        .finance-row { display: flex; gap: 10px; margin: 10px 0; }
        .finance-item {
            flex: 1;
            background: #f8f9fa;
            border: 1px solid #adb5bd;
            border-radius: 8px;
            padding: 12px 10px;
            text-align: center;
        }
        .finance-item h4 { margin: 0 0 5px; font-size: 12px; color: #4a5568; font-weight: 500; }
        .finance-item p { margin: 0; font-size: 17px; font-weight: bold; color: #1e3a5f; }

        .signature {
            margin-top: 30px; text-align: left; padding-left: 15px; font-size: 13px; color: #4a5568;
        }
        .signature .line { display: inline-block; width: 200px; border-bottom: 1px solid #333; margin-right: 10px; }
    </style>
</head>
<body>
    <div class="header">
        <div class="bismillah">بسمه تعالی</div>
        <div class="school-name">{{ $student->school->name ?? 'مکتب' }}</div>
        <div>سال تعلیمی: {{ request('year_filter') ?? '—' }}</div>
    </div>

    <table style="width:100%; border-collapse: collapse;">
        <tr>
            <td style="width:50%; vertical-align:top; padding:0 5px 0 0;">
                <table class="info-table">
                    <tr><td class="label">کد دانش‌آموز</td><td class="value">{{ $student->student_code }}</td></tr>
                    <tr><td class="label">نام</td><td class="value">{{ $student->first_name }}</td></tr>
                    <tr><td class="label">تخلص</td><td class="value">{{ $student->last_name }}</td></tr>
                    <tr><td class="label">نام پدر</td><td class="value">{{ $student->father_name }}</td></tr>
                    <tr><td class="label">پدرکلان</td><td class="value">{{ $student->grandfather_name ?? '—' }}</td></tr>
                    <tr><td class="label">تاریخ تولد</td><td class="value">{{ $student->birth_date ? date('Y/m/d', strtotime($student->birth_date)) : ($student->birth_year ?? '—') }}</td></tr>
                    <tr><td class="label">شماره تذکره</td><td class="value">{{ $student->national_id }}</td></tr>
                    <tr><td class="label">نمبر اساس</td><td class="value">{{ $student->base_number ?? '—' }}</td></tr>
                    <tr><td class="label">جنسیت</td><td class="value">{{ $student->gender == 'male' ? 'پسر' : 'دختر' }}</td></tr>
                    <tr><td class="label">صنف</td><td class="value">{{ $student->class ?? '—' }}</td></tr>
                </table>
            </td>
            <td style="width:50%; vertical-align:top; padding:0 0 0 5px;">
                <table class="info-table">
                    <tr><td class="label">تلفن</td><td class="value">{{ $student->phone ?? '—' }}</td></tr>
                    <tr><td class="label">واتساپ</td><td class="value">{{ $student->whatsapp_phone ?? '—' }}</td></tr>
                    <tr><td class="label">سکونت اصلی</td><td class="value">{{ $student->original_residence ?? '—' }}</td></tr>
                    <tr><td class="label">سکونت فعلی / آدرس</td><td class="value">{{ $student->address ?? '—' }}</td></tr>
                    <tr><td class="label">تاریخ ثبت‌نام</td><td class="value">{{ $student->enrollment_date ? date('Y/m/d', strtotime($student->enrollment_date)) : '—' }}</td></tr>
                    <tr><td class="label">وضعیت</td><td class="value">
                        @switch($student->status)
                            @case('present') <span style="color:#27ae60;">فعال</span> @break
                            @case('blocked') <span style="color:#e74c3c;">غیرفعال</span> @break
                            @case('temporary') <span style="color:#f39c12;">موقت</span> @break
                            @case('three_piece') <span style="color:#2980b9;">سه‌پارچه</span> @break
                            @default {{ $student->status }}
                        @endswitch
                    </td></tr>
                    <tr><td class="label">وضعیت مالی</td><td class="value">
                        @switch($student->financial_status)
                            @case('full') شهریه کامل @break
                            @case('discount') دارای تخفیف @break
                            @case('free') <span style="color:#8e44ad;">رایگان</span> @break
                            @default تعیین نشده
                        @endswitch
                    </td></tr>
                    <tr><td class="label">یتیم</td><td class="value">{{ $student->is_orphan ? 'بلی' : 'خیر' }}</td></tr>
                </table>
            </td>
        </tr>
    </table>

    @if($student->guardian)
    <div class="form-title">مشخصات ولی / سرپرست</div>
    <table class="info-table">
        <tr>
            <td class="label">نام کامل</td><td class="value">{{ $student->guardian->full_name }}</td>
            <td class="label">نسبت</td><td class="value">
                @switch($student->guardian->relation)
                    @case('father') پدر @break
                    @case('mother') مادر @break
                    @case('brother') برادر @break
                    @case('uncle') کاکا / ماما @break
                    @case('other') سایر @break
                    @default {{ $student->guardian->relation ?? '—' }}
                @endswitch
            </td>
        </tr>
        <tr>
            <td class="label">تحصیلات</td><td class="value">{{ $student->guardian->education ?? '—' }}</td>
            <td class="label">شغل</td><td class="value">{{ $student->guardian->job ?? '—' }}</td>
        </tr>
    </table>
    @endif

    @php
        $totalFees = $student->studentFees->sum(fn($f) => $f->amount - $f->discount);
        $totalPaid = $student->payments->sum('amount');
        $balance = $totalFees - $totalPaid;
    @endphp
    <div class="form-title">وضعیت مالی</div>
    <div class="finance-row">
        <div class="finance-item">
            <h4>کل شهریه تعیین‌شده</h4>
            <p>{{ number_format($totalFees) }} ؋</p>
        </div>
        <div class="finance-item">
            <h4>مجموع پرداخت‌ها</h4>
            <p>{{ number_format($totalPaid) }} ؋</p>
        </div>
        <div class="finance-item">
            <h4>باقی‌مانده</h4>
            <p>{{ number_format($balance) }} ؋</p>
        </div>
    </div>

    <div class="signature">
        <span>امضاء مسئول مالی:</span>
        <span class="line"></span>
    </div>
</body>
</html>
