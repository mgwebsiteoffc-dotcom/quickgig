@extends('layouts.site')
@section('content')

{{-- GIG60 is arranged as one decision journey: problem → solution → process → proof → service. --}}
<section class="pt-12 sm:pt-20 pb-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[1.08fr_.92fr] gap-12 items-center">
    <div>
      <div class="inline-flex items-center gap-2 rounded-full bg-mint-wash text-mint-deep px-3 py-1.5 text-[11px] font-bold tracking-[.12em] uppercase">The work is urgent. The process should not be.</div>
      <h1 class="mt-5 font-display text-[42px] sm:text-[62px] leading-[.98] font-semibold tracking-[-.04em]">Your business has a problem.<br><span class="grad-text">GIG60 finds the path.</span></h1>
      <p class="mt-5 text-[16px] leading-7 text-mut max-w-[590px]">GIG60 is an AI-driven managed production system. Tell us what needs solving; AI turns it into a clear brief, routes it to the right service and keeps delivery accountable behind the scenes.</p>
      <div class="mt-7 flex flex-wrap gap-3"><a href="{{ route('brief-builder') }}" class="h-12 px-6 rounded-xl btn-grad font-semibold inline-flex items-center">Describe your problem →</a><a href="{{ route('marketplace') }}" class="h-12 px-6 rounded-xl glass font-semibold inline-flex items-center">Browse services</a></div>
      <div class="mt-8 flex flex-wrap gap-x-7 gap-y-3 text-[12.5px] text-mut"><span>✓ Verified specialists</span><span>✓ Escrow protected</span><span>✓ GST shown clearly</span><span>✓ 100% of agreed creator payout</span></div>
    </div>
    <div class="glass-strong rounded-3xl p-6 sm:p-8 ring-glow">
      <div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">The problem in one screen</div>
      <div class="mt-5 space-y-3">
        @foreach([['01','Briefs are unclear','Work starts before everyone agrees what “done” means.'],['02','Finding the right person takes days','You compare profiles instead of moving the work forward.'],['03','Approval becomes negotiation','Revisions, scope and payment get mixed together.']] as [$n,$t,$b])
          <div class="flex gap-3 rounded-2xl bg-tint p-4"><div class="font-mono text-[12px] text-mint-deep pt-0.5">{{ $n }}</div><div><div class="font-semibold text-[14px]">{{ $t }}</div><div class="mt-1 text-[12.5px] leading-5 text-mut">{{ $b }}</div></div></div>
        @endforeach
      </div>
      <div class="mt-5 rounded-2xl bg-ink text-white p-4 text-[13px] leading-6">GIG60 replaces the messy middle with one accountable path from problem to approved work.</div>
    </div>
  </div>
</section>

{{-- Keep the brief generator in the first decision path: problem in, production brief out. --}}
<section id="brief-generator" class="pb-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="rounded-3xl bg-ink text-white p-6 sm:p-9 grid lg:grid-cols-[1fr_auto] gap-7 items-center">
      <div>
        <div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint">Brief generator</div>
        <h2 class="mt-3 font-display text-[26px] sm:text-[34px] font-semibold">Start with one sentence. Get a usable brief.</h2>
        <p class="mt-2 text-[14px] leading-6 text-white/70 max-w-[620px]">The free GIG60 brief generator turns your goal, audience and deadline into clear deliverables before any service is selected.</p>
      </div>
      <a href="{{ route('brief-builder') }}" class="h-12 px-6 rounded-xl bg-white text-ink font-semibold inline-flex items-center justify-center">Open brief generator →</a>
    </div>
  </div>
</section>

<section class="pb-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-4"><div><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">Start with an outcome</div><h2 class="mt-3 font-display text-[28px] sm:text-[38px] font-semibold">Choose the kind of work you need.</h2></div><a href="{{ route('marketplace') }}" class="text-[13px] font-semibold text-mint-deep">Explore services →</a></div>
    <div class="mt-7 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
      @foreach([['Video & reels','Reels, edits, clips'],['Images & design','Thumbnails, brand, social'],['UGC content','Ads and creator content'],['AI ads','Product and campaign video'],['Copy & content','Scripts, pages, captions'],['Growth services','Google and Meta campaigns']] as [$t,$b])
        <a href="{{ route('marketplace', ['q' => $t]) }}" class="glass rounded-2xl p-4 hover:border-mint hover:-translate-y-0.5 transition"><div class="w-9 h-9 rounded-xl bg-mint-wash text-mint-deep grid place-items-center font-bold">→</div><div class="mt-4 text-[13.5px] font-semibold">{{ $t }}</div><div class="mt-1 text-[11.5px] leading-4 text-mut">{{ $b }}</div></a>
      @endforeach
    </div>
  </div>
</section>

<section class="band-lav py-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[650px]"><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">The solution</div><h2 class="mt-3 font-display text-[30px] sm:text-[42px] font-semibold leading-tight">One operating system for getting digital work made.</h2><p class="mt-3 text-[15px] leading-7 text-mut">GIG60 combines the clarity of a brief, the speed of a marketplace and the accountability of a managed service.</p></div>
    <div class="mt-10 grid md:grid-cols-3 gap-4">
      @foreach([['Clear brief','One sentence becomes deliverables, references, timing and a definition of done.'],['Right match','The right managed production path is selected by skill, availability and fit.'],['Safe delivery','Escrow, live progress, feedback and approval keep both sides moving.']] as [$t,$b])
        <div class="glass rounded-3xl p-6"><div class="w-10 h-10 rounded-2xl bg-mint-wash text-mint-deep grid place-items-center font-display font-bold">✓</div><h3 class="mt-5 font-display text-[20px] font-semibold">{{ $t }}</h3><p class="mt-2 text-[13.5px] leading-6 text-mut">{{ $b }}</p></div>
      @endforeach
    </div>
  </div>
</section>

<section class="py-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8"><div class="text-center"><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">How it works</div><h2 class="mt-3 font-display text-[30px] sm:text-[40px] font-semibold">From problem to approved delivery.</h2></div>
    <div class="mt-10 grid md:grid-cols-4 gap-4">
      @foreach([['01','Describe','Tell us the goal, format, references and deadline.'],['02','Match','See the best-fit specialist and why they fit.'],['03','Make','Track the work, chat and request focused revisions.'],['04','Approve','Approve the result; escrow releases the agreed payout.']] as [$n,$t,$b])
        <div class="relative glass rounded-3xl p-6"><div class="font-mono text-[12px] text-mint-deep">{{ $n }}</div><h3 class="mt-4 font-display text-[20px] font-semibold">{{ $t }}</h3><p class="mt-2 text-[13px] leading-6 text-mut">{{ $b }}</p></div>
      @endforeach
    </div>
  </div>
</section>

<section class="py-16 bg-tint">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[680px]"><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">The AI process</div><h2 class="mt-3 font-display text-[30px] sm:text-[40px] font-semibold">AI handles the coordination. GIG60 manages the craft.</h2><p class="mt-3 text-[15px] leading-7 text-mut">GIG60 uses AI to remove the slow, repetitive parts of getting work made—without replacing the verified specialist who creates the final work.</p></div>
    <div class="mt-9 grid md:grid-cols-5 gap-3">
      @foreach([['01','Understand','Reads the goal, audience, format and deadline.'],['02','Structure','Turns a rough request into a clear production brief.'],['03','Match','Scores skills, availability, rating and delivery fit.'],['04','Check','Tests the delivery against the agreed brief and quality checks.'],['05','Improve','Learns from feedback and makes the next match sharper.']] as [$n,$t,$b])
        <div class="glass rounded-2xl p-4"><div class="font-mono text-[11px] text-mint-deep">{{ $n }}</div><div class="mt-3 text-[15px] font-semibold">{{ $t }}</div><div class="mt-1.5 text-[12px] leading-5 text-mut">{{ $b }}</div></div>
      @endforeach
    </div>
  </div>
</section>

<section class="band-dark py-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8"><div class="max-w-[620px]"><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint">The four pillars</div><h2 class="mt-3 font-display text-[30px] sm:text-[42px] font-semibold leading-tight">Less searching. Less explaining. More shipping.</h2></div>
    <div class="mt-9 grid md:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach([['Problem-led','We start with the outcome, not a catalogue of disconnected profiles.'],['Explainable','You see why a specialist is matched instead of trusting a black box.'],['Fair to talent','No creator platform fee; the creator sees the agreed payout before accepting.'],['Business-ready','One-off work, monthly plans and multi-specialist teams in one place.']] as [$t,$b])
        <div class="rounded-3xl border border-white/15 bg-white/5 p-5"><h3 class="font-display text-[18px] font-semibold text-white">{{ $t }}</h3><p class="mt-2 text-[13px] leading-6 text-faint">{{ $b }}</p></div>
      @endforeach
    </div>
  </div>
</section>

<section class="py-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8"><div class="flex flex-wrap items-end justify-between gap-4"><div><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">Services</div><h2 class="mt-3 font-display text-[30px] sm:text-[40px] font-semibold">Pick the problem you need solved.</h2><p class="mt-2 text-[14px] text-mut">Start with a fixed-scope service. Add a pod when the work becomes recurring.</p></div><a href="{{ route('marketplace') }}" class="text-[13px] font-semibold text-mint-deep">See all services →</a></div>
    <div class="mt-9 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach(collect($gigs)->take(4) as $g)
        <a href="{{ route('gigs.show',$g['id'] ?? 1) }}" class="glass rounded-3xl overflow-hidden card-hover"><img src="{{ $g['img'] }}" class="h-[145px] w-full object-cover" alt=""><div class="p-5"><div class="text-[10px] font-bold tracking-widest uppercase text-mint-deep">{{ $g['badge'] ?? 'Service' }}</div><h3 class="mt-2 text-[15px] font-semibold leading-5">{{ $g['title'] }}</h3><div class="mt-4 flex justify-between items-center"><span class="font-display text-[18px] font-semibold">{{ $g['price'] }}</span><span class="text-[11.5px] text-mut">{{ $g['time'] }}</span></div></div></a>
      @endforeach
    </div>
  </div>
</section>

<section class="band-lav py-16"><div class="max-w-shell mx-auto px-5 lg:px-8"><div class="flex flex-wrap items-center justify-between gap-6"><div><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">For businesses and teams</div><h2 class="mt-3 font-display text-[28px] sm:text-[38px] font-semibold">One gig today. A reliable creative desk tomorrow.</h2><p class="mt-3 text-[14px] leading-6 text-mut max-w-[620px]">Use fixed services for urgent work, monthly plans for recurring demand and managed pods when several specialists need to work together.</p></div><a href="{{ route('for-business') }}" class="h-12 px-6 rounded-xl btn-grad font-semibold inline-flex items-center">Build your workflow →</a></div></div></section>

<section class="py-16"><div class="max-w-shell mx-auto px-5 lg:px-8 text-center"><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">Ready to move?</div><h2 class="mt-3 font-display text-[32px] sm:text-[46px] font-semibold">Bring the problem. We will bring the process.</h2><p class="mt-3 text-[15px] text-mut">Free to start. Clear scope. Verified talent. Escrow protected.</p><div class="mt-7 flex justify-center gap-3"><a href="{{ route('brief-builder') }}" class="h-12 px-7 rounded-xl btn-grad font-semibold inline-flex items-center">Build a free brief</a><a href="{{ route('contact') }}" class="h-12 px-7 rounded-xl glass font-semibold inline-flex items-center">Talk to the team</a></div></div></section>
@endsection