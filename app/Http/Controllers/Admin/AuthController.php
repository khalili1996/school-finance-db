<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * نمایش فرم ورود سوپر ادمین
     */
    public function showLoginForm()
    {
        return view('admin.login');
    }

    /**
     * پردازش ورود سوپر ادمین
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            // فقط سوپر ادمین اجازه ورود به پنل مرکزی دارد
            if (!auth()->user()->hasRole('Super Admin')) {
                Auth::logout();
                return back()->withErrors([
                    'email' => 'شما دسترسی به پنل مدیریت مرکزی ندارید.',
                ]);
            }

            return redirect()->intended('/admin/dashboard');
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

        return redirect('/admin/login');
    }
}
