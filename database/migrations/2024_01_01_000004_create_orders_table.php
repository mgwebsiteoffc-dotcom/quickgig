<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique(); // UNJ-8841
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('service_id')->constrained()->cascadeOnDelete();
            $table->text('brief');
            $table->json('references')->nullable();
            $table->string('turnaround')->default('1 Day');
            $table->integer('subtotal');
            $table->integer('fee')->default(125);
            $table->integer('discount')->default(250);
            $table->integer('total');
            $table->string('status')->default('working'); // working, review, delivered
            $table->string('escrow_status')->default('held');
            $table->integer('progress')->default(25);
            $table->timestamp('due_at')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void { Schema::dropIfExists('orders'); }
};
