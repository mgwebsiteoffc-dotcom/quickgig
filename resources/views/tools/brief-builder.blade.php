@extends('layouts.site')

@section('content')

<section class="pt-16 pb-10">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[720px]">
      <div class="inline-flex items-center gap-2.5 glass rounded-full pl-2 pr-3.5 py-1.5 text-[12px] font-medium">
        <span class="btn-grad text-ink text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-full">Free tool</span>
        <span class="text-white/75">No account, no card, no email wall</span>
      </div>
      <h1 class="mt-5 font-display text-[38px] sm:text-[50px] font-semibold leading-[1.04]">
        One line in.<br><span class="grad-text">A production brief</span> out.
      </h1>
      <p class="mt-5 text-[16px] leading-7 text-mut max-w-[620px]">
        Bad briefs cause most revisions. Describe the job the way you would say it out loud and get back
        hook options, a timed beat sheet, deliverables, the technical spec and the quality checks your
        delivery will be scored against.
      </p>
    </div>
  </div>
</section>

{{-- ── the form ── --}}
<section class="pb-8">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <form method="POST" action="{{ route('brief-builder.generate') }}"
          x-data="{ format: @js(old('format', $input['format'] ?? 'reel')), urgency: @js(old('urgency', $input['urgency'] ?? 'standard')) }"
          class="glass rounded-3xl p-6 sm:p-8">
      @csrf

      @if($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-[13.5px] text-rose-200">{{ $errors->first() }}</div>
      @endif

      <div class="grid lg:grid-cols-[1.25fr_1fr] gap-8">
        <div>
          <label class="label" for="idea">What do you need? <span class="normal-case tracking-normal text-white/30">one sentence</span></label>
          <textarea id="idea" name="idea" rows="3" required class="field"
                    placeholder="A reel announcing our ₹999 protein bar launch, founder on camera, shot at the factory">{{ old('idea', $input['idea'] ?? '') }}</textarea>

          <div class="mt-5">
            <span class="label">Format</span>
            <div class="grid sm:grid-cols-3 gap-2.5">
              @foreach($formats as $key => $f)
                <label class="rounded-2xl p-3.5 border cursor-pointer transition block"
                       :class="format === '{{ $key }}' ? 'border-violet bg-violet/12' : 'border-white/10 bg-white/3 hover:border-white/25'">
                  <input type="radio" name="format" value="{{ $key }}" x-model="format" class="sr-only">
                  <div class="text-[13.5px] font-semibold">{{ $f['label'] }}</div>
                  <div class="text-[11.5px] text-mut mt-0.5">from ₹{{ number_format($f['price']) }} · {{ $f['length'] }}</div>
                </label>
              @endforeach
            </div>
          </div>

          <div class="mt-5">
            <label class="label" for="audience">Who is it for? <span class="normal-case tracking-normal text-white/30">optional</span></label>
            <input id="audience" name="audience" value="{{ old('audience', $input['audience'] ?? '') }}" class="field"
                   placeholder="gym-goers in metros who buy supplements online">
          </div>
        </div>

        <div class="space-y-5">
          <div>
            <label class="label" for="goal">Goal</label>
            <select id="goal" name="goal" class="field">
              @foreach($goals as $key => $desc)
                <option value="{{ $key }}" @selected(old('goal', $input['goal'] ?? 'conversion') === $key)>{{ ucfirst($key) }} — {{ $desc }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="label" for="tone">Tone</label>
            <select id="tone" name="tone" class="field">
              @foreach($tones as $key => $t)
                <option value="{{ $key }}" @selected(old('tone', $input['tone'] ?? 'confident') === $key)>{{ ucfirst($key) }} — {{ $t[0] }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <span class="label">Speed</span>
            <div class="space-y-2">
              @foreach(['express' => 'Express · 3 hours (+60%)', 'standard' => 'Standard · 24 hours', 'relaxed' => 'Relaxed · 48 hours (−15%)'] as $key => $label)
                <label class="flex items-center gap-3 rounded-2xl px-4 py-3 border cursor-pointer transition"
                       :class="urgency === '{{ $key }}' ? 'border-cyan bg-cyan/10' : 'border-white/10 bg-white/3 hover:border-white/25'">
                  <input type="radio" name="urgency" value="{{ $key }}" x-model="urgency" class="accent-cyan">
                  <span class="text-[13.5px] font-medium">{{ $label }}</span>
                </label>
              @endforeach
            </div>
          </div>

          <button class="w-full h-13 py-3.5 rounded-xl btn-grad font-semibold text-[15px] shadow-lg shadow-violet/25">
            Generate my brief →
          </button>
          <div class="text-center text-[11.5px] text-mut">Runs instantly. Nothing is stored against your name.</div>
        </div>
      </div>
    </form>
  </div>
</section>

{{-- ── the result ── --}}
@if($brief)
<section id="result" class="py-12 scroll-mt-20" x-data="{ copied:false, copy(t){ navigator.clipboard.writeText(t).then(()=>{ this.copied=true; setTimeout(()=>this.copied=false, 2200); }); } }">
  <div class="max-w-shell mx-auto px-5 lg:px-8">

    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-cyan">Your brief</div>
        <h2 class="mt-2 font-display text-[28px] sm:text-[34px] font-semibold">{{ $brief['title'] }}</h2>
      </div>
      <div class="flex flex-wrap gap-2.5">
        <button x-on:click="copy(@js(session('brief.draft')))" class="h-11 px-5 rounded-xl glass text-[13.5px] font-medium hover:border-white/30 transition">
          <span x-show="!copied">Copy brief</span><span x-show="copied" x-cloak class="text-lime">Copied ✓</span>
        </button>
        <form method="POST" action="{{ route('brief-builder.reset') }}">@csrf
          <button class="h-11 px-5 rounded-xl glass text-[13.5px] font-medium hover:border-white/30 transition">Start over</button>
        </form>
      </div>
    </div>

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
                  <div class="font-mono text-[12px] text-cyan">{{ $beat['t'] }}</div>
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

          <div class="rounded-3xl p-6 border border-cyan/20 bg-cyan/5">
            <h3 class="font-display text-[17px] font-semibold text-cyan">QA gate on delivery</h3>
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

        {{-- matched creators --}}
        @if($matches->count())
          <div class="glass rounded-3xl p-6">
            <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">Creators matched to this brief</div>
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
@else

{{-- ── empty state: why it matters ── --}}
<section class="py-14">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid md:grid-cols-3 gap-5">
    @foreach([
      ['Fewer revisions', 'Gigs ordered with a generated brief come back right the first time far more often — because the creator gets beats and a spec, not a vibe.'],
      ['Comparable quotes', 'A structured brief means every creator prices the same scope. No more "it depends".'],
      ['Yours to keep', 'Copy it into any tool, send it to your own editor, or order it here. No lock-in, no email gate.'],
    ] as [$t, $b])
      <div class="glass rounded-3xl p-6 reveal">
        <div class="text-[15px] font-semibold">{{ $t }}</div>
        <p class="mt-2 text-[13.5px] leading-6 text-mut">{{ $b }}</p>
      </div>
    @endforeach
  </div>
</section>
@endif

@include('partials.cta', [
  'eyebrow'   => 'After the brief',
  'title'     => 'Matched, escrowed and QA-checked — in the same tab.',
  'body'      => 'Turn the brief into a live gig and watch it move through the pipeline.',
  'primary'   => ['Browse the marketplace', route('marketplace')],
  'secondary' => ['See how the engine works', route('ai')],
])

@endsection
