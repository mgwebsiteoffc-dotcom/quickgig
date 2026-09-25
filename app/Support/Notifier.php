<?php

namespace App\Support;

use App\Models\Company;
use App\Models\Creator;
use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification as NotificationFacade;

/**
 * Sends transactional mail without ever taking a request down with it.
 * Freelancers and companies are not User models, so we route on the address.
 */
class Notifier
{
    public static function toUserOf(?Company $company, Notification $notification): void
    {
        $email = optional(User::where('company_id', $company?->id)->first())->email ?: $company?->email;

        self::send($email, $notification);
    }

    public static function toFreelancer(?Creator $creator, Notification $notification): void
    {
        self::send($creator?->email, $notification);
    }

    public static function send(?string $email, Notification $notification): void
    {
        if (! $email || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return;
        }

        try {
            NotificationFacade::route('mail', $email)->notify($notification);
        } catch (\Throwable $e) {
            Log::warning('notification.failed', ['to' => $email, 'error' => $e->getMessage()]);
        }
    }
}
