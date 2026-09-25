@extends('layouts.site')

@section('content')

<section class="pt-16 pb-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[1.05fr_0.95fr] gap-12 items-center">
    <div>
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-soft">For freelancers</div>
      <h1 class="mt-4 font-display text-[38px] sm:text-[50px] font-semibold leading-[1.04]">
        Stop bidding.<br><span class="grad-text">Start delivering.</span>
      </h1>
      <p class="mt-5 text-[16px] leading-7 text-mut max-w-[540px]">
        No proposals, no connects, no racing to the bottom on price. Gigs arrive with a full brief
        attached, the money is already in escrow, and you keep 90% of it.
      </p>

      <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('register') }}?type=creator" class="h-12 px-6 rounded-xl btn-grad font-semibold text-[14.5px] inline-flex items-center gap-2 shadow-xl shadow-violet/25">
          Apply as a freelancer
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a href="#earnings" class="h-12 px-6 rounded-xl glass font-medium text-[14.5px] inline-flex items-center hover:border-white/30 transition">See what you would earn</a>
      </div>

      <div class="mt-9 flex flex-wrap gap-x-8 gap-y-4">
        @foreach([['90%', 'you keep'], ['₹0', 'to bid or list'], ['Instant', 'payout on approval']] as [$v, $l])
          <div>
            <div class="font-display text-[24px] font-semibold tracking-tight">{{ $v }}</div>
            <div class="text-[12px] text-mut mt-0.5">{{ $l }}</div>
          </div>
        @endforeach
      </div>
    </div>

    {{-- incoming gig card --}}
    <div class="relative">
      <div class="absolute -inset-6 bg-violet/12 blur-3xl rounded-full -z-10"></div>
      <div class="glass-strong rounded-3xl p-6 ring-glow">
        <div class="flex items-center justify-between">
          <div class="flex items-center gap-2 text-[11px] font-semibold tracking-[.14em] uppercase text-white/50">
            <span class="w-1.5 h-1.5 rounded-full bg-lime pulse-dot text-lime"></span> New gig offer
          </div>
          <span class="text-[11.5px] font-mono text-white/45">expires in 9:41</span>
        </div>

        <div class="mt-5">
          <div class="font-display text-[19px] font-semibold">Retention reel · 45 seconds</div>
          <div class="text-[12.5px] text-mut mt-1">Avante Studio · express lane · brief attached</div>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-3">
          <div class="rounded-2xl border border-white/8 bg-white/3 px-4 py-3">
            <div class="text-[11px] text-mut">Your payout</div>
            <div class="text-[19px] font-semibold font-mono text-lime">₹3,598</div>
          </div>
          <div class="rounded-2xl border border-white/8 bg-white/3 px-4 py-3">
            <div class="text-[11px] text-mut">Due</div>
            <div class="text-[19px] font-semibold font-mono">3h 00m</div>
          </div>
        </div>

        <div class="mt-4 rounded-2xl border border-white/8 bg-black/25 p-4">
          <div class="text-[11px] font-semibold tracking-[.12em] uppercase text-white/40">Included with the offer</div>
          <ul class="mt-2.5 space-y-1.5 text-[12.5px] text-mut">
            <li>• Timed beat sheet and three hook options</li>
            <li>• Technical spec (aspect, length, loudness)</li>
            <li>• Money already held in escrow</li>
          </ul>
        </div>

        <div class="mt-5 grid grid-cols-2 gap-3">
          <button class="h-11 rounded-xl btn-grad font-semibold text-[13.5px]">Accept</button>
          <button class="h-11 rounded-xl glass font-medium text-[13.5px]">Pass</button>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── earnings calculator ── --}}
<section id="earnings" class="py-16 border-y border-white/8 scroll-mt-20">
  <div class="max-w-shell mx-auto px-5 lg:px-8" x-data="earnings()">
    <div class="max-w-[620px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-soft">Earnings</div>
      <h2 class="mt-3 font-display text-[30px] sm:text-[38px] font-semibold leading-[1.1]">What a realistic month looks like.</h2>
      <p class="mt-4 text-[15px] leading-7 text-mut">Set your weekly capacity and average gig price. This is take-home after our flat 10%.</p>
    </div>

    <div class="mt-10 grid lg:grid-cols-[1fr_1fr] gap-6 items-start">
      <div class="glass rounded-3xl p-6 sm:p-7 space-y-7">
        <div>
          <div class="flex items-center justify-between text-[13.5px]">
            <span>Gigs per week</span><span class="font-mono text-white/80" x-text="perWeek"></span>
          </div>
          <input type="range" min="1" max="20" x-model.number="perWeek" class="mt-2.5 w-full accent-violet cursor-pointer">
        </div>
        <div>
          <div class="flex items-center justify-between text-[13.5px]">
            <span>Average gig price</span><span class="font-mono text-white/80" x-text="'₹' + price.toLocaleString('en-IN')"></span>
          </div>
          <input type="range" min="999" max="9999" step="100" x-model.number="price" class="mt-2.5 w-full accent-violet cursor-pointer">
        </div>
        <label class="flex items-center gap-3 text-[13.5px] text-mut pt-2 border-t border-white/8">
          <input type="checkbox" x-model="express" class="w-4 h-4 rounded border-white/20 bg-white/5 accent-violet">
          I take express gigs (1.6× price, 3-hour turnaround)
        </label>
      </div>

      <div class="space-y-4">
        <div class="glass-strong rounded-3xl p-7 ring-glow">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">Monthly take-home</div>
          <div class="mt-2 font-display text-[44px] font-semibold tracking-tight" x-text="'₹' + takeHome.toLocaleString('en-IN')"></div>
          <div class="mt-1 text-[13px] text-mut"><span x-text="perMonth"></span> gigs · after the 10% platform fee</div>

          <div class="mt-6 space-y-2.5 text-[13px]">
            <div class="flex justify-between"><span class="text-mut">Gross billed</span><span class="font-mono" x-text="'₹' + gross.toLocaleString('en-IN')"></span></div>
            <div class="flex justify-between"><span class="text-mut">Platform fee (10%)</span><span class="font-mono">−<span x-text="'₹' + fee.toLocaleString('en-IN')"></span></span></div>
            <div class="flex justify-between"><span class="text-mut">Bidding / connect fees</span><span class="font-mono text-lime">₹0</span></div>
          </div>
        </div>

        <div class="glass rounded-3xl p-6">
          <div class="text-[13px] text-mut leading-6">
            On a 20% commission marketplace the same month pays
            <span class="font-mono text-white/80" x-text="'₹' + elsewhere.toLocaleString('en-IN')"></span> —
            <span class="text-lime font-medium" x-text="'₹' + (takeHome - elsewhere).toLocaleString('en-IN') + ' less'"></span>,
            before whatever you spend on bids to win the work.
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── how work arrives ── --}}
<section class="band-lav py-20">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-soft">How work reaches you</div>
      <h2 class="mt-3 font-display text-[30px] sm:text-[38px] font-semibold leading-[1.1]">Four steps, no sales work.</h2>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach([
        ['Get verified', 'ID, portfolio and two references. Usually reviewed within a day. Verified profiles rank higher and can take express gigs.'],
        ['Stay visible', 'Flip your availability on. The engine only matches freelancers who are actually free — no ghost listings.'],
        ['Accept a brief', 'Offers arrive with beats, spec and escrow already funded. Accept or pass in one tap; passing costs you nothing.'],
        ['Get paid', 'Deliver, pass the QA gate, get approved. Escrow releases to your UPI or bank immediately.'],
      ] as $i => [$t, $b])
        <div class="reveal glass rounded-3xl p-6 card-hover" style="transition-delay: {{ $i * 70 }}ms">
          <div class="font-display text-[30px] font-semibold text-white/10">0{{ $i + 1 }}</div>
          <div class="mt-3 text-[16px] font-semibold">{{ $t }}</div>
          <p class="mt-2 text-[13.5px] leading-6 text-mut">{{ $b }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── copilot + fairness ── --}}
<section class="py-16 border-y border-white/8">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-2 gap-6">
    <div class="glass rounded-3xl p-7">
      <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-violet-soft">Freelancer copilot</div>
      <h3 class="mt-3 font-display text-[22px] font-semibold">Tools that reduce your rounds</h3>
      <ul class="mt-5 space-y-3.5">
        @foreach([
          'Shot list and reference frames generated from the client brief',
          'Live spec checks while you work — aspect, length, loudness',
          'Client feedback arrives as timestamped notes, not paragraphs',
          'Pricing guidance based on what similar gigs actually closed at',
        ] as $line)
          <li class="flex gap-3 text-[13.5px] leading-6 text-mut">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.6" class="shrink-0 mt-1"><path d="M20 6 9 17l-5-5"/></svg>{{ $line }}
          </li>
        @endforeach
      </ul>
    </div>

    <div class="glass rounded-3xl p-7">
      <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-violet-soft">Fair by design</div>
      <h3 class="mt-3 font-display text-[22px] font-semibold">The rules we hold ourselves to</h3>
      <ul class="mt-5 space-y-3.5">
        @foreach([
          'Clients cannot reject a delivery that passed QA without a written reason',
          'Two free revisions are the limit — further rounds are billable at your rate',
          'Your match score breakdown is visible to you, so ranking is never a mystery',
          'Passing on a gig never lowers your score; missing a deadline does',
        ] as $line)
          <li class="flex gap-3 text-[13.5px] leading-6 text-mut">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#22D3EE" stroke-width="2.6" class="shrink-0 mt-1"><path d="M20 6 9 17l-5-5"/></svg>{{ $line }}
          </li>
        @endforeach
      </ul>
    </div>
  </div>
</section>

@include('partials.cta', [
  'eyebrow'   => 'Applications are open',
  'tone'      => 'cyan',
  'title'     => 'Join ' . number_format($creatorCount) . '+ verified freelancers taking briefs today.',
  'body'      => 'Free to join. Verification usually takes under a day. You choose every gig you accept.',
  'primary'   => ['Apply as a freelancer', route('register').'?type=creator'],
  'secondary' => ['See how matching works', route('ai')],
])

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('earnings', () => ({
    perWeek: 5, price: 2499, express: false,
    get perMonth() { return Math.round(this.perWeek * 4.3); },
    get gross()    { return Math.round(this.perMonth * this.price * (this.express ? 1.6 : 1)); },
    get fee()      { return Math.round(this.gross * 0.10); },
    get takeHome() { return this.gross - this.fee; },
    get elsewhere(){ return Math.round(this.gross * 0.80); },
  }));
});
</script>
@endpush
