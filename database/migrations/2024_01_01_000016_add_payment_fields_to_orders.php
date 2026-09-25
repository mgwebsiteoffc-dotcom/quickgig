<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (! Schema::hasColumn('orders', 'payment_provider'))  $table->string('payment_provider')->nullable();   // razorpay, demo
            if (! Schema::hasColumn('orders', 'payment_order_id'))  $table->string('payment_order_id')->nullable();   // order_XXXX from the gateway
            if (! Schema::hasColumn('orders', 'payment_id'))        $table->string('payment_id')->nullable();         // pay_XXXX
            if (! Schema::hasColumn('orders', 'payment_status'))    $table->string('payment_status')->default('unpaid'); // unpaid, paid, refunded, failed
            if (! Schema::hasColumn('orders', 'paid_at'))           $table->timestamp('paid_at')->nullable();
            if (! Schema::hasColumn('orders', 'payout_reference'))  $table->string('payout_reference')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['payment_provider', 'payment_order_id', 'payment_id', 'payment_status', 'paid_at', 'payout_reference']);
        });
    }
};
