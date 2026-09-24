<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('faqs', function (Blueprint $table) {
            $table->id();
            $table->string('question');
            $table->text('answer');
            $table->string('category')->default('General');
            $table->integer('sort_order')->default(0);
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('slug')->nullable()->unique();
            $table->timestamps();
        });
        // Insert rows separately / or uniform keys — each row has both is_published and is_featured to avoid mismatch
        DB::table('faqs')->insert(['question'=>'How is QuickContent different from Unjob.ai, Fiverr or Upwork?','answer'=>'Unjob is instant-assign only with no choice. Fiverr takes 20% + bidding chaos, Upwork is 50+ proposals & pay-to-bid. QuickContent gives you 3 paths (Instant 12 min / Choose Pro Top 3 / Prompt-a-Team), live tracking + chat, easy like ordering food, and Razorpay escrow — pay only when you Approve. All creators are ID + portfolio verified (blue tick) with live availability.','category'=>'General','sort_order'=>1,'is_published'=>true,'is_featured'=>true,'slug'=>'how-different-from-unjob-fiverr','created_at'=>now(),'updated_at'=>now()]);
        DB::table('faqs')->insert(['question'=>'How fast is delivery really?','answer'=>'Talking-head reels & thumbnails: next-day delivery is standard. Instant-assign orders are assigned in ~12 minutes. AI videos & team packs: 2 days. You get a countdown + live updates like ordering food. 1-day delivery is included, not an upsell.','category'=>'Delivery','sort_order'=>2,'is_published'=>true,'is_featured'=>true,'slug'=>'how-fast-delivery','created_at'=>now(),'updated_at'=>now()]);
        DB::table('faqs')->insert(['question'=>'What if I don’t like the work?','answer'=>'Every order includes 2 free revisions. Payment is held in Razorpay escrow — we release to the creator only when you click Approve. 100% work guarantee or we re-assign free.','category'=>'Pricing','sort_order'=>3,'is_published'=>true,'is_featured'=>true,'slug'=>'what-if-not-like','created_at'=>now(),'updated_at'=>now()]);
        DB::table('faqs')->insert(['question'=>'Who are the creators? Are they verified?','answer'=>'All pros are ID-verified, portfolio-checked and rated by real companies (Avante, BrandScale etc). Blue tick = verified. You see live availability (green dot) before you book, plus avg reply time and on-time rate.','category'=>'Creators','sort_order'=>4,'is_published'=>true,'is_featured'=>true,'slug'=>'who-are-creators','created_at'=>now(),'updated_at'=>now()]);
        DB::table('faqs')->insert(['question'=>'Can I run this on Hostinger shared hosting?','answer'=>'Yes. Built for Hostinger shared: no Redis, no Node, no Supervisor needed. File cache, database queue (cron), Tailwind via CDN. Works on Hostinger Single/Premium/Business. Cron: queue:work --stop-when-empty every minute.','category'=>'General','sort_order'=>5,'is_published'=>true,'is_featured'=>true,'slug'=>'hostinger-shared','created_at'=>now(),'updated_at'=>now()]);
        DB::table('faqs')->insert(['question'=>'What does it cost?','answer'=>'Flat, upfront pricing: Reels from ₹1,299, Thumbnails from ₹1,299, AI Ads from ₹6,499. Platform fee 5%. No bidding, no Connects, no hidden 20%. Creators keep 90% — paid weekly via UPI after escrow release.','category'=>'Pricing','sort_order'=>6,'is_published'=>true,'is_featured'=>true,'slug'=>'what-does-it-cost','created_at'=>now(),'updated_at'=>now()]);
        DB::table('faqs')->insert(['question'=>'Do you support GST invoices?','answer'=>'Yes. Companies add GSTIN in Business Profile → every order generates a GST-compliant invoice. Creators receive payout statements for accounting.','category'=>'Pricing','sort_order'=>7,'is_published'=>true,'is_featured'=>false,'slug'=>'gst-invoices','created_at'=>now(),'updated_at'=>now()]);
    }
    public function down(): void { Schema::dropIfExists('faqs'); }
};
