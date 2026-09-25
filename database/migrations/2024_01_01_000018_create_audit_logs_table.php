<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name')->nullable();          // kept even if the user is deleted
            $table->string('role', 40)->nullable();
            $table->string('method', 10);
            $table->string('route')->nullable();         // route name, e.g. admin.payouts.paid
            $table->string('path');
            $table->json('payload')->nullable();         // scrubbed of secrets
            $table->unsignedSmallInteger('status')->nullable();
            $table->string('ip', 45)->nullable();
            $table->timestamps();

            $table->index(['user_id', 'created_at']);
            $table->index('route');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('audit_logs');
    }
};
