<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    protected $fillable = [
        'creator_id','title','slug','description','cover','price','mrp','delivery_days','category',
        'profile_type','price_type','barter_value','is_active','is_barter','deliverables','revision_count','collab_terms',
        'badge','sold_count','rating'
    ];

    protected $casts = [
        'deliverables'=>'array',
        'is_active'=>'boolean',
        'is_barter'=>'boolean',
        'rating'=>'decimal:1',
    ];

    const CATEGORIES = ['Reel','Thumbnail','AI Video','UGC Video','Barter Collab','Bundle'];
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
        if ($this->price_type === 'hybrid') return '₹'.number_format($this->price).' + Barter';
        return '₹'.number_format($this->price);
    }

    public function isBarter(): bool { return $this->price_type === 'barter' || $this->is_barter; }

    /** Cover image URL with a graceful fallback. */
    public function coverUrl(): string
    {
        if ($this->cover && filter_var($this->cover, FILTER_VALIDATE_URL)) return $this->cover;
        if ($this->cover) return asset('storage/'.$this->cover);
        return 'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=800&q=80';
    }

    public function deliveryLabel(): string
    {
        return $this->delivery_days.($this->delivery_days > 1 ? ' days' : ' day');
    }
}
