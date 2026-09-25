<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Creator;
use App\Models\Service;
use Illuminate\Support\Facades\Cache;

class SitemapController extends Controller
{
    public function index()
    {
        if ($cached = Cache::get('quickgig:sitemap')) {
            return response($cached, 200)->header('Content-Type', 'application/xml');
        }
        $urls = collect();

        $statics = [
            ['loc' => url('/'),            'changefreq' => 'daily',  'priority' => '1.0'],
            ['loc' => url('/marketplace'), 'changefreq' => 'daily',  'priority' => '0.9'],
            ['loc' => url('/register'),    'changefreq' => 'monthly','priority' => '0.8'],
            ['loc' => url('/login'),       'changefreq' => 'monthly','priority' => '0.4'],
            ['loc' => url('/blog'),          'changefreq' => 'daily',   'priority' => '0.8'],
            ['loc' => url('/how-it-works'),  'changefreq' => 'weekly',  'priority' => '0.9'],
            ['loc' => url('/ai-engine'),     'changefreq' => 'weekly',  'priority' => '0.9'],
            ['loc' => url('/brief-builder'), 'changefreq' => 'weekly',  'priority' => '0.9'],
            ['loc' => url('/pricing'),       'changefreq' => 'weekly',  'priority' => '0.9'],
            ['loc' => url('/compare'),       'changefreq' => 'monthly', 'priority' => '0.8'],
            ['loc' => url('/for-business'),  'changefreq' => 'weekly',  'priority' => '0.9'],
            ['loc' => url('/for-creators'),  'changefreq' => 'weekly',  'priority' => '0.8'],
            ['loc' => url('/enterprise'),    'changefreq' => 'monthly', 'priority' => '0.7'],
            ['loc' => url('/about'),         'changefreq' => 'monthly', 'priority' => '0.5'],
            ['loc' => url('/faq'),           'changefreq' => 'weekly',  'priority' => '0.7'],
            ['loc' => url('/contact'),       'changefreq' => 'monthly', 'priority' => '0.5'],
        ];

        foreach ($statics as $s) {
            $urls->push(array_merge($s, ['lastmod' => now()->toDateString()]));
        }

        try {
            Service::where('is_active', true)->get()->each(function ($s) use ($urls) {
                $urls->push([
                    'loc'        => url('/gigs/' . $s->id),
                    'lastmod'    => optional($s->updated_at)->toDateString() ?? now()->toDateString(),
                    'changefreq' => 'weekly',
                    'priority'   => '0.8',
                ]);
            });

            Blog::published()->get()->each(function ($b) use ($urls) {
                $urls->push([
                    'loc'        => url('/blog/' . $b->slug),
                    'lastmod'    => optional($b->updated_at)->toDateString() ?? now()->toDateString(),
                    'changefreq' => 'weekly',
                    'priority'   => '0.7',
                ]);
            });

            Creator::where('is_verified', true)->get()->each(function ($c) use ($urls) {
                $urls->push([
                    'loc'        => url('/creators/' . $c->id),
                    'lastmod'    => optional($c->updated_at)->toDateString() ?? now()->toDateString(),
                    'changefreq' => 'weekly',
                    'priority'   => '0.6',
                ]);
            });
        } catch (\Throwable $e) {
            // database not ready — static urls are still valid
        }

        $xml  = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

        foreach ($urls as $u) {
            $xml .= '  <url>' . PHP_EOL
                 .  '    <loc>' . e($u['loc']) . '</loc>' . PHP_EOL
                 .  '    <lastmod>' . e($u['lastmod']) . '</lastmod>' . PHP_EOL
                 .  '    <changefreq>' . e($u['changefreq']) . '</changefreq>' . PHP_EOL
                 .  '    <priority>' . e($u['priority']) . '</priority>' . PHP_EOL
                 .  '  </url>' . PHP_EOL;
        }

        $xml .= '</urlset>';

        Cache::put('quickgig:sitemap', $xml, now()->addDay());
        return response($xml, 200)->header('Content-Type', 'application/xml');
    }

    public function robots()
    {
        $content = "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /orders\nSitemap: " . url('/sitemap.xml') . "\n";

        return response($content, 200)->header('Content-Type', 'text/plain');
    }
}
