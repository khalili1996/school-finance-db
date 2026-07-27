<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['school_id', 'key', 'value'];

    public static function get($key, $default = null, $schoolId = null)
    {
        if (!$schoolId) {
            $schoolId = session('active_school_id', auth()->user()->school_id ?? null);
        }
        $setting = static::where('school_id', $schoolId)->where('key', $key)->first();
        return $setting ? $setting->value : $default;
    }

    public static function set($key, $value, $schoolId = null)
    {
        if (!$schoolId) {
            $schoolId = session('active_school_id', auth()->user()->school_id ?? null);
        }
        return static::updateOrCreate(
            ['school_id' => $schoolId, 'key' => $key],
            ['value' => $value]
        );
    }
}
