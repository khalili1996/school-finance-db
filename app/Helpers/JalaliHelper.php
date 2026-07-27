<?php

namespace App\Helpers;

use Morilog\Jalali\Jalalian;
use Carbon\Carbon;

class JalaliHelper
{
    /**
     * تبدیل تاریخ (میلادی یا جلالی) به رشته‌ی جلالی
     */
    public static function toJalali($date, $format = 'Y/m/d'): string
    {
        if (!$date) return '';

        // اگر تاریخ از نوع DateTimeInterface باشد
        if ($date instanceof \DateTimeInterface) {
            return Jalalian::fromCarbon(Carbon::instance($date))->format($format);
        }

        $cleaned = trim($date);

        // بررسی اینکه آیا ورودی خودش یک تاریخ جلالی معتبر است (سال ۱۳۰۰ تا ۱۵۰۰)
        if (preg_match('/^1[3-4]\d{2}[\/\-]\d{1,2}[\/\-]\d{1,2}$/', $cleaned)) {
            try {
                $parts = preg_split('/[\/\-]/', $cleaned);
                $parts[1] = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
                $parts[2] = str_pad($parts[2], 2, '0', STR_PAD_LEFT);
                return Jalalian::fromFormat('Y/m/d', implode('/', $parts))->format($format);
            } catch (\Exception $e) {
                return $cleaned;
            }
        }

        // در غیر این صورت فرض بر میلادی بودن
        try {
            return Jalalian::fromCarbon(Carbon::parse($cleaned))->format($format);
        } catch (\Exception $e) {
            return $cleaned;
        }
    }

    public static function toGregorian(string $jalaliDate): Carbon
{
    $date = str_replace('/', '-', trim($jalaliDate));
    $parts = explode('-', $date);

    // اگر تاریخ ناقص است، آن را تکمیل کن
    if (count($parts) === 1) {
        // فقط سال
        $parts[1] = '01';
        $parts[2] = '01';
    } elseif (count($parts) === 2) {
        // سال-ماه
        $parts[2] = '01';
    } elseif (count($parts) >= 3) {
        // کامل – فقط سه جزء اول
        $parts = array_slice($parts, 0, 3);
    } else {
        throw new \InvalidArgumentException("فرمت تاریخ نامعتبر است: $jalaliDate");
    }

    $parts[1] = str_pad($parts[1], 2, '0', STR_PAD_LEFT);
    $parts[2] = str_pad($parts[2], 2, '0', STR_PAD_LEFT);
    $date = implode('-', $parts);

    return Jalalian::fromFormat('Y-m-d', $date)->toCarbon();
}

    public static function todayJalali($format = 'Y/m/d'): string
    {
        return Jalalian::now()->format($format);
    }
}
