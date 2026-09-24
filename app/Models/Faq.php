<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Faq extends Model
{
    protected $fillable = ['question','answer','category','sort_order','is_published','is_featured','slug'];
    protected $casts = ['is_published'=>'boolean','is_featured'=>'boolean'];

    protected static function booted()
    {
        static::creating(function ($faq) {
            if (empty($faq->slug)) $faq->slug = Str::slug($faq->question);
        });
        static::updating(function ($faq) {
            if (empty($faq->slug)) $faq->slug = Str::slug($faq->question);
        });
    }

    public function scopePublished($q){ return $q->where('is_published', true); }
    public function scopeFeatured($q){ return $q->where('is_featured', true); }
    public function scopeOrdered($q){ return $q->orderBy('sort_order')->orderBy('id'); }

    // For AEO JSON-LD FAQPage
    public static function toJsonLd($faqs): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $faqs->map(fn($f)=>[
                '@type'=>'Question',
                'name'=>$f->question,
                'acceptedAnswer'=>['@type'=>'Answer','text'=>strip_tags($f->answer)]
            ])->values()->toArray()
        ];
    }
}
