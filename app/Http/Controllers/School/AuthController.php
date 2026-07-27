<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * نمایش فرم ورود کاربران مدرسه
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * پردازش ورود کاربران مدرسه
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // هدایت به داشبورد مدرسه (بعداً می‌سازیم)
            return redirect()->intended('/school/dashboard');
        }

        return back()->withErrors([
            'email' => 'ایمیل یا رمز عبور اشتباه است.',
        ])->onlyInput('email');
    }

    /**
     * خروج از سیستم
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
