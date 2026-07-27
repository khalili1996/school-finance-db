<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Helpers\JalaliHelper;

class DateController extends Controller
{
    /**
     * تبدیل تاریخ شمسی ↔ میلادی
     */
    public function convert(Request $request)
    {
        $request->validate([
            'date'   => 'required|string',
            'target' => 'required|in:jalali,gregorian',
        ]);

        try {
            if ($request->target === 'jalali') {
                // ورودی میلادی → خروجی شمسی
                $result = JalaliHelper::toJalali($request->date);
            } else {
                // ورودی شمسی → خروجی میلادی
                $result = JalaliHelper::toGregorian($request->date)->format('Y-m-d');
            }
            return response()->json(['date' => $result]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'فرمت تاریخ نامعتبر است'], 422);
        }
    }

    /**
     * دریافت تاریخ امروز با تقویم دلخواه
     */
    public function today(Request $request)
{
    $target = $request->input('target', 'jalali');   // input() به‌جای get()
    if ($target === 'jalali') {
        return response()->json(['date' => JalaliHelper::todayJalali()]);
    } else {
        return response()->json(['date' => date('Y-m-d')]);
    }
}
}
