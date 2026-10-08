@extends('layouts.site')

@section('content')
<section class="py-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8">

    <nav class="flex items-center gap-2 text-[12.5px] text-mut">
      <a href="{{ route('marketplace') }}" class="hover:text-ink">Marketplace</a>
      <span class="opacity-40">/</span>
      <span class="text-body">{{ $gig->category }}</span>
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
                @if($gig->badge)<span class="text-[11px] font-semibold tracking-wider uppercase btn-grad text-white rounded-full px-2.5 py-1">{{ $gig->badge }}</span>@endif
                <span class="text-[11px] font-mono glass-strong rounded-full px-2.5 py-1">{{ $gig->billingLabel() }}</span>
              </div>
              <h1 class="mt-3 font-display text-[26px] sm:text-[32px] font-semibold leading-tight">{{ $gig->title }}</h1>
            </div>
          </div>

          <div class="p-6 sm:p-7">
            <div class="flex flex-wrap items-center gap-5 pb-6 border-b border-line">
              <div class="flex items-center gap-2 text-[13.5px]"><span class="text-mint-deep">★</span> {{ number_format((float) $gig->rating, 1) }} <span class="text-mut">rating</span></div>
              <div class="text-[13.5px] text-mut">{{ $gig->sold_count }} gigs delivered</div>
              <div class="text-[13.5px] text-mut">{{ $gig->revision_count > 0 ? $gig->revision_count.' revision'.($gig->revision_count > 1 ? 's' : '').' included' : 'Scope-based delivery' }}</div>
            </div>

            <div class="mt-6 prose-invert">
              <h2 class="font-display text-[18px] font-semibold">What you get</h2>
              <p class="mt-3 text-[14.5px] leading-7 text-mut whitespace-pre-line">{{ $gig->description ?: 'A clearly scoped service delivered by a verified GIG60 specialist.' }}</p>
              @if($gig->billing_type === 'monthly')
                <div class="mt-4 rounded-2xl border border-blue-200 bg-blue-50 px-4 py-3 text-[13px] text-blue-900"><b>Monthly service:</b> this is an ongoing management package. Ad spend, software subscriptions and third-party charges are separate unless stated otherwise.</div>
              @endif
              @if($gig->scope)
                <div class="mt-5"><h3 class="text-[14px] font-semibold">What is included</h3><p class="mt-1.5 text-[13.5px] leading-6 text-mut whitespace-pre-line">{{ $gig->scope }}</p></div>
              @endif
              @if($gig->not_included)
                <div class="mt-5 rounded-2xl border border-amber-200 bg-amber-50 p-4"><h3 class="text-[14px] font-semibold text-amber-900">What is not included</h3><p class="mt-1.5 text-[13.5px] leading-6 text-amber-900/75 whitespace-pre-line">{{ $gig->not_included }}</p></div>
              @endif
              @if($gig->process)
                <div class="mt-5"><h3 class="text-[14px] font-semibold">How it works</h3><p class="mt-1.5 text-[13.5px] leading-6 text-mut whitespace-pre-line">{{ $gig->process }}</p></div>
              @endif

              @php $deliverables = is_array($gig->deliverables) ? $gig->deliverables : []; @endphp
              @if(count($deliverables))
                <ul class="mt-6 grid sm:grid-cols-2 gap-2.5">
                  @foreach($deliverables as $d)
                    <li class="flex gap-2.5 text-[13.5px] glass rounded-2xl px-4 py-3">
                      <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#A3E635" stroke-width="2.6" class="shrink-0 mt-0.5"><path d="M20 6 9 17l-5-5"/></svg>
                      <span class="text-body">{{ $d }}</span>
                    </li>
                  @endforeach
                </ul>
              @endif
              @if(is_array($gig->faq_json) && count($gig->faq_json))
                <div class="mt-7 border-t border-line pt-6"><h3 class="font-display text-[17px] font-semibold">Questions about this service</h3><div class="mt-3 space-y-3">@foreach($gig->faq_json as $faq)<details class="rounded-2xl border border-line px-4 py-3"><summary class="cursor-pointer text-[13.5px] font-semibold">{{ $faq['q'] ?? '' }}</summary><p class="mt-2 text-[13px] leading-5 text-mut">{{ $faq['a'] ?? '' }}</p></details>@endforeach</div></div>
              @endif
            </div>
          </div>
        </div>

        <div class="mt-5 glass rounded-3xl p-6">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-mint-deep">What this solves</div>
          <div class="mt-4 grid sm:grid-cols-3 gap-3">
            @foreach([['Clear scope','You know exactly what will be delivered.'],['Managed matching','GIG60 selects the right specialist behind the scenes.'],['Protected approval','Funds stay in escrow until the result is approved.']] as [$t,$b])
              <div class="rounded-2xl border border-line bg-tint p-4"><div class="font-semibold text-[13px]">{{ $t }}</div><div class="mt-1 text-[12px] leading-5 text-mut">{{ $b }}</div></div>
            @endforeach
          </div>
        </div>
      </div>

      {{-- right: order panel --}}
      <aside class="lg:sticky lg:top-24" x-data="checkout({{ (int) ($gig->billing_type === 'monthly' && $gig->monthly_price ? $gig->monthly_price : $gig->price) }}, {{ (float) \App\Http\Controllers\OrderController::feeRate() }}, {{ (float) setting('platform.gst_percent', 18) }})">
        <div class="glass-strong rounded-3xl p-6 ring-glow">
          <div class="flex items-baseline justify-between">
            <span class="text-[12.5px] text-mut">Total</span>
            <span class="flex items-baseline gap-2">
              @if($gig->compareAt())
                <span class="text-[13px] line-through price-strike text-mut">₹{{ number_format($gig->compareAt()) }}</span>
              @endif
              <span class="font-display text-[32px] font-semibold" x-text="'₹' + total.toLocaleString('en-IN')"></span>
            </span>
          </div>
          @if($gig->discountPercent())
            <div class="mt-1.5 text-right">
              <span class="text-[11px] font-semibold px-2 py-0.5 rounded badge-off">{{ $gig->discountPercent() }}% off list price</span>
            </div>
          @endif

          @if($gig->billing_type === 'monthly')
            <div class="mt-5 rounded-2xl border border-blue-200 bg-blue-50 p-4 text-[13px] text-blue-900"><div class="font-semibold">Monthly management package</div><div class="mt-1">The amount shown is the monthly service charge. Your ad spend and third-party tools are billed separately.</div></div>
          @else
            <div class="mt-5 text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Delivery speed</div>
            <div class="mt-3 space-y-2">
              @foreach(\App\Http\Controllers\OrderController::LANES as $key => $lane)
                <label class="flex items-center justify-between gap-3 rounded-2xl px-4 py-3 border cursor-pointer transition" :class="lane === '{{ $key }}' ? 'border-mint bg-mint-wash' : 'border-line bg-tint hover:border-line'">
                  <span class="flex items-center gap-3"><input type="radio" name="lane_preview" value="{{ $key }}" x-model="lane" class="accent-violet"><span class="text-[13.5px] font-medium">{{ $lane['label'] }}</span></span>
                  <span class="text-[12px] font-mono text-mut">{{ $lane['mult'] > 1 ? '+'.round(($lane['mult']-1)*100).'%' : ($lane['mult'] < 1 ? round(($lane['mult']-1)*100).'%' : 'base') }}</span>
                </label>
              @endforeach
            </div>
          @endif

          <div class="mt-5 rounded-2xl border border-line bg-tint p-4 space-y-2 text-[12.5px]">
            <div class="flex justify-between"><span class="text-mut">Service amount</span><span class="font-mono" x-text="'₹' + customerSubtotal.toLocaleString('en-IN')"></span></div>
            <div class="flex justify-between"><span class="text-mut">GST</span><span class="font-mono" x-text="'₹' + tax.toLocaleString('en-IN')"></span></div>
            <div class="flex justify-between font-semibold"><span class="text-body">Total payable</span><span class="font-mono text-mint-deep" x-text="'₹' + payable.toLocaleString('en-IN')"></span></div>
            <div class="pt-2 mt-2 border-t border-line flex justify-between"><span class="text-mut">Held in escrow until you approve</span><span class="font-mono">✓</span></div>
          </div>

          @auth
            <form method="POST" action="{{ route('orders.store') }}" class="mt-5">
              @csrf
              <input type="hidden" name="service_id" value="{{ $gig->id }}">
              <input type="hidden" name="lane" :value="lane">

              <div class="flex items-center justify-between">
                <label class="label" for="brief">Your brief</label>
                @if(session('brief.draft'))
                  <span class="label text-mint-deep">loaded from brief builder</span>
                @else
                  <a href="{{ route('brief-builder') }}" class="label text-mint-deep hover:text-ink transition">generate one free</a>
                @endif
              </div>
              <textarea id="brief" name="brief" rows="{{ session('brief.draft') ? 8 : 4 }}" required class="field" placeholder="What are we making? Share the goal, tone, references and any deadlines.">{{ old('brief', session('brief.draft')) }}</textarea>
              @error('brief')<div class="mt-2 text-[12.5px] text-rose-300">{{ $message }}</div>@enderror

              <button class="mt-4 w-full h-12 rounded-xl btn-grad font-semibold text-[14.5px] shadow-lg shadow-ink/10">
                {{ $gig->billing_type === 'monthly' ? 'Start monthly package' : 'Order now — pay into escrow' }}
              </button>
              <div class="mt-3 text-[11.5px] text-center text-mut">You are not charged until you approve the delivery in this demo environment.</div>
            </form>
          @else
            <a href="{{ route('register') }}?type=business" class="mt-5 w-full h-12 rounded-xl btn-grad font-semibold text-[14.5px] grid place-items-center shadow-lg shadow-ink/10">
              Create an account to order
            </a>
            <div class="mt-3 text-[12.5px] text-center text-mut">Already a member? <a href="{{ route('login') }}" class="text-ink font-medium underline decoration-mint decoration-2 underline-offset-4">Log in</a></div>
          @endauth
        </div>

        <div class="mt-4 glass rounded-3xl p-5">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Included with every gig</div>
          <ul class="mt-3.5 space-y-2.5">
            @foreach(($gig->billing_type === 'monthly' ? ['Monthly scope and reporting', 'Account review and optimisation', 'Clear performance updates', 'Chat with the specialist'] : ['Escrow protection', ($gig->revision_count ? $gig->revision_count.' revisions included' : 'Scope-based delivery'), 'Live production tracking', 'Chat with the managed delivery team']) as $item)
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
  Alpine.data('checkout', (base, marginRate, gstRate) => ({
    base, marginRate, gstRate,
    lane: 'standard',
    mults: { express: 1.6, standard: 1, relaxed: 0.85 },
    get payout() { return Math.round(this.base * this.mults[this.lane]); },
    get margin() { return Math.round(this.payout * this.marginRate); },
    get customerSubtotal() { return this.payout + this.margin; },
    get tax() { return Math.round(this.customerSubtotal * (this.gstRate / 100)); },
    get payable() { return this.customerSubtotal + this.tax; },
  }));
});
</script>
@endpush
