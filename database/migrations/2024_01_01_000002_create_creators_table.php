<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        Schema::create('creators', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('handle')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('phone')->nullable();
            $table->string('avatar')->nullable();
            $table->string('cover')->nullable();
            $table->text('bio')->nullable();
            $table->string('headline')->nullable();
            $table->json('skills')->nullable();
            $table->json('languages')->nullable();
            $table->string('location')->nullable();
            $table->integer('price_from')->default(1299);
            $table->decimal('rating',2,1)->default(4.9);
            $table->integer('reviews_count')->default(0);
            $table->integer('orders_count')->default(0);
            $table->integer('response_minutes')->default(6);
            $table->integer('on_time_rate')->default(97);
            $table->integer('repeat_rate')->default(42);
            $table->boolean('is_available')->default(true);
            $table->boolean('is_verified')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->string('upi_id')->nullable();
            $table->string('portfolio_url')->nullable();
            $table->string('instagram')->nullable();
            $table->string('youtube')->nullable();
            $table->string('seo_title')->nullable();
            $table->text('seo_description')->nullable();
            $table->timestamps();
        });
        // Insert each row separately — avoids Column count mismatch on MySQL when rows have different keys
        DB::table('creators')->insert(['name'=>'Priya Sharma','handle'=>'@priyaedits','email'=>'priya@quickcontent.in','phone'=>'98765 00001','avatar'=>'https://i.pravatar.cc/150?img=5','bio'=>'Talking-head & retention editor for D2C founders. 4.9★ across 1,200 reels. I obsess over hooks & captions that hold watch-time.','headline'=>'Talking-Head • Retention • For @devtalksbusiness','skills'=>json_encode(['Talking-Head','Retention','Captions','Hook Writing']),'languages'=>json_encode(['Hindi','English']),'location'=>'Delhi, IN','price_from'=>1299,'rating'=>4.9,'reviews_count'=>1243,'orders_count'=>1200,'is_available'=>true,'is_verified'=>true,'is_featured'=>true,'upi_id'=>'priya@upi','instagram'=>'https://instagram.com/priyaedits','created_at'=>now(),'updated_at'=>now()]);
        DB::table('creators')->insert(['name'=>'Rahul Verma','handle'=>'@rahulcuts','email'=>'rahul@quickcontent.in','phone'=>null,'avatar'=>'https://i.pravatar.cc/150?img=12','bio'=>'Retention specialist — I cut for 50%+ avg watch-time. Fast, clean, caption-perfect.','headline'=>'Retention • Captions • For @priyanksingh','skills'=>json_encode(['Retention','Jump Cuts','Captions']),'languages'=>null,'location'=>'Noida, IN','price_from'=>1299,'rating'=>4.9,'reviews_count'=>892,'orders_count'=>980,'is_available'=>true,'is_verified'=>true,'is_featured'=>true,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('creators')->insert(['name'=>'Aman Khan','handle'=>'@amanmotion','email'=>'aman@quickcontent.in','phone'=>null,'avatar'=>'https://i.pravatar.cc/150?img=15','bio'=>'Motion + AI. I blend After Effects with Veo 3 / Midjourney for ads that stop thumbs.','headline'=>'Motion + AI • For @fullstackmodiji','skills'=>json_encode(['Motion','AI UGC','Veo 3']),'languages'=>null,'location'=>'Gurugram, IN','price_from'=>2799,'rating'=>4.8,'reviews_count'=>412,'orders_count'=>760,'is_available'=>false,'is_verified'=>true,'is_featured'=>false,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('creators')->insert(['name'=>'Neha Jain','handle'=>'@nehacreates','email'=>'neha@quickcontent.in','phone'=>null,'avatar'=>'https://i.pravatar.cc/150?img=9','bio'=>'Thumbnail CTR obsessive. 2,100 thumbs, avg +34% CTR lift in tests.','headline'=>'Thumbnail CTR • 2,100 delivered','skills'=>json_encode(['Thumbnails','CTR Testing','Design']),'languages'=>null,'location'=>'Jaipur, IN','price_from'=>999,'rating'=>5.0,'reviews_count'=>2100,'orders_count'=>2100,'is_available'=>true,'is_verified'=>true,'is_featured'=>true,'created_at'=>now(),'updated_at'=>now()]);
        DB::table('creators')->insert(['name'=>'Sahil Dua','handle'=>'@sahilai','email'=>'sahil@quickcontent.in','phone'=>null,'avatar'=>'https://i.pravatar.cc/150?img=68','bio'=>'AI UGC & Veo 3 ads. One prompt → ready-to-run ad with VO & captions.','headline'=>'AI UGC • Veo 3 • For D2C Ads','skills'=>json_encode(['AI UGC','Veo 3','Script']),'languages'=>null,'location'=>'Bengaluru, IN','price_from'=>6499,'rating'=>4.7,'reviews_count'=>210,'orders_count'=>440,'is_available'=>true,'is_verified'=>false,'is_featured'=>false,'created_at'=>now(),'updated_at'=>now()]);
    }
    public function down(): void { Schema::dropIfExists('creators'); }
};
