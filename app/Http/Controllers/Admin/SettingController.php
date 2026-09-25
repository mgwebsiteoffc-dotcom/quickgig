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
        'platform.escrow_hours'           => false,
        'platform.support_email'          => false,
        'platform.support_phone'          => false,
        'platform.maintenance'            => false,
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
                'platform.fee_percent'   => setting('platform.fee_percent', 10),
                'platform.escrow_hours'  => setting('platform.escrow_hours', 48),
                'platform.support_email' => setting('platform.support_email', 'support@quickgigs.in'),
                'platform.support_phone' => setting('platform.support_phone', '+91 98765 43210'),
                'platform.maintenance'   => (bool) setting('platform.maintenance', false),
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
            'platform.escrow_hours'  => ['required', 'numeric', 'min:1', 'max:720'],
            'platform.support_email' => ['required', 'email'],
            'platform.support_phone' => ['nullable', 'string', 'max:30'],
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
