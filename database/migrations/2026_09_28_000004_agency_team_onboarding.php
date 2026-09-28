<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void { Schema::table('creators', function(Blueprint $t){ $t->string('account_kind')->default('individual')->index(); $t->string('agency_name')->nullable(); $t->unsignedInteger('team_size')->nullable(); $t->json('team_services')->nullable(); $t->text('team_description')->nullable(); }); } public function down(): void { Schema::table('creators',fn(Blueprint $t)=>$t->dropColumn(['account_kind','agency_name','team_size','team_services','team_description'])); } };
