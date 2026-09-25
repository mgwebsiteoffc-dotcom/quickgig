@extends('layouts.site')

@section('content')

<section class="pt-16 pb-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[720px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">How it works</div>
      <h1 class="mt-4 font-display text-[38px] sm:text-[50px] font-semibold leading-[1.04]">
        Seven stages between <span class="grad-text">your idea</span> and a published asset.
      </h1>
      <p class="mt-5 text-[16px] leading-7 text-mut max-w-[600px]">
        Most platforms automate the checkout and leave you to run the project. Quick GIGS automates the
        parts that actually waste your week: writing the brief, choosing a freelancer, chasing status,
        checking quality and re-cutting for every format.
      </p>
      <div class="mt-8 flex flex-wrap gap-3">
        <a href="{{ route('brief-builder') }}" class="h-12 px-6 rounded-xl btn-grad font-semibold text-[14.5px] inline-flex items-center gap-2 shadow-xl shadow-ink/10">Start with a free brief</a>
        <a href="{{ route('marketplace') }}" class="h-12 px-6 rounded-xl glass font-medium text-[14.5px] inline-flex items-center hover:border-line transition">Skip to the marketplace</a>
      </div>
    </div>

    {{-- timing strip --}}
    <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach([
        ['00:00', 'You describe the job', 'one sentence is enough'],
        ['00:40', 'Brief is generated', 'hooks, beats, spec, QA gate'],
        ['04:12', 'Freelancer accepts', 'scored and explained'],
        ['03:00 h', 'Delivery in review', 'express lane, QA already passed'],
      ] as $i => [$t, $title, $sub])
        <div class="glass rounded-3xl p-5 reveal" style="transition-delay: {{ $i * 70 }}ms">
          <div class="font-mono text-[12px] text-mint-deep">{{ $t }}</div>
          <div class="mt-2 text-[15px] font-semibold">{{ $title }}</div>
          <div class="text-[12.5px] text-mut mt-1">{{ $sub }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── pipeline ── --}}
<section class="py-16 border-y border-line">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">The pipeline</div>
      <h2 class="mt-3 font-display text-[30px] sm:text-[38px] font-semibold leading-[1.1]">Every gig runs the same seven stages.</h2>
    </div>

    <div class="mt-14 space-y-4">
      @foreach($pipeline as $i => $stage)
        <div class="reveal glass rounded-3xl p-6 sm:p-8 card-hover grid lg:grid-cols-[180px_1fr_200px] gap-6 items-start">
          <div>
            <div class="font-mono text-[11.5px] text-mint-deep">{{ $stage['stage'] }}</div>
            <div class="mt-2 font-display text-[20px] font-semibold leading-tight">{{ $stage['title'] }}</div>
            <div class="mt-2 inline-flex items-center gap-1.5 text-[11.5px] text-mut glass rounded-full px-2.5 py-1">
              <span class="w-1.5 h-1.5 rounded-full bg-mint"></span>{{ $stage['time'] }}
            </div>
          </div>

          <p class="text-[14.5px] leading-7 text-mut">{{ $stage['body'] }}</p>

          <div class="lg:text-right">
            <div class="text-[12px] text-body font-medium">{{ $stage['proof'] }}</div>
            <a href="{{ route($stage['link']) }}" class="mt-3 inline-flex h-10 px-4 rounded-xl glass items-center text-[13px] font-medium hover:border-mint transition">
              See it →
            </a>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── guarantees ── --}}
<section class="band-light py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="grid lg:grid-cols-[0.9fr_1.1fr] gap-12 items-start">
      <div>
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">What we guarantee</div>
        <h2 class="mt-3 font-display text-[30px] sm:text-[38px] font-semibold leading-[1.1]">Written down, not implied.</h2>
        <p class="mt-4 text-[15px] leading-7 text-mut">
          These are enforced by the product, not by a support agent's goodwill.
        </p>
        <a href="{{ route('compare') }}" class="mt-7 inline-flex h-11 px-5 rounded-xl glass items-center text-[13.5px] font-medium hover:border-line transition">Compare us honestly →</a>
      </div>

      <div class="grid sm:grid-cols-2 gap-4">
        @foreach([
          ['Escrow by default', 'Your money is held, not spent. Released only when you press approve, refundable before that.'],
          ['SLA or credit', 'Miss the promised window and the express premium is credited back automatically.'],
          ['Two free revisions', 'Included on every gig, translated into timestamped notes so they actually land.'],
          ['No lock-in', 'No subscription, no commitment fee, no minimum volume. Order one gig or two hundred.'],
          ['Freelancers keep 90%', 'A flat 10% platform fee. No connects, no bidding credits, no listing charges.'],
          ['Your files, your rights', 'Full commercial rights transfer on approval, with licensed music and footage only.'],
        ] as $i => [$t, $b])
          <div class="reveal glass rounded-3xl p-6" style="transition-delay: {{ $i * 60 }}ms">
            <div class="w-9 h-9 rounded-xl bg-mint-wash grid place-items-center">
              <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A3E635" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg>
            </div>
            <div class="mt-4 text-[15px] font-semibold">{{ $t }}</div>
            <p class="mt-2 text-[13.5px] leading-6 text-mut">{{ $b }}</p>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</section>

@include('partials.cta', [
  'eyebrow'   => 'Ready when you are',
  'title'     => 'Write one line. We will handle the other six stages.',
  'body'      => 'The brief builder is free and needs no account. If you like what it produces, order the gig in two clicks.',
  'primary'   => ['Build a free brief', route('brief-builder')],
  'secondary' => ['Create an account', route('register')],
  'note'      => 'No card required · escrow protected',
])

@endsection
