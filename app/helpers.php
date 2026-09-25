<?php

use App\Models\Setting;

if (! function_exists('setting')) {
    /** Read a DB setting, falling back to config()/env() so .env-only installs keep working. */
    function setting(string $key, mixed $default = null): mixed
    {
        try {
            return Setting::get($key, $default);
        } catch (\Throwable) {
            return $default;   // settings table not migrated yet
        }
    }
}
