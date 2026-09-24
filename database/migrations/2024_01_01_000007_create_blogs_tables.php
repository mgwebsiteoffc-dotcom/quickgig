<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

return new class extends Migration {
    public function up(): void {
        Schema::create('blog_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('color')->default('#2563EB');
            $table->timestamps();
        });
        DB::table('blog_categories')->insert([
            ['name'=>'Reels & Editing','slug'=>'reels-editing','description'=>'Hooks, retention, captions','color'=>'#2563EB','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Thumbnails & CTR','slug'=>'thumbnails-ctr','description'=>'CTR tests, design','color'=>'#0E8A4B','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'AI Video','slug'=>'ai-video','description'=>'Veo 3, UGC, automation','color'=>'#7C3AED','created_at'=>now(),'updated_at'=>now()],
            ['name'=>'Growth Stories','slug'=>'growth-stories','description'=>'How teams ship faster','color'=>'#D97706','created_at'=>now(),'updated_at'=>now()],
        ]);

        Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('author_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('category_id')->nullable()->constrained('blog_categories')->nullOnDelete();
            $table->string('title');
            $table->string('slug')->unique();
            $table->string('excerpt', 360)->nullable();
            $table->longText('content'); // HTML from Quill editor
            $table->string('cover')->nullable(); // image path
            $table->string('cover_alt')->nullable();
            $table->json('tags')->nullable();
            $table->string('meta_title', 70)->nullable();
            $table->string('meta_description', 165)->nullable();
            $table->string('og_image')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('is_published')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->timestamp('published_at')->nullable();
            $table->integer('views')->default(0);
            $table->integer('reading_minutes')->default(4);
            $table->json('faq_json')->nullable(); // per-post FAQs for AEO
            $table->timestamps();
        });

        // Seed 3 blogs with SEO + AEO ready content (author is linked later by the seeder)
        $authorId = DB::table('users')->min('id');
        DB::table('blogs')->insert([
            [
                'author_id'=>$authorId,'category_id'=>1,'title'=>'How Avante Studio Ships a Talking-Head Reel in 1 Day (Hook → Captions → Delivery)','slug'=>'avante-studio-1-day-reel-process','excerpt'=>'Steal the exact 5-step checklist Priya uses to deliver 1-day reels with 71% hold rate — from hook to captions to approval.','content'=>'<h2>Why 1-day reels work</h2><p>Avante Studio ships 42 reels/month without a full-time editor. Their secret: <strong>4-min matching + live tracker + 2 free revisions</strong>. Here is the exact SOP Priya Sharma (@priyaedits) follows on Quick GIGS.</p><h3>1) Hook first (0-2s)</h3><p>We write 3 hook variants before editing. The winner is the one you can read out loud in 2 seconds. Test: if you can’t remember it after hearing once, rewrite.</p><h3>2) Retention cuts</h3><p>Jump cuts every 1.8–2.4s, B-roll on every claim, captions burned-in with 1.2 line height. Avg watch-time lifts from 38% → 61% in our tests.</p><h3>3) Captions that hold</h3><p>Use 2 words per frame max, yellow for keyword, white for rest. SRT included — you get both burned-in + file.</p><h3>4) Live tracker + chat</h3><p>Client sees “Working → Review” in a live tracker. No “we’ll update you”. Chat updates every 15 seconds inside the order page.</p><h3>5) Approve → escrow releases</h3><p>Money is held via Razorpay until you click Approve. Two free revisions included — we re-assign free if needed.</p><p><strong>Result:</strong> Avante cut editing lag from 3 days → 1 day, doubled output, 4.9★ avg.</p><p><em>Want this? Start at <a href="/business">/business</a> → Instant Assign → ₹1,299.</em></p>','cover'=>'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=1200&q=80','cover_alt'=>'Editor cutting talking-head reel','tags'=>json_encode(['Reels','Retention','Captions','SOP']),'meta_title'=>'1-Day Talking-Head Reel SOP — Avante Studio Case Study | Quick GIGS','meta_description'=>'Steal Avante Studio’s 1-day reel SOP: hook, cuts, captions, tracker, escrow. How Priya delivers 71% hold rate reels on Quick GIGS.','is_published'=>true,'is_featured'=>true,'published_at'=>now(),'views'=>3420,'reading_minutes'=>5,'created_at'=>now(),'updated_at'=>now()
            ],
            [
                'author_id'=>$authorId,'category_id'=>2,'title'=>'Thumbnail CTR: How Neha Lifts CTR +34% in 48 Hours (A/B Method)','slug'=>'thumbnail-ctr-34-percent-method','excerpt'=>'Neha’s 3-variant thumb system: face + 3 words + contrast border. Tested on 2,100 thumbs — here’s the exact method.','content'=>'<h2>The 3-variant rule</h2><p>Neha Jain (@nehacreates) never ships 1 thumb. She ships 3: <strong>Face Variant / Text Variant / Contrast Variant</strong>. Client A/Bs for 48h — winner stays.</p><h3>Face variant</h3><p>Big face, 30% of frame, looking at text. Eye-line drives click.</p><h3>Text variant</h3><p>3 words max, 120pt, stroke + shadow. No more than 3 words — test: can you read it at 1-inch size?</p><h3>Contrast border</h3><p>2px white stroke + 8% dark vignette. Makes thumb pop on white YouTube BG.</p><p><strong>Data:</strong> Across 214 tests, 3-variant lifts CTR +34% vs single thumb. Cost: same ₹1,299.</p>','cover'=>'https://images.unsplash.com/photo-1611224923853-80b023f02d71?w=1200&q=80','cover_alt'=>'Thumbnail CTR test grid','tags'=>json_encode(['Thumbnails','CTR','A/B Test']),'meta_title'=>'Thumbnail CTR +34% — Neha’s 3-Variant Method | Quick GIGS','meta_description'=>'How Neha Jain lifts CTR +34% with 3 thumbnail variants. Face + 3 words + contrast — tested on 2,100 thumbs. Template inside.','is_published'=>true,'is_featured'=>true,'published_at'=>now()->subDays(2),'views'=>2104,'reading_minutes'=>4,'created_at'=>now(),'updated_at'=>now()
            ],
            [
                'author_id'=>$authorId,'category_id'=>3,'title'=>'AI UGC Ads with Veo 3: One Prompt → Ready Ad (Script Inside)','slug'=>'ai-ugc-veo3-one-prompt-ad','excerpt'=>'Sahil’s Veo 3 prompt that turns one brief into UGC ad with VO, captions, and music — no studio needed.','content'=>'<h2>The Prompt</h2><pre style="background:#F8F8F7;border:1px solid #E8E8E6;padding:12px;border-radius:12px;white-space:pre-wrap">Create a 30-sec UGC ad for [product] — hook in 2s, demo at 0:08, social proof at 0:18, CTA at 0:26. Voice: Hindi+English mix, captions burned-in, 9:16, add upbeat music at -18db, export 1080x1920.</pre><p>Sahil (@sahilai) runs this on Quick GIGS’ Prompt-a-Team: editor + AI + VO in one order. Delivery 2 days, ₹6,499. Result: 420+ AI ads, avg 2.1x ROAS for D2C clients.</p><h3>Checklist before you publish</h3><ul><li>Hook text readable at 2x speed?</li><li>Captions 2 words/frame, yellow keyword?</li><li>CTA in last 4 seconds with voice + text?</li></ul>','cover'=>'https://images.unsplash.com/photo-1677442136019-21780ecad995?w=1200&q=80','cover_alt'=>'AI UGC video generation','tags'=>json_encode(['AI','Veo 3','UGC','Ads']),'meta_title'=>'AI UGC Ads with Veo 3 — One Prompt Template | Quick GIGS','meta_description'=>'Sahil’s Veo 3 prompt: one brief → 30-sec UGC ad with VO & captions. Template + checklist for D2C ads on Quick GIGS.','is_published'=>true,'is_featured'=>false,'published_at'=>now()->subDays(5),'views'=>1870,'reading_minutes'=>6,'created_at'=>now(),'updated_at'=>now()
            ],
        ]);
    }
    public function down(): void {
        Schema::dropIfExists('blogs');
        Schema::dropIfExists('blog_categories');
    }
};
