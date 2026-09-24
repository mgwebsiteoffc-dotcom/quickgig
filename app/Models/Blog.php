<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Blog extends Model
{
    protected $fillable = [
        'author_id','category_id','title','slug','excerpt','content','cover','cover_alt',
        'tags','meta_title','meta_description','og_image','canonical_url',
        'is_published','is_featured','published_at','views','reading_minutes','faq_json'
    ];

    protected $casts = [
        'tags'=>'array',
        'faq_json'=>'array',
        'is_published'=>'boolean',
        'is_featured'=>'boolean',
        'published_at'=>'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($blog) {
            if (empty($blog->slug)) $blog->slug = Str::slug($blog->title);
            if (empty($blog->published_at) && $blog->is_published) $blog->published_at = now();
            if (empty($blog->reading_minutes)) $blog->reading_minutes = max(1, ceil(str_word_count(strip_tags($blog->content))/200));
        });
        static::updating(function ($blog) {
            if (empty($blog->slug)) $blog->slug = Str::slug($blog->title);
        });
    }

    public function author(){ return $this->belongsTo(User::class,'author_id'); }
    public function category(){ return $this->belongsTo(BlogCategory::class,'category_id'); }

    public function scopePublished($q){ return $q->where('is_published',true); }
    public function scopeFeatured($q){ return $q->where('is_featured',true); }

    public function getRouteKeyName(){ return 'slug'; }

    public function incrementViews(){ $this->increment('views'); }

    // SEO helpers
    public function seoTitle(): string { return $this->meta_title ?: $this->title . ' | QuickContent'; }
    public function seoDescription(): string { return $this->meta_description ?: Str::limit(strip_tags($this->excerpt ?: $this->content), 155); }
    public function canonical(): string { return $this->canonical_url ?: url('/blog/'.$this->slug); }

    // JSON-LD Article for AEO
    public function jsonLdArticle(): array
    {
        return [
            '@context'=>'https://schema.org',
            '@type'=>'BlogPosting',
            'headline'=>$this->title,
            'description'=>$this->seoDescription(),
            'image'=>$this->cover ? url($this->cover) : url('/og-default.jpg'),
            'author'=>['@type'=>'Person','name'=>$this->author->name ?? 'QuickContent Team'],
            'publisher'=>['@type'=>'Organization','name'=>'QuickContent','logo'=>['@type'=>'ImageObject','url'=>url('/logo.png')]],
            'datePublished'=> optional($this->published_at)->toIso8601String(),
            'dateModified'=> $this->updated_at->toIso8601String(),
            'mainEntityOfPage'=>['@type'=>'WebPage','@id'=>$this->canonical()],
            'keywords'=> is_array($this->tags) ? implode(', ', $this->tags) : null,
        ];
    }
}
