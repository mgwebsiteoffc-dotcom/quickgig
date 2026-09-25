<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Task extends Model
{
    protected $fillable = [
        'uid', 'company_id', 'created_by', 'creator_id', 'service_id', 'order_id',
        'title', 'brief', 'category', 'priority', 'status',
        'start_on', 'due_on', 'links', 'comments_count', 'sort_order', 'delivered_at',
    ];

    protected $casts = [
        'links'        => 'array',
        'start_on'     => 'date',
        'due_on'       => 'date',
        'delivered_at' => 'datetime',
    ];

    public const STATUSES = [
        'queued'     => 'Queued',
        'assigned'   => 'Assigned',
        'production' => 'In production',
        'review'     => 'In review',
        'done'       => 'Delivered',
    ];

    public const PRIORITIES = ['low' => 'Low', 'normal' => 'Normal', 'high' => 'High', 'urgent' => 'Urgent'];

    protected static function booted(): void
    {
        static::creating(function (Task $task) {
            $task->uid ??= 'T-' . Str::upper(Str::random(5));
            while (static::where('uid', $task->uid)->exists()) {
                $task->uid = 'T-' . Str::upper(Str::random(5));
            }
        });
    }

    public function company() { return $this->belongsTo(Company::class); }
    public function creator() { return $this->belongsTo(Creator::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function order()   { return $this->belongsTo(Order::class); }
    public function author()  { return $this->belongsTo(User::class, 'created_by'); }

    public function scopeOpen($q)   { return $q->whereNot('status', 'done'); }
    public function scopeOverdue($q){ return $q->whereNot('status', 'done')->whereDate('due_on', '<', now()); }

    public function isOverdue(): bool
    {
        return $this->status !== 'done' && $this->due_on && $this->due_on->isPast();
    }

    public function statusLabel(): string   { return self::STATUSES[$this->status] ?? ucfirst($this->status); }
    public function priorityLabel(): string { return self::PRIORITIES[$this->priority] ?? ucfirst($this->priority); }

    /** Shape used by the board's inline (JSON) updates. */
    public function toBoardArray(): array
    {
        return [
            'id'        => $this->id,
            'uid'       => $this->uid,
            'title'     => $this->title,
            'brief'     => $this->brief,
            'category'  => $this->category,
            'priority'  => $this->priority,
            'status'    => $this->status,
            'due_on'    => $this->due_on?->toDateString(),
            'due_label' => $this->due_on?->format('d M'),
            'overdue'   => $this->isOverdue(),
            'links'     => $this->links ?? [],
            'comments'  => $this->comments_count,
            'creator'   => $this->creator ? [
                'name' => $this->creator->name,
                'img'  => $this->creator->avatarUrl(),
            ] : null,
            'order_url' => $this->order ? route('orders.show', $this->order->uid) : null,
        ];
    }
}
