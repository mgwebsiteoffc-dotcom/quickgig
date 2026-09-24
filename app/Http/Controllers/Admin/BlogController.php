<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->query('q');
        $blogs = Blog::with('category','author')
            ->when($q, fn($qq)=> $qq->where('title','like',"%$q%"))
            ->orderByDesc('updated_at')
            ->paginate(10)->withQueryString();
        $categories = BlogCategory::all();
        return view('admin.blogs.index', compact('blogs','categories','q'));
    }

    public function create()
    {
        $categories = BlogCategory::all();
        return view('admin.blogs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'=>'required|string|max:140',
            'slug'=>'nullable|string|max:160|unique:blogs,slug',
            'excerpt'=>'nullable|string|max:360',
            'content'=>'required|string|min:30',
            'category_id'=>'nullable|exists:blog_categories,id',
            'tags'=>'nullable|string|max:300',
            'meta_title'=>'nullable|string|max:70',
            'meta_description'=>'nullable|string|max:165',
            'canonical_url'=>'nullable|url|max:300',
            'cover'=>'nullable|image|max:3072',
            'cover_alt'=>'nullable|string|max:120',
            'is_published'=>'nullable|boolean',
            'is_featured'=>'nullable|boolean',
            'reading_minutes'=>'nullable|integer|min:1|max:60',
            'faq_json'=>'nullable|string', // JSON string from hidden
        ]);

        if (empty($data['slug'])) $data['slug'] = Str::slug($data['title']);
        if (isset($data['tags'])) $data['tags'] = array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
        if (!empty($data['faq_json'])) {
            $decoded = json_decode($data['faq_json'], true);
            $data['faq_json'] = is_array($decoded) ? $decoded : null;
        } else unset($data['faq_json']);

        $data['author_id'] = auth()->id();
        $data['is_published'] = $request->boolean('is_published', true);
        $data['is_featured'] = $request->boolean('is_featured');
        $data['published_at'] = $data['is_published'] ? now() : null;

        if ($request->hasFile('cover')) {
            try { $data['cover'] = $request->file('cover')->store('blogs', 'public'); }
            catch(\Throwable $e){ $fn=time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','',$request->file('cover')->getClientOriginalName()); $request->file('cover')->move(public_path('uploads/blogs'), $fn); $data['cover']='uploads/blogs/'.$fn; }
        }

        Blog::create($data);

        return redirect()->route('admin.blogs.index')->with('toast','Blog created ✓');
    }

    public function edit(Blog $blog)
    {
        $categories = BlogCategory::all();
        return view('admin.blogs.edit', compact('blog','categories'));
    }

    public function update(Request $request, Blog $blog)
    {
        $data = $request->validate([
            'title'=>'required|string|max:140',
            'slug'=>'required|string|max:160|unique:blogs,slug,'.$blog->id,
            'excerpt'=>'nullable|string|max:360',
            'content'=>'required|string|min:30',
            'category_id'=>'nullable|exists:blog_categories,id',
            'tags'=>'nullable|string|max:300',
            'meta_title'=>'nullable|string|max:70',
            'meta_description'=>'nullable|string|max:165',
            'canonical_url'=>'nullable|url|max:300',
            'cover'=>'nullable|image|max:3072',
            'cover_alt'=>'nullable|string|max:120',
            'is_published'=>'nullable|boolean',
            'is_featured'=>'nullable|boolean',
            'reading_minutes'=>'nullable|integer|min:1|max:60',
            'faq_json'=>'nullable|string',
        ]);

        if (isset($data['tags'])) $data['tags'] = array_values(array_filter(array_map('trim', explode(',', $data['tags']))));
        if (!empty($data['faq_json'])) {
            $decoded = json_decode($data['faq_json'], true);
            $data['faq_json'] = is_array($decoded) ? $decoded : null;
        } else unset($data['faq_json']);

        $data['is_published'] = $request->boolean('is_published');
        $data['is_featured'] = $request->boolean('is_featured');
        if ($data['is_published'] && !$blog->published_at) $data['published_at'] = now();

        if ($request->hasFile('cover')) {
            try { $data['cover'] = $request->file('cover')->store('blogs', 'public'); }
            catch(\Throwable $e){ $fn=time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','',$request->file('cover')->getClientOriginalName()); $request->file('cover')->move(public_path('uploads/blogs'), $fn); $data['cover']='uploads/blogs/'.$fn; }
        } else unset($data['cover']);

        $blog->update($data);

        return redirect()->route('admin.blogs.index')->with('toast','Blog updated ✓');
    }

    public function destroy(Blog $blog)
    {
        $blog->delete();
        return back()->with('toast','Blog deleted');
    }

    // For rich-text image uploads via the local file driver
    public function uploadImage(Request $request)
    {
        $request->validate(['image'=>'required|image|max:3072']);
        try {
            $path = $request->file('image')->store('blogs/content', 'public');
            $url = asset('storage/'.$path);
        } catch(\Throwable $e){
            $fn=time().'_'.preg_replace('/[^a-zA-Z0-9._-]/','',$request->file('image')->getClientOriginalName());
            $request->file('image')->move(public_path('uploads/blogs'), $fn);
            $url = asset('uploads/blogs/'.$fn);
        }
        return response()->json(['url'=>$url]);
    }
}
