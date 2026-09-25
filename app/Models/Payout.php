<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payout extends Model
{
    protected $fillable = [
        'uid', 'creator_id', 'order_id', 'gross', 'fee', 'amount',
        'status', 'method', 'destination', 'provider', 'reference', 'notes',
        'available_at', 'processed_at',
    ];

    protected $casts = [
        'available_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public const STATUSES = [
        'pending'    => 'Pending',
        'on_hold'    => 'On hold',
        'processing' => 'Processing',
        'paid'       => 'Paid',
        'failed'     => 'Failed',
    ];

    protected static function booted(): void
    {
        static::creating(function (Payout $payout) {
            $payout->uid ??= 'PO-' . Str::upper(Str::random(8));
        });
    }

    public function creator() { return $this->belongsTo(Creator::class); }
    public function order()   { return $this->belongsTo(Order::class); }

    public function scopeOpen($q)      { return $q->whereIn('status', ['pending', 'on_hold', 'processing']); }
    public function scopeReady($q)     { return $q->where('status', 'pending')->where(fn ($w) => $w->whereNull('available_at')->orWhere('available_at', '<=', now())); }
    public function scopeSettled($q)   { return $q->where('status', 'paid'); }

    public function isReady(): bool
    {
        return $this->status === 'pending' && (! $this->available_at || $this->available_at->isPast());
    }

    public function statusLabel(): string
    {
        return self::STATUSES[$this->status] ?? ucfirst($this->status);
    }

    /**
     * Raise the payout owed for an approved order. Idempotent — approving twice
     * must never create two obligations.
     */
    public static function raiseFor(Order $order): ?self
    {
        if (! $order->creator_id) {
            return null;
        }

        $existing = static::where('order_id', $order->id)->first();
        if ($existing) {
            return $existing;
        }

        $holdHours = (int) setting('platform.escrow_hours', 48);

        return static::create([
            'creator_id'   => $order->creator_id,
            'order_id'     => $order->id,
            'gross'        => (int) $order->total,
            'fee'          => (int) $order->fee,
            'amount'       => (int) ($order->total - $order->fee),
            'status'       => 'pending',
            'method'       => $order->creator?->upi_id ? 'upi' : 'manual',
            'destination'  => $order->creator?->upi_id,
            'available_at' => now()->addHours(max(0, $holdHours)),
        ]);
    }
}
