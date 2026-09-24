<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained('creators')->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('cover')->nullable(); // URL or storage path
            $table->integer('price'); // 2499 ; 0 = barter
            $table->integer('compare_price')->nullable(); // strikethrough mrp
            $table->integer('mrp')->nullable(); // legacy alias for compare_price
            $table->integer('delivery_days')->default(1);
            $table->string('category')->default('Reel'); // Reel, Thumbnail, AI Video, UGC Video, Barter Collab, Bundle
            $table->string('badge')->nullable(); // Best seller, UGC, Barter
            $table->integer('sold_count')->default(0);
            $table->decimal('rating',2,1)->default(4.9);
            $table->timestamps();
        });

        // Seed base services (paid) — FK-safe, after creators seeded in previous migration
        // Hostinger: runs once; safe to re-run check by slug
        try {
            $priya = DB::table('creators')->where('handle','@priyaedits')->first();
            $rahul = DB::table('creators')->where('handle','@rahulcuts')->first();
            $sahil = DB::table('creators')->where('handle','@sahilai')->first();
            $neha  = DB::table('creators')->where('handle','@nehacreates')->first();
            $now = now();
            $seeds = [];
            if ($priya && DB::table('services')->where('slug','talking-head-reel')->doesntExist()) {
                $seeds[] = ['creator_id'=>$priya->id,'title'=>'Engaging Talking-Head Reel','slug'=>'talking-head-reel','description'=>'Hook in 2s + captions burned-in + 1-day delivery. For D2C founders.','cover'=>'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=600&q=80','price'=>1299,'compare_price'=>1999,'mrp'=>1999,'delivery_days'=>1,'category'=>'Reel','badge'=>'Best seller','sold_count'=>1200,'rating'=>4.9,'created_at'=>$now,'updated_at'=>$now];
            }
            if ($rahul && DB::table('services')->where('slug','retention-reel')->doesntExist()) {
                $seeds[] = ['creator_id'=>$rahul->id,'title'=>'Retention Reel — 60%+ watch-time cut','slug'=>'retention-reel','description'=>'Jump cuts every 1.8s, B-roll on every claim. Avg 61% watch-time.','cover'=>'https://images.unsplash.com/photo-1536243287037-7f1444775910?w=600&q=80','price'=>2499,'compare_price'=>3299,'delivery_days'=>1,'category'=>'Reel','badge'=>'Popular','sold_count'=>980,'rating'=>4.9,'created_at'=>$now,'updated_at'=>$now];
            }
            if ($neha && DB::table('services')->where('slug','high-ctr-thumbnail')->doesntExist()) {
                $seeds[] = ['creator_id'=>$neha->id,'title'=>'High CTR Thumbnail + 3 variants','slug'=>'high-ctr-thumbnail','description'=>'Face + 3 words + contrast border. Ships 3 variants for A/B.','cover'=>'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=600&q=80','price'=>999,'compare_price'=>1499,'delivery_days'=>1,'category'=>'Thumbnail','badge'=>'Thumbnail','sold_count'=>2100,'rating'=>5.0,'created_at'=>$now,'updated_at'=>$now];
            }
            if ($sahil && DB::table('services')->where('slug','ai-ugc-ad-veo3')->doesntExist()) {
                $seeds[] = ['creator_id'=>$sahil->id,'title'=>'AI UGC Ad — Veo 3 (30s)','slug'=>'ai-ugc-ad-veo3','description'=>'One prompt → UGC ad with VO, captions, music. 9:16 + 1:1.','cover'=>'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=600&q=80','price'=>6499,'compare_price'=>null,'mrp'=>null,'delivery_days'=>2,'category'=>'AI Video','badge'=>'AI','sold_count'=>420,'rating'=>4.7,'created_at'=>$now,'updated_at'=>$now];
            }
            if ($priya && DB::table('services')->where('slug','bundle-reel-thumb-captions')->doesntExist()) {
                $seeds[] = ['creator_id'=>$priya->id,'title'=>'Bundle — Reel + Thumbnail + Captions','slug'=>'bundle-reel-thumb-captions','description'=>'Micro-team delivers reel + thumb + SRT in 2 days. Best value.','cover'=>'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?w=600&q=80','price'=>8999,'compare_price'=>11999,'mrp'=>11999,'delivery_days'=>2,'category'=>'Bundle','badge'=>'Best Value','sold_count'=>310,'rating'=>4.8,'created_at'=>$now,'updated_at'=>$now];
            }
            // Insert one-by-one to avoid Column count mismatch when rows have slightly different keys
            foreach ($seeds as $row) { DB::table('services')->insert($row); }
        } catch (\Throwable $e) {
            // Fresh install without creators yet — seeded later by add_dynamic migration if needed
        }
    }
    public function down(): void { Schema::dropIfExists('services'); }
};
