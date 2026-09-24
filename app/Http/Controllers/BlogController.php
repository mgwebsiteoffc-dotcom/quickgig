<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $cat = $request->query('category');

        $blogs = Blog::with('category','author')
            ->published()
            ->when($q, fn($qq)=> $qq->where(fn($w)=> $w->where('title','like',"%$q%")->orWhere('excerpt','like',"%$q%")))
            ->when($cat, fn($qq)=> $qq->whereHas('category', fn($c)=> $c->where('slug',$cat)))
            ->orderByDesc('is_featured')
            ->orderByDesc('published_at')
            ->paginate(9)->withQueryString();

        $categories = BlogCategory::withCount('blogs')->get();
        $featured = Blog::published()->featured()->orderByDesc('published_at')->limit(3)->get();

        // SEO for blog listing
        $seo = [
            'title' => 'Blog — Reels, Thumbnails & AI Video Playbooks | Quick GIGS',
            'description' => 'Playbooks from verified creators: retention cuts, CTR thumbs, Veo 3 UGC ads. -safe SOPs you can ship tomorrow.',
            'canonical' => url('/blog'),
            'image' => url('/og-blog.jpg'),
        ];

        return view('blogs.index', compact('blogs','categories','featured','seo','q','cat'));
    }

    public function show(string $slug)
    {
        $blog = Blog::with('category','author')->where('slug',$slug)->published()->firstOrFail();
        $blog->incrementViews();

        $related = Blog::published()->where('id','!=',$blog->id)
            ->when($blog->category_id, fn($q)=>$q->where('category_id',$blog->category_id))
            ->orderByDesc('published_at')->limit(3)->get();

        $seo = [
            'title' => $blog->seoTitle(),
            'description' => $blog->seoDescription(),
            'canonical' => $blog->canonical(),
            'image' => $blog->cover ? (filter_var($blog->cover, FILTER_VALIDATE_URL) ? $blog->cover : asset('storage/'.$blog->cover)) : url('/og-blog.jpg'),
            'type' => 'article',
            'published' => $blog->published_at,
            'author' => $blog->author->name ?? 'Quick GIGS Team',
            'tags' => $blog->tags,
        ];

        // Breadcrumb JSON-LD
        $breadcrumbs = [
            ['name'=>'Home','url'=>url('/')],
            ['name'=>'Blog','url'=>url('/blog')],
            ['name'=>$blog->title,'url'=>$blog->canonical()],
        ];

        return view('blogs.show', compact('blog','related','seo','breadcrumbs'));
    }
}
