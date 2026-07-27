<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود سوپر ادمین – دیتابیس مالی الزهرا (س)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #0f2027, #203a43, #2c5364);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
        }
        .login-card {
            max-width: 400px;
            width: 100%;
            padding: 2rem;
            border-radius: 15px;
            box-shadow: 0 0 30px rgba(0,0,0,0.3);
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <h3 class="text-center mb-4">پنل مدیریت مرکزی</h3>

        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="/admin/login">
            @csrf
            <div class="mb-3">
                <label class="form-label">ایمیل سوپر ادمین</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
            </div>
            <div class="mb-3">
                <label class="form-label">رمز عبور</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <button type="submit" class="btn btn-dark w-100">ورود به پنل مرکزی</button>
        </form>

        <div class="text-center mt-3">
            <a href="/" class="text-decoration-none">← بازگشت به صفحه اصلی</a>
        </div>
    </div>
</body>
</html>
