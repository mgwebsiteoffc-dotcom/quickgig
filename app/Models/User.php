<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'avatar', 'phone', 'is_active',
        'company_id', 'creator_id',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // Roles for -safe RBAC (no extra package)
    const ROLES = [
        'super_admin' => 'Super Admin',
        'admin'       => 'Admin',
        'manager'     => 'Manager',
        'support'     => 'Support',
        'finance'     => 'Finance',
        'business'    => 'Business',
        'creator'     => 'Creator',
    ];

    const ADMIN_ROLES = ['super_admin','admin','manager','support','finance'];

    public function isAdmin(): bool { return in_array($this->role, self::ADMIN_ROLES); }
    public function isSuperAdmin(): bool { return $this->role === 'super_admin'; }
    public function hasRole(string $role): bool { return $this->role === $role; }
    public function hasAnyRole(array $roles): bool { return in_array($this->role, $roles); }

    // Relations — link staff to company/creator if needed
    public function company() { return $this->belongsTo(Company::class); }
    public function creator() { return $this->belongsTo(Creator::class); }
    public function orders() { return $this->hasMany(Order::class, 'company_id', 'company_id'); }

    public function scopeAdmins($q){ return $q->whereIn('role', self::ADMIN_ROLES); }
    public function scopeActive($q){ return $q->where('is_active', true); }
}
