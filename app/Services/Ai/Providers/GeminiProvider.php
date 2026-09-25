<?php

namespace App\Services\Ai\Providers;

use App\Services\Ai\AiUnavailable;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;

/**
 * Google Generative Language API. Chat messages are mapped onto Gemini's
 * contents/parts shape, and the system turn becomes system_instruction.
 */
class GeminiProvider implements ChatProvider
{
    public function key(): string   { return 'gemini'; }
    public function label(): string { return 'Google Gemini'; }

    public function enabled(): bool { return filled($this->apiKey()); }

    public function model(): string
    {
        return (string) (setting('ai.gemini.model') ?: env('GEMINI_MODEL', 'gemini-2.0-flash'));
    }

    private function apiKey(): ?string
    {
        return setting('ai.gemini.key') ?: env('GEMINI_API_KEY');
    }

    private function baseUrl(): string
    {
        return rtrim((string) (setting('ai.gemini.base_url') ?: env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta')), '/');
    }

    public function chat(array $messages, array $options = []): array
    {
        if (! $this->enabled()) {
            throw new AiUnavailable('Gemini key is not configured.');
        }

        $system   = collect($messages)->firstWhere('role', 'system')['content'] ?? null;
        $contents = [];

        foreach ($messages as $m) {
            if (($m['role'] ?? 'user') === 'system') continue;

            $contents[] = [
                'role'  => ($m['role'] ?? 'user') === 'assistant' ? 'model' : 'user',
                'parts' => [['text' => (string) ($m['content'] ?? '')]],
            ];
        }

        $payload = array_filter([
            'contents'           => $contents,
            'system_instruction' => $system ? ['parts' => [['text' => $system]]] : null,
            'generationConfig'   => array_filter([
                'temperature'      => $options['temperature'] ?? 0.4,
                'maxOutputTokens'  => $options['max_tokens'] ?? 1600,
                'responseMimeType' => ($options['json'] ?? false) ? 'application/json' : null,
            ], fn ($v) => ! is_null($v)),
        ], fn ($v) => ! is_null($v));

        $model   = $options['model'] ?? $this->model();
        $started = microtime(true);

        try {
            $res = Http::withHeaders(['Content-Type' => 'application/json'])
                ->timeout($options['timeout'] ?? 45)
                ->retry(2, 500, fn ($e) => $this->retryable($e), throw: false)
                ->post($this->baseUrl() . '/models/' . $model . ':generateContent?key=' . urlencode((string) $this->apiKey()), $payload);
        } catch (Throwable $e) {
            throw new AiUnavailable('Gemini request failed: ' . $e->getMessage());
        }

        $latency = (int) round((microtime(true) - $started) * 1000);

        if ($res->failed()) {
            throw new AiUnavailable('Gemini returned HTTP ' . $res->status() . ': '
                . (data_get($res->json(), 'error.message') ?? Str::limit($res->body(), 200)), $res->status());
        }

        $parts   = (array) data_get($res->json(), 'candidates.0.content.parts', []);
        $content = trim(collect($parts)->pluck('text')->filter()->implode("\n"));

        if ($content === '') {
            throw new AiUnavailable('Gemini returned an empty completion.');
        }

        return [
            'content'           => $content,
            'reasoning_details' => null,   // Gemini does not expose reasoning traces on this endpoint
            'model'             => $model,
            'usage'             => [
                'prompt_tokens'     => data_get($res->json(), 'usageMetadata.promptTokenCount'),
                'completion_tokens' => data_get($res->json(), 'usageMetadata.candidatesTokenCount'),
                'total_tokens'      => data_get($res->json(), 'usageMetadata.totalTokenCount'),
            ],
            'latency_ms'        => $latency,
        ];
    }

    private function retryable($exception): bool
    {
        $status = $exception instanceof \Illuminate\Http\Client\RequestException ? $exception->response->status() : null;

        return $status === null || $status === 429 || $status >= 500;
    }
}
