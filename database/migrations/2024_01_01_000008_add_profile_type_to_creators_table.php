<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void {
        // Add profile type & barter/UGC fields — MySQL/SQLite safe, no doctrine needed.
        // Use plain addColumn (no after()) for SQLite compatibility on local tests.
        Schema::table('creators', function (Blueprint $table) {
            if (!Schema::hasColumn('creators','profile_type')) {
                $table->string('profile_type')->default('video_editor'); // video_editor, ugc_creator, influencer, designer, hybrid
            }
            if (!Schema::hasColumn('creators','barter_available')) {
                $table->boolean('barter_available')->default(false);
            }
            if (!Schema::hasColumn('creators','barter_categories')) {
                $table->json('barter_categories')->nullable(); // ["Fashion","Tech"]
            }
            if (!Schema::hasColumn('creators','ugc_niches')) {
                $table->json('ugc_niches')->nullable(); // ["Beauty","Fitness"]
            }
            if (!Schema::hasColumn('creators','followers_count')) {
                $table->integer('followers_count')->default(0);
            }
            if (!Schema::hasColumn('creators','collab_type')) {
                $table->string('collab_type')->nullable(); // paid, barter, both
            }
            if (!Schema::hasColumn('creators','verification_notes')) {
                $table->text('verification_notes')->nullable();
            }
            if (!Schema::hasColumn('creators','verified_at')) {
                $table->timestamp('verified_at')->nullable();
            }
            if (!Schema::hasColumn('creators','rejection_reason')) {
                $table->string('rejection_reason')->nullable();
            }
        });

        // Backfill existing creators to proper profile types — idempotent
        try {
            DB::table('creators')->where('handle', '@priyaedits')->update(['profile_type'=>'video_editor','collab_type'=>'paid']);
            DB::table('creators')->where('handle', '@rahulcuts')->update(['profile_type'=>'video_editor','collab_type'=>'paid']);
            DB::table('creators')->where('handle', '@amanmotion')->update(['profile_type'=>'designer','collab_type'=>'paid']);
            DB::table('creators')->where('handle', '@nehacreates')->update(['profile_type'=>'designer','collab_type'=>'paid']);
            DB::table('creators')->where('handle', '@sahilai')->update(['profile_type'=>'ugc_creator','ugc_niches'=>json_encode(['AI','Tech']),'barter_available'=>true,'barter_categories'=>json_encode(['Tech','AI']),'followers_count'=>45000,'collab_type'=>'both']);
            if (DB::table('creators')->where('handle','@ugc_riya')->doesntExist()) {
                DB::table('creators')->insert([
                    'name'=>'Riya Malhotra','handle'=>'@ugc_riya','email'=>'riya.ugc@quickcontent.in','phone'=>'98765 00002','avatar'=>'https://i.pravatar.cc/150?img=26','bio'=>'UGC creator — 120+ D2C videos, natural unboxing + testimonial style. 85K followers. Barter + paid both.','headline'=>'UGC Video • 85K • Beauty & Lifestyle','profile_type'=>'ugc_creator','skills'=>json_encode(['UGC','Unboxing','Testimonial']),'languages'=>json_encode(['Hindi','English']),'location'=>'Mumbai, IN','price_from'=>1999,'rating'=>4.8,'reviews_count'=>342,'orders_count'=>120,'is_available'=>true,'is_verified'=>true,'is_featured'=>true,'barter_available'=>true,'barter_categories'=>json_encode(['Beauty','Lifestyle','Fashion']),'ugc_niches'=>json_encode(['Beauty','Lifestyle']),'followers_count'=>85000,'collab_type'=>'both','verified_at'=>now(),'created_at'=>now(),'updated_at'=>now()
                ]);
            }
            if (DB::table('creators')->where('handle','@barter_aman')->doesntExist()) {
                DB::table('creators')->insert([
                    'name'=>'Aman Influencer','handle'=>'@barter_aman','email'=>'aman.barter@quickcontent.in','phone'=>'98765 00003','avatar'=>'https://i.pravatar.cc/150?img=18','bio'=>'Micro-influencer — 45K engaged followers in tech & gadgets. Open to barter collabs for gadgets + paid.','headline'=>'Barter Collab • 45K • Tech & Gadgets','profile_type'=>'influencer','skills'=>json_encode(['Reels','Reviews','Unboxing']),'languages'=>json_encode(['Hindi','English']),'location'=>'Delhi, IN','price_from'=>0,'rating'=>4.7,'reviews_count'=>210,'orders_count'=>89,'is_available'=>true,'is_verified'=>false,'barter_available'=>true,'barter_categories'=>json_encode(['Tech','Gadgets']),'followers_count'=>45000,'collab_type'=>'barter','created_at'=>now(),'updated_at'=>now()
                ]);
            }
        } catch (\Throwable $e) {}
    }
    public function down(): void {
        Schema::table('creators', function (Blueprint $table) {
            $cols = ['profile_type','barter_available','barter_categories','ugc_niches','followers_count','collab_type','verification_notes','verified_at','rejection_reason'];
            foreach ($cols as $c) { if (Schema::hasColumn('creators',$c)) { try { $table->dropColumn($c); } catch (\Throwable $e) {} } }
        });
    }
};
