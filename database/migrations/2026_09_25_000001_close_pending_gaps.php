<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('users', function(Blueprint $t){ $t->string('two_factor_secret')->nullable(); $t->timestamp('two_factor_confirmed_at')->nullable(); });
  Schema::table('orders', function(Blueprint $t){ $t->text('delivery_url')->nullable(); $t->string('delivery_path')->nullable(); $t->unsignedInteger('delivery_version')->default(0); $t->string('currency',3)->default('INR'); $t->decimal('tax_amount',12,2)->default(0); });
  Schema::table('blogs', function(Blueprint $t){ $t->timestamp('publish_at')->nullable()->index(); });
  Schema::create('audit_logs', function(Blueprint $t){ $t->id(); $t->foreignId('user_id')->nullable()->nullOnDelete(); $t->string('action'); $t->string('route')->nullable(); $t->string('ip',45)->nullable(); $t->json('metadata')->nullable(); $t->timestamps(); $t->index(['action','created_at']); });
 }
 public function down(): void { Schema::dropIfExists('audit_logs'); Schema::table('blogs',fn(Blueprint $t)=>$t->dropColumn('publish_at')); Schema::table('orders',fn(Blueprint $t)=>$t->dropColumn(['delivery_url','delivery_path','delivery_version','currency','tax_amount'])); Schema::table('users',fn(Blueprint $t)=>$t->dropColumn(['two_factor_secret','two_factor_confirmed_at'])); }
};
