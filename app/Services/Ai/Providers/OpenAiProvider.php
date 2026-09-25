<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\AiUnavailable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

class OpenAiProvider implements ChatProvider
{
    public function key(): string   { return 'openai'; }
    public function label(): string { return 'OpenAI'; }

    public function enabled(): bool { return filled($this->apiKey()); }

    public function model(): string
    {
        return (string) (setting('ai.openai.model') ?: env('OPENAI_MODEL', 'gpt-4o-mini'));
    }

    private function apiKey(): ?string
    {
        return setting('ai.openai.key') ?: env('OPENAI_API_KEY');
    }

    private function baseUrl(): string
    {
        return rtrim((string) (setting('ai.openai.base_url') ?: env('OPENAI_BASE_URL', 'https://api.openai.com/v1')), '/');
    }

    public function chat(array $messages, array $options = []): array
    {
        if (! $this->enabled()) {
            throw new AiUnavailable('OpenAI key is not configured.');
        }

        $payload = array_filter([
            'model'       => $options['model'] ?? $this->model(),
            'messages'    => $this->normalise($messages),
            'temperature' => $options['temperature'] ?? 0.4,
            'max_tokens'  => $options['max_tokens'] ?? 1600,
            'response_format' => ($options['json'] ?? false) ? ['type' => 'json_object'] : null,
        ], fn ($v) => ! is_null($v));

        $started = microtime(true);

        try {
            $res = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->apiKey(),
                'Content-Type'  => 'application/json',
            ])->timeout($options['timeout'] ?? 45)
              ->retry(2, 500, fn ($e) => $this->retryable($e), throw: false)
              ->post($this->baseUrl() . '/chat/completions', $payload);
        } catch (Throwable $e) {
            throw new AiUnavailable('OpenAI request failed: ' . $e->getMessage());
        }

        $latency = (int) round((microtime(true) - $started) * 1000);

        if ($res->failed()) {
            throw new AiUnavailable('OpenAI returned HTTP ' . $res->status() . ': '
                . (data_get($res->json(), 'error.message') ?? Str::limit($res->body(), 200)), $res->status());
        }

        $message = data_get($res->json(), 'choices.0.message', []);
        $content = (string) ($message['content'] ?? '');

        if (trim($content) === '') {
            throw new AiUnavailable('OpenAI returned an empty completion.');
        }

        return [
            'content'           => $content,
            'reasoning_details' => $message['reasoning_details'] ?? null,
            'model'             => (string) (data_get($res->json(), 'model') ?? $this->model()),
            'usage'             => (array) (data_get($res->json(), 'usage') ?? []),
            'latency_ms'        => $latency,
        ];
    }

    private function retryable($exception): bool
    {
        $status = $exception instanceof \Illuminate\Http\Client\RequestException ? $exception->response->status() : null;

        return $status === null || $status === 429 || $status >= 500;
    }

    private function normalise(array $messages): array
    {
        return array_values(array_map(fn ($m) => array_filter([
            'role'    => $m['role'] ?? 'user',
            'content' => (string) ($m['content'] ?? ''),
        ], fn ($v) => $v !== null), $messages));
    }
}
