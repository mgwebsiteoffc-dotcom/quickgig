@extends('layouts.site')

@php
  // Raw factor data for the client-side re-ranking demo.
  $matchData = $ranked->map(fn ($r) => [
    'name'   => $r['creator']->name,
    'handle' => $r['creator']->handle,
    'img'    => $r['creator']->avatarUrl(),
    'role'   => $r['creator']->headline ?: $r['creator']->profileLabel(),
    'price'  => (int) $r['creator']->price_from,
    'online' => (bool) $r['creator']->is_available,
    'factors'=> collect($r['breakdown'])->map(fn ($b) => [
        'label' => $b['label'],
        'pct'   => $b['max'] > 0 ? round($b['points'] / $b['max'], 3) : 0,
        'reason'=> $b['reason'],
    ])->values(),
  ])->values();
@endphp

@section('content')

<section class="pt-16 pb-14">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[720px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">The engine</div>
      <h1 class="mt-4 font-display text-[38px] sm:text-[50px] font-semibold leading-[1.04]">
        Automation you can <span class="grad-text">audit</span>.
      </h1>
      <p class="mt-5 text-[16px] leading-7 text-mut max-w-[620px]">
        Everyone says "AI-powered". Here is exactly what ours does, what it scores, and what it refuses
        to let through — with three working demos on this page. Nothing here is a video of a product.
      </p>
    </div>

    <div class="mt-12 grid sm:grid-cols-3 gap-4">
      @foreach([
        ['01', 'Explainable matching', 'Five weighted factors, published. Re-rank them yourself below.'],
        ['02', 'Quality gate', 'Six automated checks run before a delivery can reach you.'],
        ['03', 'Revision translator', 'Vague feedback becomes timestamped editor instructions.'],
      ] as [$n, $t, $b])
        <a href="#m{{ $n }}" class="reveal glass rounded-3xl p-6 card-hover">
          <div class="font-mono text-[12px] text-mint-deep">{{ $n }}</div>
          <div class="mt-3 text-[16px] font-semibold">{{ $t }}</div>
          <div class="mt-2 text-[13px] leading-6 text-mut">{{ $b }}</div>
        </a>
      @endforeach
    </div>
  </div>
</section>

{{-- ── 01 · MATCH SCORE EXPLORER ── --}}
<section id="m01" class="py-16 border-y border-line scroll-mt-20">
  <div class="max-w-shell mx-auto px-5 lg:px-8" x-data="matchLab(@js($matchData), @js($weights))">
    <div class="flex flex-wrap items-end justify-between gap-6">
      <div class="max-w-[620px]">
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Module 01</div>
        <h2 class="mt-3 font-display text-[30px] sm:text-[38px] font-semibold leading-[1.1]">Explainable matching</h2>
        <p class="mt-4 text-[15px] leading-7 text-mut">
          These are real freelancers from the live marketplace, scored right now against a sample brief
          (short-form reel, express lane, ₹3,000 budget). Move the priorities and watch the ranking change —
          this is the same maths the platform runs when it assigns your gig.
        </p>
      </div>
      <button x-on:click="reset()" class="h-11 px-5 rounded-xl glass text-[13.5px] font-medium hover:border-line transition">Reset weights</button>
    </div>

    <div class="mt-10 grid lg:grid-cols-[320px_1fr] gap-6 items-start">
      {{-- weights --}}
      <div class="glass rounded-3xl p-6 lg:sticky lg:top-24">
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">What matters to you</div>
        <div class="mt-5 space-y-5">
          <template x-for="(w, key) in weights" :key="key">
            <div>
              <div class="flex items-center justify-between text-[13px]">
                <span class="capitalize" x-text="key === 'skill' ? 'skill fit' : key"></span>
                <span class="font-mono text-mut" x-text="w"></span>
              </div>
              <input type="range" min="0" max="40" step="1" x-model.number="weights[key]"
                     class="mt-2 w-full accent-violet cursor-pointer">
            </div>
          </template>
        </div>
        <div class="mt-6 pt-5 border-t border-line text-[12.5px] text-mut leading-6">
          Total weight <span class="font-mono text-body" x-text="totalWeight"></span>. Scores are normalised,
          so you can weight one factor to zero and the ranking still works.
        </div>
      </div>

      {{-- ranked freelancers --}}
      <div class="space-y-4">
        <template x-for="(m, i) in ranked" :key="m.handle">
          <div class="glass rounded-3xl p-5 sm:p-6" :class="i === 0 ? 'ring-glow' : ''">
            <div class="flex flex-wrap items-center gap-4">
              <div class="relative">
                <img :src="m.img" class="w-12 h-12 rounded-2xl object-cover border border-line" alt="">
                <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white" :class="m.online ? 'bg-mint' : 'bg-amber-400'"></span>
              </div>
              <div class="min-w-0 flex-1">
                <div class="flex items-center gap-2">
                  <span class="text-[15px] font-semibold" x-text="m.name"></span>
                  <span x-show="i === 0" class="text-[10.5px] font-semibold btn-grad text-ink rounded-full px-2 py-0.5">Top match</span>
                </div>
                <div class="text-[12.5px] text-mut truncate" x-text="m.role"></div>
              </div>
              <div class="text-right">
                <div class="font-display text-[26px] font-semibold tracking-tight" x-text="m.score"></div>
                <div class="text-[11px] text-mut">match score</div>
              </div>
            </div>

            <div class="mt-5 grid sm:grid-cols-2 gap-x-6 gap-y-3">
              <template x-for="f in m.factors" :key="f.label">
                <div>
                  <div class="flex items-center justify-between text-[12px]">
                    <span class="text-body" x-text="f.label"></span>
                    <span class="font-mono text-mut" x-text="Math.round(f.pct * 100) + '%'"></span>
                  </div>
                  <div class="mt-1.5 h-1.5 rounded-full bg-tint overflow-hidden">
                    <div class="h-full btn-grad transition-all duration-500" :style="`width:${f.pct * 100}%`"></div>
                  </div>
                  <div class="mt-1 text-[11.5px] text-mut leading-snug" x-text="f.reason"></div>
                </div>
              </template>
            </div>
          </div>
        </template>

        <div class="text-[12.5px] text-mut">
          Freelancers see the same breakdown on their side — which is how they know what to fix to win more gigs.
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── 02 · QA GATE ── --}}
<section id="m02" class="py-16 scroll-mt-20">
  <div class="max-w-shell mx-auto px-5 lg:px-8" x-data="qaGate()">
    <div class="grid lg:grid-cols-[0.95fr_1.05fr] gap-10 items-start">
      <div>
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Module 02</div>
        <h2 class="mt-3 font-display text-[30px] sm:text-[38px] font-semibold leading-[1.1]">The quality gate</h2>
        <p class="mt-4 text-[15px] leading-7 text-mut">
          A delivery cannot reach your inbox until it passes six automated checks derived from your brief.
          If one fails, it bounces straight back to the freelancer with the exact fix — you never see the bad cut,
          and you never have to write "the captions are missing" again.
        </p>

        <div class="mt-7 glass rounded-3xl p-5">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">What this replaces</div>
          <ul class="mt-3.5 space-y-2.5 text-[13.5px] text-mut">
            <li>• A reviewer watching every file by hand (slow, inconsistent)</li>
            <li>• You discovering a 4:5 export when you needed 9:16</li>
            <li>• Revision rounds spent on spec, not on creative</li>
          </ul>
        </div>

        <button x-on:click="run()" class="mt-7 h-12 px-6 rounded-xl btn-grad font-semibold text-[14.5px] inline-flex items-center gap-2 shadow-lg shadow-ink/10">
          <span x-show="!running && !done">Run the gate on a sample delivery</span>
          <span x-show="running" x-cloak>Checking…</span>
          <span x-show="done" x-cloak>Run again</span>
        </button>
      </div>

      <div class="glass-strong rounded-3xl p-6 sm:p-7">
        <div class="flex items-center justify-between">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">quick-gigs://qa/QC-4821</div>
          <div class="text-[12px] font-mono" :class="done ? (passed ? 'text-mint-deep' : 'text-amber-300') : 'text-mut'"
               x-text="done ? (passed ? 'PASSED' : 'BOUNCED') : (running ? 'RUNNING' : 'IDLE')"></div>
        </div>

        <div class="mt-5 space-y-2.5">
          <template x-for="(c, i) in checks" :key="c.name">
            <div class="flex items-start gap-3 rounded-2xl border px-4 py-3 transition-all duration-300"
                 :class="c.state === 'pass' ? 'border-mint/35 bg-mint/6' : (c.state === 'fail' ? 'border-amber-400/35 bg-amber-400/8' : 'border-line bg-tint')">
              <div class="w-6 h-6 rounded-lg grid place-items-center shrink-0 mt-0.5"
                   :class="c.state === 'pass' ? 'bg-mint-wash text-mint-deep' : (c.state === 'fail' ? 'bg-amber-400/20 text-amber-300' : 'bg-tint text-faint')">
                <template x-if="c.state === 'pass'"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></template>
                <template x-if="c.state === 'fail'"><span class="text-[12px] font-bold">!</span></template>
                <template x-if="c.state === 'idle'"><span class="text-[11px] font-mono" x-text="i+1"></span></template>
                <template x-if="c.state === 'running'"><span class="w-2 h-2 rounded-full bg-white/60 animate-ping"></span></template>
              </div>
              <div class="min-w-0">
                <div class="text-[13.5px] font-medium" x-text="c.name"></div>
                <div class="text-[12px] text-mut mt-0.5" x-text="c.state === 'fail' ? c.failNote : c.note"></div>
              </div>
            </div>
          </template>
        </div>

        <div x-show="done && !passed" x-cloak x-transition class="mt-5 rounded-2xl border border-amber-400/30 bg-amber-400/8 p-5">
          <div class="text-[13px] font-semibold text-amber-200">Bounced to the freelancer automatically</div>
          <p class="mt-1.5 text-[13px] leading-6 text-body">
            Caption coverage came back at 82%. The freelancer got the failing timestamps and a 40-minute window to
            re-upload before the SLA clock is affected. You were never interrupted.
          </p>
        </div>

        <div x-show="done && passed" x-cloak x-transition class="mt-5 rounded-2xl border border-mint/35 bg-mint/8 p-5">
          <div class="text-[13px] font-semibold text-mint-deep">All six checks passed — delivery released to the buyer</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── 03 · REVISION TRANSLATOR ── --}}
<section id="m03" class="py-16 border-y border-line scroll-mt-20">
  <div class="max-w-shell mx-auto px-5 lg:px-8" x-data="revisionLab()">
    <div class="max-w-[620px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Module 03</div>
      <h2 class="mt-3 font-display text-[30px] sm:text-[38px] font-semibold leading-[1.1]">Revision translator</h2>
      <p class="mt-4 text-[15px] leading-7 text-mut">
        Type feedback the way you actually say it. We turn it into instructions an editor can execute
        without a call — the single biggest reason second rounds go wrong.
      </p>
    </div>

    <div class="mt-10 grid lg:grid-cols-2 gap-5 items-start">
      <div class="glass rounded-3xl p-6">
        <label class="label" for="feedback">Your feedback</label>
        <textarea id="feedback" rows="4" class="field" x-model="text" x-on:input="translate()"
                  placeholder="e.g. make it punchier, the intro drags and the colours feel cheap"></textarea>

        <div class="mt-4 flex flex-wrap gap-2">
          <template x-for="s in samples" :key="s">
            <button type="button" x-on:click="text = s; translate()"
                    class="rounded-full border border-line px-3 py-1.5 text-[12px] text-mut hover:text-ink hover:border-mint transition"
                    x-text="s"></button>
          </template>
        </div>
      </div>

      <div class="glass-strong rounded-3xl p-6 min-h-[240px]">
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Editor-ready notes</div>

        <div x-show="notes.length === 0" x-cloak class="mt-6 text-[13.5px] text-mut">
          Notes will appear here as you type. Try one of the sample phrases.
        </div>

        <ol class="mt-5 space-y-3">
          <template x-for="(n, i) in notes" :key="i">
            <li class="flex gap-3">
              <span class="font-mono text-[11.5px] text-mint-deep shrink-0 mt-0.5" x-text="n.t"></span>
              <span class="text-[13.5px] leading-6 text-body" x-text="n.do"></span>
            </li>
          </template>
        </ol>

        <div x-show="notes.length" x-cloak class="mt-6 pt-5 border-t border-line flex items-center justify-between gap-4">
          <div class="text-[12.5px] text-mut">Sent to the freelancer with the delivery timeline attached.</div>
          <span class="text-[11.5px] font-semibold rounded-full bg-mint-wash text-mint-deep px-2.5 py-1 shrink-0">Round 1 of 2 free</span>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── model card / honesty ── --}}
<section class="band-light py-20">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="grid lg:grid-cols-[0.8fr_1.2fr] gap-10">
      <div>
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Straight answers</div>
        <h2 class="mt-3 font-display text-[28px] sm:text-[34px] font-semibold leading-[1.1]">What the engine is — and is not.</h2>
      </div>
      <div class="space-y-4">
        @foreach([
          ['Does a model write my video?', 'No. Humans make the work. The engine writes the brief, ranks the humans, checks the output against the spec and translates your feedback. AI video gigs are a category you can order — not something we secretly substitute.'],
          ['Is my brief used to train anything?', 'No. Briefs stay attached to your workspace and are shared only with the freelancer assigned to the gig.'],
          ['Can I override the match?', 'Always. The score is a recommendation — you can pick any available freelancer from the marketplace, or re-run matching with different priorities.'],
          ['What happens when the gate is wrong?', 'You can accept a bounced delivery manually, and freelancers can dispute a failed check. Every override is logged on the order.'],
        ] as [$q, $a])
          <div class="glass rounded-3xl p-6">
            <div class="text-[15px] font-semibold">{{ $q }}</div>
            <p class="mt-2 text-[13.5px] leading-6 text-mut">{{ $a }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

@include('partials.cta', [
  'eyebrow'   => 'See it on your own brief',
  'tone'      => 'cyan',
  'title'     => 'Run the engine on something you actually need this week.',
  'body'      => 'The brief builder is free, needs no account, and ends with three scored freelancers who can start today.',
  'primary'   => ['Open the brief builder', route('brief-builder')],
  'secondary' => ['Browse the marketplace', route('marketplace')],
])

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {

  /* 01 — live re-ranking with adjustable weights */
  Alpine.data('matchLab', (freelancers, baseWeights) => ({
    freelancers,
    base: { ...baseWeights },
    weights: { ...baseWeights },
    get totalWeight() { return Object.values(this.weights).reduce((a, b) => a + b, 0); },
    get ranked() {
      const keys = Object.keys(this.weights);
      const total = Math.max(1, this.totalWeight);
      return this.freelancers
        .map(c => {
          let s = 0;
          c.factors.forEach((f, i) => { s += (this.weights[keys[i]] || 0) * f.pct; });
          return { ...c, score: Math.min(99, Math.max(41, Math.round((s / total) * 100))) };
        })
        .sort((a, b) => b.score - a.score)
        .slice(0, 3);
    },
    reset() { this.weights = { ...this.base }; },
  }));

  /* 02 — QA gate simulation */
  Alpine.data('qaGate', () => ({
    running: false, done: false, passed: false, runs: 0,
    checks: [
      { name: 'Brief coverage',   note: 'All five beats present in the cut',        failNote: 'Beat 4 (proof) missing from the cut', state: 'idle' },
      { name: 'Hook timing',      note: 'Hook lands at 0:01.4 — inside 3 seconds',  failNote: 'Hook lands at 0:05.2 — too late',    state: 'idle' },
      { name: 'Aspect + length',  note: '1080×1920, 0:34 — matches the order',      failNote: 'Exported 4:5 instead of 9:16',       state: 'idle' },
      { name: 'Caption coverage', note: '99% of spoken words captioned',            failNote: 'Only 82% of speech has captions',    state: 'idle' },
      { name: 'Loudness',         note: 'Dialogue −15.2 LUFS, music ducked',        failNote: 'Music peaks over dialogue at 0:18',  state: 'idle' },
      { name: 'Rights clear',     note: 'Music and stock licensed for paid ads',    failNote: 'Unlicensed track detected',          state: 'idle' },
    ],
    run() {
      this.runs++;
      this.running = true; this.done = false;
      this.checks.forEach(c => c.state = 'idle');
      // First run shows a realistic bounce, later runs pass.
      const failIndex = this.runs === 1 ? 3 : -1;

      this.checks.forEach((c, i) => {
        setTimeout(() => { c.state = 'running'; }, i * 420);
        setTimeout(() => { c.state = (i === failIndex) ? 'fail' : 'pass'; }, i * 420 + 360);
      });

      setTimeout(() => {
        this.running = false; this.done = true;
        this.passed = failIndex === -1;
      }, this.checks.length * 420 + 420);
    },
  }));

  /* 03 — feedback → editor notes */
  Alpine.data('revisionLab', () => ({
    text: '',
    notes: [],
    samples: ['make it punchier', 'the intro drags', 'colours feel cheap', 'add more energy', 'too salesy', 'music is loud'],
    rules: [
      { match: /punch|snappy|tight/i,            t: '0:00–0:32', do: 'Trim 0.2–0.4s off every cut, remove all breath pauses, and shorten each on-screen line to six words.' },
      { match: /intro|slow start|drags|boring/i, t: '0:00–0:05', do: 'Cut the first sentence entirely and start on the strongest claim. Hook must land before 0:03.' },
      { match: /colou?r|grade|cheap|flat/i,      t: 'Whole edit', do: 'Apply a single consistent LUT, lift shadows by 6%, and remove the saturation boost on skin tones.' },
      { match: /energy|boring|dull|flat/i,       t: '0:05–0:20', do: 'Add beat-synced cuts on the music, two zoom punch-ins on key claims, and a 3-frame whip transition at the midpoint.' },
      { match: /sales|pushy|salesy|ad/i,         t: '0:20–0:32', do: 'Replace the second call to action with a demonstration frame. Keep one CTA, spoken not captioned.' },
      { match: /music|loud|audio|sound/i,        t: 'Audio bed', do: 'Duck music to −22 LUFS under dialogue, fade in over 0.5s, and normalise voice to −15 LUFS.' },
      { match: /caption|subtitle|text/i,         t: 'Captions',  do: 'Two words per frame maximum, 1.2 line height, keyword highlighted in the accent colour.' },
      { match: /long|shorter|cut it down/i,      t: 'Duration',  do: 'Target 24 seconds: drop the setup shot and compress the middle section by 30%.' },
      { match: /logo|brand/i,                    t: 'Branding',  do: 'Move the logo to a 1.5s end card only. No watermark during the hook.' },
    ],
    translate() {
      const t = this.text;
      if (!t.trim()) { this.notes = []; return; }
      const hits = this.rules.filter(r => r.match.test(t)).map(r => ({ t: r.t, do: r.do }));
      this.notes = hits.length ? hits : [{
        t: 'General',
        do: 'Noted verbatim for the freelancer, with a request to confirm the change in writing before the re-cut starts.',
      }];
    },
  }));
});
</script>
@endpush
