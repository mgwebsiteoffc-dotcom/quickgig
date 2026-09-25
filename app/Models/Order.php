<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'uid','company_id','creator_id','service_id','brief','references','turnaround',
        'subtotal','fee','discount','total','status','escrow_status','progress','due_at'
    ];
    protected $casts = [
        'references'=>'array',
        'due_at'=>'datetime',
    ];
    const STATUS_WORKING='working', STATUS_REVIEW='review', STATUS_DELIVERED='delivered';

    protected static function booted()
    {
        static::creating(function($order){
            if (empty($order->uid)) {
                // Generate QC-XXXX for paid, BART-XXXX for barter (detected via service later, default QC)
                $order->uid = 'QC-'.rand(1000,9999).'-'.Str::upper(Str::random(2));
                // Ensure uniqueness
                while (static::where('uid',$order->uid)->exists()) {
                    $order->uid = 'QC-'.rand(1000,9999).'-'.Str::upper(Str::random(2));
                }
            }
            if (empty($order->status)) $order->status = 'working';
            if (empty($order->escrow_status)) $order->escrow_status = 'held';
            if (empty($order->progress)) $order->progress = 25;
        });
    }

    public function company(){ return $this->belongsTo(Company::class); }
    public function creator(){ return $this->belongsTo(Creator::class); }
    public function service(){ return $this->belongsTo(Service::class); }

    public function isBarter(): bool { return $this->escrow_status === 'barter' || $this->total == 0; }
}
