@extends('layouts.site')

@php
  $plans = [
    ['slug'=>'starter','name'=>'Starter','price'=>1299,'retainer'=>9999,'unit'=>'/ gig','featured'=>false,
     'tagline'=>'Thumbnails, edits and quick fixes.',
     'features'=>['1-day delivery','2 free revisions','Verified freelancer + chat','Escrow protection','Generated brief included']],
    ['slug'=>'pro','name'=>'Pro','price'=>2499,'retainer'=>24999,'unit'=>'/ gig','featured'=>true,
     'tagline'=>'Retention reels and UGC that convert.',
     'features'=>['Express lane from 3 hours','Priority matching','Live production tracking','Automated QA gate','Auto-repurpose to 4 formats','Escrow protection']],
    ['slug'=>'studio','name'=>'Studio','price'=>8999,'retainer'=>74999,'unit'=>'/ pack','featured'=>false,
     'tagline'=>'A micro-team for full campaigns.',
     'features'=>['Editor + designer + AI pipeline','Reel + thumbnail + captions','2-day turnaround','Dedicated pod and manager','Consolidated invoicing']],
  ];
@endphp

@section('content')

<section class="band-light pt-16 pb-16" x-data="{ mode:'gig' }">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="text-center max-w-[680px] mx-auto">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Pricing</div>
      <h1 class="mt-4 font-display text-[38px] sm:text-[50px] font-semibold leading-[1.04]">
        You see the price <span class="grad-text">before</span> you commit.
      </h1>
      <p class="mt-5 text-[16px] leading-7 text-mut">
        Flat gig prices, a single 10% platform fee, and escrow on every order. No subscription, no
        commitment money, no connects, no surprise scope invoice at the end of the month.
      </p>

      <div class="mt-8 inline-flex glass rounded-2xl p-1">
        <button x-on:click="mode='gig'" class="h-10 px-5 rounded-xl text-[13.5px] font-medium transition" :class="mode==='gig' ? 'btn-grad text-white' : 'text-mut hover:text-ink'">Pay per gig</button>
        <button x-on:click="mode='retainer'" class="h-10 px-5 rounded-xl text-[13.5px] font-medium transition" :class="mode==='retainer' ? 'btn-grad text-white' : 'text-mut hover:text-ink'">Monthly retainer <span class="text-[11px] opacity-70">−20%</span></button>
      </div>
    </div>

    <div class="mt-12 grid md:grid-cols-3 gap-5 items-start">
      @foreach($plans as $p)
        <div class="reveal rounded-3xl p-7 card-hover relative {{ $p['featured'] ? 'glass-strong ring-glow' : 'glass' }}">
          @if($p['featured'])
            <span class="absolute -top-3 left-7 btn-grad text-white text-[10.5px] font-bold tracking-wider uppercase px-3 py-1 rounded-full">Most popular</span>
          @endif
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">{{ $p['name'] }}</div>
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
                <span class="text-body">{{ $f }}</span>
              </li>
            @endforeach
          </ul>

          <a href="{{ route('register') }}?type=business&plan={{ $p['slug'] }}"
             class="mt-7 h-12 rounded-xl grid place-items-center font-semibold text-[14px] transition {{ $p['featured'] ? 'btn-grad shadow-lg shadow-ink/10' : 'glass hover:border-line' }}">
            Start with {{ $p['name'] }}
          </a>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── calculator ── --}}
<section class="py-16 border-y border-line">
  <div class="max-w-shell mx-auto px-5 lg:px-8" x-data="costCalc()">
    <div class="max-w-[620px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Cost calculator</div>
      <h2 class="mt-3 font-display text-[30px] sm:text-[38px] font-semibold leading-[1.1]">What a month actually costs.</h2>
      <p class="mt-4 text-[15px] leading-7 text-mut">Set your monthly output and compare the three real options: Quick GIGS, an in-house editor, or an agency retainer.</p>
    </div>

    <div class="mt-10 grid lg:grid-cols-[1fr_1.15fr] gap-6 items-start">
      <div class="glass rounded-3xl p-6 sm:p-7 space-y-6">
        <template x-for="item in items" :key="item.key">
          <div>
            <div class="flex items-center justify-between text-[13.5px]">
              <span x-text="item.label"></span>
              <span class="font-mono text-body"><span x-text="item.qty"></span> / month</span>
            </div>
            <input type="range" min="0" max="40" x-model.number="item.qty" class="mt-2.5 w-full accent-violet cursor-pointer">
            <div class="mt-1 text-[11.5px] text-mut" x-text="'₹' + item.price.toLocaleString('en-IN') + ' each'"></div>
          </div>
        </template>

        <label class="flex items-center gap-3 text-[13.5px] text-mut pt-2 border-t border-line">
          <input type="checkbox" x-model="retainer" class="w-4 h-4 rounded border-line bg-tint accent-violet">
          Bill as a monthly retainer (−20%)
        </label>
      </div>

      <div class="space-y-4">
        <div class="glass-strong rounded-3xl p-6 ring-glow">
          <div class="flex items-baseline justify-between">
            <div>
              <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Quick GIGS</div>
              <div class="text-[12.5px] text-mut mt-1"><span x-text="totalUnits"></span> deliverables · escrow protected</div>
            </div>
            <div class="font-display text-[34px] font-semibold tracking-tight" x-text="'₹' + ours.toLocaleString('en-IN')"></div>
          </div>
          <div class="mt-4 h-1.5 rounded-full bg-tint overflow-hidden"><div class="h-full btn-grad" :style="`width:${barOurs}%`"></div></div>
        </div>

        <template x-for="alt in alternatives" :key="alt.label">
          <div class="glass rounded-3xl p-6">
            <div class="flex items-baseline justify-between">
              <div>
                <div class="text-[13.5px] font-semibold" x-text="alt.label"></div>
                <div class="text-[12px] text-mut mt-1" x-text="alt.note"></div>
              </div>
              <div class="font-display text-[24px] font-semibold text-body" x-text="'₹' + alt.cost.toLocaleString('en-IN')"></div>
            </div>
            <div class="mt-4 h-1.5 rounded-full bg-tint overflow-hidden"><div class="h-full bg-tint" :style="`width:${alt.bar}%`"></div></div>
          </div>
        </template>

        <div class="rounded-3xl p-6 bg-mint-wash border border-mint/35">
          <div class="text-[13px] text-body">Versus the cheapest alternative you save</div>
          <div class="mt-1 font-display text-[28px] font-semibold text-mint-deep" x-text="'₹' + saving.toLocaleString('en-IN') + ' / month'"></div>
          <div class="mt-1.5 text-[12.5px] text-mut">and you only pay for what you approve.</div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ── fee comparison ── --}}
<section class="py-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Where the money goes</div>
      <h2 class="mt-3 font-display text-[30px] sm:text-[38px] font-semibold leading-[1.1]">On a ₹2,499 gig.</h2>
    </div>

    <div class="mt-10 grid sm:grid-cols-3 gap-4">
      @foreach([
        ['Quick GIGS', '₹2,249', 'to the freelancer', '₹250 platform fee (10%). Nothing else — no listing fee, no connects, no payout charge.', true],
        ['Bidding marketplaces', '₹1,999', 'to the freelancer', 'Around 20% commission, plus paid bids or connects before they even win the job.', false],
        ['Managed agencies', 'Not disclosed', 'to the freelancer', 'You pay a retainer or commitment fee; the split with the actual maker is rarely shown.', false],
      ] as [$name, $amount, $sub, $body, $highlight])
        <div class="reveal rounded-3xl p-6 {{ $highlight ? 'glass-strong ring-glow' : 'glass' }}">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">{{ $name }}</div>
          <div class="mt-4 font-display text-[30px] font-semibold {{ $highlight ? 'text-mint-deep' : 'text-body' }}">{{ $amount }}</div>
          <div class="text-[12px] text-mut">{{ $sub }}</div>
          <p class="mt-4 text-[13px] leading-6 text-mut">{{ $body }}</p>
        </div>
      @endforeach
    </div>

    {{-- full comparison --}}
    <div class="mt-10 glass rounded-3xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[820px] text-left">
          <thead>
            <tr class="border-b border-line">
              <th class="p-5 text-[11px] font-semibold tracking-[.14em] uppercase text-faint">How it compares</th>
              @foreach($comparison['columns'] as $i => $col)
                <th class="p-5 text-[13px] font-semibold {{ $i === 0 ? 'text-ink' : 'text-mut' }}">{{ $col }}</th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach(array_slice($comparison['rows'], 0, 5) as $row)
              <tr class="border-b border-line last:border-0">
                @foreach($row as $i => $cell)
                  <td class="p-5 align-top text-[13.5px] {{ $i === 0 ? 'text-body' : ($i === 1 ? 'text-ink font-medium' : 'text-mut') }}">{{ $cell }}</td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
      <div class="p-5 border-t border-line text-center">
        <a href="{{ route('compare') }}" class="text-[13.5px] text-mint-deep hover:text-ink transition">See the full comparison, including where we are not the right choice →</a>
      </div>
    </div>
  </div>
</section>

{{-- ── pricing FAQ ── --}}
<section class="band-lav py-16">
  <div class="max-w-[820px] mx-auto px-5 lg:px-8" x-data="{ open: 0 }">
    <h2 class="font-display text-[30px] font-semibold text-center">Pricing questions</h2>
    <div class="mt-10 space-y-3">
      @foreach([
        ['Is there any subscription or minimum?', 'No. You can order a single ₹1,299 gig and never come back. The retainer option exists only because teams asked for predictable monthly billing at a discount.'],
        ['When is my card charged?', 'At order time the amount moves into escrow. It is released to the freelancer when you approve, or refunded to you if you reject the delivery within the review window.'],
        ['What does the 10% fee cover?', 'Matching, the brief engine, escrow and payouts, the QA gate, dispute handling and support. Freelancers keep the other 90%.'],
        ['Do express gigs cost more?', 'Yes — express is 1.6× the base price because the freelancer reorganises their day for it. Relaxed 48-hour delivery is 0.85×. You always see the exact number before ordering.'],
        ['What if the deadline is missed?', 'The express premium is credited back automatically, and you keep the right to reject the delivery and recover the escrow.'],
        ['Do you invoice with GST?', 'Yes. Add your GSTIN in workspace settings and every order produces a GST-compliant invoice; teams can consolidate into one monthly invoice.'],
      ] as $i => [$q, $a])
        <div class="glass rounded-2xl overflow-hidden" :class="open === {{ $i }} ? 'border-mint' : ''">
          <button type="button" x-on:click="open = open === {{ $i }} ? -1 : {{ $i }}" class="w-full flex items-center justify-between gap-5 p-5 text-left">
            <span class="text-[14.5px] font-medium">{{ $q }}</span>
            <span class="w-7 h-7 rounded-full grid place-items-center shrink-0 transition" :class="open === {{ $i }} ? 'btn-grad text-white rotate-180' : 'bg-tint text-faint'">
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

@include('partials.cta', [
  'eyebrow'   => 'No card required',
  'title'     => 'Price a real gig in 30 seconds.',
  'body'      => 'Build the brief free, see the exact price with the fee split, then decide.',
  'primary'   => ['Build a free brief', route('brief-builder')],
  'secondary' => ['Talk to us about volume', route('enterprise')],
])

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('costCalc', () => ({
    retainer: false,
    items: [
      { key:'reel',  label:'Short-form reels',   qty: 8, price: 2499 },
      { key:'thumb', label:'Thumbnail packs',    qty: 6, price: 1299 },
      { key:'ugc',   label:'UGC product videos', qty: 2, price: 3999 },
      { key:'ai',    label:'AI video ads',       qty: 1, price: 6499 },
    ],
    get totalUnits() { return this.items.reduce((n, i) => n + i.qty, 0); },
    get ours() {
      const raw = this.items.reduce((n, i) => n + i.qty * i.price, 0);
      return Math.round(this.retainer ? raw * 0.8 : raw);
    },
    get alternatives() {
      const units = this.totalUnits;
      const inhouse = 52000 + Math.max(0, units - 20) * 900;   // salary + overflow freelancers
      const agency  = 85000 + Math.max(0, units - 15) * 1800;  // retainer + overages
      const max = Math.max(this.ours, inhouse, agency, 1);
      return [
        { label: 'In-house editor', note: 'salary, tools and overflow creators', cost: inhouse, bar: Math.round(inhouse / max * 100) },
        { label: 'Agency retainer', note: 'monthly retainer plus scope overages',   cost: agency,  bar: Math.round(agency / max * 100) },
      ];
    },
    get barOurs() {
      const max = Math.max(this.ours, ...this.alternatives.map(a => a.cost), 1);
      return Math.round(this.ours / max * 100);
    },
    get saving() {
      const cheapest = Math.min(...this.alternatives.map(a => a.cost));
      return Math.max(0, cheapest - this.ours);
    },
  }));
});
</script>
@endpush
