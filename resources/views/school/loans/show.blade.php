<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>قرارداد قرض‌الحسنه – {{ $loan->borrower_name }}</title>
    <link href="https://cdn.jsdelivr.net/gh/rastikerdar/vazir-font@v30.1.0/dist/font-face.css" rel="stylesheet">
    <style>
        @page { size: A4; margin: 12mm; }
        body { font-family: 'Vazir', Tahoma, sans-serif; background: #fff; margin: 0; padding: 10px; direction: rtl; color: #222; }
        .container { max-width: 190mm; margin: auto; position: relative; }
        h2, h3 { text-align: center; margin: 8px 0; }
        .provider-name { text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 15px; color: #2c3e50; }

        /* جداول مشخصات */
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; font-size: 13px; }
        .info-table td { padding: 6px; vertical-align: top; border: 1px solid #999; }
        .info-table .label { background: #f8f9fa; font-weight: bold; width: 25%; }
        .photo-cell { width: 32mm; text-align: center; vertical-align: middle; }
        .photo-cell img { width: 25mm; height: 33mm; object-fit: cover; border: 1px solid #ccc; }

        /* جزئیات وام */
        .loan-details { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 13px; }
        .loan-details th, .loan-details td { border: 1px solid #333; padding: 6px 8px; }
        .loan-details th { background: #2c3e50; color: #fff; width: 30%; }

        /* اقساط */
        .installments { width: 100%; border-collapse: collapse; margin: 20px 0; font-size: 11px; }
        .installments th, .installments td { border: 1px solid #333; padding: 4px 6px; text-align: center; }
        .installments th { background: #2c3e50; color: #fff; }

        /* تعهدنامه */
        .commitment { margin-top: 30px; padding: 10px; border: 1px solid #333; background: #fdfdfd; font-size: 13px; line-height: 2; text-align: justify; }

        /* امضاها */
        .signatures { margin-top: 30px; display: flex; justify-content: space-between; }
        .signature { text-align: center; width: 45%; }
        .signature .line { border-top: 1px solid #333; margin: 40px auto 5px; width: 150px; }

        .btn-print { display: inline-block; background: #2c3e50; color: #fff; padding: 5px 15px; border: none; cursor: pointer; margin-bottom: 10px; }
        @media print { .btn-print { display: none; } }
    </style>
</head>
<body>
<div class="container">
    <button class="btn-print" onclick="window.print()">🖨️ چاپ</button>

    <!-- نام مرجع قرض‌الحسنه -->
    <div class="provider-name">{{ $loan->loan_provider ?? $loan->school->name ?? 'مکتب' }}</div>

    <h3>قرارداد قرض‌الحسنه</h3>

    <!-- مشخصات قرض‌گیرنده + عکس -->
    <table class="info-table">
        <tr>
            <td class="label">قرض‌گیرنده:</td>
            <td>
                <strong>{{ $loan->borrower_name }} {{ $loan->borrower_last_name }}</strong><br>
                پدر: {{ $loan->borrower_father_name ?? '—' }} | پدرکلان: {{ $loan->borrower_grandfather_name ?? '—' }}<br>
                تذکره: {{ $loan->borrower_national_id ?? '—' }} | تولد: {{ $loan->borrower_birth_date ? \App\Helpers\JalaliHelper::toJalali($loan->borrower_birth_date) : '—' }}<br>
                تلفن: {{ $loan->borrower_phone ?? '—' }} | تلفن اقارب: {{ $loan->borrower_relative_phone ?? '—' }}<br>
                سکونت اصلی: {{ $loan->borrower_original_province }} - {{ $loan->borrower_original_district }} - {{ $loan->borrower_original_village }}<br>
                آدرس فعلی: {{ $loan->borrower_address ?? '—' }}
            </td>
            <td class="photo-cell">
                @if($loan->borrower_photo)
                    <img src="{{ asset('storage/'.$loan->borrower_photo) }}" alt="عکس قرض‌گیرنده">
                @else
                    <div style="width:25mm; height:33mm; border:1px solid #ccc; display:inline-block; line-height:33mm; color:#999;">عکس</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- جزئیات وام -->
    <table class="loan-details">
        <tr><th>مبلغ قرضه</th><td>{{ number_format($loan->amount) }} ؋</td></tr>
        <tr><th>مدت اقساط</th><td>{{ $loan->duration_months }} ماه</td></tr>
        <tr><th>مبلغ هر قسط</th><td>{{ number_format($loan->installment_amount) }} ؋</td></tr>
        <tr><th>تاریخ شروع</th><td>{{ \App\Helpers\JalaliHelper::toJalali($loan->start_date) }}</td></tr>
        <tr><th>تاریخ پایان تقریبی</th><td>{{ \App\Helpers\JalaliHelper::toJalali($loan->end_date) }}</td></tr>
        @if($loan->notes)
        <tr><th>ملاحظات</th><td>{{ $loan->notes }}</td></tr>
        @endif
    </table>

    <!-- اقساط -->
    <h4>وضعیت اقساط</h4>
    <table class="installments">
        <thead>
            <tr>
                <th>شماره</th><th>مبلغ</th><th>سررسید</th><th>پرداخت شده</th><th>تاریخ پرداخت</th><th>وضعیت</th>
            </tr>
        </thead>
        <tbody>
            @foreach($loan->installments as $index => $inst)
            <tr>
                <td>{{ $index+1 }}</td>
                <td>{{ number_format($inst->amount) }}</td>
                <td>{{ \App\Helpers\JalaliHelper::toJalali($inst->due_date) }}</td>
                <td>{{ $inst->paid_amount ? number_format($inst->paid_amount) : '—' }}</td>
                <td>{{ $inst->paid_date ? \App\Helpers\JalaliHelper::toJalali($inst->paid_date) : '—' }}</td>
                <td>
                    @if($inst->status == 'paid')
                        <span style="color:green;">پرداخت</span>
                    @else
                        <span style="color:red;">معوق</span>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- مشخصات ضامن + عکس -->
    <table class="info-table">
        <tr>
            <td class="label">ضامن:</td>
            <td>
                <strong>{{ $loan->guarantor_name }}</strong><br>
                پدر: {{ $loan->guarantor_father_name ?? '—' }}<br>
                تذکره: {{ $loan->guarantor_national_id ?? '—' }}<br>
                تلفن: {{ $loan->guarantor_phone ?? '—' }}<br>
                آدرس: {{ $loan->guarantor_address ?? '—' }}
            </td>
            <td class="photo-cell">
                @if($loan->guarantor_photo)
                    <img src="{{ asset('storage/'.$loan->guarantor_photo) }}" alt="عکس ضامن">
                @else
                    <div style="width:25mm; height:33mm; border:1px solid #ccc; display:inline-block; line-height:33mm; color:#999;">عکس</div>
                @endif
            </td>
        </tr>
    </table>

    <!-- متن تعهدنامه -->
    <div class="commitment">
        <p><strong>تعهدنامه:</strong></p>
        <p>
            اینجانب <strong>{{ $loan->borrower_name }} {{ $loan->borrower_last_name }}</strong> فرزند {{ $loan->borrower_father_name ?? '......' }}
            متعهد می‌شوم مبلغ قرضه دریافتی به مبلغ {{ number_format($loan->amount) }} افغانی را مطابق جدول اقساط فوق، در موعد مقرر به {{ $loan->loan_provider ?? 'صندوق قرض‌الحسنه' }} بازپرداخت نمایم.
            در صورت تأخیر در پرداخت، مطابق مقررات جاری عمل خواهد شد.
        </p>
    </div>

    <!-- امضاها -->
    <div class="signatures">
        <div class="signature">
            <div class="line"></div>
            <p>امضاء قرض‌گیرنده</p>
        </div>
        <div class="signature">
            <div class="line"></div>
            <p>امضاء مسئول صندوق قرض‌الحسنه</p>
        </div>
    </div>
</div>
</body>
</html>
