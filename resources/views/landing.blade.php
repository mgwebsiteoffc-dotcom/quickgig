@extends('layouts.site')

@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="relative pt-14 sm:pt-20 pb-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[1.05fr_0.95fr] gap-14 items-center">

    <div class="reveal-l">
      <div class="inline-flex items-center gap-2.5 glass rounded-full pl-2 pr-3.5 py-1.5 text-[12px] font-medium">
        <span class="btn-grad text-white text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-full">New</span>
        <span class="text-white/75">Matching engine v3 — average match time {{ $stats['match_time'] }}</span>
      </div>

      <h1 class="mt-6 font-display text-[40px] sm:text-[58px] leading-[1.02] font-semibold">
        Hire a verified pro<br>
        <span class="grad-text">in minutes</span>, not weeks.
      </h1>

      <p class="mt-5 text-[16.5px] leading-7 text-mut max-w-[540px]">
        Quick GIGS writes the brief, matches the right creator automatically and holds your money in escrow
        until you approve. Reels, thumbnails, UGC, AI ads and design.
      </p>

      <div class="mt-8 flex flex-wrap items-center gap-3">
        <a href="{{ route('register') }}?type=business" class="h-12 px-6 rounded-xl btn-grad font-semibold text-[14.5px] inline-flex items-center gap-2 shadow-xl shadow-pink/20">
          Post a gig — it's free
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a href="#demo" class="h-12 px-6 rounded-xl glass btn-ghost font-medium text-[14.5px] inline-flex items-center gap-2 hover:border-white/30">
          <span class="w-7 h-7 rounded-full bg-gradient-to-br from-violet to-pink grid place-items-center">
            <svg width="11" height="11" viewBox="0 0 24 24" fill="#fff"><path d="M8 5v14l11-7z"/></svg>
          </span>
          Watch it run
        </a>
      </div>

      <div class="mt-4 text-[13px] text-mut">
        Not sure what to ask for?
        <a href="{{ route('brief-builder') }}" class="text-white font-medium hover:text-pink-soft transition underline decoration-white/20 underline-offset-4">Write a free brief in 40 seconds</a>
      </div>

      <div class="mt-9 flex flex-wrap items-center gap-x-8 gap-y-4">
        @foreach([
          ['4', ' min', 'Average match time', 0],
          ['18400', '+', 'Gigs delivered', 0],
          ['4.9', '/5', 'Client rating', 1],
          ['100', '%', 'Escrow protected', 0],
        ] as $i => [$value, $suffix, $label, $dec])
          <div class="reveal" data-delay="{{ 120 + $i * 80 }}">
            <div class="font-display text-[22px] font-semibold tracking-tight">
              <span data-count="{{ $value }}" data-suffix="{{ $suffix }}" data-decimals="{{ $dec }}">0</span>
            </div>
            <div class="text-[12px] text-mut mt-0.5">{{ $label }}</div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- live order card --}}
    <div class="relative reveal-s" x-data="liveOrder()" x-init="start()">
      <div class="absolute -inset-8 bg-gradient-to-br from-violet/25 via-pink/20 to-amber/10 blur-3xl rounded-full -z-10"></div>

      <div class="glass-strong rounded-3xl p-5 sm:p-6 ring-glow">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2 text-[11px] font-semibold tracking-[.14em] uppercase text-white/50">
            <span class="w-1.5 h-1.5 rounded-full bg-lime pulse-dot text-lime"></span> Live order
          </div>
          <span class="text-[11.5px] font-mono text-white/45">#QG-{{ $liveOrderId }}</span>
        </div>

        <div class="mt-4 flex items-start gap-3.5">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet to-pink grid place-items-center shrink-0 animate-floaty">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.7"><rect x="2" y="5" width="14" height="14" rx="3"/><path d="m16 11 6-4v10l-6-4z"/></svg>
          </div>
          <div class="min-w-0">
            <div class="font-display text-[16px] font-semibold leading-tight">Retention reel · 45 sec</div>
            <div class="text-[12.5px] text-mut mt-1">Express lane · ₹2,499 in escrow</div>
          </div>
        </div>

        <div class="mt-6 space-y-3">
          <template x-for="(s, i) in steps" :key="i">
            <div class="flex items-center gap-3 transition-all duration-500" :class="i <= active ? 'opacity-100' : 'opacity-45'">
              <div class="w-7 h-7 rounded-full grid place-items-center shrink-0 transition-all duration-500"
                   :class="i < active ? 'bg-lime/20 text-lime' : (i === active ? 'btn-grad text-white scale-110' : 'bg-white/6 text-white/30')">
                <template x-if="i < active">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                </template>
                <template x-if="i >= active"><span class="text-[11px] font-semibold font-mono" x-text="i+1"></span></template>
              </div>
              <div class="flex-1 min-w-0 text-[13.5px] font-medium transition-colors" :class="i <= active ? 'text-white' : 'text-white/35'" x-text="s.label"></div>
              <div class="text-[11.5px] font-mono shrink-0" :class="i <= active ? 'text-white/55' : 'text-white/20'" x-text="s.time"></div>
            </div>
          </template>
        </div>

        <div class="mt-5 h-1.5 rounded-full bg-white/8 overflow-hidden">
          <div class="h-full btn-grad transition-all duration-[900ms] ease-out" :style="`width:${progress}%`"></div>
        </div>

        <div class="mt-5 flex items-center justify-between glass rounded-2xl px-4 py-3">
          <div class="flex items-center gap-2.5">
            <img src="https://i.pravatar.cc/80?img=5" class="w-8 h-8 rounded-full object-cover border-2 border-pink/40" alt="">
            <div>
              <div class="text-[13px] font-semibold">Priya S.</div>
              <div class="text-[11.5px] text-mut">Verified editor · 4.9 ★</div>
            </div>
          </div>
          <div class="text-right">
            <div class="text-[11px] text-mut">Escrow</div>
            <div class="text-[13px] font-semibold font-mono transition-colors" :class="active >= steps.length ? 'text-lime' : 'text-white'" x-text="escrow"></div>
          </div>
        </div>
      </div>

      <div class="absolute -bottom-4 -left-4 hidden sm:flex glass-strong rounded-2xl px-3.5 py-2.5 items-center gap-2.5 shadow-xl animate-floaty" style="animation-delay:-3s">
        <span class="w-7 h-7 rounded-lg bg-lime/20 grid place-items-center">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A3E635" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>
        </span>
        <div class="text-[12px] leading-tight"><b class="font-semibold">Zero</b> bidding wars<div class="text-mut text-[11px]">You never chase freelancers</div></div>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════ LOGOS ═══════════════ --}}
<section class="py-8 border-y border-white/8 overflow-hidden">
  <div class="max-w-shell mx-auto px-5 lg:px-8 mb-5 text-center text-[11px] font-semibold tracking-[.16em] uppercase text-white/35">
    Trusted by fast-moving teams
  </div>
  <div class="marquee">
    @foreach(array_merge($logos, $logos) as $i => $logo)
      <span class="font-display text-[17px] font-medium whitespace-nowrap {{ ['text-violet-soft/50','text-pink-soft/50','text-amber-soft/50','text-teal/50','text-cyan/50'][$i % 5] }}">{{ $logo }}</span>
    @endforeach
  </div>
</section>

{{-- ═══════════════ AUDIENCES (light) ═══════════════ --}}
<section class="band-light py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px] reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-deep">Who it's for</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1] text-deep">Built for people who publish every week.</h2>
      <p class="mt-4 text-[15px] leading-7 text-mut">Four kinds of teams run most of the gigs on Quick GIGS. Find yours.</p>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-5">
      @php
        $tones = [
          'violet' => ['bg-violet/12','text-violet-deep','from-violet to-violet-deep'],
          'pink'   => ['bg-pink/12','text-pink','from-pink to-pink-soft'],
          'amber'  => ['bg-amber/15','text-[#B26A00]','from-amber to-amber-soft'],
          'teal'   => ['bg-teal/12','text-[#0E8A79]','from-teal to-cyan'],
        ];
      @endphp
      @foreach($audiences as $i => $a)
        @php [$chipBg, $chipText, $grad] = $tones[$a['tone']]; @endphp
        <div class="reveal glass rounded-3xl p-6 card-hover" data-delay="{{ $i * 90 }}">
          <span class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $grad }} grid place-items-center text-white shadow-lg">{!! $a['icon'] !!}</span>
          <div class="mt-5 font-display text-[18px] font-semibold text-deep">{{ $a['label'] }}</div>
          <p class="mt-2 text-[13.5px] leading-6 text-mut">{{ $a['body'] }}</p>
          <div class="mt-5 pt-4 border-t border-white/10 flex items-baseline gap-2">
            <span class="font-display text-[24px] font-semibold {{ $chipText }}">{{ $a['stat'] }}</span>
            <span class="text-[12px] text-mut">{{ $a['statLabel'] }}</span>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════ HOW IT WORKS ═══════════════ --}}
<section id="how" class="py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px] reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-pink-soft">How it works</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1]">Three steps. No calls, no bidding, no chasing.</h2>
    </div>

    <div class="mt-14 grid md:grid-cols-3 gap-5">
      @foreach($steps as $i => $step)
        @php $accent = ['from-violet to-violet-deep','from-pink to-amber','from-teal to-cyan'][$i]; @endphp
        <div class="reveal glass rounded-3xl p-7 card-hover" data-delay="{{ $i * 110 }}">
          <div class="flex items-center justify-between">
            <span class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $accent }} grid place-items-center text-white shadow-lg">{!! $step['icon'] !!}</span>
            <span class="font-display text-[34px] font-semibold text-white/8">0{{ $i+1 }}</span>
          </div>
          <div class="mt-5 font-display text-[18px] font-semibold">{{ $step['title'] }}</div>
          <p class="mt-2.5 text-[14px] leading-6 text-mut">{{ $step['body'] }}</p>
          <div class="mt-5 inline-flex items-center gap-2 text-[12px] font-medium text-white/70 glass rounded-full px-3 py-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-lime"></span>{{ $step['meta'] }}
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════ USP GRID ═══════════════ --}}
<section class="py-24 border-t border-white/8">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-6 reveal">
      <div class="max-w-[620px]">
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-cyan">Why this is different</div>
        <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1]">Software does the admin. People do the craft.</h2>
        <p class="mt-4 text-[15px] leading-7 text-mut">Six things that run automatically on every gig — and that you can inspect, override or switch off.</p>
      </div>
      <a href="{{ route('ai') }}" class="h-11 px-5 rounded-xl glass btn-ghost inline-flex items-center gap-2 text-[13.5px] font-medium hover:border-white/25">
        Open the engine
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </a>
    </div>

    <div class="mt-12 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
      @php $uspGrads = ['from-violet to-violet-deep','from-pink to-pink-soft','from-lime to-teal','from-amber to-pink','from-cyan to-violet','from-teal to-lime']; @endphp
      @foreach($usps as $i => $u)
        <a href="{{ $u['href'] }}" class="reveal glass rounded-3xl p-6 card-hover group" data-delay="{{ $i * 70 }}">
          <div class="flex items-start justify-between">
            <span class="w-12 h-12 rounded-2xl bg-gradient-to-br {{ $uspGrads[$i % 6] }} grid place-items-center text-white shadow-lg group-hover:scale-105 transition-transform duration-300">{!! $u['icon'] !!}</span>
            <span class="text-[10.5px] font-semibold tracking-wider uppercase rounded-full px-2.5 py-1 {{ $u['tagTone'] }}">{{ $u['tag'] }}</span>
          </div>
          <div class="mt-5 font-display text-[17px] font-semibold group-hover:text-pink-soft transition">{{ $u['title'] }}</div>
          <p class="mt-2 text-[13.5px] leading-6 text-mut">{{ $u['body'] }}</p>
          <div class="mt-4 text-[12.5px] text-white/55 group-hover:text-white transition">{{ $u['cta'] }} →</div>
        </a>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════ DEMO SIMULATION (auto-plays in view) ═══════════════ --}}
<section id="demo" class="py-24 border-y border-white/8 relative overflow-hidden">
  <div class="absolute inset-0 -z-10 bg-gradient-to-br from-violet/[0.10] via-pink/[0.06] to-transparent"></div>
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-6 reveal">
      <div class="max-w-[620px]">
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-amber-soft">Live simulation</div>
        <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1]">Build a gig. Watch it get delivered.</h2>
        <p class="mt-4 text-[15px] leading-7 text-mut">It starts on its own when you scroll here. Change anything and it replays instantly — same pricing, same pipeline, same escrow rules as the live platform. You never leave this page.</p>
      </div>
      <div class="glass rounded-2xl px-4 py-3 text-[12px] text-mut">Simulation only · nothing is charged</div>
    </div>

    <div class="mt-12 grid lg:grid-cols-[0.95fr_1.05fr] gap-5" x-data="gigSim(@js($sim))" x-intersect.once="autoplay()">

      {{-- configurator --}}
      <div class="glass rounded-3xl p-6 sm:p-7">
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">1 · What do you need?</div>
        <div class="mt-4 grid grid-cols-2 gap-2.5">
          <template x-for="(c, ci) in cfg.categories" :key="c.id">
            <button type="button" x-on:click="pick('category', c.id)"
              class="text-left rounded-2xl p-3.5 border transition-all duration-300"
              :class="category === c.id ? 'border-pink bg-pink/12 scale-[1.02]' : 'border-white/10 bg-white/3 hover:border-white/30'">
              <div class="text-[13.5px] font-semibold" x-text="c.label"></div>
              <div class="text-[11.5px] text-mut mt-0.5" x-text="'from ₹' + c.base.toLocaleString('en-IN')"></div>
            </button>
          </template>
        </div>

        <div class="mt-7 text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">2 · How fast?</div>
        <div class="mt-4 space-y-2.5">
          <template x-for="s in cfg.speeds" :key="s.id">
            <button type="button" x-on:click="pick('speed', s.id)"
              class="w-full flex items-center justify-between rounded-2xl px-4 py-3 border transition-all duration-300"
              :class="speed === s.id ? 'border-teal bg-teal/12' : 'border-white/10 bg-white/3 hover:border-white/30'">
              <span class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full transition-colors" :class="speed === s.id ? 'bg-teal' : 'bg-white/20'"></span>
                <span class="text-[13.5px] font-medium" x-text="s.label"></span>
              </span>
              <span class="text-[12px] font-mono" :class="s.mult > 1 ? 'text-pink-soft' : 'text-mut'" x-text="s.mult > 1 ? '+' + Math.round((s.mult-1)*100) + '%' : 'included'"></span>
            </button>
          </template>
        </div>

        <div class="mt-7 text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">3 · Add-ons</div>
        <div class="mt-4 flex flex-wrap gap-2">
          <template x-for="a in cfg.addons" :key="a.id">
            <button type="button" x-on:click="toggleAddon(a.id)"
              class="rounded-full px-3.5 py-2 text-[12.5px] font-medium border transition-all duration-300"
              :class="addons.includes(a.id) ? 'border-amber bg-amber/15 text-white' : 'border-white/10 text-mut hover:text-white hover:border-white/30'">
              <span x-text="a.label"></span> <span class="font-mono opacity-60" x-text="'+₹' + a.price"></span>
            </button>
          </template>
        </div>

        <div class="mt-7 rounded-2xl border border-white/10 bg-black/25 p-5">
          <div class="flex items-baseline justify-between">
            <span class="text-[13px] text-mut">Gig total</span>
            <span class="font-display text-[30px] font-semibold tracking-tight transition-all duration-300" x-text="'₹' + total.toLocaleString('en-IN')"></span>
          </div>
          <div class="mt-3 space-y-1.5 text-[12.5px] text-mut">
            <div class="flex justify-between"><span>Creator receives (90%)</span><span class="font-mono text-white/80" x-text="'₹' + creatorCut.toLocaleString('en-IN')"></span></div>
            <div class="flex justify-between"><span>Platform fee (10%)</span><span class="font-mono text-white/80" x-text="'₹' + fee.toLocaleString('en-IN')"></span></div>
            <div class="flex justify-between"><span>Estimated delivery</span><span class="font-mono text-white/80" x-text="eta"></span></div>
          </div>
          <button type="button" x-on:click="run()" class="mt-5 w-full h-12 rounded-xl btn-grad font-semibold text-[14.5px] shadow-lg shadow-pink/20">
            <span x-show="!running">Replay the simulation ▸</span>
            <span x-show="running" x-cloak>Running…</span>
          </button>
        </div>
      </div>

      {{-- pipeline --}}
      <div class="glass-strong rounded-3xl p-6 sm:p-7 flex flex-col">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2 text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">
            <span class="w-1.5 h-1.5 rounded-full transition-colors" :class="running ? 'bg-lime pulse-dot text-lime' : 'bg-white/25'"></span>
            <span x-text="running ? 'Pipeline running' : (done ? 'Delivered' : 'Ready')"></span>
          </div>
          <div class="text-[12px] font-mono text-white/45" x-text="clock"></div>
        </div>

        <div class="mt-6 space-y-3.5 flex-1">
          <template x-for="(s, i) in timeline" :key="i">
            <div class="flex gap-3.5 transition-all duration-500" :class="i <= stage ? 'opacity-100 translate-x-0' : 'opacity-30 translate-x-1'">
              <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-xl grid place-items-center shrink-0 transition-all duration-500"
                     :class="i < stage ? 'bg-lime/20 text-lime' : (i === stage ? 'btn-grad text-white scale-110' : 'bg-white/6 text-white/30')">
                  <template x-if="i < stage"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></template>
                  <template x-if="i === stage"><span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span></template>
                  <template x-if="i > stage"><span class="text-[11px] font-mono" x-text="i+1"></span></template>
                </div>
                <div x-show="i < timeline.length - 1" class="w-px flex-1 my-1 transition-colors duration-500" :class="i < stage ? 'bg-lime/40' : 'bg-white/10'"></div>
              </div>
              <div class="pb-2 min-w-0 flex-1">
                <div class="text-[14px] font-medium" x-text="s.title"></div>
                <div class="text-[12.5px] text-mut mt-0.5" x-html="s.detail"></div>

                <div x-show="s.key === 'match' && stage >= i" x-cloak x-transition.duration.400ms class="mt-3 flex items-center gap-3 glass rounded-2xl p-3">
                  <img :src="matched.img" class="w-9 h-9 rounded-full object-cover border-2 border-pink/40" alt="">
                  <div class="min-w-0">
                    <div class="text-[13px] font-semibold truncate" x-text="matched.name"></div>
                    <div class="text-[11.5px] text-mut truncate" x-text="matched.role + ' · ' + matched.rating + ' ★'"></div>
                  </div>
                  <span class="ml-auto text-[11px] font-semibold text-lime bg-lime/15 rounded-full px-2.5 py-1 shrink-0 animate-popIn">Accepted</span>
                </div>

                <div x-show="s.key === 'work' && stage >= i" x-cloak class="mt-3">
                  <div class="h-1.5 rounded-full bg-white/8 overflow-hidden">
                    <div class="h-full btn-grad transition-all duration-200 ease-linear" :style="`width:${work}%`"></div>
                  </div>
                  <div class="mt-1.5 text-[11.5px] font-mono text-white/45" x-text="work + '% · ' + workLabel"></div>
                </div>
              </div>
            </div>
          </template>
        </div>

        <div x-show="done" x-cloak x-transition.duration.500ms class="mt-4 rounded-2xl border border-lime/30 bg-lime/10 p-5">
          <div class="flex items-center gap-2 text-lime text-[12px] font-semibold tracking-[.12em] uppercase">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg> Delivered & approved
          </div>
          <div class="mt-2 text-[14px] leading-6">
            Your <span class="font-semibold" x-text="categoryLabel"></span> landed in <span class="font-mono" x-text="eta"></span>.
            Escrow released: <span class="font-mono text-lime" x-text="'₹' + creatorCut.toLocaleString('en-IN')"></span> to the creator.
          </div>
          <a href="{{ route('register') }}?type=business" class="mt-4 inline-flex h-11 px-5 rounded-xl btn-grad items-center font-semibold text-[14px]">Do this for real →</a>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════ CATEGORIES (light) ═══════════════ --}}
<section class="band-lav py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-5 reveal">
      <div class="max-w-[560px]">
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-deep">Browse</div>
        <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1] text-deep">Pick a category, see the price.</h2>
      </div>
      <a href="{{ route('marketplace') }}" class="h-11 px-5 rounded-xl bg-deep text-white inline-flex items-center gap-2 text-[13.5px] font-medium hover:opacity-90 transition">
        All gigs
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </a>
    </div>

    <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach($categories as $i => $cat)
        <a href="{{ route('marketplace', ['category' => $cat['q']]) }}"
           class="reveal-s group relative overflow-hidden rounded-3xl p-6 bg-gradient-to-br {{ $cat['tone'] }} text-white shadow-lg hover:shadow-2xl transition-all duration-400 hover:-translate-y-1.5"
           data-delay="{{ $i * 70 }}">
          <div class="absolute -right-8 -top-8 w-32 h-32 rounded-full bg-white/15 blur-2xl group-hover:scale-125 transition-transform duration-500"></div>
          <div class="relative">
            <div class="font-display text-[20px] font-semibold">{{ $cat['name'] }}</div>
            <div class="mt-1 text-[12.5px] text-white/80">{{ $cat['count'] }}</div>
            <div class="mt-8 flex items-center justify-between">
              <span class="text-[13px] text-white/85">from <b class="font-semibold">{{ $cat['from'] }}</b></span>
              <span class="w-9 h-9 rounded-xl bg-white/20 grid place-items-center group-hover:bg-white/30 group-hover:translate-x-1 transition-all duration-300">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
              </span>
            </div>
          </div>
        </a>
      @endforeach
    </div>

    {{-- gig cards --}}
    <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach($gigs as $i => $g)
        <a href="{{ route('gigs.show', $g['id']) }}" class="reveal glass rounded-3xl overflow-hidden card-hover group" data-delay="{{ $i * 70 }}">
          <div class="relative h-[150px] overflow-hidden">
            <img src="{{ $g['img'] }}" alt="{{ $g['title'] }}" class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
            <span class="absolute left-3 top-3 text-[10.5px] font-semibold tracking-wider uppercase bg-deep/80 text-white rounded-full px-2.5 py-1 backdrop-blur">{{ $g['badge'] }}</span>
            <span class="absolute right-3 top-3 text-[11px] font-mono bg-white/90 text-deep rounded-full px-2.5 py-1">{{ $g['time'] }}</span>
          </div>
          <div class="p-4">
            <div class="text-[14px] font-semibold leading-snug line-clamp-2 min-h-[38px] text-deep">{{ $g['title'] }}</div>
            <div class="mt-3 flex items-center justify-between">
              <span class="font-display text-[17px] font-semibold text-violet-deep">{{ $g['price'] }}</span>
              <span class="text-[11.5px] text-mut">{{ $g['rating'] }} ★ · {{ $g['sold'] }}</span>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════ CREATORS ═══════════════ --}}
<section class="py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px] reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-teal">The talent layer</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1]">Verified pros only. Live availability.</h2>
      <p class="mt-4 text-[15px] leading-7 text-mut">Every creator is ID-checked and portfolio-reviewed before they can accept a gig. You always see who is free right now.</p>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach($creators as $i => $c)
        <div class="reveal glass rounded-3xl p-5 card-hover" data-delay="{{ $i * 80 }}">
          <div class="flex items-start gap-3.5">
            <div class="relative shrink-0">
              <img src="{{ $c['img'] }}" alt="{{ $c['name'] }}" class="w-12 h-12 rounded-2xl object-cover border-2 {{ $c['available'] ? 'border-lime/50' : 'border-amber/50' }}">
              <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-ink {{ $c['available'] ? 'bg-lime' : 'bg-amber' }}"></span>
            </div>
            <div class="min-w-0">
              <div class="text-[14px] font-semibold truncate">{{ $c['name'] }}</div>
              <div class="text-[12px] text-mut truncate">{{ $c['handle'] }}</div>
            </div>
          </div>
          <div class="mt-4 text-[12.5px] text-mut leading-5 line-clamp-2 min-h-[38px]">{{ $c['role'] }}</div>
          <div class="mt-4 flex items-center justify-between">
            <span class="text-[11.5px] font-medium rounded-full px-2.5 py-1 {{ $c['available'] ? 'bg-lime/15 text-lime' : 'bg-amber/15 text-amber-soft' }}">
              {{ $c['available'] ? 'Available now' : 'Free in 2h' }}
            </span>
            <span class="text-[13px] font-semibold font-mono">{{ $c['price'] }}</span>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════ DELIVERED GALLERY ═══════════════ --}}
<section class="py-20 border-y border-white/8 overflow-hidden">
  <div class="max-w-shell mx-auto px-5 lg:px-8 flex flex-wrap items-end justify-between gap-4 reveal">
    <div>
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-pink-soft">Recently delivered</div>
      <h2 class="mt-3 font-display text-[28px] sm:text-[34px] font-semibold">Shipped this week on Quick GIGS</h2>
    </div>
    <span class="text-[12.5px] text-mut">Average turnaround shown per piece</span>
  </div>

  <div class="mt-9 marquee">
    @foreach(array_merge($gallery, $gallery) as $item)
      <div class="relative w-[240px] h-[150px] rounded-2xl overflow-hidden shrink-0 group">
        <img src="{{ $item['img'] }}" alt="{{ $item['label'] }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
        <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/30 to-transparent"></div>
        <div class="absolute bottom-3 left-3 right-3 flex items-center justify-between">
          <span class="text-[12.5px] font-medium">{{ $item['label'] }}</span>
          <span class="text-[11px] font-mono bg-white/15 rounded-full px-2 py-0.5">{{ $item['meta'] }}</span>
        </div>
      </div>
    @endforeach
  </div>
</section>

{{-- ═══════════════ COMPARISON ═══════════════ --}}
<section class="py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px] reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-amber-soft">Honest comparison</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1]">How we differ from the alternatives.</h2>
    </div>

    <div class="mt-10 glass rounded-3xl overflow-hidden reveal">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[820px] text-left">
          <thead>
            <tr class="border-b border-white/10 bg-white/3">
              <th class="p-5 text-[11px] font-semibold tracking-[.14em] uppercase text-white/45 w-[230px]"></th>
              @foreach($comparison['columns'] as $i => $col)
                <th class="p-5 text-[13px] font-semibold {{ $i === 0 ? 'text-white' : 'text-mut' }}">{{ $col }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach(array_slice($comparison['rows'], 0, 4) as $row)
              <tr class="border-b border-white/6 last:border-0 hover:bg-white/[0.03] transition-colors">
                @foreach($row as $i => $cell)
                  <td class="p-5 align-top text-[13.5px] leading-6 {{ $i === 0 ? 'text-white/60 font-medium' : ($i === 1 ? 'text-white' : 'text-mut') }}">{{ $cell }}</td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="p-5 border-t border-white/8 text-center">
        <a href="{{ route('compare') }}" class="text-[13.5px] text-pink-soft hover:text-white transition">Full comparison, including where we are the wrong choice →</a>
      </div>
    </div>

    <div class="mt-6 grid sm:grid-cols-3 gap-4">
      @foreach([
        ['3 days → 4 hours', 'Average turnaround after Avante Studio moved their reels onto Quick GIGS.', 'from-violet/15 to-transparent'],
        ['−62% revisions', 'Drop in second rounds once briefs were generated instead of written in chat.', 'from-pink/15 to-transparent'],
        ['₹0 wasted', 'Escrow means nothing is paid for work that never gets approved.', 'from-teal/15 to-transparent'],
      ] as $i => [$stat, $body, $grad])
        <div class="reveal rounded-3xl p-6 border border-white/10 bg-gradient-to-br {{ $grad }}" data-delay="{{ $i * 80 }}">
          <div class="font-display text-[24px] font-semibold tracking-tight grad-text">{{ $stat }}</div>
          <p class="mt-2.5 text-[13.5px] leading-6 text-mut">{{ $body }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════ PRICING (light) ═══════════════ --}}
<section id="pricing" class="band-light py-24" x-data="{ mode:'gig' }">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="text-center max-w-[640px] mx-auto reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-deep">Pricing</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1] text-deep">Flat prices. No bidding. No surprises.</h2>
      <p class="mt-4 text-[15px] leading-7 text-mut">See the price before you order. Money sits in escrow until you approve the delivery.</p>

      <div class="mt-8 inline-flex rounded-2xl p-1 bg-white shadow-sm border border-deep/8">
        <button x-on:click="mode='gig'" class="h-10 px-5 rounded-xl text-[13.5px] font-medium transition-all duration-300" :class="mode==='gig' ? 'btn-grad text-white' : 'text-mut hover:text-deep'">Pay per gig</button>
        <button x-on:click="mode='retainer'" class="h-10 px-5 rounded-xl text-[13.5px] font-medium transition-all duration-300" :class="mode==='retainer' ? 'btn-grad text-white' : 'text-mut hover:text-deep'">Monthly retainer <span class="text-[11px] opacity-70">−20%</span></button>
      </div>
    </div>

    <div class="mt-12 grid md:grid-cols-3 gap-5 items-start">
      @foreach($plans as $i => $p)
        <div class="reveal rounded-3xl p-7 card-hover relative {{ $p['featured'] ? 'glass-strong ring-glow-pink' : 'glass' }}" data-delay="{{ $i * 90 }}">
          @if($p['featured'])
            <span class="absolute -top-3 left-7 btn-grad text-white text-[10.5px] font-bold tracking-wider uppercase px-3 py-1 rounded-full shadow-lg">Most popular</span>
          @endif
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">{{ $p['name'] }}</div>
          <div class="mt-4 flex items-baseline gap-1.5">
            <span class="font-display text-[38px] font-semibold tracking-tight text-deep"
                  x-text="mode==='gig' ? '₹{{ number_format($p['price']) }}' : '₹{{ number_format($p['retainer']) }}'"></span>
            <span class="text-[13px] text-mut" x-text="mode==='gig' ? '{{ $p['unit'] }}' : '/ month'"></span>
          </div>
          <p class="mt-2 text-[13.5px] text-mut">{{ $p['tagline'] }}</p>

          <ul class="mt-6 space-y-3">
            @foreach($p['features'] as $f)
              <li class="flex gap-2.5 text-[13.5px] leading-5">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $p['featured'] ? '#FF4D8D' : '#7C5CFF' }}" stroke-width="2.6" class="shrink-0 mt-0.5"><path d="M20 6 9 17l-5-5"/></svg>
                <span class="text-white/80">{{ $f }}</span>
              </li>
            @endforeach
          </ul>

          <a href="{{ route('register') }}?type=business&plan={{ $p['slug'] }}"
             class="mt-7 h-12 rounded-xl grid place-items-center font-semibold text-[14px] transition {{ $p['featured'] ? 'btn-grad shadow-lg shadow-pink/25' : 'bg-deep text-white hover:opacity-90' }}">
            {{ $p['cta'] }}
          </a>
        </div>
      @endforeach
    </div>

    <div class="mt-8 text-center">
      <a href="{{ route('pricing') }}" class="text-[13.5px] text-violet-deep font-medium hover:underline">See the full pricing page, calculator and fee breakdown →</a>
    </div>

    <div class="mt-8 grid sm:grid-cols-3 gap-4">
      @foreach($feeNotes as $i => $n)
        <div class="reveal glass rounded-2xl p-5" data-delay="{{ $i * 70 }}">
          <div class="text-[13.5px] font-semibold text-deep">{{ $n['title'] }}</div>
          <div class="mt-1.5 text-[12.5px] leading-5 text-mut">{{ $n['body'] }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════ TESTIMONIALS ═══════════════ --}}
<section class="py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="grid md:grid-cols-3 gap-5">
      @foreach($testimonials as $i => $t)
        @php $ring = ['border-violet/40','border-pink/40','border-teal/40'][$i % 3]; @endphp
        <figure class="reveal glass rounded-3xl p-7 card-hover" data-delay="{{ $i * 90 }}">
          <div class="text-amber text-[13px] tracking-[.2em]">★★★★★</div>
          <blockquote class="mt-4 text-[14.5px] leading-7 text-white/85">“{{ $t['text'] }}”</blockquote>
          <figcaption class="mt-6 flex items-center gap-3">
            <span class="w-9 h-9 rounded-xl btn-grad grid place-items-center font-display font-bold text-[13px] text-white border {{ $ring }}">{{ substr($t['name'],0,1) }}</span>
            <span>
              <span class="block text-[13.5px] font-semibold">{{ $t['name'] }}</span>
              <span class="block text-[12px] text-mut">{{ $t['company'] }}</span>
            </span>
          </figcaption>
        </figure>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════ BLOG ═══════════════ --}}
@if(isset($blogs) && $blogs->count())
<section class="py-20 border-t border-white/8">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-5 reveal">
      <div>
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-cyan">Insights</div>
        <h2 class="mt-3 font-display text-[30px] font-semibold">Playbooks from the fastest teams</h2>
      </div>
      <a href="{{ route('blog.index') }}" class="h-11 px-5 rounded-xl glass btn-ghost inline-flex items-center text-[13.5px] font-medium hover:border-white/25">Read all →</a>
    </div>
    <div class="mt-10 grid md:grid-cols-3 gap-5">
      @foreach($blogs as $i => $b)
        <a href="{{ route('blog.show', $b->slug) }}" class="reveal glass rounded-3xl overflow-hidden card-hover group" data-delay="{{ $i * 80 }}">
          <div class="h-[170px] overflow-hidden">
            <img src="{{ filter_var($b->cover, FILTER_VALIDATE_URL) ? $b->cover : asset('storage/'.$b->cover) }}" alt="{{ $b->cover_alt ?: $b->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
          </div>
          <div class="p-5">
            <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-pink-soft">{{ $b->category->name ?? 'Playbook' }} · {{ $b->reading_minutes }} min</div>
            <div class="mt-2 text-[15px] font-semibold leading-snug line-clamp-2">{{ $b->title }}</div>
            <div class="mt-2 text-[13px] leading-5 text-mut line-clamp-2">{{ $b->excerpt }}</div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ═══════════════ FAQ (light) ═══════════════ --}}
<section id="faq" class="band-light py-24">
  <div class="max-w-[820px] mx-auto px-5 lg:px-8" x-data="{ open: 0 }">
    <div class="text-center reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-deep">FAQ</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold text-deep">Everything you'd ask us on a call</h2>
    </div>

    <div class="mt-12 space-y-3">
      @foreach($faqs as $i => $f)
        @php $q = $f->question ?? $f['question'] ?? ''; $a = $f->answer ?? $f['answer'] ?? ''; @endphp
        <div class="glass rounded-2xl overflow-hidden transition-all duration-300" :class="open === {{ $i }} ? 'ring-2 ring-violet/30' : ''">
          <button type="button" x-on:click="open = open === {{ $i }} ? -1 : {{ $i }}" class="w-full flex items-center justify-between gap-5 p-5 text-left">
            <span class="text-[14.5px] font-medium text-deep">{{ $q }}</span>
            <span class="w-7 h-7 rounded-full grid place-items-center shrink-0 transition-all duration-300" :class="open === {{ $i }} ? 'btn-grad text-white rotate-180' : 'bg-deep/6 text-mut'">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6"/></svg>
            </span>
          </button>
          <div x-show="open === {{ $i }}" x-collapse x-cloak>
            <div class="px-5 pb-5 text-[14px] leading-7 text-mut">{{ $a }}</div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════ NEWSLETTER / LEAD BAND ═══════════════ --}}
<section class="py-20">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="relative overflow-hidden rounded-[32px] px-7 sm:px-12 py-12 bg-gradient-to-br from-violet-deep via-violet to-pink">
      <div class="absolute -top-20 -right-10 w-[26rem] h-[26rem] rounded-full bg-amber/30 blur-[100px]"></div>
      <div class="relative grid lg:grid-cols-[1fr_1fr] gap-10 items-center">
        <div>
          <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-white/70">Weekly, short, useful</div>
          <h2 class="mt-3 font-display text-[30px] sm:text-[36px] font-semibold leading-[1.1] text-white">The hooks, formats and prices that worked this week.</h2>
          <p class="mt-4 text-[15px] leading-7 text-white/80 max-w-[460px]">One email, every Thursday. What our top-performing gigs did differently — with the actual numbers.</p>
        </div>

        <form method="POST" action="{{ route('leads.store') }}" class="bg-white/12 backdrop-blur rounded-3xl p-5 sm:p-6 border border-white/20">
          @csrf
          <input type="hidden" name="type" value="contact">
          <input type="hidden" name="source" value="newsletter">
          <input type="hidden" name="message" value="Newsletter signup from the landing page">
          <div class="grid sm:grid-cols-2 gap-3">
            <input name="name" required placeholder="Your name" class="h-12 px-4 rounded-xl bg-white/95 text-deep text-[14px] placeholder:text-deep/40 outline-none focus:ring-4 focus:ring-white/30 transition">
            <input name="email" type="email" required placeholder="you@company.com" class="h-12 px-4 rounded-xl bg-white/95 text-deep text-[14px] placeholder:text-deep/40 outline-none focus:ring-4 focus:ring-white/30 transition">
          </div>
          <button class="mt-3 w-full h-12 rounded-xl bg-deep text-white font-semibold text-[14.5px] hover:bg-black transition">Send me the weekly breakdown</button>
          <div class="mt-2.5 text-[11.5px] text-white/70 text-center">No spam. Unsubscribe in one click.</div>
        </form>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════ FINAL CTA ═══════════════ --}}
<section class="pb-10">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="relative overflow-hidden rounded-[32px] glass-strong px-7 sm:px-14 py-14 sm:py-16 text-center">
      <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[38rem] h-[38rem] bg-gradient-to-br from-violet/30 to-pink/20 blur-[120px] rounded-full -z-10 animate-drift"></div>

      <div class="inline-flex items-center gap-2 glass rounded-full px-3.5 py-1.5 text-[12px] text-white/75">
        <span class="w-1.5 h-1.5 rounded-full bg-lime pulse-dot text-lime"></span>
        {{ $stats['online_creators'] }} creators online right now
      </div>

      <h2 class="mt-6 font-display text-[34px] sm:text-[48px] font-semibold leading-[1.05] max-w-[760px] mx-auto">
        Your next deliverable is <span class="grad-text">one brief away</span>.
      </h2>
      <p class="mt-5 text-[15.5px] leading-7 text-mut max-w-[540px] mx-auto">
        Free to post. Matched in minutes. Pay only when you approve the work.
      </p>

      <div class="mt-9 flex flex-wrap justify-center gap-3">
        <a href="{{ route('register') }}?type=business" class="px-7 py-3.5 rounded-xl btn-grad font-semibold text-[15px] inline-flex items-center gap-2 shadow-xl shadow-pink/25">
          Start hiring
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a href="{{ route('register') }}?type=creator" class="px-7 py-3.5 rounded-xl glass btn-ghost font-medium text-[15px] inline-flex items-center hover:border-white/30">Join as a creator</a>
      </div>

      <div class="mt-7 flex flex-wrap justify-center gap-x-7 gap-y-2 text-[12.5px] text-mut">
        <span>✓ No subscription required</span>
        <span>✓ Escrow protected</span>
        <span>✓ 2 free revisions on every gig</span>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js" defer></script>
<script>
document.addEventListener('alpine:init', () => {

  /* hero: looping order pipeline */
  Alpine.data('liveOrder', () => ({
    active: 0, progress: 8, escrow: '₹2,499 held',
    steps: [
      { label: 'Brief posted',               time: '00:00' },
      { label: 'Matched with Priya S.',      time: '00:04' },
      { label: 'Production in progress',     time: '01:20' },
      { label: 'Delivered for review',       time: '02:48' },
      { label: 'Approved · escrow released', time: '03:05' },
    ],
    start() {
      setInterval(() => {
        this.active = (this.active + 1) % (this.steps.length + 1);
        this.progress = Math.round((this.active / this.steps.length) * 100) || 8;
        this.escrow = this.active >= this.steps.length ? '₹2,249 released' : '₹2,499 held';
      }, 2400);
    }
  }));

  /* interactive simulator — auto-plays in view, replays on any change, never navigates */
  Alpine.data('gigSim', (cfg) => ({
    cfg,
    category: cfg.categories[0].id,
    speed: cfg.speeds[1].id,
    addons: [],
    running: false, done: false, stage: -1, work: 0, seconds: 0,
    matched: cfg.creators[0],
    _timers: [], _tick: null, _prog: null, _debounce: null,

    get cat()   { return this.cfg.categories.find(c => c.id === this.category); },
    get spd()   { return this.cfg.speeds.find(s => s.id === this.speed); },
    get categoryLabel() { return this.cat.label; },
    get total() {
      const addons = this.cfg.addons.filter(a => this.addons.includes(a.id)).reduce((n, a) => n + a.price, 0);
      return Math.round((this.cat.base + addons) * this.spd.mult);
    },
    get fee()        { return Math.round(this.total * 0.10); },
    get creatorCut() { return this.total - this.fee; },
    get eta()        { return this.spd.eta; },
    get clock()      { const m = String(Math.floor(this.seconds / 60)).padStart(2,'0'), s = String(this.seconds % 60).padStart(2,'0'); return m + ':' + s; },
    get workLabel()  { return this.work < 40 ? 'cutting timeline' : (this.work < 75 ? 'sound + captions' : 'final export'); },
    get timeline() {
      return [
        { key:'post',    title:'Brief posted',            detail:`${this.cat.label} · ${this.spd.label} · <span class="font-mono">₹${this.total.toLocaleString('en-IN')}</span>` },
        { key:'match',   title:'Matching engine',         detail:`Scanned ${this.cfg.pool.toLocaleString('en-IN')} verified pros · ranked by skill, speed, rating` },
        { key:'escrow',  title:'Payment secured',         detail:`<span class="font-mono">₹${this.total.toLocaleString('en-IN')}</span> held in escrow — released only on your approval` },
        { key:'work',    title:'Production in progress',  detail:'Live progress, chat and file previews inside your dashboard' },
        { key:'deliver', title:'Delivered for review',    detail:'2 free revisions included · 24h auto-approve window' },
        { key:'release', title:'Approved · escrow released', detail:`<span class="font-mono">₹${this.creatorCut.toLocaleString('en-IN')}</span> paid out instantly via UPI` },
      ];
    },

    autoplay() { setTimeout(() => this.run(), 500); },

    /* any change re-runs the sim smoothly, in place */
    pick(field, value) { this[field] = value; this.queue(); },
    toggleAddon(id) {
      this.addons = this.addons.includes(id) ? this.addons.filter(a => a !== id) : [...this.addons, id];
      this.queue();
    },
    queue() {
      clearTimeout(this._debounce);
      this._debounce = setTimeout(() => this.run(), 350);
    },

    reset() {
      this._timers.forEach(clearTimeout); this._timers = [];
      clearInterval(this._tick); clearInterval(this._prog);
      this.running = false; this.done = false; this.stage = -1; this.work = 0; this.seconds = 0;
    },
    run() {
      this.reset();
      this.running = true;
      this.matched = this.cfg.creators[Math.floor(Math.random() * this.cfg.creators.length)];
      this._tick = setInterval(() => this.seconds++, 1000);

      const at = (ms, fn) => this._timers.push(setTimeout(fn, ms));
      at(100,  () => this.stage = 0);
      at(900,  () => this.stage = 1);
      at(2000, () => this.stage = 2);
      at(2900, () => {
        this.stage = 3;
        this._prog = setInterval(() => {
          this.work = Math.min(100, this.work + 5);
          if (this.work >= 100) clearInterval(this._prog);
        }, 70);
      });
      at(5000, () => this.stage = 4);
      at(6000, () => { this.stage = 5; this.done = true; this.running = false; clearInterval(this._tick); });
    }
  }));
});
</script>
@endpush
