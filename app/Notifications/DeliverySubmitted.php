<?php

namespace App\Notifications;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeliverySubmitted extends Notification
{
    use Queueable;

    public function __construct(public Order $order) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $o = $this->order;

        return (new MailMessage)
            ->subject('Delivery ready to review — ' . $o->uid)
            ->greeting('Your delivery is in.')
            ->line(($o->creator->name ?? 'Your freelancer') . ' submitted "' . ($o->service->title ?? 'your gig') . '".')
            ->line('It passed the automated quality gate. Approve to release ₹' . number_format($o->total - $o->fee) . ', or ask for one of your two free revisions.')
            ->action('Review the delivery', route('orders.show', $o->uid));
    }
}
