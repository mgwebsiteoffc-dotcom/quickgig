<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            if (! Schema::hasColumn('companies', 'plan_tier'))       $table->string('plan_tier')->default('payg');   // payg, starter, growth, scale
            if (! Schema::hasColumn('companies', 'monthly_credits')) $table->integer('monthly_credits')->default(0); // tasks included per month
            if (! Schema::hasColumn('companies', 'credits_used'))    $table->integer('credits_used')->default(0);
            if (! Schema::hasColumn('companies', 'seats'))           $table->integer('seats')->default(1);
            if (! Schema::hasColumn('companies', 'renews_on'))       $table->date('renews_on')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn(['plan_tier', 'monthly_credits', 'credits_used', 'seats', 'renews_on']);
        });
    }
};
