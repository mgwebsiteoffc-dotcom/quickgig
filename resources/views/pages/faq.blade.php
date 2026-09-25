@extends('layouts.site')

@section('content')
<section class="pt-14 pb-10">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[1fr_0.8fr] gap-10 items-center">
    <div>
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">FAQ</div>
      <h1 class="mt-4 font-display text-[38px] sm:text-[48px] font-semibold leading-[1.05]">
        Straight answers, <span class="grad-text">no sales call</span>.
      </h1>
      <p class="mt-5 text-[16px] leading-7 text-body max-w-[540px]">
        Matching, delivery speed, revisions, escrow, refunds, invoices and payouts. If something is
        missing, ask us and we will add it here.
      </p>
      <div class="mt-7 flex flex-wrap gap-3">
        <a href="{{ route('contact') }}" class="h-11 px-5 rounded-xl btn-grad font-semibold text-[13.5px] inline-flex items-center">Ask a question</a>
        <a href="{{ route('blog.index') }}" class="h-11 px-5 rounded-xl border border-line font-medium text-[13.5px] inline-flex items-center hover:border-ink/30 transition">Read the insights</a>
      </div>
    </div>
    <img src="{{ asset('img/iso-brief.png') }}" alt="" class="w-full max-w-[380px] mx-auto">
  </div>
</section>

<section class="band-light py-20">
  <div class="max-w-[860px] mx-auto px-5 lg:px-8" x-data="{ open: '0-0' }">
    @forelse($groups as $group => $items)
      <div class="mb-10">
        <div class="flex items-center gap-3">
          <h2 class="font-display text-[20px] font-semibold">{{ $group }}</h2>
          <span class="text-[11.5px] text-faint">{{ $items->count() }} {{ Str::plural('answer', $items->count()) }}</span>
        </div>

        <div class="mt-4 space-y-2.5">
          @foreach($items as $i => $f)
            @php $key = $loop->parent->index . '-' . $i; @endphp
            <div class="glass rounded-2xl overflow-hidden transition" :class="open === '{{ $key }}' ? 'border-mint' : ''">
              <button type="button" x-on:click="open = open === '{{ $key }}' ? '' : '{{ $key }}'" class="w-full flex items-center justify-between gap-5 p-5 text-left">
                <span class="text-[14.5px] font-medium">{{ $f->question }}</span>
                <span class="w-7 h-7 rounded-full grid place-items-center shrink-0 transition" :class="open === '{{ $key }}' ? 'bg-mint text-ink rotate-180' : 'bg-tint text-faint'">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6"/></svg>
                </span>
              </button>
              <div x-show="open === '{{ $key }}'" x-collapse x-cloak>
                <div class="px-5 pb-5 text-[14px] leading-7 text-body">{{ $f->answer }}</div>
              </div>
            </div>
          @endforeach
        </div>
      </div>
    @empty
      <div class="glass rounded-3xl p-12 text-center text-[14px] text-body">No questions published yet.</div>
    @endforelse
  </div>
</section>

@include('partials.cta', [
  'eyebrow'   => 'Still deciding?',
  'title'     => 'Try it on one gig — ₹1,299, refundable until you approve.',
  'body'      => 'The brief is free to generate and the escrow only moves when the work is right.',
  'primary'   => ['Browse the marketplace', route('marketplace')],
  'secondary' => ['Talk to us', route('contact')],
])
@endsection
