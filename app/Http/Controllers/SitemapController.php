<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Creator;
use App\Models\Service;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function index()
    {
        $urls = collect();

        // Static — includes beautiful onboarding (easy like ordering food)
        $statics = [
            ['loc'=>url('/'), 'changefreq'=>'daily','priority'=>'1.0'],
            ['loc'=>url('/onboarding/business'), 'changefreq'=>'weekly','priority'=>'0.9'],
            ['loc'=>url('/onboarding/creator'), 'changefreq'=>'weekly','priority'=>'0.9'],
            ['loc'=>url('/business'), 'changefreq'=>'daily','priority'=>'0.9'],
            ['loc'=>url('/creator'), 'changefreq'=>'weekly','priority'=>'0.7'],
            ['loc'=>url('/blog'), 'changefreq'=>'daily','priority'=>'0.8'],
        ];
        foreach($statics as $s) $urls->push(array_merge($s, ['lastmod'=>now()->toDateString()]));

        // Blogs
        try {
            Blog::published()->orderByDesc('updated_at')->each(function($b) use ($urls){
                $urls->push(['loc'=>url('/blog/'.$b->slug),'lastmod'=>$b->updated_at->toDateString(),'changefreq'=>'weekly','priority'=>'0.7']);
            });
            Creator::where('is_verified',true)->each(function($c) use ($urls){
                $urls->push(['loc'=>url('/creator/'.$c->id),'lastmod'=>$c->updated_at->toDateString(),'changefreq'=>'weekly','priority'=>'0.6']);
            });
        } catch(\Throwable $e) {}

        $xml = view('sitemap', compact('urls'))->render();
        return response($xml, 200)->header('Content-Type','application/xml');
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /orders\nSitemap: ".url('/sitemap.xml')."\n";
        return response($content, 200)->header('Content-Type','text/plain');
    }
}
