<section id="result" class="py-12 scroll-mt-20" x-data="{ copied:false, copy(t){ navigator.clipboard.writeText(t).then(()=>{ this.copied=true; setTimeout(()=>this.copied=false, 2200); }); } }">
  <div class="max-w-shell mx-auto px-5 lg:px-8">

    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <div class="flex flex-wrap items-center gap-2.5">
          <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-soft">Your brief</div>
          @if(($meta['source'] ?? 'rules') === 'ai')
            <span class="inline-flex items-center gap-1.5 text-[10.5px] font-semibold rounded-full bg-violet/15 text-violet-soft px-2.5 py-1">
              <span class="w-1.5 h-1.5 rounded-full bg-violet-soft"></span>
              Written by {{ $meta['model'] }}@if($meta['latency_ms']) · {{ $meta['latency_ms'] }} ms @endif
            </span>
          @else
            <span class="inline-flex items-center gap-1.5 text-[10.5px] font-semibold rounded-full bg-white/8 text-mut px-2.5 py-1">
              <span class="w-1.5 h-1.5 rounded-full bg-white/40"></span> Rule-based engine
            </span>
          @endif
        </div>
        <h2 class="mt-2 font-display text-[28px] sm:text-[34px] font-semibold">{{ $brief['title'] }}</h2>
      </div>
      <div class="flex flex-wrap gap-2.5">
        <button x-on:click="copy(@js(session('brief.draft')))" class="h-11 px-5 rounded-xl glass text-[13.5px] font-medium hover:border-white/30 transition">
          <span x-show="!copied">Copy brief</span><span x-show="copied" x-cloak class="text-lime">Copied ✓</span>
        </button>
        <button type="button" x-on:click="$dispatch('brief-reset')" class="h-11 px-5 rounded-xl glass btn-ghost text-[13.5px] font-medium hover:border-white/30">Start over</button>
      </div>
    </div>

    @if(!empty($meta['error']))
      <div class="mt-5 rounded-2xl border border-white/10 bg-white/3 px-4 py-3 text-[12.5px] text-mut">
        Model unavailable — showing the deterministic brief. <span class="text-white/60">{{ $meta['error'] }}</span>
      </div>
    @endif
    @if(!empty($meta['refine_error']))
      <div class="mt-5 rounded-2xl border border-amber-400/25 bg-amber-400/8 px-4 py-3 text-[12.5px] text-amber-200">{{ $meta['refine_error'] }}</div>
    @endif

    <div class="mt-8 grid lg:grid-cols-[1fr_360px] gap-6 items-start">

      {{-- left: the brief --}}
      <div class="space-y-5">
        <div class="glass rounded-3xl p-6 sm:p-7">
          <p class="text-[15px] leading-7 text-white/85">{{ $brief['summary'] }}</p>
          <div class="mt-5 grid sm:grid-cols-3 gap-4">
            @foreach([['Objective', $brief['objective']], ['Audience', $brief['audience']], ['Tone', $brief['tone']]] as [$k, $v])
              <div class="rounded-2xl border border-white/8 bg-white/3 px-4 py-3.5">
                <div class="text-[11px] font-semibold tracking-[.12em] uppercase text-white/40">{{ $k }}</div>
                <div class="text-[13px] leading-6 text-mut mt-1">{{ $v }}</div>
              </div>
            @endforeach
          </div>
        </div>

        {{-- hooks --}}
        <div class="glass rounded-3xl p-6 sm:p-7">
          <div class="flex items-center justify-between">
            <h3 class="font-display text-[19px] font-semibold">Hook options</h3>
            <span class="text-[11.5px] text-mut">test all three, keep the winner</span>
          </div>
          <div class="mt-4 space-y-2.5">
            @foreach($brief['hooks'] as $i => $hook)
              <div class="flex gap-3.5 rounded-2xl border border-white/8 bg-white/3 p-4">
                <span class="w-7 h-7 rounded-lg btn-grad grid place-items-center text-[12px] font-bold text-ink shrink-0">{{ $i + 1 }}</span>
                <span class="text-[14.5px] leading-6">{{ $hook }}</span>
              </div>
            @endforeach
          </div>
        </div>

        {{-- beat sheet --}}
        <div class="glass rounded-3xl p-6 sm:p-7">
          <h3 class="font-display text-[19px] font-semibold">Beat sheet</h3>
          <div class="mt-5 space-y-4">
            @foreach($brief['beats'] as $i => $beat)
              <div class="flex gap-4">
                <div class="flex flex-col items-center">
                  <span class="w-2.5 h-2.5 rounded-full btn-grad mt-2"></span>
                  @if(!$loop->last)<span class="w-px flex-1 bg-white/12 my-1"></span>@endif
                </div>
                <div class="pb-1">
                  <div class="font-mono text-[12px] text-violet-soft">{{ $beat['t'] }}</div>
                  <div class="text-[14px] leading-6 text-white/85 mt-0.5">{{ $beat['what'] }}</div>
                </div>
              </div>
            @endforeach
          </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
          <div class="glass rounded-3xl p-6">
            <h3 class="font-display text-[17px] font-semibold">Deliverables</h3>
            <ul class="mt-4 space-y-2.5">
              @foreach($brief['deliverables'] as $d)
                <li class="flex gap-2.5 text-[13.5px] leading-6 text-mut">
                  <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A3E635" stroke-width="2.6" class="shrink-0 mt-1"><path d="M20 6 9 17l-5-5"/></svg>{{ $d }}
                </li>
              @endforeach
            </ul>
          </div>

          <div class="glass rounded-3xl p-6">
            <h3 class="font-display text-[17px] font-semibold">Technical spec</h3>
            <dl class="mt-4 space-y-2.5">
              @foreach($brief['spec'] as $k => $v)
                <div class="flex justify-between gap-4 text-[13px]">
                  <dt class="text-mut shrink-0">{{ $k }}</dt>
                  <dd class="text-right text-white/80">{{ $v }}</dd>
                </div>
              @endforeach
            </dl>
          </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-5">
          <div class="rounded-3xl p-6 border border-rose-500/20 bg-rose-500/5">
            <h3 class="font-display text-[17px] font-semibold text-rose-200">Do not</h3>
            <ul class="mt-4 space-y-2.5">
              @foreach($brief['avoid'] as $a)
                <li class="text-[13px] leading-6 text-white/70">— {{ $a }}</li>
              @endforeach
            </ul>
          </div>

          <div class="rounded-3xl p-6 border border-cyan/20 bg-violet/5">
            <h3 class="font-display text-[17px] font-semibold text-violet-soft">QA gate on delivery</h3>
            <ul class="mt-4 space-y-2.5">
              @foreach($brief['qa_gate'] as $g)
                <li class="text-[13px] leading-6 text-white/70"><span class="font-medium text-white/90">{{ $g['check'] }}</span> — {{ $g['detail'] }}</li>
              @endforeach
            </ul>
          </div>
        </div>
      </div>

      {{-- right: act on it --}}
      <aside class="space-y-5 lg:sticky lg:top-24">
        <div class="glass-strong rounded-3xl p-6 ring-glow">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">Suggested order</div>
          <div class="mt-3 flex items-baseline justify-between">
            <span class="font-display text-[30px] font-semibold">₹{{ number_format($brief['suggested']['price']) }}</span>
            <span class="text-[12.5px] text-mut">{{ $brief['suggested']['eta'] }}</span>
          </div>
          <div class="mt-1.5 text-[12.5px] text-mut">{{ $brief['suggested']['category'] }} · {{ ucfirst($brief['suggested']['lane']) }} lane · escrow protected</div>

          @if($gig)
            <a href="{{ route('gigs.show', $gig->id) }}" class="mt-5 h-12 rounded-xl btn-grad grid place-items-center font-semibold text-[14.5px]">
              Order this gig →
            </a>
            <div class="mt-2.5 text-[11.5px] text-center text-mut">Your brief is pre-filled on the order form.</div>
          @else
            <a href="{{ route('register') }}?type=business" class="mt-5 h-12 rounded-xl btn-grad grid place-items-center font-semibold text-[14.5px]">Create an account to order</a>
          @endif
        </div>

        {{-- confidence --}}
        <div class="glass rounded-3xl p-6">
          <div class="flex items-center justify-between">
            <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">Brief strength</div>
            <div class="font-display text-[20px] font-semibold">{{ $brief['confidence']['score'] }}%</div>
          </div>
          <div class="mt-3 h-1.5 rounded-full bg-white/8 overflow-hidden">
            <div class="h-full btn-grad" style="width: {{ $brief['confidence']['score'] }}%"></div>
          </div>
          <ul class="mt-4 space-y-2">
            @foreach($brief['confidence']['tips'] as $tip)
              <li class="text-[12.5px] leading-5 text-mut">• {{ $tip }}</li>
            @endforeach
          </ul>
        </div>

        {{-- matched freelancers --}}
        @if($matches->count())
          <div class="glass rounded-3xl p-6">
            <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">Freelancers matched to this brief</div>
            <div class="mt-4 space-y-4">
              @foreach($matches as $m)
                @php $c = $m['creator']; @endphp
                <div class="rounded-2xl border border-white/8 bg-white/3 p-4">
                  <div class="flex items-center gap-3">
                    <img src="{{ $c->avatarUrl() }}" class="w-9 h-9 rounded-xl object-cover border border-white/12" alt="">
                    <div class="min-w-0 flex-1">
                      <a href="{{ route('creator.public', $c->id) }}" class="text-[13.5px] font-semibold hover:text-violet-soft transition">{{ $c->name }}</a>
                      <div class="text-[11.5px] text-mut truncate">{{ $m['headline'] }}</div>
                    </div>
                    <div class="text-right shrink-0">
                      <div class="font-display text-[18px] font-semibold">{{ $m['score'] }}</div>
                      <div class="text-[10px] text-mut">match</div>
                    </div>
                  </div>
                  <div class="mt-3 space-y-1.5">
                    @foreach(array_slice($m['breakdown'], 0, 3) as $b)
                      <div class="flex items-center gap-2">
                        <div class="h-1 rounded-full bg-white/8 flex-1 overflow-hidden">
                          <div class="h-full btn-grad" style="width: {{ $b['max'] ? round($b['points'] / $b['max'] * 100) : 0 }}%"></div>
                        </div>
                        <span class="text-[10.5px] text-mut w-[92px] shrink-0">{{ $b['label'] }}</span>
                      </div>
                    @endforeach
                  </div>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </aside>
    </div>
  </div>
</section>
