@extends('layouts.site')

@section('content')

{{-- ───────────────────────── HERO ───────────────────────── --}}
<section class="relative pt-16 sm:pt-24 pb-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[1.05fr_0.95fr] gap-14 items-center">

    <div>
      <div class="inline-flex items-center gap-2.5 glass rounded-full pl-2 pr-3.5 py-1.5 text-[12px] font-medium">
        <span class="btn-grad text-ink text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-full">New</span>
        <span class="text-white/75">AI matching engine v3 — average match time {{ $stats['match_time'] }}</span>
      </div>

      <h1 class="mt-6 font-display text-[40px] sm:text-[58px] leading-[1.02] font-semibold">
        Hire a verified pro<br>
        <span class="grad-text">in minutes</span>, not weeks.
      </h1>

      <p class="mt-5 text-[16.5px] leading-7 text-mut max-w-[540px]">
        Quick GIGS matches your brief to the right creator automatically — reels, thumbnails, AI ads and design.
        Track production live, release payment only when you approve.
      </p>

      <div class="mt-8 flex flex-wrap items-center gap-3">
        <a href="{{ route('register') }}?type=business" class="h-12 px-6 rounded-xl btn-grad font-semibold text-[14.5px] inline-flex items-center gap-2 shadow-xl shadow-violet/25">
          Post a gig — it's free
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a href="#demo" class="h-12 px-6 rounded-xl glass font-medium text-[14.5px] inline-flex items-center gap-2 hover:border-white/25 transition">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M8 5v14l11-7z"/></svg>
          Watch the 30-second demo
        </a>
      </div>

      <div class="mt-9 flex flex-wrap items-center gap-x-8 gap-y-4">
        @foreach($heroStats as $s)
          <div>
            <div class="font-display text-[22px] font-semibold tracking-tight">{{ $s['value'] }}</div>
            <div class="text-[12px] text-mut mt-0.5">{{ $s['label'] }}</div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Live order card --}}
    <div class="relative" x-data="liveOrder()" x-init="start()">
      <div class="absolute -inset-6 bg-violet/15 blur-3xl rounded-full -z-10"></div>

      <div class="glass-strong rounded-3xl p-5 sm:p-6 ring-glow">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2 text-[11px] font-semibold tracking-[.14em] uppercase text-white/50">
            <span class="w-1.5 h-1.5 rounded-full bg-lime pulse-dot text-lime"></span> Live order
          </div>
          <span class="text-[11.5px] font-mono text-white/45">#QG-{{ $liveOrderId }}</span>
        </div>

        <div class="mt-4 flex items-start gap-3.5">
          <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet/40 to-cyan/30 grid place-items-center shrink-0">
            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="1.6"><rect x="2" y="5" width="14" height="14" rx="3"/><path d="m16 11 6-4v10l-6-4z"/></svg>
          </div>
          <div class="min-w-0">
            <div class="font-display text-[16px] font-semibold leading-tight">Retention reel · 45 sec</div>
            <div class="text-[12.5px] text-mut mt-1">Brief posted 00:00 · Express lane · ₹2,499 in escrow</div>
          </div>
        </div>

        {{-- animated pipeline --}}
        <div class="mt-6 space-y-3">
          <template x-for="(s, i) in steps" :key="i">
            <div class="flex items-center gap-3">
              <div class="w-7 h-7 rounded-full grid place-items-center shrink-0 transition-all duration-500"
                   :class="i < active ? 'bg-lime/20 text-lime' : (i === active ? 'btn-grad text-ink' : 'bg-white/6 text-white/30')">
                <template x-if="i < active">
                  <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
                </template>
                <template x-if="i >= active">
                  <span class="text-[11px] font-semibold font-mono" x-text="i+1"></span>
                </template>
              </div>
              <div class="flex-1 min-w-0">
                <div class="text-[13.5px] font-medium transition-colors" :class="i <= active ? 'text-white' : 'text-white/35'" x-text="s.label"></div>
              </div>
              <div class="text-[11.5px] font-mono shrink-0" :class="i <= active ? 'text-white/55' : 'text-white/20'" x-text="s.time"></div>
            </div>
          </template>
        </div>

        <div class="mt-5 h-1.5 rounded-full bg-white/8 overflow-hidden">
          <div class="h-full btn-grad transition-all duration-700 ease-out" :style="`width:${progress}%`"></div>
        </div>

        <div class="mt-5 flex items-center justify-between glass rounded-2xl px-4 py-3">
          <div class="flex items-center gap-2.5">
            <img src="https://i.pravatar.cc/80?img=5" class="w-8 h-8 rounded-full object-cover border border-white/15" alt="">
            <div>
              <div class="text-[13px] font-semibold flex items-center gap-1.5">Priya S.
                <svg width="13" height="13" viewBox="0 0 24 24" fill="#22D3EE"><path d="M12 2l2.4 1.8 3-.3 1 2.8 2.6 1.5-1 2.9 1 2.9-2.6 1.5-1 2.8-3-.3L12 22l-2.4-1.8-3 .3-1-2.8L3 16.2l1-2.9-1-2.9 2.6-1.5 1-2.8 3 .3z" opacity=".22"/><path d="M10.6 15.4 7.8 12.6l1.2-1.2 1.6 1.6 4-4 1.2 1.2z" fill="#22D3EE"/></svg>
              </div>
              <div class="text-[11.5px] text-mut">Verified editor · 4.9 ★ · 312 gigs</div>
            </div>
          </div>
          <div class="text-right">
            <div class="text-[11px] text-mut">Escrow</div>
            <div class="text-[13px] font-semibold font-mono" x-text="escrow"></div>
          </div>
        </div>
      </div>

      <div class="absolute -bottom-4 -left-4 hidden sm:flex glass-strong rounded-2xl px-3.5 py-2.5 items-center gap-2.5 shadow-xl">
        <span class="w-7 h-7 rounded-lg bg-lime/15 grid place-items-center">
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#A3E635" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>
        </span>
        <div class="text-[12px] leading-tight"><b class="font-semibold">Zero</b> bidding wars<div class="text-mut text-[11px]">You never chase freelancers</div></div>
      </div>
    </div>
  </div>
</section>

{{-- ───────────────────────── TRUST MARQUEE ───────────────────────── --}}
<section class="py-8 border-y border-white/8 overflow-hidden">
  <div class="max-w-shell mx-auto px-5 lg:px-8 mb-5 text-center text-[11px] font-semibold tracking-[.16em] uppercase text-white/35">
    Trusted by fast-moving teams
  </div>
  <div class="relative">
    <div class="marquee">
      @foreach(array_merge($logos, $logos) as $logo)
        <span class="font-display text-[17px] font-medium text-white/30 whitespace-nowrap">{{ $logo }}</span>
      @endforeach
    </div>
  </div>
</section>

{{-- ───────────────────────── HOW IT WORKS ───────────────────────── --}}
<section id="how" class="py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px] reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-soft">How it works</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1]">Three steps. No calls, no bidding, no chasing.</h2>
    </div>

    <div class="mt-14 grid md:grid-cols-3 gap-5">
      @foreach($steps as $i => $step)
        <div class="reveal glass rounded-3xl p-7 card-hover" style="transition-delay: {{ $i * 90 }}ms">
          <div class="flex items-center justify-between">
            <span class="w-11 h-11 rounded-2xl glass grid place-items-center text-violet-soft">{!! $step['icon'] !!}</span>
            <span class="font-display text-[34px] font-semibold text-white/8">0{{ $i+1 }}</span>
          </div>
          <div class="mt-5 font-display text-[18px] font-semibold">{{ $step['title'] }}</div>
          <p class="mt-2.5 text-[14px] leading-6 text-mut">{{ $step['body'] }}</p>
          <div class="mt-5 inline-flex items-center gap-2 text-[12px] font-medium text-white/60 glass rounded-full px-3 py-1.5">
            <span class="w-1.5 h-1.5 rounded-full bg-cyan"></span>{{ $step['meta'] }}
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ───────────────────────── DEMO SIMULATION ───────────────────────── --}}
<section id="demo" class="py-24 border-y border-white/8 bg-gradient-to-b from-violet/[0.06] to-transparent">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-6 reveal">
      <div class="max-w-[620px]">
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-cyan">Interactive demo</div>
        <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1]">Build a gig. Watch it get delivered.</h2>
        <p class="mt-4 text-[15px] leading-7 text-mut">Configure a real order below and run the simulation — same pricing, same pipeline, same escrow rules as the live platform. No signup needed.</p>
      </div>
      <div class="glass rounded-2xl px-4 py-3 text-[12px] text-mut">Simulation only · nothing is charged</div>
    </div>

    <div class="mt-12 grid lg:grid-cols-[0.95fr_1.05fr] gap-5" x-data="gigSim(@js($sim))">

      {{-- configurator --}}
      <div class="glass rounded-3xl p-6 sm:p-7">
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">1 · What do you need?</div>
        <div class="mt-4 grid grid-cols-2 gap-2.5">
          <template x-for="c in cfg.categories" :key="c.id">
            <button type="button" x-on:click="category = c.id; if(running) reset()"
              class="text-left rounded-2xl p-3.5 border transition"
              :class="category === c.id ? 'border-violet bg-violet/12' : 'border-white/10 bg-white/3 hover:border-white/25'">
              <div class="text-[13.5px] font-semibold" x-text="c.label"></div>
              <div class="text-[11.5px] text-mut mt-0.5" x-text="'from ₹' + c.base.toLocaleString('en-IN')"></div>
            </button>
          </template>
        </div>

        <div class="mt-7 text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">2 · How fast?</div>
        <div class="mt-4 space-y-2.5">
          <template x-for="s in cfg.speeds" :key="s.id">
            <button type="button" x-on:click="speed = s.id; if(running) reset()"
              class="w-full flex items-center justify-between rounded-2xl px-4 py-3 border transition"
              :class="speed === s.id ? 'border-cyan bg-cyan/10' : 'border-white/10 bg-white/3 hover:border-white/25'">
              <span class="flex items-center gap-3">
                <span class="w-2 h-2 rounded-full" :class="speed === s.id ? 'bg-cyan' : 'bg-white/20'"></span>
                <span class="text-[13.5px] font-medium" x-text="s.label"></span>
              </span>
              <span class="text-[12px] font-mono" :class="s.mult > 1 ? 'text-violet-soft' : 'text-mut'" x-text="s.mult > 1 ? '+' + Math.round((s.mult-1)*100) + '%' : 'included'"></span>
            </button>
          </template>
        </div>

        <div class="mt-7 text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">3 · Add-ons</div>
        <div class="mt-4 flex flex-wrap gap-2">
          <template x-for="a in cfg.addons" :key="a.id">
            <button type="button" x-on:click="toggleAddon(a.id); if(running) reset()"
              class="rounded-full px-3.5 py-2 text-[12.5px] font-medium border transition"
              :class="addons.includes(a.id) ? 'border-violet bg-violet/15 text-white' : 'border-white/10 text-mut hover:text-white hover:border-white/25'">
              <span x-text="a.label"></span> <span class="font-mono opacity-60" x-text="'+₹' + a.price"></span>
            </button>
          </template>
        </div>

        {{-- price --}}
        <div class="mt-7 rounded-2xl border border-white/10 bg-black/25 p-5">
          <div class="flex items-baseline justify-between">
            <span class="text-[13px] text-mut">Gig total</span>
            <span class="font-display text-[30px] font-semibold tracking-tight" x-text="'₹' + total.toLocaleString('en-IN')"></span>
          </div>
          <div class="mt-3 space-y-1.5 text-[12.5px] text-mut">
            <div class="flex justify-between"><span>Creator receives (90%)</span><span class="font-mono text-white/80" x-text="'₹' + creatorCut.toLocaleString('en-IN')"></span></div>
            <div class="flex justify-between"><span>Platform fee (10%)</span><span class="font-mono text-white/80" x-text="'₹' + fee.toLocaleString('en-IN')"></span></div>
            <div class="flex justify-between"><span>Estimated delivery</span><span class="font-mono text-white/80" x-text="eta"></span></div>
          </div>
          <button type="button" x-on:click="running ? reset() : run()"
            class="mt-5 w-full h-12 rounded-xl font-semibold text-[14.5px] transition"
            :class="running ? 'glass hover:border-white/30' : 'btn-grad shadow-lg shadow-violet/25'">
            <span x-show="!running">Run live simulation ▸</span>
            <span x-show="running" x-cloak>Reset simulation</span>
          </button>
        </div>
      </div>

      {{-- pipeline output --}}
      <div class="glass-strong rounded-3xl p-6 sm:p-7 flex flex-col">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2 text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">
            <span class="w-1.5 h-1.5 rounded-full" :class="running ? 'bg-lime' : 'bg-white/25'"></span>
            <span x-text="running ? 'Pipeline running' : 'Pipeline idle'"></span>
          </div>
          <div class="text-[12px] font-mono text-white/45" x-text="clock"></div>
        </div>

        <div class="mt-6 space-y-3.5 flex-1">
          <template x-for="(s, i) in timeline" :key="i">
            <div class="flex gap-3.5 transition-all duration-500" :class="i <= stage ? 'opacity-100' : 'opacity-25'">
              <div class="flex flex-col items-center">
                <div class="w-8 h-8 rounded-xl grid place-items-center shrink-0 transition-all duration-500"
                     :class="i < stage ? 'bg-lime/18 text-lime' : (i === stage ? 'btn-grad text-ink' : 'bg-white/6 text-white/30')">
                  <template x-if="i < stage"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></template>
                  <template x-if="i === stage"><span class="w-2.5 h-2.5 rounded-full bg-ink animate-ping"></span></template>
                  <template x-if="i > stage"><span class="text-[11px] font-mono" x-text="i+1"></span></template>
                </div>
                <div x-show="i < timeline.length - 1" class="w-px flex-1 my-1" :class="i < stage ? 'bg-lime/35' : 'bg-white/10'"></div>
              </div>
              <div class="pb-2 min-w-0 flex-1">
                <div class="text-[14px] font-medium" x-text="s.title"></div>
                <div class="text-[12.5px] text-mut mt-0.5" x-html="s.detail"></div>

                {{-- matched creator card --}}
                <div x-show="s.key === 'match' && stage >= i" x-cloak class="mt-3 flex items-center gap-3 glass rounded-2xl p-3">
                  <img :src="matched.img" class="w-9 h-9 rounded-full object-cover border border-white/15" alt="">
                  <div class="min-w-0">
                    <div class="text-[13px] font-semibold truncate" x-text="matched.name"></div>
                    <div class="text-[11.5px] text-mut truncate" x-text="matched.role + ' · ' + matched.rating + ' ★'"></div>
                  </div>
                  <span class="ml-auto text-[11px] font-semibold text-lime bg-lime/12 rounded-full px-2.5 py-1 shrink-0">Accepted</span>
                </div>

                {{-- production progress --}}
                <div x-show="s.key === 'work' && stage >= i" x-cloak class="mt-3">
                  <div class="h-1.5 rounded-full bg-white/8 overflow-hidden">
                    <div class="h-full btn-grad transition-all duration-300 ease-linear" :style="`width:${work}%`"></div>
                  </div>
                  <div class="mt-1.5 text-[11.5px] font-mono text-white/45" x-text="work + '% · ' + workLabel"></div>
                </div>
              </div>
            </div>
          </template>
        </div>

        {{-- result --}}
        <div x-show="stage >= timeline.length - 1 && done" x-cloak x-transition class="mt-4 rounded-2xl border border-lime/25 bg-lime/8 p-5">
          <div class="flex items-center gap-2 text-lime text-[12px] font-semibold tracking-[.12em] uppercase">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg> Delivered & approved
          </div>
          <div class="mt-2 text-[14px] leading-6">
            Your <span class="font-semibold" x-text="categoryLabel"></span> was delivered in <span class="font-mono" x-text="eta"></span>.
            Escrow released: <span class="font-mono" x-text="'₹' + creatorCut.toLocaleString('en-IN')"></span> to the creator,
            <span class="font-mono" x-text="'₹' + fee.toLocaleString('en-IN')"></span> platform fee.
          </div>
          <a href="{{ route('register') }}?type=business" class="mt-4 inline-flex h-11 px-5 rounded-xl btn-grad items-center font-semibold text-[14px]">Do this for real →</a>
        </div>

        <div x-show="!running" x-cloak class="mt-4 text-center text-[12.5px] text-mut">Pick your options and hit <span class="text-white/80 font-medium">Run live simulation</span>.</div>
      </div>
    </div>
  </div>
</section>

{{-- ───────────────────────── MARKETPLACE PREVIEW ───────────────────────── --}}
<section class="py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-5 reveal">
      <div class="max-w-[560px]">
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-soft">Marketplace</div>
        <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1]">Ready-to-buy gigs, fixed prices.</h2>
      </div>
      <a href="{{ route('marketplace') }}" class="h-11 px-5 rounded-xl glass inline-flex items-center gap-2 text-[13.5px] font-medium hover:border-white/25 transition">
        Browse all gigs
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
      </a>
    </div>

    <div class="mt-10 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach($gigs as $i => $g)
        <a href="{{ route('gigs.show', $g['id']) }}" class="reveal glass rounded-3xl overflow-hidden card-hover group" style="transition-delay: {{ $i * 70 }}ms">
          <div class="relative h-[150px] overflow-hidden">
            <img src="{{ $g['img'] }}" alt="{{ $g['title'] }}" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition duration-500">
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/20 to-transparent"></div>
            <span class="absolute left-3 top-3 text-[10.5px] font-semibold tracking-wider uppercase glass-strong rounded-full px-2.5 py-1">{{ $g['badge'] }}</span>
            <span class="absolute right-3 top-3 text-[11px] font-mono glass-strong rounded-full px-2.5 py-1">{{ $g['time'] }}</span>
          </div>
          <div class="p-4">
            <div class="text-[14px] font-semibold leading-snug line-clamp-2 min-h-[38px]">{{ $g['title'] }}</div>
            <div class="mt-3 flex items-center justify-between">
              <span class="font-display text-[17px] font-semibold">{{ $g['price'] }}</span>
              <span class="text-[11.5px] text-mut">{{ $g['rating'] }} ★ · {{ $g['sold'] }}</span>
            </div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>

{{-- ───────────────────────── CREATORS ───────────────────────── --}}
<section class="py-24 border-y border-white/8">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px] reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-cyan">The talent layer</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1]">Verified pros only. Live availability.</h2>
      <p class="mt-4 text-[15px] leading-7 text-mut">Every creator is ID-checked and portfolio-reviewed before they can accept a gig. You always see who is free right now.</p>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach($creators as $i => $c)
        <div class="reveal glass rounded-3xl p-5 card-hover" style="transition-delay: {{ $i * 70 }}ms">
          <div class="flex items-start gap-3.5">
            <div class="relative shrink-0">
              <img src="{{ $c['img'] }}" alt="{{ $c['name'] }}" class="w-12 h-12 rounded-2xl object-cover border border-white/12">
              <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-ink {{ $c['available'] ? 'bg-lime' : 'bg-amber-400' }}"></span>
            </div>
            <div class="min-w-0">
              <div class="text-[14px] font-semibold truncate">{{ $c['name'] }}</div>
              <div class="text-[12px] text-mut truncate">{{ $c['handle'] }}</div>
            </div>
          </div>
          <div class="mt-4 text-[12.5px] text-mut leading-5 line-clamp-2 min-h-[38px]">{{ $c['role'] }}</div>
          <div class="mt-4 flex items-center justify-between">
            <span class="text-[11.5px] font-medium rounded-full px-2.5 py-1 {{ $c['available'] ? 'bg-lime/12 text-lime' : 'bg-amber-400/12 text-amber-300' }}">
              {{ $c['available'] ? 'Available now' : 'Free in 2h' }}
            </span>
            <span class="text-[13px] font-semibold font-mono">{{ $c['price'] }}</span>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ───────────────────────── PRICING ───────────────────────── --}}
<section id="pricing" class="py-24" x-data="{ mode:'gig' }">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="text-center max-w-[640px] mx-auto reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-soft">Pricing</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1]">Flat prices. No bidding. No surprises.</h2>
      <p class="mt-4 text-[15px] leading-7 text-mut">See the price before you order. Money sits in escrow until you approve the delivery.</p>

      <div class="mt-8 inline-flex glass rounded-2xl p-1">
        <button x-on:click="mode='gig'" class="h-10 px-5 rounded-xl text-[13.5px] font-medium transition" :class="mode==='gig' ? 'btn-grad text-ink' : 'text-mut hover:text-white'">Pay per gig</button>
        <button x-on:click="mode='retainer'" class="h-10 px-5 rounded-xl text-[13.5px] font-medium transition" :class="mode==='retainer' ? 'btn-grad text-ink' : 'text-mut hover:text-white'">Monthly retainer <span class="text-[11px] opacity-70">−20%</span></button>
      </div>
    </div>

    <div class="mt-12 grid md:grid-cols-3 gap-5 items-start">
      @foreach($plans as $p)
        <div class="reveal rounded-3xl p-7 card-hover relative {{ $p['featured'] ? 'glass-strong ring-glow' : 'glass' }}">
          @if($p['featured'])
            <span class="absolute -top-3 left-7 btn-grad text-ink text-[10.5px] font-bold tracking-wider uppercase px-3 py-1 rounded-full">Most popular</span>
          @endif
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">{{ $p['name'] }}</div>
          <div class="mt-4 flex items-baseline gap-1.5">
            <span class="font-display text-[38px] font-semibold tracking-tight"
                  x-text="mode==='gig' ? '₹{{ number_format($p['price']) }}' : '₹{{ number_format($p['retainer']) }}'"></span>
            <span class="text-[13px] text-mut" x-text="mode==='gig' ? '{{ $p['unit'] }}' : '/ month'"></span>
          </div>
          <p class="mt-2 text-[13.5px] text-mut">{{ $p['tagline'] }}</p>

          <ul class="mt-6 space-y-3">
            @foreach($p['features'] as $f)
              <li class="flex gap-2.5 text-[13.5px] leading-5">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $p['featured'] ? '#A3E635' : '#7C5CFF' }}" stroke-width="2.6" class="shrink-0 mt-0.5"><path d="M20 6 9 17l-5-5"/></svg>
                <span class="text-white/80">{{ $f }}</span>
              </li>
            @endforeach
          </ul>

          <a href="{{ route('register') }}?type=business&plan={{ $p['slug'] }}"
             class="mt-7 h-12 rounded-xl grid place-items-center font-semibold text-[14px] transition {{ $p['featured'] ? 'btn-grad shadow-lg shadow-violet/25' : 'glass hover:border-white/30' }}">
            {{ $p['cta'] }}
          </a>
        </div>
      @endforeach
    </div>

    <div class="mt-6 grid sm:grid-cols-3 gap-4">
      @foreach($feeNotes as $n)
        <div class="glass rounded-2xl p-5">
          <div class="text-[13.5px] font-semibold">{{ $n['title'] }}</div>
          <div class="mt-1.5 text-[12.5px] leading-5 text-mut">{{ $n['body'] }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ───────────────────────── TESTIMONIALS ───────────────────────── --}}
<section class="py-24 border-y border-white/8">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="grid md:grid-cols-3 gap-5">
      @foreach($testimonials as $i => $t)
        <figure class="reveal glass rounded-3xl p-7" style="transition-delay: {{ $i * 80 }}ms">
          <div class="text-cyan text-[13px] tracking-[.2em]">★★★★★</div>
          <blockquote class="mt-4 text-[14.5px] leading-7 text-white/85">“{{ $t['text'] }}”</blockquote>
          <figcaption class="mt-6 flex items-center gap-3">
            <span class="w-9 h-9 rounded-xl btn-grad grid place-items-center font-display font-bold text-[13px] text-ink">{{ substr($t['name'],0,1) }}</span>
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

{{-- ───────────────────────── BLOG ───────────────────────── --}}
@if(isset($blogs) && $blogs->count())
<section class="py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-5 reveal">
      <div>
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-soft">Insights</div>
        <h2 class="mt-3 font-display text-[32px] font-semibold">Playbooks from the fastest teams</h2>
      </div>
      <a href="{{ route('blog.index') }}" class="h-11 px-5 rounded-xl glass inline-flex items-center text-[13.5px] font-medium hover:border-white/25">Read all →</a>
    </div>
    <div class="mt-10 grid md:grid-cols-3 gap-5">
      @foreach($blogs as $b)
        <a href="{{ route('blog.show', $b->slug) }}" class="reveal glass rounded-3xl overflow-hidden card-hover group">
          <img src="{{ filter_var($b->cover, FILTER_VALIDATE_URL) ? $b->cover : asset('storage/'.$b->cover) }}" alt="{{ $b->cover_alt ?: $b->title }}" class="h-[170px] w-full object-cover opacity-85 group-hover:opacity-100 transition">
          <div class="p-5">
            <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-cyan">{{ $b->category->name ?? 'Playbook' }} · {{ $b->reading_minutes }} min</div>
            <div class="mt-2 text-[15px] font-semibold leading-snug line-clamp-2">{{ $b->title }}</div>
            <div class="mt-2 text-[13px] leading-5 text-mut line-clamp-2">{{ $b->excerpt }}</div>
          </div>
        </a>
      @endforeach
    </div>
  </div>
</section>
@endif

{{-- ───────────────────────── FAQ ───────────────────────── --}}
<section id="faq" class="py-24 border-t border-white/8">
  <div class="max-w-[820px] mx-auto px-5 lg:px-8" x-data="{ open: 0 }">
    <div class="text-center reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-soft">FAQ</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold">Everything you'd ask us on a call</h2>
    </div>

    <div class="mt-12 space-y-3">
      @foreach($faqs as $i => $f)
        @php $q = $f->question ?? $f['question'] ?? ''; $a = $f->answer ?? $f['answer'] ?? ''; @endphp
        <div class="glass rounded-2xl overflow-hidden transition" :class="open === {{ $i }} ? 'border-violet/40' : ''">
          <button type="button" x-on:click="open = open === {{ $i }} ? -1 : {{ $i }}" class="w-full flex items-center justify-between gap-5 p-5 text-left">
            <span class="text-[14.5px] font-medium">{{ $q }}</span>
            <span class="w-7 h-7 rounded-full grid place-items-center shrink-0 transition" :class="open === {{ $i }} ? 'btn-grad text-ink rotate-180' : 'bg-white/6 text-white/50'">
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

{{-- ───────────────────────── FINAL CTA ───────────────────────── --}}
<section class="pb-8">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="relative overflow-hidden rounded-[32px] glass-strong px-7 sm:px-14 py-14 sm:py-16 text-center">
      <div class="absolute -top-24 left-1/2 -translate-x-1/2 w-[38rem] h-[38rem] bg-violet/25 blur-[120px] rounded-full -z-10"></div>

      <div class="inline-flex items-center gap-2 glass rounded-full px-3.5 py-1.5 text-[12px] text-white/70">
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
        <a href="{{ route('register') }}?type=business" class="px-7 py-3.5 rounded-xl btn-grad font-semibold text-[15px] inline-flex items-center gap-2 shadow-xl shadow-violet/25">
          Start hiring
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a href="{{ route('register') }}?type=creator" class="px-7 py-3.5 rounded-xl glass font-medium text-[15px] inline-flex items-center hover:border-white/30 transition">Join as a creator</a>
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
<script>
document.addEventListener('alpine:init', () => {

  /* hero: looping order pipeline */
  Alpine.data('liveOrder', () => ({
    active: 0, progress: 8, escrow: '₹2,499 held',
    steps: [
      { label: 'Brief posted',            time: '00:00' },
      { label: 'Matched with Priya S.',   time: '00:04' },
      { label: 'Production in progress',  time: '01:20' },
      { label: 'Delivered for review',    time: '02:48' },
      { label: 'Approved · escrow released', time: '03:05' },
    ],
    start() {
      setInterval(() => {
        this.active = (this.active + 1) % (this.steps.length + 1);
        this.progress = Math.round((this.active / this.steps.length) * 100) || 8;
        this.escrow = this.active >= this.steps.length ? '₹2,249 released' : '₹2,499 held';
      }, 2200);
    }
  }));

  /* interactive gig simulator */
  Alpine.data('gigSim', (cfg) => ({
    cfg,
    category: cfg.categories[0].id,
    speed: cfg.speeds[1].id,
    addons: [],
    running: false, done: false, stage: -1, work: 0, seconds: 0,
    matched: cfg.creators[0],
    _timers: [],

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
        { key:'post',   title:'Brief posted',            detail:`${this.cat.label} · ${this.spd.label} · <span class="font-mono">₹${this.total.toLocaleString('en-IN')}</span>` },
        { key:'match',  title:'AI matching engine',      detail:`Scanned ${this.cfg.pool.toLocaleString('en-IN')} verified pros · ranked by skill, speed, rating` },
        { key:'escrow', title:'Payment secured',         detail:`<span class="font-mono">₹${this.total.toLocaleString('en-IN')}</span> held in escrow — released only on your approval` },
        { key:'work',   title:'Production in progress',  detail:'Live progress, chat and file previews inside your dashboard' },
        { key:'deliver',title:'Delivered for review',    detail:'2 free revisions included · 24h auto-approve window' },
        { key:'release',title:'Approved · escrow released', detail:`<span class="font-mono">₹${this.creatorCut.toLocaleString('en-IN')}</span> paid out instantly via UPI` },
      ];
    },
    toggleAddon(id) { this.addons = this.addons.includes(id) ? this.addons.filter(a => a !== id) : [...this.addons, id]; },
    reset() {
      this._timers.forEach(clearTimeout); this._timers = [];
      if (this._tick) clearInterval(this._tick);
      if (this._prog) clearInterval(this._prog);
      this.running = false; this.done = false; this.stage = -1; this.work = 0; this.seconds = 0;
    },
    run() {
      this.reset();
      this.running = true;
      this.matched = this.cfg.creators[Math.floor(Math.random() * this.cfg.creators.length)];
      this._tick = setInterval(() => this.seconds++, 1000);

      const at = (ms, fn) => this._timers.push(setTimeout(fn, ms));
      at(120,  () => this.stage = 0);
      at(1100, () => this.stage = 1);
      at(2600, () => this.stage = 2);
      at(3600, () => {
        this.stage = 3;
        this._prog = setInterval(() => {
          this.work = Math.min(100, this.work + 4);
          if (this.work >= 100) clearInterval(this._prog);
        }, 90);
      });
      at(6400, () => this.stage = 4);
      at(7600, () => { this.stage = 5; this.done = true; clearInterval(this._tick); });
    }
  }));
});
</script>
@endpush
