<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class OrderPlaced extends Notification
{
    use Queueable;

    public function __construct(public Order $order, public string $audience = 'buyer') {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $o = $this->order;

        if ($this->audience === 'freelancer') {
            return (new MailMessage)
                ->subject('New gig assigned — ' . ($o->service->title ?? 'Quick GIGS'))
                ->greeting('You have a new gig.')
                ->line(($o->company->name ?? 'A client') . ' booked "' . ($o->service->title ?? 'a gig') . '".')
                ->line('Payout on approval: ₹' . number_format($o->total - $o->fee) . ' · due ' . ($o->due_at?->format('d M, H:i') ?? 'soon'))
                ->line('The brief is attached to the gig in your studio.')
                ->action('Open the gig', route('creator.dashboard'));
        }

        return (new MailMessage)
            ->subject('Order ' . $o->uid . ' is live')
            ->greeting('Your gig is booked.')
            ->line('"' . ($o->service->title ?? 'Your gig') . '" is with ' . ($o->creator->name ?? 'a verified freelancer') . '.')
            ->line('₹' . number_format($o->total) . ' is held in escrow and is released only when you approve.')
            ->action('Track the order', route('orders.show', $o->uid));
    }
}
