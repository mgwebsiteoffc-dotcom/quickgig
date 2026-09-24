<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void {
        Schema::table('services', function (Blueprint $table) {
            // Hostinger + SQLite safe: no after(), check existence
            if (!Schema::hasColumn('services','profile_type')) {
                $table->string('profile_type')->nullable(); // video_editor, ugc_creator, influencer, designer, hybrid, any
            }
            if (!Schema::hasColumn('services','price_type')) {
                $table->string('price_type')->default('paid'); // paid, barter, hybrid
            }
            if (!Schema::hasColumn('services','barter_value')) {
                $table->integer('barter_value')->nullable(); // product value for barter
            }
            if (!Schema::hasColumn('services','is_active')) {
                $table->boolean('is_active')->default(true);
            }
            if (!Schema::hasColumn('services','is_barter')) {
                $table->boolean('is_barter')->default(false);
            }
            if (!Schema::hasColumn('services','deliverables')) {
                $table->json('deliverables')->nullable(); // ["Reel 30s","SRT","Thumbnail"]
            }
            if (!Schema::hasColumn('services','revision_count')) {
                $table->integer('revision_count')->default(2);
            }
            if (!Schema::hasColumn('services','collab_terms')) {
                $table->string('collab_terms')->nullable(); // for barter terms
            }
            if (!Schema::hasColumn('services','compare_price')) {
                $table->integer('compare_price')->nullable();
            }
        });

        // Backfill + seed UGC & Barter — idempotent
        try {
            DB::table('services')->whereNull('profile_type')->update(['profile_type'=>'video_editor']);
            // Ensure existing have price_type
            DB::table('services')->whereNull('price_type')->update(['price_type'=>'paid','is_active'=>true]);
            // Map categories to profile_type if still generic
            DB::table('services')->where('category','AI Video')->where('profile_type','video_editor')->update(['profile_type'=>'ugc_creator']);
            DB::table('services')->where('category','Thumbnail')->where('profile_type','video_editor')->update(['profile_type'=>'designer']);

            if (DB::table('services')->where('slug','ugc-video-unboxing-30s')->doesntExist()) {
                $riya = DB::table('creators')->where('handle','@ugc_riya')->first();
                if ($riya) {
                    DB::table('services')->insert([
                        'creator_id'=>$riya->id,'title'=>'UGC Video — Unboxing + Testimonial (30s)','slug'=>'ugc-video-unboxing-30s','description'=>'Natural UGC: unboxing, first impression, B-roll of product in use. Delivered as 9:16 + 1:1. 1-day delivery.','cover'=>'https://images.unsplash.com/photo-1526948128573-703ee1aeb6fa?w=600&q=80','price'=>1999,'compare_price'=>2499,'mrp'=>2499,'delivery_days'=>1,'category'=>'UGC Video','profile_type'=>'ugc_creator','price_type'=>'paid','is_active'=>true,'is_barter'=>false,'deliverables'=>json_encode(['30s UGC Reel','SRT','1:1 cut']),'revision_count'=>2,'badge'=>'UGC','sold_count'=>340,'rating'=>4.8,'created_at'=>now(),'updated_at'=>now()
                    ]);
                }
            }
            if (DB::table('services')->where('slug','barter-collab-reel-30s')->doesntExist()) {
                $aman = DB::table('creators')->where('handle','@barter_aman')->first();
                if ($aman) {
                    DB::table('services')->insert([
                        'creator_id'=>$aman->id,'title'=>'Barter Collab — Reel + Story (Product Exchange)','slug'=>'barter-collab-reel-30s','description'=>'Barter collab: creator creates 1 Reel (30s) + 1 Story in exchange for your product. No cash — product value ~₹2,000. Terms: product shipped first, content in 3 days.','cover'=>'https://images.unsplash.com/photo-1557838923-2985c318be48?w=600&q=80','price'=>0,'compare_price'=>0,'mrp'=>0,'barter_value'=>2000,'delivery_days'=>3,'category'=>'Barter Collab','profile_type'=>'influencer','price_type'=>'barter','is_active'=>true,'is_barter'=>true,'deliverables'=>json_encode(['Reel 30s','Story 15s','Tagged post']),'revision_count'=>1,'collab_terms'=>'Product shipped in 2 days. Creator delivers in 3 days after receiving.','badge'=>'Barter','sold_count'=>89,'rating'=>4.7,'created_at'=>now(),'updated_at'=>now()
                    ]);
                    DB::table('services')->insert([
                        'creator_id'=>$aman->id,'title'=>'Barter + Paid — Hybrid Collab (Reel)','slug'=>'barter-paid-hybrid-reel','description'=>'Hybrid: product + ₹999 for 1 UGC + 1 Reel. Best for high-value products.','cover'=>'https://images.unsplash.com/photo-1515377905703-c4788e51af15?w=600&q=80','price'=>999,'barter_value'=>1500,'compare_price'=>2499,'delivery_days'=>2,'category'=>'Barter Collab','profile_type'=>'influencer','price_type'=>'hybrid','is_active'=>true,'is_barter'=>false,'deliverables'=>json_encode(['UGC 30s','Reel 30s']),'revision_count'=>2,'badge'=>'Hybrid','sold_count'=>45,'rating'=>4.9,'created_at'=>now(),'updated_at'=>now()
                    ]);
                }
            }
        } catch (\Throwable $e) {}
    }
    public function down(): void {
        Schema::table('services', function (Blueprint $table) {
            foreach (['profile_type','price_type','barter_value','is_active','is_barter','deliverables','revision_count','collab_terms','compare_price'] as $col) {
                if (Schema::hasColumn('services',$col)) { try { $table->dropColumn($col); } catch (\Throwable $e) {} }
            }
        });
    }
};
