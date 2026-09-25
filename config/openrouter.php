<?php

return [
    /*
    |--------------------------------------------------------------------------
    | OpenRouter (optional)
    |--------------------------------------------------------------------------
    | Leave OPENROUTER_API_KEY empty and every AI feature falls back to the
    | deterministic engines in app/Services — the product keeps working, it just
    | stops calling a model.
    */

    'key'   => env('OPENROUTER_API_KEY'),
    'base'  => env('OPENROUTER_BASE_URL', 'https://openrouter.ai/api/v1'),
    'model' => env('OPENROUTER_MODEL', 'nvidia/nemotron-3.5-lightning:free'),

    // Sent to OpenRouter for attribution / leaderboards. Optional but recommended.
    'referer' => env('OPENROUTER_REFERER', env('APP_URL', 'http://localhost')),
    'title'   => env('OPENROUTER_TITLE', env('APP_NAME', 'Quick GIGS')),

    'reasoning'   => (bool) env('OPENROUTER_REASONING', true),
    'temperature' => (float) env('OPENROUTER_TEMPERATURE', 0.4),
    'max_tokens'  => (int) env('OPENROUTER_MAX_TOKENS', 1600),
    'timeout'     => (int) env('OPENROUTER_TIMEOUT', 45),
    'retries'     => (int) env('OPENROUTER_RETRIES', 2),

    // Log prompt/response metadata (never the API key) for debugging.
    'log' => (bool) env('OPENROUTER_LOG', false),
];
