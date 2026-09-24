<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Creator extends Model
{
    protected $fillable = [
        'user_id','name','handle','email','phone','avatar','cover','bio','headline',
        'skills','languages','location','price_from','rating','reviews_count','orders_count',
        'response_minutes','on_time_rate','repeat_rate','is_available','is_verified','is_featured',
        'upi_id','portfolio_url','instagram','youtube','seo_title','seo_description',
        // dynamic profile type & barter/UGC
        'profile_type','barter_available','barter_categories','ugc_niches','followers_count','collab_type',
        'verification_notes','verified_at','rejection_reason'
    ];

    protected $casts = [
        'skills'=>'array',
        'languages'=>'array',
        'is_available'=>'boolean',
        'is_verified'=>'boolean',
        'is_featured'=>'boolean',
        'barter_available'=>'boolean',
        'barter_categories'=>'array',
        'ugc_niches'=>'array',
        'rating'=>'decimal:1',
        'verified_at'=>'datetime',
    ];

    const PROFILE_TYPES = [
        'video_editor' => 'Video Editor',
        'ugc_creator' => 'UGC Creator',
        'influencer' => 'Influencer (Barter)',
        'designer' => 'Designer (Thumbnails)',
        'hybrid' => 'Hybrid (All)',
    ];

    public function user(){ return $this->belongsTo(User::class); }
    public function services(){ return $this->hasMany(Service::class); }
    public function orders(){ return $this->hasMany(Order::class); }
    public function portfolio(){ return $this->hasMany(PortfolioItem::class)->orderBy('sort_order')->orderByDesc('created_at'); }

    public function scopeAvailable($q){ return $q->where('is_available', true); }
    public function scopeVerified($q){ return $q->where('is_verified', true); }
    public function scopeFeatured($q){ return $q->where('is_featured', true); }
    public function scopeProfileType($q, $type){ return $q->where('profile_type', $type); }
    public function scopeBarter($q){ return $q->where('barter_available', true); }

    public function avatarUrl(): string
    {
        if ($this->avatar && filter_var($this->avatar, FILTER_VALIDATE_URL)) return $this->avatar;
        if ($this->avatar) return asset('storage/'.$this->avatar);
        return 'https://ui-avatars.com/api/?name='.urlencode($this->name).'&background=E8E8E6&color=0F0F0F&size=200';
    }

    public function handleClean(): string { return ltrim($this->handle, '@'); }
    public function seoTitle(): string { return $this->seo_title ?: $this->name.' ('.$this->handle.') — '.($this->headline ?: 'Verified Creator') . ' | QuickContent'; }

    public function profileLabel(): string { return self::PROFILE_TYPES[$this->profile_type] ?? ucfirst(str_replace('_',' ',$this->profile_type)); }
    public function isBarter(): bool { return $this->barter_available || $this->collab_type === 'barter' || $this->collab_type === 'both'; }
}
