@extends('layouts.site')

@section('content')

<section class="pt-16 pb-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[720px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-soft">About</div>
      <h1 class="mt-4 font-display text-[38px] sm:text-[50px] font-semibold leading-[1.04]">
        We got tired of the <span class="grad-text">waiting</span>.
      </h1>
      <p class="mt-5 text-[16px] leading-7 text-mut max-w-[620px]">
        Quick GIGS started in Ghaziabad with a simple frustration: a 45-second reel took nine days —
        two writing the brief, three choosing a freelancer, one making the video and three fixing
        things nobody had written down. The making was never the slow part.
      </p>
      <p class="mt-4 text-[16px] leading-7 text-mut max-w-[620px]">
        So we automated everything except the craft. The brief writes itself, matching is scored and
        explained, money sits in escrow, and quality is checked before anything reaches your inbox.
        Creators keep 90% because they are the ones doing the work.
      </p>
    </div>

    <div class="mt-14 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach([
        [number_format($stats['gigs']) . '+', 'gigs in the catalogue'],
        [number_format($stats['creators']) . '+', 'creators on the platform'],
        ['4 min', 'average time to a match'],
        ['90%', 'of every rupee to creators'],
      ] as $i => [$v, $l])
        <div class="reveal glass rounded-3xl p-6" style="transition-delay: {{ $i * 70 }}ms">
          <div class="font-display text-[30px] font-semibold tracking-tight">{{ $v }}</div>
          <div class="text-[12.5px] text-mut mt-1">{{ $l }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── principles ── --}}
<section class="band-lav py-20">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-cyan">Principles</div>
      <h2 class="mt-3 font-display text-[30px] sm:text-[38px] font-semibold leading-[1.1]">Four rules we build against.</h2>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 gap-5">
      @foreach([
        ['Show the maths', 'If a score decides who gets paid, both sides should see how it was calculated. No hidden ranking.'],
        ['Automate the admin, never the craft', 'Briefs, matching, QA and repurposing are software problems. The work itself is made by people.'],
        ['Default to the creator', 'Flat 10%, instant payouts, no bidding fees, and rules that stop clients from moving the goalposts after delivery.'],
        ['Say the uncomfortable part', 'Our comparison page lists three cases where you should hire someone else. Trust is worth more than a conversion.'],
      ] as $i => [$t, $b])
        <div class="reveal glass rounded-3xl p-7 card-hover" style="transition-delay: {{ $i * 60 }}ms">
          <div class="font-display text-[34px] font-semibold text-white/8">0{{ $i + 1 }}</div>
          <div class="mt-3 text-[17px] font-semibold">{{ $t }}</div>
          <p class="mt-2 text-[14px] leading-7 text-mut">{{ $b }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── timeline ── --}}
<section class="py-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[0.8fr_1.2fr] gap-10">
    <div>
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-soft">So far</div>
      <h2 class="mt-3 font-display text-[28px] sm:text-[34px] font-semibold leading-[1.1]">A short history.</h2>
    </div>

    <div class="space-y-6">
      @foreach([
        ['2025', 'One spreadsheet', 'Fourteen creators, a WhatsApp group and a Google Sheet tracking who was free. It worked, and it did not scale.'],
        ['2026', 'The engine', 'Scored matching, escrow and the QA gate replaced the spreadsheet. Turnaround dropped from days to hours.'],
        ['2027', 'Brief engine and pods', 'Briefs became automatic, repurposing shipped, and teams started running dedicated creator pods.'],
      ] as $i => [$year, $t, $b])
        <div class="flex gap-5">
          <div class="flex flex-col items-center">
            <span class="w-3 h-3 rounded-full btn-grad mt-1.5"></span>
            @if(!$loop->last)<span class="w-px flex-1 bg-white/12 my-2"></span>@endif
          </div>
          <div class="pb-2">
            <div class="font-mono text-[12px] text-cyan">{{ $year }}</div>
            <div class="mt-1 text-[17px] font-semibold">{{ $t }}</div>
            <p class="mt-1.5 text-[14px] leading-7 text-mut max-w-[560px]">{{ $b }}</p>
          </div>
        </div>
      @endforeach
    </div>
  </div>
</section>

@include('partials.cta', [
  'eyebrow'   => 'Come build with us',
  'title'     => 'Hiring creators, and talking to teams who publish weekly.',
  'body'      => 'If you make things or need things made, there is a door for you here.',
  'primary'   => ['Get started free', route('register')],
  'secondary' => ['Contact the team', route('contact')],
])

@endsection
