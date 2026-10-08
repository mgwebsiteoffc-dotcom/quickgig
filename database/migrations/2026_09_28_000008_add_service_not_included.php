<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('services', function (Blueprint $table) {
            $table->text('not_included')->nullable()->after('scope');
        });
    }
    public function down(): void
    {
        Schema::table('services', fn (Blueprint $table) => $table->dropColumn('not_included'));
    }
};
