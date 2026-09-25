<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payout extends Model
{
    protected $fillable = [
        'creator_id','order_id','amount','upi_id','status','hold_until','paid_at',
        'reference','payout_id','utr','failure_reason','poll_attempts',
    ];

    protected $casts = [
        'hold_until' => 'datetime',
        'paid_at'    => 'datetime',
    ];

    public const STATUS_HOLD = 'hold';
    public const STATUS_READY = 'ready';
    public const STATUS_PROCESSING = 'processing';
    public const STATUS_PAID = 'paid';
    public const STATUS_FAILED = 'failed';

    protected static function booted(): void
    {
        static::creating(function (Payout $p) {
            if (empty($p->reference)) {
                $p->reference = 'PO-'.now()->format('ymd').'-'.Str::upper(Str::random(8));
            }
            if (empty($p->status)) {
                $p->status = self::STATUS_HOLD;
            }
        });
    }

    public function creator() { return $this->belongsTo(Creator::class); }
    public function order()   { return $this->belongsTo(Order::class); }

    public function scopeHold($q)       { return $q->where('status', self::STATUS_HOLD); }
    public function scopeReady($q)      { return $q->where('status', self::STATUS_READY); }
    public function scopePaid($q)       { return $q->where('status', self::STATUS_PAID); }
    public function scopeProcessing($q) { return $q->where('status', self::STATUS_PROCESSING); }

    /** Holds whose escrow window has elapsed and can move to ready. */
    public function scopeReleasable($q)
    {
        return $q->where('status', self::STATUS_HOLD)
                 ->whereNotNull('hold_until')
                 ->where('hold_until', '<=', now());
    }

    public function isFinal(): bool
    {
        return in_array($this->status, [self::STATUS_PAID, self::STATUS_FAILED], true);
    }
}
