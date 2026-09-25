<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    protected $fillable = [
        'user_id', 'name', 'role', 'method', 'route', 'path', 'payload', 'status', 'ip',
    ];

    protected $casts = ['payload' => 'array'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function describe(): string
    {
        return ($this->name ?: 'Someone') . ' → ' . ($this->route ?: $this->method . ' ' . $this->path);
    }
}
