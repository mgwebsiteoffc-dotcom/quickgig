@extends('layouts.site')

@section('content')
<section class="py-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8">

    <nav class="flex items-center gap-2 text-[12.5px] text-mut">
      <a href="{{ route('marketplace') }}" class="hover:text-white">Marketplace</a>
      <span class="opacity-40">/</span>
      <span class="text-white/70">{{ $gig->category }}</span>
    </nav>

    <div class="mt-6 grid lg:grid-cols-[1fr_400px] gap-8 items-start">

      {{-- left: gig --}}
      <div>
        <div class="glass rounded-3xl overflow-hidden">
          <div class="relative h-[260px] sm:h-[340px]">
            <img src="{{ $gig->coverUrl() }}" alt="{{ $gig->title }}" class="w-full h-full object-cover">
            <div class="absolute inset-0 bg-gradient-to-t from-ink via-ink/25 to-transparent"></div>
            <div class="absolute bottom-5 left-5 right-5">
              <div class="flex flex-wrap items-center gap-2">
                <span class="text-[11px] font-semibold tracking-wider uppercase glass-strong rounded-full px-2.5 py-1">{{ $gig->category }}</span>
                @if($gig->badge)<span class="text-[11px] font-semibold tracking-wider uppercase btn-grad text-ink rounded-full px-2.5 py-1">{{ $gig->badge }}</span>@endif
                <span class="text-[11px] font-mono glass-strong rounded-full px-2.5 py-1">{{ $gig->deliveryLabel() }} delivery</span>
              </div>
              <h1 class="mt-3 font-display text-[26px] sm:text-[32px] font-semibold leading-tight">{{ $gig->title }}</h1>
            </div>
          </div>

          <div class="p-6 sm:p-7">
            <div class="flex flex-wrap items-center gap-5 pb-6 border-b border-white/8">
              <div class="flex items-center gap-2 text-[13.5px]"><span class="text-cyan">★</span> {{ number_format((float) $gig->rating, 1) }} <span class="text-mut">rating</span></div>
              <div class="text-[13.5px] text-mut">{{ $gig->sold_count }} gigs delivered</div>
              <div class="text-[13.5px] text-mut">{{ $gig->revision_count ?? 2 }} free revisions</div>
            </div>

            <div class="mt-6 prose-invert">
              <h2 class="font-display text-[18px] font-semibold">What you get</h2>
              <p class="mt-3 text-[14.5px] leading-7 text-mut whitespace-pre-line">{{ $gig->description ?: 'A complete, ready-to-publish deliverable produced by a verified Quick GIGS creator. Includes source-quality export, one round of notes and fast turnaround.' }}</p>

              @php $deliverables = is_array($gig->deliverables) ? $gig->deliverables : []; @endphp
              @if(count($deliverables))
                <ul class="mt-6 grid sm:grid-cols-2 gap-2.5">
                  @foreach($deliverables as $d)
                    <li class="flex gap-2.5 text-[13.5px] glass rounded-2xl px-4 py-3">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A3E635" stroke-width="2.6" class="shrink-0 mt-0.5"><path d="M20 6 9 17l-5-5"/></svg>
                      <span class="text-white/80">{{ $d }}</span>
                    </li>
                  @endforeach
                </ul>
              @endif
            </div>
          </div>
        </div>

        {{-- creator --}}
        @if($gig->creator)
          <div class="mt-5 glass rounded-3xl p-6">
            <div class="flex items-start gap-4">
              <img src="{{ $gig->creator->avatarUrl() }}" class="w-14 h-14 rounded-2xl object-cover border border-white/12" alt="">
              <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2">
                  <a href="{{ route('creator.public', $gig->creator->id) }}" class="font-display text-[17px] font-semibold hover:text-violet-soft transition">{{ $gig->creator->name }}</a>
                  @if($gig->creator->is_verified)
                    <span class="text-[10.5px] font-semibold rounded-full bg-cyan/15 text-cyan px-2 py-0.5">Verified</span>
                  @endif
                  @if($gig->creator->is_available)
                    <span class="text-[10.5px] font-semibold rounded-full bg-lime/15 text-lime px-2 py-0.5">Available now</span>
                  @endif
                </div>
                <div class="text-[12.5px] text-mut mt-0.5">{{ $gig->creator->handle }} · {{ $gig->creator->headline }}</div>
                <p class="mt-3 text-[13.5px] leading-6 text-mut">{{ Str::limit($gig->creator->bio ?: 'Verified Quick GIGS creator.', 220) }}</p>

                <div class="mt-4 flex flex-wrap gap-2">
                  @foreach((array) ($gig->creator->skills ?? []) as $skill)
                    <span class="text-[11.5px] rounded-full border border-white/10 px-2.5 py-1 text-mut">{{ $skill }}</span>
                  @endforeach
                </div>
              </div>
            </div>

            <div class="mt-5 grid grid-cols-3 gap-3">
              @foreach([
                ['Response', ($gig->creator->response_minutes ?: 8).' min'],
                ['On time', ($gig->creator->on_time_rate ?: 97).'%'],
                ['Gigs', $gig->creator->orders_count ?: 0],
              ] as [$k, $v])
                <div class="rounded-2xl border border-white/8 bg-white/3 px-4 py-3">
                  <div class="text-[11px] text-mut">{{ $k }}</div>
                  <div class="text-[15px] font-semibold font-mono mt-0.5">{{ $v }}</div>
                </div>
              @endforeach
            </div>
          </div>
        @endif
      </div>

      {{-- right: order panel --}}
      <aside class="lg:sticky lg:top-24" x-data="checkout({{ (int) $gig->price }})">
        <div class="glass-strong rounded-3xl p-6 ring-glow">
          <div class="flex items-baseline justify-between">
            <span class="text-[12.5px] text-mut">Total</span>
            <span class="font-display text-[32px] font-semibold" x-text="'₹' + total.toLocaleString('en-IN')"></span>
          </div>

          <div class="mt-5 text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">Delivery speed</div>
          <div class="mt-3 space-y-2">
            @foreach(\App\Http\Controllers\OrderController::LANES as $key => $lane)
              <label class="flex items-center justify-between gap-3 rounded-2xl px-4 py-3 border cursor-pointer transition"
                     :class="lane === '{{ $key }}' ? 'border-violet bg-violet/12' : 'border-white/10 bg-white/3 hover:border-white/25'">
                <span class="flex items-center gap-3">
                  <input type="radio" name="lane_preview" value="{{ $key }}" x-model="lane" class="accent-violet">
                  <span class="text-[13.5px] font-medium">{{ $lane['label'] }}</span>
                </span>
                <span class="text-[12px] font-mono text-mut">{{ $lane['mult'] > 1 ? '+'.round(($lane['mult']-1)*100).'%' : ($lane['mult'] < 1 ? round(($lane['mult']-1)*100).'%' : 'base') }}</span>
              </label>
            @endforeach
          </div>

          <div class="mt-5 rounded-2xl border border-white/10 bg-black/25 p-4 space-y-2 text-[12.5px]">
            <div class="flex justify-between"><span class="text-mut">Gig price</span><span class="font-mono" x-text="'₹' + total.toLocaleString('en-IN')"></span></div>
            <div class="flex justify-between"><span class="text-mut">Platform fee (10%)</span><span class="font-mono" x-text="'₹' + fee.toLocaleString('en-IN')"></span></div>
            <div class="flex justify-between"><span class="text-mut">Creator receives</span><span class="font-mono text-lime" x-text="'₹' + payout.toLocaleString('en-IN')"></span></div>
            <div class="pt-2 mt-2 border-t border-white/8 flex justify-between"><span class="text-mut">Held in escrow until you approve</span><span class="font-mono">✓</span></div>
          </div>

          @auth
            <form method="POST" action="{{ route('orders.store') }}" class="mt-5">
              @csrf
              <input type="hidden" name="service_id" value="{{ $gig->id }}">
              <input type="hidden" name="lane" :value="lane">

              <label class="label" for="brief">Your brief</label>
              <textarea id="brief" name="brief" rows="4" required class="field" placeholder="What are we making? Share the goal, tone, references and any deadlines.">{{ old('brief') }}</textarea>
              @error('brief')<div class="mt-2 text-[12.5px] text-rose-300">{{ $message }}</div>@enderror

              <button class="mt-4 w-full h-12 rounded-xl btn-grad font-semibold text-[14.5px] shadow-lg shadow-violet/20">
                Order now — pay into escrow
              </button>
              <div class="mt-3 text-[11.5px] text-center text-mut">You are not charged until you approve the delivery in this demo environment.</div>
            </form>
          @else
            <a href="{{ route('register') }}?type=business" class="mt-5 w-full h-12 rounded-xl btn-grad font-semibold text-[14.5px] grid place-items-center shadow-lg shadow-violet/20">
              Create an account to order
            </a>
            <div class="mt-3 text-[12.5px] text-center text-mut">Already a member? <a href="{{ route('login') }}" class="text-white hover:underline">Log in</a></div>
          @endauth
        </div>

        <div class="mt-4 glass rounded-3xl p-5">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">Included with every gig</div>
          <ul class="mt-3.5 space-y-2.5">
            @foreach(['Escrow protection', '2 free revisions', 'Live production tracking', 'Chat with the creator'] as $item)
              <li class="flex gap-2.5 text-[13px] text-mut">
                <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="2.6" class="shrink-0 mt-0.5"><path d="M20 6 9 17l-5-5"/></svg>
                {{ $item }}
              </li>
            @endforeach
          </ul>
        </div>
      </aside>
    </div>

    {{-- related --}}
    @if($related->count())
      <div class="mt-16">
        <h2 class="font-display text-[22px] font-semibold">Similar gigs</h2>
        <div class="mt-6 grid sm:grid-cols-3 gap-5">
          @foreach($related as $r)
            <a href="{{ route('gigs.show', $r->id) }}" class="glass rounded-3xl overflow-hidden card-hover group">
              <img src="{{ $r->coverUrl() }}" alt="{{ $r->title }}" class="h-[140px] w-full object-cover opacity-85 group-hover:opacity-100 transition">
              <div class="p-4">
                <div class="text-[14px] font-semibold leading-snug line-clamp-2">{{ $r->title }}</div>
                <div class="mt-2.5 flex items-center justify-between">
                  <span class="font-display text-[16px] font-semibold">{{ $r->displayPrice() }}</span>
                  <span class="text-[11.5px] text-mut">{{ $r->deliveryLabel() }}</span>
                </div>
              </div>
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('checkout', (base) => ({
    base,
    lane: 'standard',
    mults: { express: 1.6, standard: 1, relaxed: 0.85 },
    get total()  { return Math.round(this.base * this.mults[this.lane]); },
    get fee()    { return Math.round(this.total * 0.10); },
    get payout() { return this.total - this.fee; },
  }));
});
</script>
@endpush
