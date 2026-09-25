<?php

namespace App\Services\Ai\Providers;

interface ChatProvider
{
    /** Machine key: openrouter, openai, gemini. */
    public function key(): string;

    public function label(): string;

    /** Configured with a usable API key? */
    public function enabled(): bool;

    public function model(): string;

    /**
     * @param  array<int,array<string,mixed>>  $messages  OpenAI-style messages; assistant turns may carry reasoning_details
     * @return array{content:string,reasoning_details:mixed,model:string,usage:array,latency_ms:int}
     *
     * @throws \App\Services\Ai\AiUnavailable
     */
    public function chat(array $messages, array $options = []): array;
}
