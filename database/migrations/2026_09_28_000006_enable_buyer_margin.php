<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration {
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(
            ['key' => 'platform.fee_percent'],
            ['value' => '10', 'is_secret' => false, 'updated_at' => now(), 'created_at' => now()]
        );
        Cache::forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->where('key', 'platform.fee_percent')->update([
            'value' => '0',
            'updated_at' => now(),
        ]);
    }
};
