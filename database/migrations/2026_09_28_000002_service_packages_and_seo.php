<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('services', function(Blueprint $t){
   $t->string('billing_type')->default('one_time')->index();
   $t->integer('monthly_price')->nullable();
   $t->text('scope')->nullable();
   $t->text('process')->nullable();
   $t->json('faq_json')->nullable();
   $t->string('seo_title')->nullable();
   $t->string('seo_description', 165)->nullable();
   $t->text('aeo_answer')->nullable();
  });
  $common=['scope'=>'Clear scope agreed before payment. Reporting and access requirements are confirmed during onboarding.','process'=>'Kickoff → account and tracking review → implementation → review/report.','faq_json'=>json_encode([['q'=>'Is this a one-time service or ongoing?','a'=>'The package billing shown above is the commitment. Ongoing packages renew monthly only when selected.'],['q'=>'What do I need to provide?','a'=>'Business goals, access to the relevant ad or store account, brand assets and landing-page details.']])];
  DB::table('services')->whereIn('slug',['google-ads-account-setup-first-campaign','meta-ads-campaign-management-30-days'])->each(function($s) use($common){
   if ($s->slug==='google-ads-account-setup-first-campaign') {
    DB::table('services')->where('id',$s->id)->update(array_merge($common,['billing_type'=>'one_time','description'=>'One-time Google Ads setup for a conversion-ready search campaign. Includes conversion tracking review, keyword research, campaign and ad-group structure, negative keyword plan, ad copy direction and a launch-ready optimisation plan. Media spend is paid directly to Google and is not included.','deliverables'=>json_encode(['Conversion tracking and tag review','Keyword and search-intent research','Campaign, ad-group and negative-keyword structure','Launch checklist and optimisation plan']),'revision_count'=>0,'seo_title'=>'Google Ads Setup Service | Conversion Tracking and Search Campaigns','seo_description'=>'One-time Google Ads setup with conversion tracking review, keyword research, campaign structure and a launch-ready optimisation plan.','aeo_answer'=>'Quick GIGS Google Ads setup is a one-time service for businesses that need tracking reviewed, keywords researched and a search campaign structured before launch. Google media spend is separate.']));
   } else {
    DB::table('services')->where('id',$s->id)->update(array_merge($common,['billing_type'=>'monthly','monthly_price'=>14999,'description'=>'Monthly Meta Ads management for testing and improving paid social performance. We review tracking, build audiences, plan creative tests, monitor results and share a clear performance report. Ad spend is paid directly to Meta and is not included.','deliverables'=>json_encode(['Pixel and event tracking review','Audience and campaign setup','Creative testing plan and optimisation','Weekly monitoring with monthly performance report']),'revision_count'=>0,'seo_title'=>'Meta Ads Management Service | Monthly Performance Marketing','seo_description'=>'Monthly Meta Ads management with tracking review, audience setup, creative testing, optimisation and performance reporting. Ad spend is separate.','aeo_answer'=>'Quick GIGS Meta Ads management is a monthly performance marketing service covering tracking review, audiences, creative testing, optimisation and reporting. Meta ad spend is separate.']));
   }
  });
  DB::table('services')->whereIn('category',['Performance Marketing','Google Ads','Meta Ads','AI Automation','WhatsApp Automation','Shopify Operations'])->whereNull('scope')->update($common);
 }
 public function down(): void { Schema::table('services', function(Blueprint $t){$t->dropColumn(['billing_type','monthly_price','scope','process','faq_json','seo_title','seo_description','aeo_answer']);}); }
};
