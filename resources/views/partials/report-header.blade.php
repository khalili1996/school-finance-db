@php
    $schoolId = session('active_school_id', auth()->user()->school_id ?? null);
    $logo = \App\Models\Setting::get('logo', null, $schoolId);
    $schoolName = \App\Models\Setting::get('school_name')
        ?: (\App\Models\School::find($schoolId)->name ?? 'مکتب');
    $address = \App\Models\Setting::get('address', '', $schoolId);
    $phone   = \App\Models\Setting::get('phone', '', $schoolId);
    $email   = \App\Models\Setting::get('email', '', $schoolId);
    $yearName = session('active_academic_year_name', '');

    // تبدیل لوگو به base64 برای نمایش در چاپ
    $logoData = '';
    if ($logo) {
        $logoPath = storage_path('app/public/' . $logo);
        if (file_exists($logoPath)) {
            $type = pathinfo($logoPath, PATHINFO_EXTENSION);
            $data = file_get_contents($logoPath);
            $logoData = 'data:image/' . $type . ';base64,' . base64_encode($data);
        }
    }
@endphp

<div style="
    background: linear-gradient(135deg, #1e3a5f 0%, #2c5282 100%);
    color: #fff;
    padding: 12px 20px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    direction: rtl;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
">
    {{-- لوگو --}}
    @if($logoData)
        <div style="flex: 0 0 auto; margin-left: 20px; background: #fff; border-radius: 6px; padding: 5px;">
            <img src="{{ $logoData }}" alt="لوگو"
                 style="width: 65px; height: 65px; object-fit: contain;">
        </div>
    @endif

    {{-- اطلاعات مکتب --}}
    <div style="flex: 1; text-align: center;">
        <h2 style="margin: 0 0 5px 0; font-size: 20px; font-weight: bold; letter-spacing: 1px; color: #f0d78c;">
            {{ $schoolName }}
        </h2>
        <p style="margin: 3px 0; font-size: 11px; opacity: 0.9; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            @if($address)
                <span><i class="fas fa-map-marker-alt"></i> {{ $address }}</span>
            @endif
            @if($phone)
                <span><i class="fas fa-phone"></i> {{ $phone }}</span>
            @endif
            @if($email)
                <span><i class="fas fa-envelope"></i> {{ $email }}</span>
            @endif
            @if($yearName)
                <span><i class="fas fa-calendar-alt"></i> سال مالی: {{ $yearName }}</span>
            @endif
        </p>
    </div>
</div>

{{-- خط جداکننده طلایی --}}
<div style="border-bottom: 3px solid #c49b2a; margin-bottom: 15px;"></div>

{{-- عنوان گزارش (در صورت وجود) --}}
@if(isset($title))
    <h3 style="text-align: center; color: #1e3a5f; margin: 15px 0 5px 0; font-size: 16px;">
        {{ $title }}
    </h3>
@endif
@if(isset($subtitle) && $subtitle)
    <p style="text-align: center; color: #555; font-size: 12px; margin: 0 0 15px 0;">
        {{ $subtitle }}
    </p>
@endif
