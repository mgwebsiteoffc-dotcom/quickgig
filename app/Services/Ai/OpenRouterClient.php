<?php

namespace App\Services\Ai;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Throwable;

/**
 * Thin OpenRouter chat client.
 *
 * Two things it does that a naive wrapper does not:
 *
 *  1. It returns `reasoning_details` untouched, so a follow-up call can replay the
 *     assistant turn and the model continues reasoning instead of starting over.
 *     Pass the previous assistant message straight back in `$messages`.
 *  2. It never throws past the caller's control flow — every failure becomes an
 *     {@see AiUnavailable}, which the services catch to fall back to the
 *     deterministic engines.
 */
class OpenRouterClient implements \App\Services\Ai\Providers\ChatProvider
{
    public function __construct(
        private ?string $apiKey = null,
        private ?string $model = null,
    ) {
        // DB setting first (admin console), then config/env
        $this->apiKey ??= setting('ai.openrouter.key') ?: config('openrouter.key');
        $this->model  ??= setting('ai.openrouter.model') ?: config('openrouter.model');
    }

    public function enabled(): bool
    {
        return filled($this->apiKey);
    }

    public function model(): string
    {
        return (string) $this->model;
    }

    public function key(): string   { return 'openrouter'; }
    public function label(): string { return 'OpenRouter'; }

    /**
     * @param  array<int,array<string,mixed>>  $messages  OpenAI-style messages. Assistant turns may
     *                                                    carry `reasoning_details` — passed through as-is.
     * @param  array{model?:string,temperature?:float,max_tokens?:int,reasoning?:bool,json?:bool,timeout?:int}  $options
     * @return array{content:string,reasoning_details:mixed,model:string,usage:array,latency_ms:int,raw:array}
     *
     * @throws AiUnavailable
     */
    public function chat(array $messages, array $options = []): array
    {
        if (! $this->enabled()) {
            throw new AiUnavailable('OPENROUTER_API_KEY is not set — running on the deterministic engine.');
        }

        $reasoning = $options['reasoning'] ?? config('openrouter.reasoning');

        $payload = array_filter([
            'model'       => $options['model'] ?? $this->model,
            'messages'    => $this->normalise($messages),
            'temperature' => $options['temperature'] ?? config('openrouter.temperature'),
            'max_tokens'  => $options['max_tokens'] ?? config('openrouter.max_tokens'),
            'reasoning'   => $reasoning ? ['enabled' => true] : null,
            // Ask for a JSON body when we are going to parse one. Models that do not
            // support the flag simply ignore it, and the extractor copes either way.
            'response_format' => ($options['json'] ?? false) ? ['type' => 'json_object'] : null,
        ], fn ($v) => ! is_null($v));

        $started = microtime(true);

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey,
                'Content-Type'  => 'application/json',
                // Optional attribution headers documented by OpenRouter.
                'HTTP-Referer'  => (string) config('openrouter.referer'),
                'X-Title'       => (string) config('openrouter.title'),
            ])
                ->timeout($options['timeout'] ?? (int) config('openrouter.timeout'))
                ->retry(max(1, (int) config('openrouter.retries')), 500, function ($exception) {
                    // Retry transport errors, rate limits and 5xx — never a bad key or bad request.
                    $status = $exception instanceof \Illuminate\Http\Client\RequestException
                        ? $exception->response->status()
                        : null;

                    return $status === null || $status === 429 || $status >= 500;
                }, throw: false)
                ->post(rtrim((string) config('openrouter.base'), '/') . '/chat/completions', $payload);
        } catch (Throwable $e) {
            throw new AiUnavailable('OpenRouter request failed: ' . $e->getMessage());
        }

        $latency = (int) round((microtime(true) - $started) * 1000);

        if ($response->failed()) {
            $body = Str::limit((string) $response->body(), 400);
            $this->log('openrouter.failed', ['status' => $response->status(), 'body' => $body, 'ms' => $latency]);

            throw new AiUnavailable(
                'OpenRouter returned HTTP ' . $response->status() . ': ' . $this->errorMessage($response->json(), $body),
                $response->status(),
                $body
            );
        }

        $json    = $response->json() ?? [];
        $message = data_get($json, 'choices.0.message', []);
        $content = (string) ($message['content'] ?? '');

        if (trim($content) === '') {
            throw new AiUnavailable('OpenRouter returned an empty completion.');
        }

        $this->log('openrouter.ok', [
            'model'  => data_get($json, 'model'),
            'ms'     => $latency,
            'tokens' => data_get($json, 'usage.total_tokens'),
        ]);

        return [
            'content'           => $content,
            // Hand this back on the assistant turn of the next call, unmodified.
            'reasoning_details' => $message['reasoning_details'] ?? null,
            'model'             => (string) (data_get($json, 'model') ?? $this->model),
            'usage'             => (array) (data_get($json, 'usage') ?? []),
            'latency_ms'        => $latency,
            'raw'               => $json,
        ];
    }

    /**
     * Build the assistant turn to replay on the next request so reasoning continues.
     *
     * @return array<string,mixed>
     */
    public static function assistantTurn(array $result): array
    {
        return array_filter([
            'role'              => 'assistant',
            'content'           => $result['content'] ?? '',
            'reasoning_details' => $result['reasoning_details'] ?? null,
        ], fn ($v) => ! is_null($v));
    }

    /**
     * Pull a JSON object out of a completion, tolerating code fences and prose.
     *
     * @return array<string,mixed>|null
     */
    public static function extractJson(string $content): ?array
    {
        $text = trim($content);

        // ```json … ``` fences
        if (preg_match('/```(?:json)?\s*(.+?)```/s', $text, $m)) {
            $text = trim($m[1]);
        }

        $decoded = json_decode($text, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        // Fall back to the outermost {...} block.
        $start = strpos($text, '{');
        $end   = strrpos($text, '}');

        if ($start !== false && $end !== false && $end > $start) {
            $decoded = json_decode(substr($text, $start, $end - $start + 1), true);
            if (is_array($decoded)) {
                return $decoded;
            }
        }

        return null;
    }

    /** Keep only keys the API accepts, and never send a null content. */
    private function normalise(array $messages): array
    {
        return array_values(array_map(function (array $m) {
            $clean = [
                'role'    => $m['role'] ?? 'user',
                'content' => (string) ($m['content'] ?? ''),
            ];

            if (! empty($m['reasoning_details'])) {
                $clean['reasoning_details'] = $m['reasoning_details'];
            }

            if (! empty($m['name'])) {
                $clean['name'] = $m['name'];
            }

            return $clean;
        }, $messages));
    }

    private function errorMessage(mixed $json, string $fallback): string
    {
        return (string) (data_get($json, 'error.message') ?? data_get($json, 'message') ?? $fallback);
    }

    private function log(string $event, array $context): void
    {
        if (config('openrouter.log')) {
            Log::info($event, $context);
        }
    }
}
