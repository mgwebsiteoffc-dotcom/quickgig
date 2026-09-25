<?php

namespace App\Console\Commands;

use App\Services\Ai\AiUnavailable;
use App\Services\Ai\OpenRouterClient;
use Illuminate\Console\Command;

class AiPing extends Command
{
    protected $signature = 'ai:ping {--model= : Override the configured model}';

    protected $description = 'Check the OpenRouter connection and print the model, latency and token usage';

    public function handle(OpenRouterClient $client): int
    {
        if (! $client->enabled()) {
            $this->components->error('OPENROUTER_API_KEY is not set. Every AI feature is running on the deterministic engine.');
            $this->line('  Add the key to .env, then run this again.');

            return self::FAILURE;
        }

        $this->components->info('Calling ' . ($this->option('model') ?: $client->model()) . ' …');

        try {
            $result = $client->chat(
                [['role' => 'user', 'content' => 'Reply with exactly: quick gigs online']],
                array_filter(['model' => $this->option('model'), 'max_tokens' => 64])
            );
        } catch (AiUnavailable $e) {
            $this->components->error($e->getMessage());

            return self::FAILURE;
        }

        $this->components->twoColumnDetail('Model', $result['model']);
        $this->components->twoColumnDetail('Latency', $result['latency_ms'] . ' ms');
        $this->components->twoColumnDetail('Tokens', (string) ($result['usage']['total_tokens'] ?? 'n/a'));
        $this->components->twoColumnDetail('Reasoning returned', $result['reasoning_details'] ? 'yes' : 'no');
        $this->components->twoColumnDetail('Reply', trim($result['content']));

        return self::SUCCESS;
    }
}
