<?php

namespace App\Services\Ai;

use Carbon\Carbon;
use Illuminate\Support\Str;

/**
 * Turns a sentence like
 *
 *     "create task to create mobile app, delivery date is 29 aug 2026"
 *
 * into the structured record the rest of the app (and your integrations) can use:
 *
 *     { title, start_date, end_date, description, client }
 *
 * A model does it when OPENROUTER_API_KEY is configured; otherwise a deterministic
 * parser produces the same shape so the feature never goes dark. Model output is
 * always validated and normalised — we never trust the JSON blindly.
 */
class TaskParser
{
    public const FIELDS = ['title', 'start_date', 'end_date', 'description', 'client'];

    public function __construct(private AiManager $client)
    {
    }

    /**
     * @param  array{client?:string,today?:string}  $context
     * @return array<string,mixed>
     */
    public function parse(string $prompt, array $context = []): array
    {
        $prompt = trim($prompt);
        $today  = isset($context['today']) ? Carbon::parse($context['today']) : Carbon::today();

        $fallback = $this->rules($prompt, $context, $today);

        if (! $this->client->enabled()) {
            return $fallback + ['source' => 'rules', 'model' => null, 'error' => null, 'messages' => [], 'reasoning_details' => null];
        }

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt($today, $context)],
            ['role' => 'user',   'content' => $prompt],
        ];

        try {
            $result = $this->client->chat($messages, ['json' => true, 'temperature' => 0.2]);
            $data   = OpenRouterClient::extractJson($result['content']);

            if (! is_array($data)) {
                throw new AiUnavailable('Model did not return JSON.');
            }

            $task = $this->validate($data, $fallback, $today);

            return $task + [
                'source'            => 'ai',
                'model'             => $result['model'],
                'error'             => null,
                'reasoning_details' => $result['reasoning_details'],
                'messages'          => array_merge($messages, [OpenRouterClient::assistantTurn($result)]),
            ];
        } catch (AiUnavailable $e) {
            return $fallback + [
                'source'            => 'rules',
                'model'             => null,
                'error'             => $e->getMessage(),
                'messages'          => [],
                'reasoning_details' => null,
            ];
        }
    }

    /**
     * Second pass: "Are you sure? Think carefully." — the assistant turn (including
     * reasoning_details) is replayed so the model continues its earlier reasoning.
     *
     * @param  array<string,mixed>  $state  the array returned by parse()
     * @return array<string,mixed>
     */
    public function refine(array $state, string $instruction): array
    {
        $today = Carbon::today();

        if (empty($state['messages']) || ! $this->client->enabled()) {
            return $state + ['refine_error' => 'Refinement needs a model — set OPENROUTER_API_KEY.'];
        }

        $messages = array_merge($state['messages'], [
            ['role' => 'user', 'content' => $instruction . "\n\nReturn the corrected JSON object only."],
        ]);

        try {
            $result = $this->client->chat($messages, ['json' => true, 'temperature' => 0.2]);
            $data   = OpenRouterClient::extractJson($result['content']);

            if (! is_array($data)) {
                throw new AiUnavailable('Model did not return JSON on refinement.');
            }

            $task = $this->validate($data, $state, $today);

            return $task + [
                'source'            => 'ai',
                'model'             => $result['model'],
                'error'             => null,
                'reasoning_details' => $result['reasoning_details'],
                'messages'          => array_merge($messages, [OpenRouterClient::assistantTurn($result)]),
            ];
        } catch (AiUnavailable $e) {
            return $state + ['refine_error' => $e->getMessage()];
        }
    }

    /* ───────────────────────── prompt ───────────────────────── */

    private function systemPrompt(Carbon $today, array $context): string
    {
        $client = $context['client'] ?? null;

        return implode("\n", array_filter([
            'You convert a short work request into a single JSON object for a project tracker.',
            'Return JSON only — no prose, no markdown fences.',
            'Schema: {"title": string (max 80 chars, no trailing period), "start_date": "YYYY-MM-DD", "end_date": "YYYY-MM-DD", "description": string (1-3 sentences describing scope and deliverables), "client": string or null}.',
            'Rules:',
            '- Today is ' . $today->toDateString() . '. Resolve relative dates ("tomorrow", "in 3 weeks") against it.',
            '- If a delivery or due date is given, it is end_date. If no start date is given, start_date is today.',
            '- end_date must never be before start_date.',
            '- Never invent a client name; use null when the request does not name one.',
            $client ? '- The requesting workspace is "' . $client . '". Use it as the client when none is named.' : null,
        ]));
    }

    /* ───────────────────────── validation ───────────────────────── */

    /**
     * @param  array<string,mixed>  $data      raw model output
     * @param  array<string,mixed>  $fallback  deterministic result used to fill gaps
     * @return array<string,mixed>
     */
    private function validate(array $data, array $fallback, Carbon $today): array
    {
        $title = trim((string) ($data['title'] ?? ''));
        $title = $title !== '' ? Str::limit(rtrim($title, '.'), 80, '') : $fallback['title'];

        $start = $this->date($data['start_date'] ?? null) ?? $fallback['start_date'];
        $end   = $this->date($data['end_date'] ?? null)   ?? $fallback['end_date'];

        $warnings = $this->warnings($start, $end, $today);

        $description = trim((string) ($data['description'] ?? ''));
        $description = $description !== '' ? Str::limit($description, 600) : $fallback['description'];

        $client = $data['client'] ?? null;
        $client = is_string($client) && trim($client) !== '' && Str::lower(trim($client)) !== 'null'
            ? Str::limit(trim($client), 80, '')
            : $fallback['client'];

        return [
            'title'       => $title,
            'start_date'  => $start,
            'end_date'    => $end,
            'description' => $description,
            'client'      => $client,
            'days'        => (int) Carbon::parse($start)->diffInDays(Carbon::parse($end), false),
            'warnings'    => $warnings,
        ];
    }

    private function date(mixed $value): ?string
    {
        if (! is_string($value) || trim($value) === '') {
            return null;
        }

        try {
            return Carbon::parse(trim($value))->toDateString();
        } catch (\Throwable) {
            return null;
        }
    }

    /* ───────────────────────── deterministic fallback ───────────────────────── */

    /** @return array<string,mixed> */
    private function rules(string $prompt, array $context, Carbon $today): array
    {
        $end   = $this->findDate($prompt, $today) ?? $today->copy()->addDays(3);
        $start = $this->findStartDate($prompt, $today) ?? $today->copy();
        $title = $this->findTitle($prompt);

        return [
            'title'       => $title,
            'start_date'  => $start->toDateString(),
            'end_date'    => $end->toDateString(),
            'description' => $this->describe($title, $prompt, $start, $end),
            'client'      => $this->findClient($prompt) ?? ($context['client'] ?? null),
            'days'        => (int) $start->diffInDays($end, false),
            'warnings'    => $this->warnings($start->toDateString(), $end->toDateString(), $today),
        ];
    }

    private function findTitle(string $prompt): string
    {
        $text = Str::of($prompt)->squish()
            ->replaceMatches('/^(please\s+)?(create|add|make|open|raise|log|new)\s+(a\s+|an\s+|new\s+)?(task|job|gig|project|ticket)\s*(to|for|:|-)?\s*/i', '')
            ->replaceMatches('/^(task|job|gig|project|ticket)\s*[:\-]\s*/i', '')
            ->replaceMatches('/^(to|for)\s+/i', '')
            ->__toString();

        // Drop scheduling and client clauses from the title.
        $text = preg_split(
            '/,|\bfor\s+client\b|\bin\s+(?:a|an|\d{1,3})\s+(?:day|week|month)s?\b|\b(?:delivery date|due date|due|deadline|deliver by|by|before|on or before|starting|start)\b/i',
            $text
        )[0] ?? $text;
        $text = trim($text, " .;:-");

        if ($text === '') {
            return 'New task';
        }

        return Str::limit(Str::ucfirst($text), 80, '');
    }

    private function describe(string $title, string $prompt, Carbon $start, Carbon $end): string
    {
        $days = (int) $start->diffInDays($end, false);

        $window = $days >= 0
            ? sprintf('%d %s from %s', $days, Str::plural('day', $days), $start->format('d M Y'))
            : 'the requested date has already passed';

        return sprintf(
            '%s. Requested as: "%s". Delivery due %s (%s).',
            rtrim($title, '.'),
            Str::limit(Str::squish($prompt), 180),
            $end->format('d M Y'),
            $window
        );
    }

    /** Flag anything a human should look at rather than silently rewriting it. */
    private function warnings(string $start, string $end, Carbon $today): array
    {
        $out   = [];
        $sDate = Carbon::parse($start);
        $eDate = Carbon::parse($end);

        if ($eDate->lt($sDate)) {
            $out[] = 'Delivery date ' . $eDate->format('d M Y') . ' is before the start date — confirm the year.';
        }

        if ($eDate->lt($today)) {
            $out[] = 'Delivery date ' . $eDate->format('d M Y') . ' is in the past.';
        }

        if ($eDate->gt($today->copy()->addYears(2))) {
            $out[] = 'Delivery date is more than two years out — check the year.';
        }

        return $out;
    }

    private function findClient(string $prompt): ?string
    {
        // "for client Nova Foods", "client is Acme", "client: Acme"
        if (preg_match('/\b(?:for\s+)?client(?:\s+is|\s*[:=])?\s+([A-Za-z0-9&.\' -]{2,60}?)(?=\s+(?:in|by|before|due|deadline|starting|on)\b|[,.]|$)/i', $prompt, $m)) {
            return Str::limit(trim($m[1]), 60, '');
        }

        if (preg_match('/\bfor\s+((?:[A-Z][A-Za-z0-9&.\']*\s?){1,4})(?=\s+(?:in|by|before|due|deadline)\b|[,.]|$)/', $prompt, $m)) {
            $candidate = trim($m[1]);
            if (Str::length($candidate) > 2) {
                return $candidate;
            }
        }

        return null;
    }

    /** Absolute or relative deadline anywhere in the sentence. */
    private function findDate(string $prompt, Carbon $today): ?Carbon
    {
        $text = Str::lower($prompt);

        // in 3 days / in 2 weeks / in a month
        if (preg_match('/\bin\s+(a|an|\d{1,3})\s+(day|week|month)s?\b/', $text, $m)) {
            $n    = ($m[1] === 'a' || $m[1] === 'an') ? 1 : (int) $m[1];
            $unit = $m[2];

            return match ($unit) {
                'day'   => $today->copy()->addDays($n),
                'week'  => $today->copy()->addWeeks($n),
                default => $today->copy()->addMonths($n),
            };
        }

        foreach (['tomorrow' => 1, 'today' => 0] as $word => $days) {
            if (str_contains($text, $word)) {
                return $today->copy()->addDays($days);
            }
        }

        if (str_contains($text, 'next week'))  return $today->copy()->addWeek();
        if (str_contains($text, 'next month')) return $today->copy()->addMonth();

        $patterns = [
            '/\b(\d{4}-\d{2}-\d{2})\b/',                                                   // 2026-08-29
            '/\b(\d{1,2}[\/\-]\d{1,2}[\/\-]\d{2,4})\b/',                                   // 29/08/2026
            '/\b(\d{1,2}\s+(?:jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec)[a-z]*\.?\s*\d{0,4})\b/i', // 29 aug 2026
            '/\b((?:jan|feb|mar|apr|may|jun|jul|aug|sep|sept|oct|nov|dec)[a-z]*\.?\s+\d{1,2},?\s*\d{0,4})\b/i', // aug 29 2026
        ];

        foreach ($patterns as $pattern) {
            if (preg_match($pattern, $text, $m)) {
                try {
                    $date = Carbon::parse(trim($m[1]));

                    // "29 aug" with no year means the next occurrence.
                    if (! preg_match('/\d{4}/', $m[1]) && $date->lt($today)) {
                        $date->addYear();
                    }

                    return $date;
                } catch (\Throwable) {
                    continue;
                }
            }
        }

        return null;
    }

    private function findStartDate(string $prompt, Carbon $today): ?Carbon
    {
        if (preg_match('/\b(?:start|starting|kick\s?off|begins?)\s*(?:on|from)?\s*([a-z0-9\/\- ]{4,20})/i', $prompt, $m)) {
            try {
                return Carbon::parse(trim($m[1]));
            } catch (\Throwable) {
                return null;
            }
        }

        return null;
    }
}
