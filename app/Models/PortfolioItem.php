<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortfolioItem extends Model
{
    protected $fillable = ['creator_id','title','slug','description','cover','video_url','external_url','category','tags','views','likes','is_featured','is_published','sort_order'];
    protected $casts = ['tags'=>'array','is_featured'=>'boolean','is_published'=>'boolean'];
    public function creator(){ return $this->belongsTo(Creator::class); }
    public function scopePublished($q){ return $q->where('is_published',true); }
    public function scopeFeatured($q){ return $q->where('is_featured',true); }
}
