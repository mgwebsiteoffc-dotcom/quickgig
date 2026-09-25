<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class OrderDelivery extends Model
{
    protected $fillable = [
        'order_id','creator_id','delivery_url','note','file_path','file_name','mime','size',
    ];

    public function order()   { return $this->belongsTo(Order::class); }
    public function creator() { return $this->belongsTo(Creator::class); }

    public function hasFile(): bool
    {
        return filled($this->file_path);
    }

    /** Temporary signed URL where the driver supports it, otherwise a public URL. */
    public function url(): ?string
    {
        if (! $this->hasFile()) {
            return $this->delivery_url;
        }

        try {
            return Storage::disk('public')->temporaryUrl($this->file_path, now()->addMinutes(30));
        } catch (\Throwable $e) {
            return asset('storage/'.$this->file_path);
        }
    }

    public function humanSize(): string
    {
        $bytes = (int) $this->size;
        if ($bytes >= 1073741824) return round($bytes / 1073741824, 1).' GB';
        if ($bytes >= 1048576)    return round($bytes / 1048576, 1).' MB';
        if ($bytes >= 1024)       return round($bytes / 1024).' KB';

        return $bytes.' B';
    }
}
