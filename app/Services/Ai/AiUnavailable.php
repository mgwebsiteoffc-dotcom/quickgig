<?php

namespace App\Services\Ai;

use RuntimeException;

/**
 * Thrown whenever the model layer cannot be used: no key, network failure,
 * rate limit, bad status or unusable response body.
 *
 * Callers are expected to catch this and fall back to the deterministic engine.
 */
class AiUnavailable extends RuntimeException
{
    public function __construct(string $message, public readonly ?int $status = null, public readonly ?string $body = null)
    {
        parent::__construct($message);
    }
}
