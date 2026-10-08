<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    protected $fillable = [
        'creator_id','title','slug','description','cover','price','mrp','delivery_days','category',
        'profile_type','price_type','barter_value','is_active','is_barter','deliverables','revision_count','collab_terms',
        'badge','sold_count','rating','billing_type','monthly_price','scope','process','faq_json','seo_title','seo_description','aeo_answer'
    ];

    protected $casts = [
        'deliverables'=>'array',
        'is_active'=>'boolean',
        'is_barter'=>'boolean',
        'rating'=>'decimal:1','monthly_price'=>'integer','faq_json'=>'array',
    ];

    const CATEGORIES = ['Reel','Thumbnail','AI Video','UGC Video','Writing','Development','Design','Voice Over','Marketing','Bundle'];
    const PROFILE_TYPES = ['video_editor','ugc_creator','influencer','designer','hybrid','any'];
    const PRICE_TYPES = ['paid','barter','hybrid'];

    protected static function booted()
    {
        static::creating(function($s){
            if (empty($s->slug)) $s->slug = Str::slug($s->title).'-'.time();
        });
    }

    public function creator(){ return $this->belongsTo(Creator::class); }

    public function scopeActive($q){ return $q->where('is_active', true); }
    public function scopeCategory($q, $cat){ return $q->where('category', $cat); }
    public function scopeProfileType($q, $type){ return $q->where('profile_type', $type); }

    public function displayPrice(): string
    {
        if ($this->price_type === 'barter') return 'Barter';
        $amount = $this->billing_type === 'monthly' && $this->monthly_price ? $this->monthly_price : $this->price;
        $suffix = $this->billing_type === 'monthly' ? '/month' : '';
        if ($this->price_type === 'hybrid') return '₹'.number_format($amount).' + Barter'.$suffix;
        return '₹'.number_format($amount).$suffix;
    }

    public function billingLabel(): string
    {
        return $this->billing_type === 'monthly' ? 'Monthly management' : 'One-time project';
    }

    public function seoTitle(): string { return $this->seo_title ?: $this->title.' | GIG60'; }
    public function seoDescription(): string { return $this->seo_description ?: \Illuminate\Support\Str::limit(strip_tags($this->description ?: $this->title), 155); }

    public function isBarter(): bool { return $this->price_type === 'barter' || $this->is_barter; }

    /** Cover image URL with a graceful fallback. */
    public function coverUrl(): string
    {
        if ($this->cover && filter_var($this->cover, FILTER_VALIDATE_URL)) return $this->cover;
        if ($this->cover) return asset('storage/'.$this->cover);
        return 'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=800&q=80';
    }

    /** Strike-through price, when the gig has an MRP above the selling price. */
    public function compareAt(): ?int
    {
        $mrp = (int) ($this->compare_price ?: $this->mrp);

        return $mrp > (int) $this->price ? $mrp : null;
    }

    public function discountPercent(): ?int
    {
        $mrp = $this->compareAt();

        return $mrp ? (int) round((($mrp - (int) $this->price) / $mrp) * 100) : null;
    }

    public function deliveryLabel(): string
    {
        return $this->delivery_days.($this->delivery_days > 1 ? ' days' : ' day');
    }
}
