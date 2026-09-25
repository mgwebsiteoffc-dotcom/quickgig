<?php

namespace App\Services\Ai;

use App\Services\BriefComposer;
use Illuminate\Support\Str;

/**
 * Model-written briefs, with the deterministic {@see BriefComposer} as both the
 * scaffold and the safety net.
 *
 * Flow:
 *   1. Compose the rule-based brief (always — it also supplies pricing, the QA
 *      gate and the confidence meter, which are product rules, not creative copy).
 *   2. If a key is configured, ask the model to rewrite only the creative fields
 *      (title, summary, hooks, beats, avoid) using the draft as a starting point.
 *   3. Validate the JSON and merge field by field. Anything missing or malformed
 *      keeps the deterministic version.
 */
class BriefWriter
{
    public function __construct(
        private BriefComposer $composer,
        private OpenRouterClient $client,
    ) {
    }

    /**
     * @param  array<string,mixed>  $input  idea, format, goal, tone, audience, urgency
     * @return array{brief:array,source:string,model:?string,error:?string,messages:array,reasoning_details:mixed,latency_ms:?int}
     */
    public function write(array $input): array
    {
        $draft = $this->composer->compose($input);

        if (! $this->client->enabled()) {
            return $this->result($draft, 'rules', null, null);
        }

        $messages = [
            ['role' => 'system', 'content' => $this->systemPrompt()],
            ['role' => 'user',   'content' => $this->userPrompt($input, $draft)],
        ];

        try {
            $result = $this->client->chat($messages, ['json' => true, 'temperature' => 0.6]);
            $data   = OpenRouterClient::extractJson($result['content']);

            if (! is_array($data)) {
                throw new AiUnavailable('Model did not return JSON.');
            }

            return $this->result(
                $this->merge($draft, $data),
                'ai',
                $result['model'],
                null,
                array_merge($messages, [OpenRouterClient::assistantTurn($result)]),
                $result['reasoning_details'],
                $result['latency_ms'],
            );
        } catch (AiUnavailable $e) {
            return $this->result($draft, 'rules', null, $e->getMessage());
        }
    }

    /**
     * Ask for a revision of an existing brief. The previous assistant turn — including
     * `reasoning_details` — is replayed unmodified so the model picks up its own thread
     * instead of re-deriving everything.
     *
     * @param  array<string,mixed>  $state  the array returned by write()
     * @return array<string,mixed>
     */
    public function refine(array $state, string $instruction): array
    {
        $draft = $state['brief'];

        if (! $this->client->enabled() || empty($state['messages'])) {
            return $state + ['refine_error' => 'Refinement needs a model — set OPENROUTER_API_KEY to enable it.'];
        }

        $messages = array_merge($state['messages'], [
            ['role' => 'user', 'content' => trim($instruction) . "\n\nReturn the full corrected JSON object only, same schema as before."],
        ]);

        try {
            $result = $this->client->chat($messages, ['json' => true, 'temperature' => 0.5]);
            $data   = OpenRouterClient::extractJson($result['content']);

            if (! is_array($data)) {
                throw new AiUnavailable('Model did not return JSON on refinement.');
            }

            return $this->result(
                $this->merge($draft, $data),
                'ai',
                $result['model'],
                null,
                array_merge($messages, [OpenRouterClient::assistantTurn($result)]),
                $result['reasoning_details'],
                $result['latency_ms'],
            );
        } catch (AiUnavailable $e) {
            return $state + ['refine_error' => $e->getMessage()];
        }
    }

    /* ───────────────────────── prompts ───────────────────────── */

    private function systemPrompt(): string
    {
        return implode("\n", [
            'You are a senior creative director writing production briefs for short-form video, UGC and design gigs in India.',
            'Return JSON only — no prose, no markdown fences.',
            'Schema: {"title": string, "summary": string (2 sentences max), "hooks": [3 strings, each under 120 characters, spoken-out-loud style], "beats": [{"t": string timestamp or stage label, "what": string instruction}], "avoid": [3-5 strings]}.',
            'Rules:',
            '- Keep every beat executable by an editor without asking a question.',
            '- Hooks must be distinct angles, not three phrasings of one idea.',
            '- Never promise results, discounts or claims the brief did not mention.',
            '- Use plain English. No buzzwords, no emoji, no exclamation marks.',
            '- Keep the same number of beats as the draft unless the format demands otherwise.',
        ]);
    }

    private function userPrompt(array $input, array $draft): string
    {
        $beats = collect($draft['beats'])->map(fn ($b) => $b['t'] . ' — ' . $b['what'])->implode("\n");

        return implode("\n", [
            'Request: ' . ($input['idea'] ?? ''),
            'Format: ' . ($draft['spec']['Format'] ?? 'short-form video') . ' · ' . ($draft['spec']['Length'] ?? ''),
            'Goal: ' . ($input['goal'] ?? 'conversion'),
            'Audience: ' . $draft['audience'],
            'Tone: ' . $draft['tone'],
            '',
            'Rule-based draft to improve:',
            'Title: ' . $draft['title'],
            'Summary: ' . $draft['summary'],
            'Hooks:',
            '- ' . implode("\n- ", $draft['hooks']),
            'Beats:',
            $beats,
            'Avoid:',
            '- ' . implode("\n- ", $draft['avoid']),
            '',
            'Rewrite it so a creator could shoot and cut from it today. Keep the structure, sharpen the language, make the hooks specific to the request.',
        ]);
    }

    /* ───────────────────────── merge + validation ───────────────────────── */

    /**
     * @param  array<string,mixed>  $draft
     * @param  array<string,mixed>  $data
     * @return array<string,mixed>
     */
    private function merge(array $draft, array $data): array
    {
        $brief = $draft;

        if (! empty($data['title']) && is_string($data['title'])) {
            $brief['title'] = Str::limit(trim($data['title']), 120, '');
        }

        if (! empty($data['summary']) && is_string($data['summary'])) {
            $brief['summary'] = Str::limit(trim($data['summary']), 420);
        }

        $hooks = collect($data['hooks'] ?? [])
            ->filter(fn ($h) => is_string($h) && trim($h) !== '')
            ->map(fn ($h) => Str::limit(trim($h), 160))
            ->take(3)
            ->values();

        if ($hooks->count() >= 2) {
            $brief['hooks'] = $hooks->all();
        }

        $beats = collect($data['beats'] ?? [])
            ->filter(fn ($b) => is_array($b) && ! empty($b['what']) && is_string($b['what']))
            ->map(fn ($b) => [
                't'    => Str::limit(trim((string) ($b['t'] ?? 'Stage')), 24, ''),
                'what' => Str::limit(trim($b['what']), 240),
            ])
            ->take(8)
            ->values();

        if ($beats->count() >= 3) {
            $brief['beats'] = $beats->all();
        }

        $avoid = collect($data['avoid'] ?? [])
            ->filter(fn ($a) => is_string($a) && trim($a) !== '')
            ->map(fn ($a) => Str::limit(trim($a), 160))
            ->take(6)
            ->values();

        if ($avoid->count() >= 2) {
            $brief['avoid'] = $avoid->all();
        }

        return $brief;
    }

    /** @return array<string,mixed> */
    private function result(array $brief, string $source, ?string $model, ?string $error, array $messages = [], mixed $reasoning = null, ?int $latency = null): array
    {
        return [
            'brief'             => $brief,
            'source'            => $source,
            'model'             => $model,
            'error'             => $error,
            'messages'          => $messages,
            'reasoning_details' => $reasoning,
            'latency_ms'        => $latency,
        ];
    }
}
