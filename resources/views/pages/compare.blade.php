@extends('layouts.site')

@section('content')

<section class="pt-16 pb-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[720px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Compare</div>
      <h1 class="mt-4 font-display text-[38px] sm:text-[50px] font-semibold leading-[1.04]">
        Four ways to get content made. <span class="grad-text">Here they are side by side.</span>
      </h1>
      <p class="mt-5 text-[16px] leading-7 text-mut max-w-[620px]">
        Quick-commerce gig apps are fast to check out. Managed agencies take the quality burden off you.
        Bidding marketplaces have the deepest supply. We built Quick GIGS because none of them tell you
        <em>why</em> a freelancer was chosen, or stop bad work before it reaches your inbox.
      </p>
    </div>
  </div>
</section>

{{-- ── the table ── --}}
<section class="band-light py-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="glass rounded-3xl overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left">
          <thead>
            <tr class="border-b border-line bg-tint">
              <th class="p-5 text-[11px] font-semibold tracking-[.14em] uppercase text-faint w-[220px]"></th>
              @foreach($comparison['columns'] as $i => $col)
                <th class="p-5 text-[13.5px] font-semibold {{ $i === 0 ? 'text-white' : 'text-mut' }}">
                  @if($i === 0)
                    <span class="inline-flex items-center gap-2">
                      <span class="w-5 h-5 rounded-md btn-grad grid place-items-center">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="#06060B"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z"/></svg>
                      </span>{{ $col }}
                    </span>
                  @else
                    {{ $col }}
                  @endif
                </th>
              @endforeach
            </tr>
          </thead>
          <tbody>
            @foreach($comparison['rows'] as $row)
              <tr class="border-b border-line last:border-0 hover:bg-tint transition">
                @foreach($row as $i => $cell)
                  <td class="p-5 align-top text-[13.5px] leading-6
                    {{ $i === 0 ? 'text-faint font-medium' : ($i === 1 ? 'text-white' : 'text-mut') }}">
                    @if($i === 1)
                      <span class="inline-flex items-start gap-2">
                        <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="#A3E635" stroke-width="2.6" class="shrink-0 mt-1"><path d="M20 6 9 17l-5-5"/></svg>
                        {{ $cell }}
                      </span>
                    @else
                      {{ $cell }}
                    @endif
                  </td>
                @endforeach
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    <div class="mt-3 text-[11.5px] text-mut">
      Comparison reflects publicly documented behaviour of each model in September 2026. Individual providers vary — check their current terms.
    </div>
  </div>
</section>

{{-- ── the three switches ── --}}
<section class="py-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Why teams switch</div>
      <h2 class="mt-3 font-display text-[30px] sm:text-[38px] font-semibold leading-[1.1]">Three differences that change the week.</h2>
    </div>

    <div class="mt-12 space-y-5">
      @foreach([
        ['The brief is not your job anymore', 'Every other model starts with "send us a brief". We generate it from one sentence — hooks, beats, spec, QA gate — and hand the same document to the freelancer. Fewer rounds, comparable quotes, nothing lost in a DM.', 'Try the brief builder', route('brief-builder')],
        ['Matching you can audit', 'You get a score out of 100 with the five factors that produced it: skill fit, availability, reliability, rating and budget. Re-weight them and the ranking changes in front of you.', 'Open the engine', route('ai')],
        ['Bad deliveries never reach you', 'Six automated checks run before hand-off. A 4:5 export, missing captions or unlicensed music bounces back to the freelancer automatically — you only see work that already matches the spec.', 'See the QA gate', route('ai').'#m02'],
      ] as $i => [$t, $b, $cta, $href])
        <div class="reveal glass rounded-3xl p-7 sm:p-8 grid lg:grid-cols-[80px_1fr_180px] gap-6 items-center card-hover">
          <div class="font-display text-[42px] font-semibold text-ink/10">0{{ $i + 1 }}</div>
          <div>
            <div class="font-display text-[20px] font-semibold">{{ $t }}</div>
            <p class="mt-2.5 text-[14.5px] leading-7 text-mut">{{ $b }}</p>
          </div>
          <a href="{{ $href }}" class="h-11 px-5 rounded-xl glass inline-flex items-center justify-center text-[13px] font-medium hover:border-mint transition">{{ $cta }} →</a>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── honesty section ── --}}
<section class="py-16 border-y border-line">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[0.85fr_1.15fr] gap-10">
    <div>
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Straight talk</div>
      <h2 class="mt-3 font-display text-[28px] sm:text-[34px] font-semibold leading-[1.1]">When we are not the right choice.</h2>
      <p class="mt-4 text-[15px] leading-7 text-mut">
        Sales pages that pretend to win every scenario are not useful. Three cases where you should pick something else.
      </p>
    </div>

    <div class="space-y-4">
      @foreach([
        ['You need a full-time person embedded in your team', 'If someone needs to sit in your standups and own a roadmap, hire them. We are built for defined deliverables, not headcount.'],
        ['You need a 12-week brand film with a crew', 'Multi-week productions with locations, casting and legal clearances belong with a production house. We handle work measured in hours and days.'],
        ['You want the absolute lowest price on earth', 'Bidding marketplaces will always undercut us at the bottom end. Our floor is ₹1,299 because that is what a verified freelancer can do the work for properly.'],
      ] as [$t, $b])
        <div class="glass rounded-3xl p-6">
          <div class="flex gap-3.5">
            <span class="w-7 h-7 rounded-lg bg-amber-400/15 grid place-items-center shrink-0 text-amber-300 text-[14px] font-bold">!</span>
            <div>
              <div class="text-[15px] font-semibold">{{ $t }}</div>
              <p class="mt-1.5 text-[13.5px] leading-6 text-mut">{{ $b }}</p>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

@include('partials.cta', [
  'eyebrow'   => 'Decide with your own brief',
  'title'     => 'Run one gig through us and compare the result.',
  'body'      => 'Start at ₹1,299, escrow protected, refundable until you approve. That is a cheap way to settle the argument.',
  'primary'   => ['Browse the marketplace', route('marketplace')],
  'secondary' => ['Build a free brief', route('brief-builder')],
])

@endsection
