<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

return new class extends Migration {
    public function up(): void
    {
        DB::table('settings')->updateOrInsert(['key' => 'site.name'], ['value' => 'GIG60', 'is_secret' => false, 'updated_at' => now(), 'created_at' => now()]);
        DB::table('settings')->updateOrInsert(['key' => 'site.title'], ['value' => 'GIG60 — solve creative work faster', 'is_secret' => false, 'updated_at' => now(), 'created_at' => now()]);
        DB::table('settings')->updateOrInsert(['key' => 'site.description'], ['value' => 'GIG60 turns business problems into clear briefs, verified specialist matches and accountable delivery.', 'is_secret' => false, 'updated_at' => now(), 'created_at' => now()]);
        Cache::forget('settings.all');
    }

    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['site.name', 'site.title', 'site.description'])->delete();
        Cache::forget('settings.all');
    }
};
