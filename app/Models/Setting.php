<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public const DEFAULTS = [
        'platform_fee'  => 5,
        'creator_fee'   => 10,
        'escrow_hours'  => 48,
        'support_email' => 'support@example.com',
        'support_phone' => '',
        'maintenance'   => false,
    ];

    public static function all_settings(): array
    {
        return Cache::rememberForever('settings.all', function () {
            $rows = static::query()->pluck('value', 'key')->all();
            return array_merge(self::DEFAULTS, $rows);
        });
    }

    public static function get(string $key, $default = null)
    {
        return self::all_settings()[$key] ?? $default ?? (self::DEFAULTS[$key] ?? null);
    }

    public static function put(string $key, $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => is_bool($value) ? (int) $value : $value]);
        Cache::forget('settings.all');
    }

    public static function putMany(array $pairs): void
    {
        foreach ($pairs as $k => $v) {
            static::updateOrCreate(['key' => $k], ['value' => is_bool($v) ? (int) $v : $v]);
        }
        Cache::forget('settings.all');
    }
}
