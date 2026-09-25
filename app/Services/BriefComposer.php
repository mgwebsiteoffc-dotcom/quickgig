<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Quick GIGS Brief Engine.
 *
 * Turns a one-line idea into a production-ready brief: objective, hook options,
 * a timed beat sheet, deliverables, technical spec and the QA gate the delivery
 * will be checked against.
 *
 * The composition is deterministic (rule + template based) so it works offline,
 * costs nothing per run and always returns the same brief for the same input.
 * `compose()` is the single extension point if you later want to hand the draft
 * to an LLM for rewriting — keep the returned array shape identical.
 */
class BriefComposer
{
    public const FORMATS = [
        'reel' => [
            'label'    => 'Short-form reel',
            'category' => 'Reel',
            'price'    => 2499,
            'length'   => '30–45 seconds',
            'aspect'   => '9:16 vertical (1080×1920)',
        ],
        'ugc' => [
            'label'    => 'UGC product video',
            'category' => 'UGC Video',
            'price'    => 3999,
            'length'   => '30 seconds',
            'aspect'   => '9:16 vertical + 1:1 crop',
        ],
        'thumbnail' => [
            'label'    => 'Thumbnail pack',
            'category' => 'Thumbnail',
            'price'    => 1299,
            'length'   => '3 variants',
            'aspect'   => '1280×720 (16:9)',
        ],
        'ai_ad' => [
            'label'    => 'AI video ad',
            'category' => 'AI Video',
            'price'    => 6499,
            'length'   => '30 seconds',
            'aspect'   => '9:16 + 1:1 + 16:9 exports',
        ],
        'podcast' => [
            'label'    => 'Podcast clip pack',
            'category' => 'Reel',
            'price'    => 4499,
            'length'   => '5 clips, 45–60 seconds each',
            'aspect'   => '9:16 vertical',
        ],
        'brandkit' => [
            'label'    => 'Brand + social kit',
            'category' => 'Bundle',
            'price'    => 7499,
            'length'   => 'Logo, palette, 10 templates',
            'aspect'   => 'Editable source files',
        ],
    ];

    public const GOALS = [
        'awareness'  => 'reach new people who have never heard of you',
        'conversion' => 'turn attention into purchases',
        'launch'     => 'land a launch with maximum first-week impact',
        'trust'      => 'build credibility with proof and demonstrations',
        'retention'  => 'keep existing customers engaged and buying again',
    ];

    public const TONES = [
        'confident' => ['confident and direct', 'no fluff, straight claims, punchy delivery'],
        'friendly'  => ['warm and conversational', 'talk like a helpful friend, contractions, light humour'],
        'premium'   => ['premium and restrained', 'slower pacing, cinematic grade, minimal text'],
        'playful'   => ['playful and fast', 'quick cuts, meme-literate, high energy'],
        'expert'    => ['expert and analytical', 'data on screen, precise language, credible sourcing'],
    ];

    /** @return array<string,mixed> */
    public function compose(array $input): array
    {
        $idea     = trim($input['idea'] ?? '');
        $format   = array_key_exists($input['format'] ?? '', self::FORMATS) ? $input['format'] : 'reel';
        $goal     = array_key_exists($input['goal'] ?? '', self::GOALS) ? $input['goal'] : 'conversion';
        $tone     = array_key_exists($input['tone'] ?? '', self::TONES) ? $input['tone'] : 'confident';
        $audience = trim($input['audience'] ?? '') ?: 'people who already follow the category but have not bought yet';
        $product  = $this->subject($idea);
        $urgency  = $input['urgency'] ?? 'standard';

        $spec  = self::FORMATS[$format];
        $toneL = self::TONES[$tone];

        return [
            'title'        => $this->title($product, $spec['label']),
            'summary'      => $this->summary($product, $goal, $audience, $toneL[0]),
            'objective'    => 'Primary objective: ' . self::GOALS[$goal] . '.',
            'audience'     => $audience,
            'tone'         => $toneL[0] . ' — ' . $toneL[1],
            'hooks'        => $this->hooks($product, $goal, $tone),
            'beats'        => $this->beats($format, $product, $goal),
            'deliverables' => $this->deliverables($format),
            'spec'         => [
                'Format'      => $spec['label'],
                'Length'      => $spec['length'],
                'Aspect'      => $spec['aspect'],
                'Captions'    => $format === 'thumbnail' ? 'Not applicable' : 'Burned-in, max 2 words per frame',
                'Music'       => $format === 'thumbnail' ? 'Not applicable' : 'Licensed bed at −18 LUFS under voice',
                'Source files'=> 'Included on request (+₹699)',
            ],
            'avoid'        => $this->avoid($goal, $format),
            'qa_gate'      => $this->qaGate($format),
            'suggested'    => [
                'category' => $spec['category'],
                'format'   => $format,
                'lane'     => $urgency,
                'price'    => (int) round($spec['price'] * $this->laneMultiplier($urgency)),
                'eta'      => $this->eta($urgency),
            ],
            'confidence'   => $this->confidence($idea, $audience),
        ];
    }

    /** Plain-text version used for copy-to-clipboard and for prefilling an order. */
    public function toText(array $brief): string
    {
        $lines = [];
        $lines[] = $brief['title'];
        $lines[] = '';
        $lines[] = $brief['summary'];
        $lines[] = '';
        $lines[] = 'OBJECTIVE: ' . $brief['objective'];
        $lines[] = 'AUDIENCE: ' . $brief['audience'];
        $lines[] = 'TONE: ' . $brief['tone'];
        $lines[] = '';
        $lines[] = 'HOOK OPTIONS (pick one, test the others):';
        foreach ($brief['hooks'] as $i => $h) {
            $lines[] = '  ' . ($i + 1) . '. ' . $h;
        }
        $lines[] = '';
        $lines[] = 'BEAT SHEET:';
        foreach ($brief['beats'] as $b) {
            $lines[] = '  ' . $b['t'] . ' — ' . $b['what'];
        }
        $lines[] = '';
        $lines[] = 'DELIVERABLES:';
        foreach ($brief['deliverables'] as $d) {
            $lines[] = '  • ' . $d;
        }
        $lines[] = '';
        $lines[] = 'SPEC:';
        foreach ($brief['spec'] as $k => $v) {
            $lines[] = '  ' . $k . ': ' . $v;
        }
        $lines[] = '';
        $lines[] = 'DO NOT:';
        foreach ($brief['avoid'] as $a) {
            $lines[] = '  • ' . $a;
        }

        return implode("\n", $lines);
    }

    /* ───────────────────────── internals ───────────────────────── */

    /** Pull a short, usable subject out of the sentence the user typed. */
    private function subject(string $idea): string
    {
        if ($idea === '') return 'your product';

        $clean = Str::of($idea)->squish()->lower()
            // drop the "I want to make a ..." preamble
            ->replaceMatches('/^(i|we)\s+(want|need|would like)\s+(to\s+)?/', '')
            ->replaceMatches('/^(please\s+)?(make|create|build|produce|shoot|design|edit|cut)\s+/', '')
            ->replaceMatches('/^(a|an|the)\s+/', '')
            // drop the format noun so the subject is the thing, not the deliverable
            ->replaceMatches('/^(short[- ]?form\s+)?(reel|video|short|ad|advert|advertisement|thumbnail|clip|ugc|brand kit|logo|design|film)s?\s+/', '')
            ->replaceMatches('/^(about|for|announcing|promoting|explaining|showing|on|featuring)\s+/', '')
            ->__toString();

        // cut at the first natural break so we keep the noun phrase only
        $clean = preg_split('/,|\bwith\b|\bshot\b|\bfeaturing\b|\bthat\b|\bwhich\b|\bso that\b/', $clean)[0] ?? $clean;
        $clean = trim($clean, " .;:-");

        if ($clean === '') return 'your product';

        return (string) Str::of($clean)->words(8, '');
    }

    private function title(string $product, string $formatLabel): string
    {
        return Str::ucfirst($formatLabel) . ' — ' . Str::ucfirst($product);
    }

    private function summary(string $product, string $goal, string $audience, string $tone): string
    {
        return sprintf(
            'We are making a %s piece about %s for %s. The edit should feel %s and earn the next three seconds at every cut.',
            self::GOALS[$goal] === '' ? 'performance' : Str::before(self::GOALS[$goal], ' '),
            $product,
            $audience,
            $tone
        );
    }

    /** Three testable hook angles — problem, proof and pattern-interrupt. */
    private function hooks(string $product, string $goal, string $tone): array
    {
        $p = $product;

        $sets = [
            'awareness' => [
                "Nobody talks about this part of {$p} — so we filmed it.",
                "We tried {$p} for 30 days. Here is the honest result.",
                "If you scroll past this, you will keep doing it the slow way.",
            ],
            'conversion' => [
                "Stop overpaying for {$p}. Here is the 30-second version.",
                "Three reasons people buy {$p} — number two changed our numbers.",
                "This costs less than your last mistake with {$p}.",
            ],
            'launch' => [
                "It is live: {$p}. Here is what changed.",
                "We rebuilt {$p} from scratch. Watch the first 10 seconds.",
                "Day one of {$p} — and we are already sold out of the first batch.",
            ],
            'trust' => [
                "Here is {$p} tested on camera, unedited take included.",
                "Our customers asked the same question about {$p}. Answering it properly.",
                "Receipts first: what {$p} actually delivered last month.",
            ],
            'retention' => [
                "You already own {$p}. You are probably missing this feature.",
                "The three-minute setup that doubles what {$p} does for you.",
                "Before you cancel {$p}, try this once.",
            ],
        ];

        $hooks = $sets[$goal] ?? $sets['conversion'];

        if ($tone === 'premium') {
            $hooks[0] = Str::of($hooks[0])->replace('Stop overpaying for', 'A quieter way to think about')->__toString();
        }

        return $hooks;
    }

    private function beats(string $format, string $product, string $goal): array
    {
        return match ($format) {
            'thumbnail' => [
                ['t' => 'Variant A', 'what' => 'Face-led: subject at 30% of frame, eye-line pointing at the text block.'],
                ['t' => 'Variant B', 'what' => 'Text-led: three words maximum, 120pt, heavy stroke, single accent colour.'],
                ['t' => 'Variant C', 'what' => 'Contrast-led: product isolated on a flat background with a 2px outline.'],
                ['t' => 'All',       'what' => 'Readable at 120px wide. No more than two competing focal points.'],
            ],
            'brandkit' => [
                ['t' => 'Stage 1', 'what' => 'Primary logo, horizontal lockup and monogram.'],
                ['t' => 'Stage 2', 'what' => 'Colour palette with accessible contrast pairs and a type scale.'],
                ['t' => 'Stage 3', 'what' => 'Ten editable social templates covering launch, quote, offer and carousel.'],
                ['t' => 'Stage 4', 'what' => 'One-page usage guide so the look survives contact with other designers.'],
            ],
            'podcast' => [
                ['t' => 'Pass 1', 'what' => 'Scan the episode for five self-contained moments with a clear payoff.'],
                ['t' => 'Pass 2', 'what' => 'Cut each clip to a hook in the first 2 seconds and a closing line.'],
                ['t' => 'Pass 3', 'what' => 'Burn captions, add speaker labels, remove filler words and dead air.'],
                ['t' => 'Pass 4', 'what' => 'Export five vertical clips plus one 16:9 teaser for YouTube.'],
            ],
            default => [
                ['t' => '0:00–0:03', 'what' => 'Hook on screen and in voice at the same time. No logo, no intro.'],
                ['t' => '0:03–0:08', 'what' => 'Name the problem in the audience\'s own words. One sentence.'],
                ['t' => '0:08–0:18', 'what' => 'Show ' . $product . ' doing the thing. B-roll on every claim, no talking over static frames.'],
                ['t' => '0:18–0:26', 'what' => $goal === 'trust' ? 'Proof: numbers, reviews or an unedited demo take.' : 'The single strongest reason to act, stated plainly.'],
                ['t' => '0:26–0:32', 'what' => 'Call to action in voice and text. End on the product, not on a logo sting.'],
            ],
        };
    }

    private function deliverables(string $format): array
    {
        return match ($format) {
            'thumbnail' => ['3 thumbnail variants (PNG, 1280×720)', 'Editable source file', 'A/B test naming convention', '2 free revisions'],
            'brandkit'  => ['Logo suite (SVG + PNG)', 'Colour + type system', '10 editable social templates', 'One-page usage guide', '2 free revisions'],
            'podcast'   => ['5 vertical clips with captions', '1 teaser for 16:9', 'SRT caption files', '2 free revisions'],
            'ai_ad'     => ['Master 9:16 export', '1:1 and 16:9 crops', 'Voice-over track', 'Caption file', '2 free revisions'],
            'ugc'       => ['30-second vertical master', 'Hook variants (3 openings)', 'Raw unedited take', '2 free revisions'],
            default     => ['Master 9:16 export (1080×1920)', '3 alternate hook openings', 'Burned-in captions + SRT file', '2 free revisions'],
        };
    }

    private function avoid(string $goal, string $format): array
    {
        $base = [
            'No intro animation, logo sting or "hi guys" opening.',
            'No stock footage that contradicts the product on screen.',
            'No claim on screen that we cannot back up.',
        ];

        if ($format !== 'thumbnail') {
            $base[] = 'No music louder than the voice at any point.';
        }

        if ($goal === 'conversion') {
            $base[] = 'No more than one call to action — a second one splits the click.';
        }

        return $base;
    }

    /** The automated checks the delivery is scored against before it reaches the client. */
    private function qaGate(string $format): array
    {
        $common = [
            ['check' => 'Brief coverage', 'detail' => 'Every beat in the sheet appears in the delivery.'],
            ['check' => 'Spec match',     'detail' => 'Resolution, aspect ratio and length match the order.'],
            ['check' => 'Rights clear',   'detail' => 'Music and footage are licensed for paid use.'],
        ];

        if ($format === 'thumbnail' || $format === 'brandkit') {
            return array_merge($common, [
                ['check' => 'Legibility', 'detail' => 'Readable at 120px wide with sufficient contrast.'],
                ['check' => 'Source files', 'detail' => 'Editable file included and correctly layered.'],
            ]);
        }

        return array_merge($common, [
            ['check' => 'Hook timing',   'detail' => 'The hook lands inside the first 3 seconds.'],
            ['check' => 'Caption cover', 'detail' => 'Captions cover at least 95% of spoken words.'],
            ['check' => 'Loudness',      'detail' => 'Dialogue sits between −16 and −14 LUFS; music ducks under it.'],
        ]);
    }

    private function laneMultiplier(string $urgency): float
    {
        return match ($urgency) {
            'express' => 1.6,
            'relaxed' => 0.85,
            default   => 1.0,
        };
    }

    private function eta(string $urgency): string
    {
        return match ($urgency) {
            'express' => 'about 3 hours',
            'relaxed' => 'about 48 hours',
            default   => 'about 24 hours',
        };
    }

    /** How complete the input was — shown to the user so they know what to improve. */
    private function confidence(string $idea, string $audience): array
    {
        $score = 55;
        if (Str::length($idea) > 25)  $score += 15;
        if (Str::length($idea) > 60)  $score += 10;
        if (Str::length($audience) > 12) $score += 12;
        if (preg_match('/\d/', $idea)) $score += 8;

        $score = min(98, $score);

        $tips = [];
        if (Str::length($idea) < 60)      $tips[] = 'Add one concrete detail — a number, a feature name or a customer quote.';
        if (Str::length($audience) < 12)  $tips[] = 'Describe who this is for; it changes the hook more than anything else.';
        if (! preg_match('/\d/', $idea))  $tips[] = 'Numbers earn attention: price, days saved, percentage lift.';
        if (empty($tips))                 $tips[] = 'Strong input — this brief is ready to send to a freelancer.';

        return ['score' => $score, 'tips' => $tips];
    }
}
