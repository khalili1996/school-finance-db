<?php

namespace App\Http\Controllers\School;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Response;

class BackupController extends Controller
{
    /**
     * صفحهٔ اصلی پشتیبان‌گیری
     */
    public function index()
    {
        return view('school.backup.index');
    }

    /**
     * اجرای دستور بکاپ و دانلود آخرین فایل
     */
    public function download()
    {
        try {
            // ۱. اجرای دستور بکاپ (فایل جدید ساخته می‌شود)
            Artisan::call('backup:database');
        } catch (\Exception $e) {
            return back()->with('error', 'خطا در اجرای بکاپ: ' . $e->getMessage());
        }

        // ۲. یافتن آخرین فایل در پوشهٔ backups
        $backupPath = storage_path('app/backups');

        if (!File::exists($backupPath)) {
            return back()->with('error', 'پوشهٔ بکاپ وجود ندارد.');
        }

        $files = File::files($backupPath);

        if (empty($files)) {
            return back()->with('error', 'بکاپ‌گیری انجام نشد. فایلی یافت نشد.');
        }

        // مرتب‌سازی بر اساس زمان تغییر (جدیدترین اول)
        usort($files, function ($a, $b) {
            return $b->getMTime() <=> $a->getMTime();
        });

        $latestFile = $files[0];

        // ۳. دانلود فایل
        return response()->download($latestFile->getPathname());
    }
}
