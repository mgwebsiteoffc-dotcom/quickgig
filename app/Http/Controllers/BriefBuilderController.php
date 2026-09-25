<?php

namespace App\Http\Controllers;

use App\Models\Creator;
use App\Models\Service;
use App\Services\Ai\BriefWriter;
use App\Services\BriefComposer;
use App\Services\MatchEngine;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BriefBuilderController extends Controller
{
    public function show(Request $request)
    {
        $input = $request->session()->get('brief.input', []);

        if ($request->filled('idea')) {
            $input['idea'] = Str::limit(strip_tags((string) $request->query('idea')), 400, '');
        }

        $state = $request->filled('idea') ? null : $request->session()->get('brief.state');

        return view('tools.brief-builder', [
            'brief'   => $state['brief'] ?? null,
            'meta'    => $state ? [
                'source'     => $state['source'] ?? 'rules',
                'model'      => $state['model'] ?? null,
                'error'      => $state['error'] ?? null,
                'latency_ms' => $state['latency_ms'] ?? null,
                'refinable'  => ! empty($state['messages']),
                'refine_error' => $state['refine_error'] ?? null,
            ] : null,
            'input'   => $input,
            'matches' => $request->session()->get('brief.matches', collect()),
            'gig'     => $request->session()->get('brief.gig_id') ? Service::find($request->session()->get('brief.gig_id')) : null,
            'formats' => BriefComposer::FORMATS,
            'goals'   => BriefComposer::GOALS,
            'tones'   => BriefComposer::TONES,
            'seo' => [
                'title'       => 'Free AI brief builder — turn one line into a production brief | Quick GIGS',
                'description' => 'Describe your idea in one line and get a production-ready creative brief: objective, three hook options, a timed beat sheet, deliverables, spec and the QA checks your delivery must pass. Free, no signup.',
                'canonical'   => route('brief-builder'),
            ],
        ]);
    }

    public function generate(Request $request, BriefWriter $writer, MatchEngine $engine, BriefComposer $composer)
    {
        $input = $request->validate([
            'idea'     => ['required', 'string', 'min:10', 'max:400'],
            'format'   => ['required', 'string', 'in:' . implode(',', array_keys(BriefComposer::FORMATS))],
            'goal'     => ['required', 'string', 'in:' . implode(',', array_keys(BriefComposer::GOALS))],
            'tone'     => ['required', 'string', 'in:' . implode(',', array_keys(BriefComposer::TONES))],
            'audience' => ['nullable', 'string', 'max:160'],
            'urgency'  => ['required', 'string', 'in:express,standard,relaxed'],
        ], [
            'idea.min' => 'Give us a little more to work with — one full sentence is plenty.',
        ]);

        // Model-written when a key is configured, deterministic otherwise.
        $state = $writer->write($input);

        $this->store($request, $input, $state, $engine, $composer);

        if ($request->expectsJson()) {
            return $this->json($request, $state);
        }

        return redirect()->route('brief-builder')->withFragment('result');
    }

    /** Continue the same conversation — the model keeps its earlier reasoning. */
    public function refine(Request $request, BriefWriter $writer, MatchEngine $engine, BriefComposer $composer)
    {
        $data = $request->validate([
            'instruction' => ['required', 'string', 'min:3', 'max:300'],
        ]);

        $state = $request->session()->get('brief.state');
        $input = $request->session()->get('brief.input', []);

        if (! $state) {
            return redirect()->route('brief-builder');
        }

        $state = $writer->refine($state, $data['instruction']);

        $this->store($request, $input, $state, $engine, $composer);

        if ($request->expectsJson()) {
            return $this->json($request, $state);
        }

        return redirect()->route('brief-builder')->withFragment('result')
            ->with('toast', $state['refine_error'] ?? 'Brief updated.');
    }

    public function reset(Request $request)
    {
        $request->session()->forget(['brief.input', 'brief.state', 'brief.matches', 'brief.gig_id', 'brief.draft']);

        return $request->expectsJson()
            ? response()->json(['ok' => true])
            : redirect()->route('brief-builder');
    }

    /** Rendered brief for the in-page (no reload) flow. */
    private function json(Request $request, array $state)
    {
        $html = view('tools.partials.result', [
            'brief'   => $state['brief'],
            'meta'    => [
                'source'       => $state['source'] ?? 'rules',
                'model'        => $state['model'] ?? null,
                'error'        => $state['error'] ?? null,
                'latency_ms'   => $state['latency_ms'] ?? null,
                'refinable'    => ! empty($state['messages']),
                'refine_error' => $state['refine_error'] ?? null,
            ],
            'matches' => $request->session()->get('brief.matches', collect()),
            'gig'     => $request->session()->get('brief.gig_id') ? \App\Models\Service::find($request->session()->get('brief.gig_id')) : null,
        ])->render();

        return response()->json([
            'html' => $html,
            'meta' => [
                'source'       => $state['source'] ?? 'rules',
                'model'        => $state['model'] ?? null,
                'refinable'    => ! empty($state['messages']),
                'refine_error' => $state['refine_error'] ?? null,
            ],
        ]);
    }

    /* ───────────────────────── helpers ───────────────────────── */

    private function store(Request $request, array $input, array $state, MatchEngine $engine, BriefComposer $composer): void
    {
        $brief = $state['brief'];

        $matches = $engine->rank(
            Creator::where('is_verified', true)->get(),
            [
                'category' => $brief['suggested']['category'],
                'skills'   => $this->skillsFor($input['format'] ?? 'reel'),
                'budget'   => $brief['suggested']['price'],
                'urgency'  => $input['urgency'] ?? 'standard',
            ],
            3
        );

        $gig = Service::where('is_active', true)
            ->where('category', $brief['suggested']['category'])
            ->orderByRaw('abs(price - ?)', [$brief['suggested']['price']])
            ->first();

        $request->session()->put('brief.input', $input);
        $request->session()->put('brief.state', $state);
        $request->session()->put('brief.matches', $matches);
        $request->session()->put('brief.gig_id', $gig?->id);
        $request->session()->put('brief.draft', $composer->toText($brief));
    }

    private function skillsFor(string $format): array
    {
        return match ($format) {
            'thumbnail' => ['thumbnails', 'design', 'ctr'],
            'ugc'       => ['ugc', 'product', 'presenting'],
            'ai_ad'     => ['ai video', 'motion', 'voice over'],
            'podcast'   => ['long form', 'clips', 'captions'],
            'brandkit'  => ['design', 'branding', 'templates'],
            default     => ['reels', 'retention editing', 'captions'],
        };
    }
}
