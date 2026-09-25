<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Company extends Model
{
    protected $fillable = [
        'name','slug','person_name','email','phone','website','logo','initials','plan','gstin',
        'bio','address','city','state','pincode','industry','team_size','is_verified','is_active',
        'seo_title','seo_description',
        'plan_tier','monthly_credits','credits_used','seats','renews_on',
    ];

    protected $casts = ['is_verified'=>'boolean','is_active'=>'boolean','renews_on'=>'date'];

    /** Subscription tiers for the business panel. */
    public const TIERS = [
        'payg'    => ['label' => 'Pay as you go', 'credits' => 0,  'seats' => 2,  'price' => 0],
        'starter' => ['label' => 'Starter',       'credits' => 8,  'seats' => 3,  'price' => 24999],
        'growth'  => ['label' => 'Growth',        'credits' => 20, 'seats' => 8,  'price' => 54999],
        'scale'   => ['label' => 'Scale',         'credits' => 45, 'seats' => 20, 'price' => 99999],
    ];

    public function tier(): array
    {
        return self::TIERS[$this->plan_tier ?? 'payg'] ?? self::TIERS['payg'];
    }

    public function creditsLeft(): int
    {
        return max(0, (int) $this->monthly_credits - (int) $this->credits_used);
    }

    protected static function booted()
    {
        static::creating(function ($c) {
            if (empty($c->slug)) $c->slug = Str::slug($c->name);
            if (empty($c->initials)) $c->initials = strtoupper(substr($c->name,0,2));
        });
    }

    public function orders(){ return $this->hasMany(Order::class); }
    public function tasks(){ return $this->hasMany(Task::class); }
    public function users(){ return $this->hasMany(User::class); }

    public function getRouteKeyName(){ return 'slug'; }

    public function logoUrl(): string
    {
        if ($this->logo && filter_var($this->logo, FILTER_VALIDATE_URL)) return $this->logo;
        if ($this->logo) return asset('storage/'.$this->logo);
        return 'https://ui-avatars.com/api/?name='.urlencode($this->initials).'&background=0F0F0F&color=fff&size=100';
    }
}
