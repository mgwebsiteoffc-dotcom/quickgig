<?php

namespace App\Services\Ai;

use App\Services\Ai\Providers\ChatProvider;
use App\Services\Ai\Providers\GeminiProvider;
use App\Services\Ai\Providers\OpenAiProvider;

/**
 * Chooses which model provider to talk to.
 *
 * Order of preference: the provider picked in the admin console → any provider
 * that happens to be configured → none, in which case callers fall back to the
 * deterministic engines and the product keeps working.
 */
class AiManager implements ChatProvider
{
    /** @var array<string,ChatProvider> */
    private array $providers;

    public function __construct(OpenRouterClient $openrouter, OpenAiProvider $openai, GeminiProvider $gemini)
    {
        $this->providers = [
            $openrouter->key() => $openrouter,
            $openai->key()     => $openai,
            $gemini->key()     => $gemini,
        ];
    }

    /** @return array<string,ChatProvider> */
    public function providers(): array
    {
        return $this->providers;
    }

    public function provider(?string $key = null): ?ChatProvider
    {
        $key ??= (string) setting('ai.default_provider', '');

        if ($key !== '' && isset($this->providers[$key]) && $this->providers[$key]->enabled()) {
            return $this->providers[$key];
        }

        if ($key === 'none') {
            return null;
        }

        foreach ($this->providers as $provider) {
            if ($provider->enabled()) {
                return $provider;
            }
        }

        return null;
    }

    public function key(): string   { return $this->provider()?->key() ?? 'none'; }
    public function label(): string { return $this->provider()?->label() ?? 'Deterministic engine'; }
    public function enabled(): bool { return (bool) $this->provider(); }
    public function model(): string { return $this->provider()?->model() ?? 'rules'; }

    public function chat(array $messages, array $options = []): array
    {
        $provider = $this->provider($options['provider'] ?? null);

        if (! $provider) {
            throw new AiUnavailable('No AI provider is configured — running on the deterministic engine.');
        }

        return $provider->chat($messages, $options);
    }

    /** For the admin screen: which providers are configured and which one wins. */
    public function status(): array
    {
        $active = $this->provider();

        return collect($this->providers)->map(fn (ChatProvider $p) => [
            'key'     => $p->key(),
            'label'   => $p->label(),
            'enabled' => $p->enabled(),
            'model'   => $p->model(),
            'active'  => $active && $active->key() === $p->key(),
        ])->values()->all();
    }
}
