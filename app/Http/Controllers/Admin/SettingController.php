<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\Ai\AiManager;
use App\Services\Payments\RazorpayGateway;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    /** key => is_secret. Secrets are encrypted and never echoed back in full. */
    private const FIELDS = [
        // platform
        'platform.fee_percent'            => false,
        'platform.gst_percent'           => false,
        'platform.escrow_hours'           => false,
        'platform.support_email'          => false,
        'platform.support_phone'          => false,
        'platform.maintenance'            => false,
        'notifications.email_enabled'  => false,
        'notifications.whatsapp_enabled' => false,
        'notifications.whatify_api_key' => true,
        'notifications.whatify_account_id' => false,
        'mail.host' => false,
        'mail.port' => false,
        'mail.username' => false,
        'mail.password' => true,
        'mail.encryption' => false,
        'mail.from_address' => false,
        'mail.from_name' => false,
        'notifications.templates.welcome_business.subject' => false, 'notifications.templates.welcome_business.body' => false,
        'notifications.templates.welcome_creator.subject' => false, 'notifications.templates.welcome_creator.body' => false,
        'notifications.templates.welcome_agency.subject' => false, 'notifications.templates.welcome_agency.body' => false,
        'notifications.templates.order_buyer.subject' => false, 'notifications.templates.order_buyer.body' => false,
        'notifications.templates.order_freelancer.subject' => false, 'notifications.templates.order_freelancer.body' => false,
        'notifications.templates.delivery_submitted.subject' => false, 'notifications.templates.delivery_submitted.body' => false,
        'notifications.templates.payout_released.subject' => false, 'notifications.templates.payout_released.body' => false,
        // payments
        'payments.razorpay.key_id'        => false,
        'payments.razorpay.key_secret'    => true,
        'payments.razorpay.webhook_secret'=> true,
        'payments.razorpayx.account_number'=> false,
        // ai
        'ai.default_provider'             => false,
        'ai.openrouter.key'               => true,
        'ai.openrouter.model'             => false,
        'ai.openai.key'                   => true,
        'ai.openai.model'                 => false,
        'ai.gemini.key'                   => true,
        'ai.gemini.model'                 => false,
    ];

    public function index(AiManager $ai, RazorpayGateway $razorpay)
    {
        return view('admin.settings.index', [
            'values' => [
                'platform.fee_percent'   => setting('platform.fee_percent', 0),
                'platform.gst_percent'  => setting('platform.gst_percent', 18),
                'platform.escrow_hours'  => setting('platform.escrow_hours', 48),
                'platform.support_email' => setting('platform.support_email', 'support@quickgigs.in'),
                'platform.support_phone' => setting('platform.support_phone', '+91 98765 43210'),
                'platform.maintenance'   => (bool) setting('platform.maintenance', false),
                'notifications.email_enabled' => (bool) setting('notifications.email_enabled', true),
                'notifications.whatsapp_enabled' => (bool) setting('notifications.whatsapp_enabled', false),
                'notifications.whatify_account_id' => setting('notifications.whatify_account_id', ''),
                'mail.host' => setting('mail.host', env('MAIL_HOST','')),
                'mail.port' => setting('mail.port', env('MAIL_PORT',587)),
                'mail.username' => setting('mail.username', env('MAIL_USERNAME','')),
                'mail.encryption' => setting('mail.encryption', env('MAIL_ENCRYPTION','tls')),
                'mail.from_address' => setting('mail.from_address', env('MAIL_FROM_ADDRESS','')),
                'mail.from_name' => setting('mail.from_name', env('MAIL_FROM_NAME','Quick GIGS')),
                'notifications.templates.welcome_business.subject' => setting('notifications.templates.welcome_business.subject','Welcome to Quick GIGS'), 'notifications.templates.welcome_business.body' => setting('notifications.templates.welcome_business.body','Your business workspace is ready, {{name}}.'),
                'notifications.templates.welcome_creator.subject' => setting('notifications.templates.welcome_creator.subject','Welcome to Quick GIGS, {{name}}'), 'notifications.templates.welcome_creator.body' => setting('notifications.templates.welcome_creator.body','Your freelancer account is ready. Verify your email and complete your profile.'),
                'notifications.templates.welcome_agency.subject' => setting('notifications.templates.welcome_agency.subject','Your Quick GIGS agency profile is ready'), 'notifications.templates.welcome_agency.body' => setting('notifications.templates.welcome_agency.body','Your team can add services and work references for admin review.'),
                'notifications.templates.order_buyer.subject' => setting('notifications.templates.order_buyer.subject','Order {{uid}} is live'), 'notifications.templates.order_buyer.body' => setting('notifications.templates.order_buyer.body','{{service}} is with {{creator}}. ₹{{total}} is held in escrow.'),
                'notifications.templates.order_freelancer.subject' => setting('notifications.templates.order_freelancer.subject','New gig assigned — {{service}}'), 'notifications.templates.order_freelancer.body' => setting('notifications.templates.order_freelancer.body','{{company}} booked {{service}}. Payout on approval: ₹{{payout}}.'),
                'notifications.templates.delivery_submitted.subject' => setting('notifications.templates.delivery_submitted.subject','Delivery ready to review — {{uid}}'), 'notifications.templates.delivery_submitted.body' => setting('notifications.templates.delivery_submitted.body','{{creator}} submitted {{service}}. Review it to release ₹{{payout}}.'),
                'notifications.templates.payout_released.subject' => setting('notifications.templates.payout_released.subject','Approved — ₹{{amount}} on the way'), 'notifications.templates.payout_released.body' => setting('notifications.templates.payout_released.body','₹{{amount}} has been released. Reference: {{uid}}.'),
                'payments.razorpay.key_id' => setting('payments.razorpay.key_id', ''),
                'payments.razorpayx.account_number' => setting('payments.razorpayx.account_number', ''),
                'ai.default_provider'    => setting('ai.default_provider', 'auto'),
                'ai.openrouter.model'    => setting('ai.openrouter.model', config('openrouter.model')),
                'ai.openai.model'        => setting('ai.openai.model', 'gpt-4o-mini'),
                'ai.gemini.model'        => setting('ai.gemini.model', 'gemini-2.0-flash'),
            ],
            'masked' => [
                'payments.razorpay.key_secret'     => Setting::masked('payments.razorpay.key_secret'),
                'payments.razorpay.webhook_secret' => Setting::masked('payments.razorpay.webhook_secret'),
                'ai.openrouter.key'                => Setting::masked('ai.openrouter.key'),
                'ai.openai.key'                    => Setting::masked('ai.openai.key'),
                'ai.gemini.key'                    => Setting::masked('ai.gemini.key'),
                'notifications.whatify_api_key' => Setting::masked('notifications.whatify_api_key'),
                'mail.password' => Setting::masked('mail.password'),
            ],
            'ai'       => $ai->status(),
            'aiActive' => $ai->label(),
            'razorpay' => [
                'enabled' => $razorpay->enabled(),
                'payouts' => $razorpay->payoutsEnabled(),
                'mode'    => $razorpay->mode(),
                'webhook' => url('/webhooks/razorpay'),
            ],
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'platform.fee_percent'   => ['required', 'numeric', 'min:0', 'max:30'],
            'platform.gst_percent'  => ['required', 'numeric', 'min:0', 'max:30'],
            'platform.escrow_hours'  => ['required', 'numeric', 'min:1', 'max:720'],
            'platform.support_email' => ['required', 'email'],
            'platform.support_phone' => ['nullable', 'string', 'max:30'],
            'notifications.email_enabled' => ['nullable','boolean'],
            'notifications.whatsapp_enabled' => ['nullable','boolean'],
            'notifications.whatify_api_key' => ['nullable','string','max:255'],
            'notifications.whatify_account_id' => ['nullable','integer','min:1'],
            'mail.host' => ['nullable','string','max:120'], 'mail.port' => ['nullable','integer','min:1','max:65535'], 'mail.username' => ['nullable','string','max:160'], 'mail.password' => ['nullable','string','max:200'], 'mail.encryption' => ['nullable','in:tls,ssl,none'], 'mail.from_address' => ['nullable','email'], 'mail.from_name' => ['nullable','string','max:120'],
            'notifications.templates.*.subject' => ['nullable','string','max:180'], 'notifications.templates.*.body' => ['nullable','string','max:2000'],
            'payments.razorpay.key_id'         => ['nullable', 'string', 'max:80'],
            'payments.razorpay.key_secret'     => ['nullable', 'string', 'max:200'],
            'payments.razorpay.webhook_secret' => ['nullable', 'string', 'max:200'],
            'payments.razorpayx.account_number' => ['nullable', 'string', 'max:40'],
            'ai.default_provider'    => ['required', 'in:auto,none,openrouter,openai,gemini'],
            'ai.openrouter.key'      => ['nullable', 'string', 'max:200'],
            'ai.openrouter.model'    => ['nullable', 'string', 'max:80'],
            'ai.openai.key'          => ['nullable', 'string', 'max:200'],
            'ai.openai.model'        => ['nullable', 'string', 'max:80'],
            'ai.gemini.key'          => ['nullable', 'string', 'max:200'],
            'ai.gemini.model'        => ['nullable', 'string', 'max:80'],
        ]);

        foreach (self::FIELDS as $key => $isSecret) {
            if ($key === 'platform.maintenance') {
                Setting::put($key, $request->boolean('platform.maintenance') ? '1' : '', false);
                continue;
            }

            $value = data_get($data, $key);

            // blank secret means "keep what is already stored"
            if ($isSecret && blank($value)) {
                continue;
            }

            Setting::put($key, $value, $isSecret);
        }

        config(['mail.mailers.smtp.host'=>setting('mail.host',env('MAIL_HOST')), 'mail.mailers.smtp.port'=>(int) setting('mail.port',env('MAIL_PORT',587)), 'mail.mailers.smtp.username'=>setting('mail.username',env('MAIL_USERNAME')), 'mail.mailers.smtp.password'=>setting('mail.password',env('MAIL_PASSWORD')), 'mail.mailers.smtp.encryption'=>setting('mail.encryption',env('MAIL_ENCRYPTION','tls')), 'mail.from.address'=>setting('mail.from_address',env('MAIL_FROM_ADDRESS')), 'mail.from.name'=>setting('mail.from_name',env('MAIL_FROM_NAME','Quick GIGS'))]);
        return back()->with('toast', 'Settings saved.');
    }

    /** Clear one stored secret. */
    public function clear(Request $request)
    {
        $key = (string) $request->input('key');

        abort_unless(array_key_exists($key, self::FIELDS) && self::FIELDS[$key], 404);

        Setting::forget($key);

        return back()->with('toast', 'Key removed.');
    }

    /** Live credential check, straight from the settings screen. */
    public function test(Request $request, AiManager $ai, RazorpayGateway $razorpay)
    {
        $target = (string) $request->input('target');

        try {
            if ($target === 'smtp') {
                \Illuminate\Support\Facades\Mail::raw('Quick GIGS SMTP test — your mail settings are working.', function ($message) { $message->to(auth()->user()->email)->subject('Quick GIGS SMTP test'); });
                return response()->json(['ok'=>true,'message'=>'SMTP test email sent to '.auth()->user()->email.'.']);
            }
            if ($target === 'razorpay') {
                $result = $razorpay->ping();

                return response()->json([
                    'ok'      => true,
                    'message' => 'Razorpay reachable in ' . $result['mode'] . ' mode (' . $result['latency_ms'] . ' ms).',
                ]);
            }

            $provider = $ai->providers()[$target] ?? null;
            abort_unless($provider, 404);

            if (! $provider->enabled()) {
                return response()->json(['ok' => false, 'message' => $provider->label() . ' has no key saved yet.']);
            }

            $reply = $provider->chat(
                [['role' => 'user', 'content' => 'Reply with exactly: quick gigs online']],
                ['max_tokens' => 32, 'temperature' => 0]
            );

            return response()->json([
                'ok'      => true,
                'message' => $provider->label() . ' replied in ' . $reply['latency_ms'] . ' ms · ' . $reply['model']
                           . ' · "' . \Illuminate\Support\Str::limit(trim($reply['content']), 40) . '"',
            ]);
        } catch (\Throwable $e) {
            return response()->json(['ok' => false, 'message' => \Illuminate\Support\Str::limit($e->getMessage(), 160)]);
        }
    }
}
