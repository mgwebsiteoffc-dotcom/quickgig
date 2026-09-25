<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        DB::table('settings')->insert([
            ['key'=>'platform_fee','value'=>'5','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'creator_fee','value'=>'10','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'escrow_hours','value'=>'48','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'support_email','value'=>'support@example.com','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'support_phone','value'=>'','created_at'=>now(),'updated_at'=>now()],
            ['key'=>'maintenance','value'=>'0','created_at'=>now(),'updated_at'=>now()],
        ]);

        Schema::create('payouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->integer('amount');                       // paise-free rupees, matching orders.total
            $table->string('upi_id')->nullable();
            $table->string('status')->default('hold');       // hold | ready | processing | paid | failed
            $table->timestamp('hold_until')->nullable();
            $table->timestamp('paid_at')->nullable();
            // RazorpayX reconciliation
            $table->string('reference')->nullable()->unique();   // our idempotency key
            $table->string('payout_id')->nullable()->index();    // RazorpayX payout id
            $table->string('utr')->nullable();
            $table->text('failure_reason')->nullable();
            $table->unsignedSmallInteger('poll_attempts')->default(0);
            $table->timestamps();

            $table->index(['status','hold_until']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payouts');
        Schema::dropIfExists('settings');
    }
};
