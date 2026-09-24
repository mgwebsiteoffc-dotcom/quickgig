<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('portfolio_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creator_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('slug')->nullable();
            $table->text('description')->nullable();
            $table->string('cover')->nullable();
            $table->string('video_url')->nullable();
            $table->string('external_url')->nullable();
            $table->string('category')->nullable();
            $table->json('tags')->nullable();
            $table->integer('views')->default(0);
            $table->integer('likes')->default(0);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_published')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
        // Seed each separately to avoid Column count mismatch on MySQL
        DB::table('portfolio_items')->insert(['creator_id'=>1,'title'=>'Founder Talking-Head — Hook that held 71% watch-time','slug'=>'founder-hook-71','description'=>null,'cover'=>'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=600&q=80','video_url'=>'https://www.youtube.com/watch?v=dQw4w9WgXcQ','external_url'=>null,'category'=>'Reel','tags'=>json_encode(['Talking-Head','Retention']),'views'=>12400,'likes'=>892,'is_featured'=>true,'is_published'=>true,'sort_order'=>0,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('portfolio_items')->insert(['creator_id'=>1,'title'=>'D2C Ad — 30 sec UGC with captions','slug'=>'d2c-ugc-30','description'=>null,'cover'=>'https://images.unsplash.com/photo-1492691527719-9d1e07e534b4?w=600&q=80','video_url'=>null,'external_url'=>null,'category'=>'AI','tags'=>json_encode(['AI','UGC']),'views'=>5400,'likes'=>0,'is_featured'=>false,'is_published'=>true,'sort_order'=>0,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('portfolio_items')->insert(['creator_id'=>4,'title'=>'Thumbnail A/B — +34% CTR lift for finance channel','slug'=>'thumb-ctr-34','description'=>null,'cover'=>'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=600&q=80','video_url'=>null,'external_url'=>null,'category'=>'Thumbnail','tags'=>json_encode(['Thumbnail','CTR']),'views'=>8900,'likes'=>412,'is_featured'=>true,'is_published'=>true,'sort_order'=>0,'created_at'=>now(),'updated_at'=>now()]);
    }
    public function down(): void { Schema::dropIfExists('portfolio_items'); }
};
