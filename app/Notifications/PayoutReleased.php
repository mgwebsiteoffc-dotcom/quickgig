<?php

namespace App\Notifications;

use App\Models\Payout;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PayoutReleased extends Notification
{
    use Queueable;

    public function __construct(public Payout $payout) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $p = $this->payout;

        $when = $p->available_at && $p->available_at->isFuture()
            ? 'It clears on ' . $p->available_at->format('d M, H:i') . '.'
            : 'It is in the payout queue now.';

        return (new MailMessage)
            ->subject('Approved — ₹' . number_format($p->amount) . ' on the way')
            ->greeting('Your work was approved.')
            ->line('₹' . number_format($p->amount) . ' has been released from escrow' . ($p->destination ? ' to ' . $p->destination : '') . '.')
            ->line($when)
            ->line('Reference: ' . $p->uid)
            ->action('Open your studio', route('creator.dashboard'));
    }
}
