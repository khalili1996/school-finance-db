<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\View;                  // ★ اضافه شد
use App\Http\View\Composers\SettingsComposer;        // ★ اضافه شد

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        // دستور کمکی برای تبدیل تاریخ میلادی به شمسی در ویوها
        Blade::directive('jalali', function ($expression) {
            return "<?php echo \App\Helpers\JalaliHelper::toJalali($expression); ?>";
        });

        // ★ تزریق خودکار نام و لوگوی مکتب به تمام viewها
        View::composer('*', SettingsComposer::class);
    }
}
