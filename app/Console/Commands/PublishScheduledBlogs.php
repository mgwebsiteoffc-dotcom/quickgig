<?php
namespace App\Console\Commands;
use Illuminate\Console\Command; use App\Models\Blog;
class PublishScheduledBlogs extends Command { protected $signature='content:publish-scheduled'; protected $description='Publish blog posts whose scheduled time has arrived'; public function handle(){ $n=Blog::where('is_published',false)->whereNotNull('publish_at')->where('publish_at','<=',now())->update(['is_published'=>true,'published_at'=>now()]); $this->info("Published {$n} post(s)."); return self::SUCCESS; } }
