<?php

namespace App\Http\View\Composers;

use Illuminate\View\View;
use App\Models\Setting;

class SettingsComposer
{
    /**
     * تزریق خودکار نام و لوگوی مکتب به تمام ویوها
     */
    public function compose(View $view)
    {
        $schoolId = session('active_school_id', auth()->user()->school_id ?? null);

        if ($schoolId) {
            $view->with('schoolName', Setting::get('school_name', 'مکتب', $schoolId));
            $view->with('schoolLogo', Setting::get('logo', null, $schoolId));
        } else {
            $view->with('schoolName', 'مکتب');
            $view->with('schoolLogo', null);
        }
    }
}
