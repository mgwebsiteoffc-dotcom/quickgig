<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    protected $fillable = [
        'type', 'name', 'email', 'company', 'phone', 'volume', 'message', 'source', 'is_handled',
    ];

    protected $casts = ['is_handled' => 'boolean'];

    public function scopeOpen($q) { return $q->where('is_handled', false); }
}
