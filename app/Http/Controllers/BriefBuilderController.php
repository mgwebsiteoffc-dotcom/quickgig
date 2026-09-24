<?php

namespace App\Http\Controllers;

use App\Models\Creator;
use App\Models\Service;
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

        return view('tools.brief-builder', [
            'brief'    => $request->filled('idea') ? null : $request->session()->get('brief.result'),
            'input'    => $input,
            'matches'  => $request->session()->get('brief.matches', collect()),
            'gig'      => $request->session()->get('brief.gig_id') ? Service::find($request->session()->get('brief.gig_id')) : null,
            'formats'  => BriefComposer::FORMATS,
            'goals'    => BriefComposer::GOALS,
            'tones'    => BriefComposer::TONES,
            'seo' => [
                'title'       => 'Free AI brief builder — turn one line into a production brief | Quick GIGS',
                'description' => 'Describe your idea in one line and get a production-ready creative brief: objective, three hook options, a timed beat sheet, deliverables, spec and the QA checks your delivery must pass. Free, no signup.',
                'canonical'   => route('brief-builder'),
            ],
        ]);
    }

    public function generate(Request $request, BriefComposer $composer, MatchEngine $engine)
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

        $brief = $composer->compose($input);

        // Rank real creators against the generated brief.
        $creators = Creator::where('is_verified', true)->get();
        $matches  = $engine->rank($creators, [
            'category' => $brief['suggested']['category'],
            'skills'   => $this->skillsFor($input['format']),
            'budget'   => $brief['suggested']['price'],
            'urgency'  => $input['urgency'],
        ], 3);

        // Closest orderable gig in the catalogue.
        $gig = Service::where('is_active', true)
            ->where('category', $brief['suggested']['category'])
            ->orderByRaw('abs(price - ?)', [$brief['suggested']['price']])
            ->first();

        $request->session()->put('brief.input', $input);
        $request->session()->put('brief.result', $brief);
        $request->session()->put('brief.matches', $matches);
        $request->session()->put('brief.gig_id', $gig?->id);
        $request->session()->put('brief.draft', $composer->toText($brief));

        return redirect()->route('brief-builder')->withFragment('result');
    }

    public function reset(Request $request)
    {
        $request->session()->forget(['brief.input', 'brief.result', 'brief.matches', 'brief.gig_id', 'brief.draft']);

        return redirect()->route('brief-builder');
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
