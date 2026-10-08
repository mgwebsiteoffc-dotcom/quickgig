@extends('layouts.site')

@section('content')
<section class="pt-14 pb-10 border-b border-line">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-6">
      <div class="max-w-[620px]">
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Services</div>
        <h1 class="mt-3 font-display text-[34px] sm:text-[42px] font-semibold leading-[1.08]">Choose the outcome. We handle the match.</h1>
        <p class="mt-4 text-[15px] leading-7 text-mut">
          Browse ready-to-order video, image, UGC, AI, design, copy and marketing services. You choose the result; GIG60 handles the specialist behind it.
        </p>
      </div>

      <div class="flex items-center gap-2.5">
        <a href="{{ route('register') }}?type=business" class="h-11 px-5 rounded-xl btn-grad inline-flex items-center text-[13.5px] font-semibold">Post a custom brief</a>
      </div>
    </div>

    {{-- search + filters (real GET form) --}}
    <form method="GET" action="{{ route('services') }}" class="mt-9 glass rounded-3xl p-4 sm:p-5">
      <div class="flex flex-col lg:flex-row gap-3">
        <div class="relative flex-1">
          <svg class="absolute left-4 top-1/2 -translate-y-1/2 text-faint" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="7"/><path d="m16.5 16.5 4.5 4.5"/></svg>
          <input name="q" value="{{ $filters['q'] }}" placeholder="Search gigs — reel editing, thumbnails, AI ads…" class="field pl-11">
        </div>
        <select name="sort" class="field lg:w-[210px]">
          @foreach($sorts as $key => $label)
            <option value="{{ $key }}" @selected($filters['sort'] === $key)>{{ $label }}</option>
          @endforeach
        </select>
        <button class="h-12 px-6 rounded-xl btn-grad font-semibold text-[14px] shrink-0">Search</button>
      </div>

      <div class="mt-4 flex flex-wrap items-center gap-2">
        <a href="{{ route('services', array_filter(['q' => $filters['q'], 'sort' => $filters['sort']])) }}"
           class="rounded-full px-3.5 py-1.5 text-[12.5px] font-medium border transition {{ $filters['category'] === '' ? 'border-mint bg-mint-wash text-ink' : 'border-line text-mut hover:text-ink hover:border-line' }}">
          All categories
        </a>
        @foreach($categories as $cat => $count)
          <a href="{{ route('services', array_filter(['q' => $filters['q'], 'sort' => $filters['sort'], 'category' => $cat])) }}"
             class="rounded-full px-3.5 py-1.5 text-[12.5px] font-medium border transition {{ $filters['category'] === $cat ? 'border-mint bg-mint-wash text-ink' : 'border-line text-mut hover:text-ink hover:border-line' }}">
            {{ $cat }} <span class="opacity-50">{{ $count }}</span>
          </a>
        @endforeach

        <label class="ml-auto flex items-center gap-2 text-[12.5px] text-mut cursor-pointer">
          <input type="checkbox" name="fast" value="1" @checked($filters['fast']) onchange="this.form.submit()" class="w-4 h-4 rounded border-line bg-tint accent-violet">
          24-hour delivery only
        </label>
      </div>
    </form>
  </div>
</section>

<section class="py-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[1fr_290px] gap-10 items-start">

    <div>
      <div class="flex items-center justify-between mb-6">
        <div class="text-[13.5px] text-mut">
          {{ $gigs->total() }} {{ Str::plural('gig', $gigs->total()) }}
          @if($filters['q']) for “<span class="text-ink font-medium">{{ $filters['q'] }}</span>” @endif
        </div>
        @if($filters['q'] || $filters['category'] || $filters['fast'])
          <a href="{{ route('services') }}" class="text-[12.5px] text-mut hover:text-ink">Clear filters ✕</a>
        @endif
      </div>

      @if($gigs->count())
        <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
          @foreach($gigs as $gig)
            <a href="{{ route('gigs.show', $gig->slug) }}" class="glass rounded-3xl overflow-hidden card-hover group flex flex-col">
              <div class="relative h-[160px] overflow-hidden">
                <img src="{{ $gig->coverUrl() }}" alt="{{ $gig->title }}" class="w-full h-full object-cover opacity-85 group-hover:opacity-100 group-hover:scale-[1.04] transition duration-500">
                <div class="absolute inset-0 bg-gradient-to-t from-ink/95 via-ink/20 to-transparent"></div>
                @if($gig->badge)<span class="absolute left-3 top-3 text-[10.5px] font-semibold tracking-wider uppercase glass-strong rounded-full px-2.5 py-1">{{ $gig->badge }}</span>@endif
                <span class="absolute right-3 top-3 text-[11px] font-mono glass-strong rounded-full px-2.5 py-1">{{ $gig->deliveryLabel() }}</span>
              </div>

              <div class="p-5 flex-1 flex flex-col">
                  <div class="flex items-center gap-2 text-[11px] font-semibold tracking-[.12em] uppercase text-mint-deep"><span class="w-1.5 h-1.5 rounded-full bg-mint"></span> GIG60 service</div>

                <div class="mt-3 text-[15px] font-semibold leading-snug line-clamp-2 group-hover:text-ink">{{ $gig->title }}</div>
                <div class="mt-2 text-[12.5px] text-mut line-clamp-2">{{ Str::limit(strip_tags($gig->description ?? ''), 80) }}</div>

                <div class="mt-auto pt-4 flex items-end justify-between">
                  <div>
                    <div class="flex items-baseline gap-2">
                      <span class="font-display text-[19px] font-semibold">{{ $gig->displayPrice() }}</span>
                      @if($gig->compareAt())
                        <span class="text-[12px] line-through price-strike text-mut">₹{{ number_format($gig->compareAt()) }}</span>
                      @endif
                    </div>
                    @if($gig->discountPercent())
                      <span class="mt-1 inline-block text-[10.5px] font-semibold px-1.5 py-0.5 rounded badge-off">{{ $gig->discountPercent() }}% off</span>
                    @endif
                  </div>
                  <div class="text-[11.5px] text-mut text-right">
                    <div>{{ number_format((float) $gig->rating, 1) }} ★</div>
                    <div>{{ $gig->sold_count }} sold</div>
                  </div>
                </div>
              </div>
            </a>
          @endforeach
        </div>

        <div class="mt-10">{{ $gigs->links('vendor.pagination.quickgigs') }}</div>
      @else
        <div class="glass rounded-3xl p-14 text-center">
          <div class="font-display text-[20px] font-semibold">No gigs match those filters</div>
          <p class="mt-2 text-[14px] text-mut">Try a different category, or post a custom brief and let the matching engine find someone.</p>
          <div class="mt-6 flex flex-wrap justify-center gap-3">
            <a href="{{ route('services') }}" class="h-11 px-5 rounded-xl glass inline-flex items-center text-[13.5px] font-medium">Clear filters</a>
            <a href="{{ route('register') }}?type=business" class="h-11 px-5 rounded-xl btn-grad inline-flex items-center text-[13.5px] font-semibold">Post a brief</a>
          </div>
        </div>
      @endif
    </div>

    {{-- sidebar --}}
    <aside class="space-y-5 lg:sticky lg:top-24">
      <div class="glass rounded-3xl p-5">
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Shop by outcome</div>
        <div class="mt-4 flex flex-wrap gap-2">
          @foreach(['Video & reels','Images & design','UGC content','AI ads','Copy & content','Google & Meta Ads'] as $category)
            <a href="{{ route('services', ['category' => $category]) }}" class="rounded-full border border-line bg-tint px-3 py-2 text-[12px] text-body hover:border-mint hover:bg-mint-wash transition">{{ $category }}</a>
          @endforeach
        </div>
      </div>

      <div class="glass rounded-3xl p-5">
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">How buying works</div>
        <ol class="mt-4 space-y-3">
          @foreach(['Pick a gig and a speed lane', 'Pay into escrow — nothing is released yet', 'Track production live and chat', 'Approve to release the payout'] as $i => $line)
            <li class="flex gap-3 text-[13px] leading-5">
              <span class="w-5 h-5 rounded-md bg-tint grid place-items-center text-[10.5px] font-mono shrink-0">{{ $i+1 }}</span>
              <span class="text-mut">{{ $line }}</span>
            </li>
          @endforeach
        </ol>
      </div>

      <div class="rounded-3xl p-5 bg-tint border border-line">
        <div class="text-[14.5px] font-semibold">Need something custom?</div>
        <p class="mt-1.5 text-[13px] leading-5 text-body">Describe it once — we match a pro in minutes.</p>
        <a href="{{ route('register') }}?type=business" class="mt-4 h-10 rounded-xl btn-grad grid place-items-center text-[13.5px] font-semibold">Post a brief</a>
      </div>
    </aside>
  </div>
</section>
@endsection
