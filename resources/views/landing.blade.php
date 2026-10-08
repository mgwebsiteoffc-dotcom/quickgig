@extends('layouts.site')
@section('content')

{{-- GIG60 customer homepage: service-first like a quick-commerce catalogue, with AI doing the brief and orchestration behind the scenes. --}}
<section class="pt-12 sm:pt-20 pb-14">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[1.05fr_.95fr] gap-10 items-center">
    <div>
      <div class="inline-flex items-center gap-2 rounded-full bg-mint-wash text-mint-deep px-3 py-1.5 text-[11px] font-bold tracking-[.12em] uppercase">AI-powered managed services</div>
      <h1 class="mt-5 font-display text-[42px] sm:text-[60px] leading-[.98] font-semibold tracking-[-.04em]">Hire &amp; manage editors for your<br><span x-data="{ words: ['reels.', 'YouTube videos.', 'podcast clips.', 'ads and UGC.', 'brand visuals.'], i: 0, text: 'reels.' }" x-init="setInterval(() => { i = (i + 1) % words.length; text = words[i] }, 2200)" class="grad-text inline-block min-w-[9ch]" x-text="text">reels.</span></h1>
      <p class="mt-5 text-[16px] leading-7 text-mut max-w-[600px]">Describe the outcome. GIG60 creates the brief, assigns a verified specialist and manages production, revisions and approval—so your team gets finished work without the hiring delay.</p>
      <div class="mt-7 flex flex-wrap gap-3"><a href="{{ route('services') }}" class="h-12 px-6 rounded-xl btn-grad font-semibold inline-flex items-center">View services →</a><a href="{{ route('brief-builder') }}" class="h-12 px-6 rounded-xl glass font-semibold inline-flex items-center">Generate a brief with AI</a></div>
      <div class="mt-8 flex flex-wrap gap-x-7 gap-y-3 text-[12.5px] text-mut"><span>✓ AI-generated brief</span><span>✓ Managed production</span><span>✓ Escrow protected</span><span>✓ GST shown clearly</span></div>
    </div>
    <div class="glass-strong rounded-3xl p-6 sm:p-7 ring-glow">
      <div class="flex items-center justify-between"><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">Your AI service desk</div><span class="w-2 h-2 rounded-full bg-mint"></span></div>
      <div class="mt-5 space-y-3">
        @foreach([['Video','Reels, editing and podcast clips','AI turns your goal into a production brief.'],['Visuals','Images, thumbnails and design','AI recommends the right output and format.'],['Growth','UGC, AI ads and campaign content','AI coordinates the workflow; managed specialists deliver.']] as [$a,$b,$c])
          <div class="rounded-2xl bg-tint p-4"><div class="flex items-center justify-between"><div class="font-semibold text-[14px]">{{ $a }}</div><span class="text-[10px] font-bold uppercase tracking-wider text-mint-deep">AI assisted</span></div><div class="mt-1 text-[13px] text-body">{{ $b }}</div><div class="mt-1 text-[12px] text-mut">{{ $c }}</div></div>
        @endforeach
      </div>
      <div class="mt-5 rounded-2xl bg-ink text-white p-4 text-[13px] leading-6">You choose the outcome. GIG60 handles the brief, match, progress and approval path.</div>
    </div>
  </div>
</section>

<section class="pb-14">
  <div class="max-w-shell mx-auto px-5 lg:px-8"><div class="flex flex-wrap items-end justify-between gap-4"><div><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">Top categories</div><h2 class="mt-3 font-display text-[28px] sm:text-[38px] font-semibold">What do you need made?</h2></div><a href="{{ route('services') }}" class="text-[13px] font-semibold text-mint-deep">See all offerings →</a></div>
    <div class="mt-7 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
      @foreach([['Video editing','Reels, clips, retention'],['Design','Thumbnails, brands, social'],['AI services','AI ads and product visuals'],['UGC content','Ads and creator content'],['Copywriting','Scripts, pages, captions'],['Marketing','Google and Meta support']] as [$t,$b])
        <a href="{{ route('services', ['q'=>$t]) }}" class="glass rounded-2xl p-4 hover:border-mint hover:-translate-y-0.5 transition"><div class="w-full h-16 rounded-xl bg-tint grid place-items-center text-mint-deep font-display text-2xl">✦</div><div class="mt-3 text-[13.5px] font-semibold">{{ $t }}</div><div class="mt-1 text-[11.5px] leading-4 text-mut">{{ $b }}</div></a>
      @endforeach
    </div>
  </div>
</section>

<section class="band-lav py-14">
  <div class="max-w-shell mx-auto px-5 lg:px-8"><div class="flex flex-wrap items-end justify-between gap-4"><div><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">Best sellers</div><h2 class="mt-3 font-display text-[28px] sm:text-[38px] font-semibold">Ready-to-order outcomes.</h2><p class="mt-2 text-[14px] text-mut">Pick a service. Tell us the context. AI generates the production-ready brief.</p></div><a href="{{ route('services') }}" class="text-[13px] font-semibold text-mint-deep">See all offerings →</a></div>
    <div class="mt-8 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach(collect($gigs)->take(4) as $g)
        <a href="{{ route('gigs.show',$g['slug'] ?? \Illuminate\Support\Str::slug($g['title'])) }}" class="glass rounded-3xl overflow-hidden card-hover"><div class="relative"><img src="{{ $g['img'] }}" class="h-[155px] w-full object-cover" alt=""><span class="absolute left-3 top-3 rounded-full glass-strong px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider">{{ $g['badge'] ?? 'Service' }}</span></div><div class="p-5"><h3 class="text-[15px] font-semibold leading-5">{{ $g['title'] }}</h3><div class="mt-4 flex justify-between items-center"><span class="font-display text-[18px] font-semibold">{{ $g['price'] }}</span><span class="text-[11.5px] text-mut">{{ $g['time'] }}</span></div><div class="mt-2 text-[11.5px] text-mint-deep">AI brief + managed delivery →</div></div></a>
      @endforeach
    </div>
  </div>
</section>

<section class="pb-14">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid grid-cols-2 lg:grid-cols-4 gap-3">
    @foreach([['AI','Brief generation'],['5 steps','Managed AI workflow'],['100%','Agreed creator payout'],['Escrow','Approval protected']] as [$v,$l])
      <div class="glass rounded-2xl p-5"><div class="font-display text-[25px] font-semibold text-mint-deep">{{ $v }}</div><div class="mt-1 text-[12px] text-mut">{{ $l }}</div></div>
    @endforeach
  </div>
</section>

<section id="brief-generator" class="py-14">
  <div class="max-w-shell mx-auto px-5 lg:px-8"><div class="rounded-3xl bg-ink text-white p-6 sm:p-9 grid lg:grid-cols-[1fr_auto] gap-7 items-center"><div><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint">GIG60 AI brief generator</div><h2 class="mt-3 font-display text-[26px] sm:text-[34px] font-semibold">One sentence in. A production brief out.</h2><p class="mt-2 text-[14px] leading-6 text-white/70 max-w-[650px]">AI extracts the objective, audience, format, deliverables, references, timing and acceptance criteria. You review it before any service starts.</p></div><a href="{{ route('brief-builder') }}" class="h-12 px-6 rounded-xl bg-white text-ink font-semibold inline-flex items-center justify-center">Try the AI generator →</a></div></div>
</section>

<section class="py-14 bg-tint">
  <div class="max-w-shell mx-auto px-5 lg:px-8"><div class="max-w-[700px]"><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">AI behind every order</div><h2 class="mt-3 font-display text-[28px] sm:text-[38px] font-semibold">AI finds the path. Managed specialists deliver the work.</h2><p class="mt-3 text-[15px] leading-7 text-mut">You never have to search through a marketplace. The human production layer stays managed behind GIG60 while the customer sees a clear service, process and result.</p></div>
    <div class="mt-8 grid md:grid-cols-5 gap-3">@foreach([['01','Understand','Goal and context'],['02','Brief','Deliverables and criteria'],['03','Route','Best service path'],['04','Check','Quality and scope'],['05','Learn','Feedback improves the next order']] as [$n,$t,$b])<div class="glass rounded-2xl p-4"><div class="font-mono text-[11px] text-mint-deep">{{ $n }}</div><div class="mt-3 text-[15px] font-semibold">{{ $t }}</div><div class="mt-1 text-[12px] leading-5 text-mut">{{ $b }}</div></div>@endforeach</div>
  </div>
</section>

<section class="py-14"><div class="max-w-shell mx-auto px-5 lg:px-8"><div class="text-center"><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">How it works</div><h2 class="mt-3 font-display text-[28px] sm:text-[38px] font-semibold">Skip the hiring pain. Move straight to great results.</h2><p class="mt-3 text-[15px] leading-7 text-mut max-w-[620px] mx-auto">Book a defined outcome and let GIG60 manage the brief, verified specialist and delivery process behind the scenes.</p></div><div class="mt-8 grid md:grid-cols-3 gap-4">@foreach([['01','Book a service','Choose a ready-to-go service, or describe what your business needs.'],['02','Managed specialist assigned','AI routes the brief to a verified specialist while GIG60 manages scope, progress and quality.'],['03','Approve and complete','Review the finished work, request included revisions and approve it. Escrow releases the agreed payout.']] as [$n,$t,$b])<div class="glass rounded-3xl p-6"><div class="font-mono text-[12px] text-mint-deep">{{ $n }}</div><h3 class="mt-4 font-display text-[20px] font-semibold">{{ $t }}</h3><p class="mt-2 text-[13px] leading-6 text-mut">{{ $b }}</p></div>@endforeach</div></div></section>

<section class="band-dark py-14"><div class="max-w-shell mx-auto px-5 lg:px-8"><div class="max-w-[650px]"><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint">Why GIG60</div><h2 class="mt-3 font-display text-[28px] sm:text-[38px] font-semibold">A managed AI service layer, not a profile directory.</h2></div><div class="mt-8 grid md:grid-cols-2 lg:grid-cols-4 gap-4">@foreach([['AI-first','Briefs, routing, QA and learning are built into the workflow.'],['Service-first','Customers choose outputs, not people to manage.'],['Managed','Production stays coordinated behind the scenes.'],['Protected','Clear scope, GST, escrow and approval keep the order accountable.']] as [$t,$b])<div class="rounded-3xl border border-white/15 bg-white/5 p-5"><h3 class="font-display text-[18px] font-semibold text-white">{{ $t }}</h3><p class="mt-2 text-[13px] leading-6 text-faint">{{ $b }}</p></div>@endforeach</div></div></section>

<section class="band-lav py-14"><div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[1.15fr_.85fr] gap-8 lg:gap-14 items-center"><div><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">For businesses and teams</div><h2 class="mt-3 font-display text-[28px] sm:text-[36px] font-semibold">One service today. A managed creative desk tomorrow.</h2><p class="mt-2 text-[14px] leading-6 text-mut max-w-[620px]">Use one-off services for urgent work, recurring services for regular demand and managed delivery when your workflow grows.</p><a href="{{ route('for-business') }}" class="mt-6 h-12 px-6 rounded-xl btn-grad font-semibold inline-flex items-center">Build your workflow →</a></div><div class="rounded-3xl border border-line bg-white/70 p-6"><div class="text-[11px] font-bold tracking-[.14em] uppercase text-mint-deep">Your managed workflow</div><div class="mt-4 space-y-3 text-[13px] text-ink"><div class="flex gap-3 items-start"><span class="text-mint-deep font-bold">01</span><span>Brief created and scope confirmed</span></div><div class="flex gap-3 items-start"><span class="text-mint-deep font-bold">02</span><span>Verified specialist assigned and managed</span></div><div class="flex gap-3 items-start"><span class="text-mint-deep font-bold">03</span><span>Quality checked, approved and paid through escrow</span></div></div></div></div></section>

<section class="py-14"><div class="max-w-shell mx-auto px-5 lg:px-8 text-center"><div class="text-[11px] font-bold tracking-[.16em] uppercase text-mint-deep">Ready to start?</div><h2 class="mt-3 font-display text-[32px] sm:text-[44px] font-semibold">Choose a service or let AI write the first brief.</h2><div class="mt-7 flex justify-center gap-3"><a href="{{ route('services') }}" class="h-12 px-7 rounded-xl btn-grad font-semibold inline-flex items-center">View all offerings</a><a href="{{ route('brief-builder') }}" class="h-12 px-7 rounded-xl glass font-semibold inline-flex items-center">Generate a brief</a></div></div></section>
@endsection