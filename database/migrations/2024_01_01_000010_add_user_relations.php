<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        // Adds FK constraints from users -> companies / creators after all tables exist.
        // Hostinger shared compatible: runs after companies, creators are created; safe to add.
        Schema::table('users', function (Blueprint $table) {
            // Add foreign keys if not already present (fresh install will not have them)
            // Use try/catch for shared hosting where FK names may already exist on rerun
            try {
                $table->foreign('company_id')->references('id')->on('companies')->nullOnDelete();
            } catch (\Throwable $e) {}
            try {
                $table->foreign('creator_id')->references('id')->on('creators')->nullOnDelete();
            } catch (\Throwable $e) {}
        });
    }
    public function down(): void {
        Schema::table('users', function (Blueprint $table) {
            try { $table->dropForeign(['company_id']); } catch (\Throwable $e) {}
            try { $table->dropForeign(['creator_id']); } catch (\Throwable $e) {}
        });
    }
};
