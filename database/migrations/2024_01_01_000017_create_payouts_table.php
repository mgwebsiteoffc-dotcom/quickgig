<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->string('uid')->unique();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();

            $table->integer('gross');                       // what the buyer paid
            $table->integer('fee');                         // platform cut
            $table->integer('amount');                      // what the freelancer receives
            $table->string('status')->default('pending');   // pending, on_hold, processing, paid, failed
            $table->string('method')->default('upi');       // upi, bank, manual
            $table->string('destination')->nullable();      // upi id / masked account
            $table->string('provider')->default('manual');  // manual, razorpayx
            $table->string('reference')->nullable();        // UTR or provider payout id
            $table->text('notes')->nullable();
            $table->timestamp('available_at')->nullable();  // when the hold period ends
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'available_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
    }
};
