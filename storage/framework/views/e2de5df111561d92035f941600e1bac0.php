<!DOCTYPE html>
<html lang="fa" dir="rtl">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>سامانه مدیریت مالی مکتب منجی الزهرا (س)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>

        body {
            background: linear-gradient(135deg, #1e3c72, #3e6fc2);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        .landing-card {
            background: rgba(255,255,255,0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 3rem;
            text-align: center;
            max-width: 500px;
            width: 100%;
        }
        .btn-custom {
            margin: 0.5rem;
            padding: 0.75rem 2rem;
            font-weight: bold;
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="landing-card">
        <h1 class="mb-3">دیتابیس مالی الزهرا (س)</h1>
        <p class="lead mb-4">سامانه‌ی مدیریت مالی چند مکتبه </p>
        <hr class="bg-light">
        <div class="d-grid gap-3">
            <a href="/login" class="btn btn-light btn-custom btn-lg">
                <i class="fas fa-sign-in-alt ms-2"></i> ورود مکاتب
            </a>
            <a href="/admin/login" class="btn btn-outline-light btn-custom btn-lg">
                <i class="fas fa-user-shield ms-2"></i> ورود سوپر ادمین
            </a>
        </div>
        <p class="mt-4 text-white-50">© 1405 - تمام حقوق محفوظ است</p>
    </div>
</body>
</html>
<?php /**PATH E:\school_finance_db\resources\views\home.blade.php ENDPATH**/ ?>