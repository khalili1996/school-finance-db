<?php
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
?>

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
    
    <?php if($logoData): ?>
        <div style="flex: 0 0 auto; margin-left: 20px; background: #fff; border-radius: 6px; padding: 5px;">
            <img src="<?php echo e($logoData); ?>" alt="لوگو"
                 style="width: 65px; height: 65px; object-fit: contain;">
        </div>
    <?php endif; ?>

    
    <div style="flex: 1; text-align: center;">
        <h2 style="margin: 0 0 5px 0; font-size: 20px; font-weight: bold; letter-spacing: 1px; color: #f0d78c;">
            <?php echo e($schoolName); ?>

        </h2>
        <p style="margin: 3px 0; font-size: 11px; opacity: 0.9; display: flex; justify-content: center; gap: 15px; flex-wrap: wrap;">
            <?php if($address): ?>
                <span><i class="fas fa-map-marker-alt"></i> <?php echo e($address); ?></span>
            <?php endif; ?>
            <?php if($phone): ?>
                <span><i class="fas fa-phone"></i> <?php echo e($phone); ?></span>
            <?php endif; ?>
            <?php if($email): ?>
                <span><i class="fas fa-envelope"></i> <?php echo e($email); ?></span>
            <?php endif; ?>
            <?php if($yearName): ?>
                <span><i class="fas fa-calendar-alt"></i> سال مالی: <?php echo e($yearName); ?></span>
            <?php endif; ?>
        </p>
    </div>
</div>


<div style="border-bottom: 3px solid #c49b2a; margin-bottom: 15px;"></div>


<?php if(isset($title)): ?>
    <h3 style="text-align: center; color: #1e3a5f; margin: 15px 0 5px 0; font-size: 16px;">
        <?php echo e($title); ?>

    </h3>
<?php endif; ?>
<?php if(isset($subtitle) && $subtitle): ?>
    <p style="text-align: center; color: #555; font-size: 12px; margin: 0 0 15px 0;">
        <?php echo e($subtitle); ?>

    </p>
<?php endif; ?>
<?php /**PATH E:\school_finance_db\resources\views\partials\report-header.blade.php ENDPATH**/ ?>