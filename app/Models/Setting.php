<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;

/**
 * Database-backed configuration the admin console can edit.
 *
 * Secrets (API keys) are encrypted at rest with the app key and are never sent
 * back to the browser in full — the settings screen shows a masked preview.
 * Values fall back to config()/env() so an .env-only deployment keeps working.
 */
class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = ['key', 'value', 'is_secret'];
    protected $casts = ['is_secret' => 'boolean'];

    private const CACHE_KEY = 'settings.all';

    public static function all($columns = ['*'])
    {
        return Cache::rememberForever(self::CACHE_KEY, fn () => static::query()->get()->keyBy('key'));
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        $row = static::all()->get($key);

        if (! $row || $row->value === null || $row->value === '') {
            return $default;
        }

        if (! $row->is_secret) {
            return $row->value;
        }

        try {
            return Crypt::decryptString($row->value);
        } catch (\Throwable) {
            return $default;   // key rotated — treat as unset rather than exploding
        }
    }

    public static function put(string $key, mixed $value, bool $secret = false): void
    {
        static::updateOrCreate(
            ['key' => $key],
            [
                'value'     => ($secret && filled($value)) ? Crypt::encryptString((string) $value) : $value,
                'is_secret' => $secret,
            ]
        );

        Cache::forget(self::CACHE_KEY);
    }

    public static function forget(string $key): void
    {
        static::where('key', $key)->delete();
        Cache::forget(self::CACHE_KEY);
    }

    public static function has(string $key): bool
    {
        return filled(static::get($key));
    }

    /** Masked preview for the admin screen: sk-or-…4f2a */
    public static function masked(string $key): ?string
    {
        $value = static::get($key);

        if (! filled($value)) return null;

        $value = (string) $value;

        return strlen($value) <= 10
            ? str_repeat('•', strlen($value))
            : substr($value, 0, 6) . str_repeat('•', 8) . substr($value, -4);
    }
}
