<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('components.seo', ['seo'=>$seo, 'faqs'=>$faqs])
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>*{font-family:Inter,system-ui,Helvetica,Arial,sans-serif}html{scroll-behavior:smooth}</style>
<script>
tailwind.config={theme:{extend:{colors:{brand:'#2563EB', ink:'#0F0F0F', muted:'#6B7280', line:'#E8E8E6', bg:'#F8F8F7'}}}}
</script>
</head>
<body class="bg-white text-ink antialiased">

<!-- TOP NAV -->
<header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-line">
  <div class="max-w-[1160px] mx-auto px-4 sm:px-6 h-[64px] flex items-center justify-between gap-4">
    <a href="{{ route('landing') }}" class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl bg-ink text-white flex items-center justify-center font-black text-[13px] tracking-tight">QC</div>
      <div class="leading-none">
        <div class="font-extrabold text-[15px] tracking-tight flex items-center gap-2">QuickContent <span class="hidden sm:inline-flex items-center gap-1 text-[10px] font-bold tracking-widest uppercase bg-amber-400 text-ink px-2 py-1 rounded-full">India's First <span class="w-1 h-1 bg-ink rounded-full"></span> Quick Delivery</span></div>
        <div class="text-[11px] font-semibold text-muted -mt-0.5">Work, Delivered. In Hours, Not Weeks.</div>
      </div>
    </a>
    <nav class="hidden md:flex items-center gap-6 text-[13px] font-semibold text-muted">
      <a href="{{ route('onboarding.business') }}" class="hover:text-ink">Business onboarding</a>
      <a href="{{ route('onboarding.creator') }}" class="hover:text-ink">Creator onboarding</a>
      <a href="#how" class="hover:text-ink">How it works</a>
      <a href="#pricing" class="hover:text-ink">Pricing</a>
      <a href="#creators" class="hover:text-ink">Creators</a>
      <a href="#faq" class="hover:text-ink">FAQ</a>
    </nav>
    <div class="flex items-center gap-2">
      <a href="{{ route('login') }}" class="hidden sm:inline-flex h-9 px-4 rounded-full border border-line font-bold text-[13px] items-center hover:bg-bg">Login</a>
      <a href="{{ route('onboarding.business') }}" class="inline-flex h-9 px-5 rounded-full bg-brand text-white font-bold text-[13px] items-center gap-1.5 hover:bg-blue-700">For Business <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
    </div>
  </div>
</header>

<!-- HERO -->
<section class="bg-bg border-b border-line">
  <div class="max-w-[1160px] mx-auto px-4 sm:px-6 py-8 sm:py-12 grid lg:grid-cols-[1.1fr_0.9fr] gap-8 items-center">
    <div>
      <div class="inline-flex items-center gap-2 bg-white border border-line rounded-full px-3 py-1.5 text-[11px] font-bold">
        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> 12-min avg. assign • 1,400+ videos delivered • 4.8/5 rating
        <span class="hidden sm:inline-flex ml-1 bg-ink text-white text-[10px] px-2 py-0.5 rounded-full tracking-widest uppercase">LIVE</span>
      </div>
      <h1 class="mt-4 text-[32px] sm:text-[44px] font-black leading-[0.95] tracking-tight">
        India’s First<br>
        <span class="text-brand">Quick Content</span><br>
        Delivery Platform
      </h1>
      <p class="mt-3 text-[15px] sm:text-[17px] leading-6 text-muted font-medium max-w-[560px]">
        <b class="text-ink">As easy as ordering food</b> — for Reels, Thumbnails & AI Videos.<br>
        Describe what you need → verified pro in <b class="text-ink">~12 minutes</b> → live tracking, just like a food order → pay only when you approve (Razorpay escrow).
      </p>

      <div class="mt-6 flex flex-wrap gap-3">
        <a href="{{ route('onboarding.business') }}" class="h-[46px] px-7 rounded-full bg-ink text-white font-extrabold text-[14px] inline-flex items-center gap-2 hover:bg-black">I need content — Start in 45 sec <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M12 5l7 7-7 7"/></svg></a>
        <a href="{{ route('onboarding.creator') }}" class="h-[46px] px-6 rounded-full bg-white border border-line font-bold text-[14px] inline-flex items-center gap-2 hover:bg-bg">I’m a Creator <span class="text-[11px] bg-green-50 text-green-700 border border-green-200 px-2 py-0.5 rounded-full">Earn 90% • Weekly UPI</span></a>
      </div>

      <div class="mt-4 flex flex-wrap items-center gap-3 text-[12px] font-semibold text-muted">
        <span class="inline-flex items-center gap-1.5"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0E8A4B" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> No bidding • No Connects</span>
        <span class="inline-flex items-center gap-1.5"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0E8A4B" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> Flat ₹1,299 onwards</span>
        <span class="inline-flex items-center gap-1.5"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#0E8A4B" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> 2 free revisions</span>
      </div>

      <div class="mt-6 flex items-center gap-3">
        <div class="flex -space-x-2">
          <img src="https://i.pravatar.cc/100?img=5" class="w-8 h-8 rounded-full border-2 border-white object-cover">
          <img src="https://i.pravatar.cc/100?img=12" class="w-8 h-8 rounded-full border-2 border-white object-cover">
          <img src="https://i.pravatar.cc/100?img=9" class="w-8 h-8 rounded-full border-2 border-white object-cover">
          <img src="https://i.pravatar.cc/100?img=68" class="w-8 h-8 rounded-full border-2 border-white object-cover">
        </div>
        <div class="text-[12px] leading-tight">
          <div class="font-bold">Trusted by 200+ companies</div>
          <div class="text-muted font-medium">Avante Studio • BrandScale • GrowthX • ConcertPass</div>
        </div>
        <div class="ml-2 hidden sm:flex items-center gap-1 bg-white border border-line rounded-full px-3 py-1.5">
          <span class="text-amber-400">★★★★★</span><b class="text-[12px]">4.8/5</b><span class="text-[11px] text-muted">(1.2k reviews)</span>
        </div>
      </div>
    </div>

    <!-- HERO VISUAL — Order tracker, easy like ordering food -->
    <div class="relative">
      <div class="bg-white border border-line rounded-[20px] shadow-[0_20px_60px_rgba(0,0,0,0.08)] p-4 sm:p-5">
        <div class="flex items-center justify-between">
          <div class="text-[11px] font-bold tracking-widest uppercase text-muted">LIVE ORDER • QC-1829</div>
          <span class="text-[11px] font-bold bg-amber-400 text-ink px-2.5 py-1 rounded-full">Working • 11:47 left</span>
        </div>
        <div class="mt-3 flex gap-3">
          <img src="https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=400&q=80" class="w-[96px] h-[72px] rounded-xl object-cover border border-line">
          <div class="flex-1">
            <div class="text-[13px] font-extrabold leading-tight">Engaging Talking-Head Reel</div>
            <div class="text-[11px] font-semibold text-muted mt-0.5">For <b class="text-ink">Avante Studio • Rohan Sharma</b> • 1 Day • ₹2,499</div>
            <div class="mt-2 flex items-center gap-2">
              <img src="https://i.pravatar.cc/100?img=5" class="w-6 h-6 rounded-full object-cover border border-line">
              <span class="text-[12px] font-bold">Priya Sharma</span>
              <span class="w-3.5 h-3.5 rounded-full bg-[#1D9BF0] text-white grid place-items-center border border-white shrink-0" style="aspect-ratio:1/1"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span>
              <span class="text-[11px] font-semibold text-muted">@priyaedits • 4.9★</span>
              <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span><span class="text-[11px] font-bold text-green-700">Working</span>
            </div>
          </div>
        </div>
        <!-- stepper -->
        <div class="mt-4 grid grid-cols-4 gap-2 text-center">
          <div><div class="w-7 h-7 mx-auto rounded-full bg-green-500 text-white grid place-items-center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg></div><div class="text-[10px] font-bold mt-1">Ordered</div><div class="text-[10px] text-muted">10:30 AM</div></div>
          <div><div class="w-7 h-7 mx-auto rounded-full bg-green-500 text-white grid place-items-center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg></div><div class="text-[10px] font-bold mt-1">Assigned</div><div class="text-[10px] text-muted">10:42 AM</div></div>
          <div><div class="w-7 h-7 mx-auto rounded-full bg-amber-400 text-ink grid place-items-center font-black text-[11px]">3</div><div class="text-[10px] font-bold mt-1">Working</div><div class="text-[10px] text-muted">Now</div></div>
          <div><div class="w-7 h-7 mx-auto rounded-full bg-white border-2 border-line grid place-items-center"><span class="w-2 h-2 bg-line rounded-full"></span></div><div class="text-[10px] font-bold mt-1 text-muted">Review</div><div class="text-[10px] text-muted">~6 PM</div></div>
        </div>
        <div class="mt-3 h-1.5 bg-bg rounded-full overflow-hidden border border-line"><div class="h-full bg-ink w-[68%]"></div></div>
        <div class="mt-3 flex gap-2">
          <button class="flex-1 h-9 rounded-full border border-line bg-white font-bold text-[12px]">Chat with Priya</button>
          <button class="flex-1 h-9 rounded-full bg-ink text-white font-bold text-[12px]">Track live</button>
        </div>
        <div class="mt-2 text-[11px] text-center text-muted font-medium">Escrow: <b class="text-ink">₹2,499 held</b> • Releases only when you Approve</div>
      </div>
      <!-- floating pills -->
      <div class="absolute -right-2 -top-2 hidden sm:flex bg-white border border-line rounded-full px-3 py-1.5 shadow-md items-center gap-2 text-[11px] font-bold"><span class="w-2 h-2 bg-green-500 rounded-full"></span> Priya is online • replies in 4 min</div>
      <div class="absolute -left-3 -bottom-3 hidden sm:flex bg-ink text-white rounded-full px-3 py-1.5 items-center gap-2 text-[11px] font-bold">⚡ Assigned in 12 min avg. <span class="bg-white text-ink px-2 py-0.5 rounded-full text-[10px]">vs 3 days elsewhere</span></div>
    </div>
  </div>
</section>

<!-- LOGOS -->
<section class="border-b border-line bg-white">
  <div class="max-w-[1160px] mx-auto px-4 sm:px-6 py-4 flex flex-wrap items-center justify-between gap-3 text-[12px] font-extrabold tracking-widest uppercase text-muted">
    <span>Trusted by 200+ teams</span>
    <div class="flex flex-wrap gap-2">
      @foreach($logos as $logo)<span class="px-3 py-1.5 bg-bg border border-line rounded-full text-ink normal-case tracking-normal font-bold text-[12px]">{{ $logo }}</span>@endforeach
    </div>
  </div>
</section>

<!-- WHY WE EXIST — vs Fiverr/Upwork/Unjob -->
<section class="max-w-[1160px] mx-auto px-4 sm:px-6 py-10">
  <div class="text-center max-w-[720px] mx-auto">
    <div class="inline-flex items-center gap-2 text-[11px] font-bold tracking-widest uppercase text-brand bg-blue-50 border border-blue-100 px-3 py-1 rounded-full">Why QuickContent exists</div>
    <h2 class="mt-3 text-[26px] sm:text-[32px] font-black tracking-tight leading-none">Freelance is broken.<br>We made it as easy as ordering food.</h2>
    <p class="mt-3 text-[14px] leading-6 text-muted font-medium">Fiverr = 20% fees + spam. Upwork = 50 proposals + pay-to-bid. Unjob = one black-box assign. <b class="text-ink">QuickContent = choice, speed, and guarantee.</b></p>
  </div>

  <div class="mt-8 grid lg:grid-cols-[1.1fr_0.9fr] gap-6">
    <div class="bg-white border border-line rounded-2xl overflow-hidden">
      <div class="grid grid-cols-[1.2fr_1fr_1fr] text-[11px] font-bold tracking-widest uppercase">
        <div class="p-3 text-muted">Compare</div><div class="p-3 bg-[#FFF7CC] text-ink text-center">Others</div><div class="p-3 bg-green-50 text-green-800 text-center">QuickContent</div>
      </div>
      <div class="divide-y divide-line text-[13px]">
        <div class="grid grid-cols-[1.2fr_1fr_1fr]"><div class="p-3 font-semibold">How you hire</div><div class="p-3 text-muted text-center">Bid / spam / 1 random assign</div><div class="p-3 font-bold text-center">3 paths — Instant / Choose Pro / Team</div></div>
        <div class="grid grid-cols-[1.2fr_1fr_1fr]"><div class="p-3 font-semibold">Time to start</div><div class="p-3 text-muted text-center">2–3 days</div><div class="p-3 font-bold text-center">~12 minutes avg.</div></div>
        <div class="grid grid-cols-[1.2fr_1fr_1fr]"><div class="p-3 font-semibold">Tracking</div><div class="p-3 text-muted text-center">“We’ll update you”</div><div class="p-3 font-bold text-center">live tracking + chat, easy like ordering food</div></div>
        <div class="grid grid-cols-[1.2fr_1fr_1fr]"><div class="p-3 font-semibold">Payment</div><div class="p-3 text-muted text-center">Pay upfront, hope</div><div class="p-3 font-bold text-center">Escrow till you Approve</div></div>
        <div class="grid grid-cols-[1.2fr_1fr_1fr]"><div class="p-3 font-semibold">Fees</div><div class="p-3 text-muted text-center">20% + Connects</div><div class="p-3 font-bold text-center">5% flat • Creator keeps 90%</div></div>
      </div>
    </div>
    <div class="bg-ink text-white rounded-2xl p-6">
      <div class="text-[11px] font-bold tracking-widest uppercase text-white/60">The promise</div>
      <div class="mt-2 text-[20px] font-black leading-tight">If you can order biryani in 10 minutes,<br>why should a reel take 10 days?</div>
      <div class="mt-4 grid grid-cols-2 gap-3 text-[13px]">
        <div class="bg-white/10 rounded-xl p-3 border border-white/10"><div class="font-black text-amber-300">45-sec</div><div class="font-semibold text-white/80">checkout</div></div>
        <div class="bg-white/10 rounded-xl p-3 border border-white/10"><div class="font-black text-amber-300">1-day</div><div class="font-semibold text-white/80">reel delivery</div></div>
        <div class="bg-white/10 rounded-xl p-3 border border-white/10"><div class="font-black text-amber-300">2</div><div class="font-semibold text-white/80">free revisions</div></div>
        <div class="bg-white/10 rounded-xl p-3 border border-white/10"><div class="font-black text-amber-300">100%</div><div class="font-semibold text-white/80">escrow guarantee</div></div>
      </div>
      <a href="{{ route('business.home') }}" class="mt-5 inline-flex h-10 px-6 rounded-full bg-white text-ink font-extrabold text-[13px] items-center">Try Instant Assign — ₹1,299</a>
      <div class="mt-2 text-[11px] text-white/60 font-medium">No login wall to see pricing. No sales call.</div>
    </div>
  </div>
</section>

<!-- 3 PATHS -->
<section id="how" class="bg-bg border-y border-line">
  <div class="max-w-[1160px] mx-auto px-4 sm:px-6 py-10">
    <div class="flex flex-wrap items-end justify-between gap-3">
      <div>
        <div class="text-[11px] font-bold tracking-widest uppercase text-brand">3 ways to get work done</div>
        <h2 class="text-[24px] sm:text-[28px] font-black tracking-tight">Pick your speed. We handle the rest.</h2>
      </div>
      <div class="text-[12px] font-semibold text-muted">All orders • live tracking, easy like ordering food • Escrow • 1-day delivery</div>
    </div>
    <div class="mt-6 grid md:grid-cols-3 gap-4">
      @foreach($paths as $p)
      <div class="bg-white border border-line rounded-2xl p-5 flex flex-col">
        <div class="flex items-center justify-between">
          <div class="w-9 h-9 rounded-xl bg-ink text-white grid place-items-center">
            @if($p['icon']=='zap')<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
            @elseif($p['icon']=='users')<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            @else<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 9h18M9 21V9"/></svg>@endif
          </div>
          <span class="text-[11px] font-bold bg-amber-400 text-ink px-2.5 py-1 rounded-full">{{ $p['badge'] }}</span>
        </div>
        <div class="mt-3 text-[16px] font-black">{{ $p['title'] }}</div>
        <div class="text-[12px] font-bold text-muted">{{ $p['time'] }} • {{ $p['price'] }}</div>
        <div class="mt-2 text-[13px] leading-5 text-muted font-medium flex-1">{{ $p['desc'] }}</div>
        <div class="mt-3 text-[11px] font-bold bg-bg border border-line rounded-full px-3 py-1.5 inline-flex self-start">Best for: {{ $p['for'] }}</div>
        <a href="{{ route('business.home') }}" class="mt-4 h-9 rounded-full bg-ink text-white font-bold text-[13px] grid place-items-center hover:bg-black">Try {{ $p['title'] }}</a>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- CATEGORIES -->
<section class="max-w-[1160px] mx-auto px-4 sm:px-6 py-10">
  <div class="flex items-end justify-between gap-4">
    <h2 class="text-[22px] font-black tracking-tight">Everything your content needs</h2>
    <a href="{{ route('business.home') }}" class="hidden sm:inline-flex text-[12px] font-bold text-muted hover:text-ink">Browse all services →</a>
  </div>
  <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-4">
    @foreach($services as $s)
    <a href="{{ route('business.home') }}" class="group bg-white border border-line rounded-2xl overflow-hidden hover:shadow-md transition">
      <div class="relative"><img src="{{ $s['img'] }}" class="h-[130px] w-full object-cover"><span class="absolute left-2 top-2 text-[11px] font-bold bg-white border border-line px-2 py-1 rounded-full">{{ $s['badge'] }}</span><span class="absolute right-2 bottom-2 text-[11px] font-bold bg-ink text-white px-2 py-1 rounded-full">{{ $s['time'] }}</span></div>
      <div class="p-3"><div class="text-[13px] font-bold leading-tight group-hover:text-brand">{{ $s['title'] }}</div><div class="mt-1 flex items-center justify-between"><span class="text-[13px] font-black">{{ $s['price'] }}</span><span class="text-[11px] font-semibold text-muted">{{ $s['orders'] }} orders • 4.9★</span></div></div>
    </a>
    @endforeach
  </div>
</section>

<!-- CREATORS -->
<section id="creators" class="bg-bg border-y border-line">
  <div class="max-w-[1160px] mx-auto px-4 sm:px-6 py-10">
    <div class="flex flex-wrap items-end justify-between gap-3">
      <div>
        <div class="text-[11px] font-bold tracking-widest uppercase text-brand">Verified creators, not random freelancers</div>
        <h2 class="text-[24px] font-black tracking-tight">Work with pros companies actually trust</h2>
      </div>
      <div class="text-[12px] font-semibold text-muted">Blue tick = ID + portfolio verified • Green dot = available now</div>
    </div>
    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach($creators as $c)
      <div class="bg-white border border-line rounded-2xl p-4">
        <div class="flex gap-3">
          <div class="relative shrink-0"><img src="{{ $c['img'] }}" class="w-12 h-12 rounded-full object-cover border border-line"><span class="absolute -bottom-1 -right-1 w-[18px] h-[18px] rounded-full bg-[#1D9BF0] text-white grid place-items-center border-2 border-white" style="aspect-ratio:1/1"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span></div>
          <div class="min-w-0"><div class="text-[13px] font-black leading-tight flex items-center gap-1">{{ $c['name'] }} <span class="w-2 h-2 rounded-full {{ $c['available'] ? 'bg-green-500' : 'bg-amber-400' }}"></span></div><div class="text-[11px] font-semibold text-muted truncate">{{ $c['handle'] }}</div><div class="text-[11px] font-medium text-muted truncate">{{ $c['role'] }}</div></div>
        </div>
        <div class="mt-3 flex items-center justify-between"><span class="text-[11px] font-bold px-2 py-1 rounded-full border {{ $c['available'] ? 'bg-green-50 border-green-200 text-green-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">{{ $c['available'] ? 'Available now' : 'In 2 hours' }}</span><span class="text-[12px] font-black">{{ $c['price'] }}</span></div>
        <a href="{{ route('business.home') }}" class="mt-3 h-8 rounded-full bg-ink text-white font-bold text-[12px] grid place-items-center">View portfolio</a>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- PRICING -->
<section id="pricing" class="max-w-[1160px] mx-auto px-4 sm:px-6 py-10">
  <div class="text-center max-w-[640px] mx-auto">
    <h2 class="text-[26px] font-black tracking-tight">Flat pricing. No bidding. No surprises.</h2>
    <p class="mt-2 text-[13px] leading-5 text-muted font-medium">You see the price before you pay. Platform fee 5% — not 20%. Creators keep 90%. Razorpay escrow holds money till you click Approve.</p>
  </div>
  <div class="mt-6 grid md:grid-cols-3 gap-4">
    <div class="bg-white border border-line rounded-2xl p-6">
      <div class="text-[11px] font-bold tracking-widest uppercase text-muted">STARTER</div>
      <div class="mt-1 text-[22px] font-black">₹1,299 <span class="text-[12px] font-semibold text-muted">/ item</span></div>
      <div class="text-[12px] font-semibold text-muted">Thumbnails • Captions • Cuts</div>
      <ul class="mt-4 space-y-2 text-[13px] font-medium">
        <li class="flex gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0E8A4B" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> 1-day delivery</li>
        <li class="flex gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0E8A4B" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> 2 free revisions</li>
        <li class="flex gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0E8A4B" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> Verified creator + chat</li>
      </ul>
      <a href="{{ route('business.home') }}" class="mt-5 h-10 rounded-full border-2 border-ink font-extrabold text-[13px] grid place-items-center">Start at ₹1,299</a>
    </div>
    <div class="bg-ink text-white rounded-2xl p-6 border-2 border-ink relative">
      <span class="absolute -top-3 left-1/2 -translate-x-1/2 bg-amber-400 text-ink text-[11px] font-black px-3 py-1 rounded-full">MOST POPULAR</span>
      <div class="text-[11px] font-bold tracking-widest uppercase text-white/60">REELS</div>
      <div class="mt-1 text-[22px] font-black">₹2,499 <span class="text-[12px] font-semibold text-white/60">/ reel</span></div>
      <div class="text-[12px] font-semibold text-white/70">Talking-head • Retention • AI UGC</div>
      <ul class="mt-4 space-y-2 text-[13px] font-medium text-white/90">
        <li class="flex gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFC300" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> 12-min assign • 1-day delivery</li>
        <li class="flex gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFC300" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> live tracking + chat, easy like ordering food</li>
        <li class="flex gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#FFC300" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> Escrow till you Approve</li>
      </ul>
      <a href="{{ route('business.home') }}" class="mt-5 h-10 rounded-full bg-white text-ink font-extrabold text-[13px] grid place-items-center">Get my reel tomorrow</a>
    </div>
    <div class="bg-white border border-line rounded-2xl p-6">
      <div class="text-[11px] font-bold tracking-widest uppercase text-muted">TEAM PACK</div>
      <div class="mt-1 text-[22px] font-black">₹8,999 <span class="text-[12px] font-semibold text-muted">/ pack</span></div>
      <div class="text-[12px] font-semibold text-muted">Reel + Thumb + Captions + AI</div>
      <ul class="mt-4 space-y-2 text-[13px] font-medium">
        <li class="flex gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0E8A4B" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> Micro-team (editor+designer)</li>
        <li class="flex gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0E8A4B" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> One prompt → all assets</li>
        <li class="flex gap-2"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0E8A4B" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> 2-day delivery • best value</li>
      </ul>
      <a href="{{ route('business.home') }}" class="mt-5 h-10 rounded-full border-2 border-ink font-extrabold text-[13px] grid place-items-center">Build my team pack</a>
    </div>
  </div>
</section>

<!-- TESTIMONIALS -->
<section class="bg-bg border-y border-line">
  <div class="max-w-[1160px] mx-auto px-4 sm:px-6 py-10">
    <h2 class="text-[20px] font-black tracking-tight text-center">Companies love how easy it is — like ordering food</h2>
    <div class="mt-6 grid md:grid-cols-3 gap-4">
      @foreach($testimonials as $t)
      <div class="bg-white border border-line rounded-2xl p-5">
        <div class="text-amber-400 text-[14px]">★★★★★</div>
        <div class="mt-2 text-[13px] leading-6 font-medium">“{{ $t['text'] }}”</div>
        <div class="mt-4 flex items-center gap-3"><div class="w-9 h-9 rounded-full bg-ink text-white grid place-items-center font-black text-[12px]">{{ substr($t['name'],0,1) }}</div><div><div class="text-[13px] font-bold">{{ $t['name'] }}</div><div class="text-[11px] font-semibold text-muted">{{ $t['company'] }}</div></div></div>
      </div>
      @endforeach
    </div>
  </div>
</section>

<!-- FAQ — DB managed, JSON-LD FAQPage for AEO -->
<section id="faq" class="max-w-[760px] mx-auto px-4 sm:px-6 py-10" x-data="{open:0}">
  <div class="text-center">
    <div class="inline-flex items-center gap-2 text-[11px] font-bold tracking-widest uppercase bg-amber-50 border border-amber-200 text-amber-800 px-3 py-1 rounded-full">AEO • Answer Engine Ready • JSON-LD FAQPage</div>
    <h2 class="mt-2 text-[22px] font-black tracking-tight">Questions? We’ve got you.</h2>
    <p class="text-[13px] font-medium text-muted">Managed in Admin → FAQs. Google & Perplexity pick up the JSON-LD.</p>
  </div>
  <div class="mt-6 divide-y divide-line border border-line rounded-2xl bg-white overflow-hidden">
    @foreach($faqs as $i => $f)
    @php $q = $f->question ?? $f['question'] ?? $f->q ?? ''; $a = $f->answer ?? $f['answer'] ?? $f->a ?? ''; @endphp
    <div class="cursor-pointer" @click="open = open === {{ $i }} ? -1 : {{ $i }}">
      <div class="flex items-center justify-between gap-4 p-4">
        <div class="text-[13px] font-bold leading-tight">{{ $q }}</div>
        <div class="w-7 h-7 rounded-full border border-line grid place-items-center shrink-0" :class="open === {{ $i }} ? 'bg-ink text-white border-ink' : 'bg-white'"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 9l6 6 6-6"/></svg></div>
      </div>
      <div x-show="open === {{ $i }}" x-collapse class="px-4 pb-4 text-[13px] leading-6 text-muted font-medium">{{ $a }}</div>
    </div>
    @endforeach
  </div>
  <div class="mt-3 text-center"><a href="{{ route('blog.index') }}" class="text-[12px] font-bold text-brand hover:underline">Browse all FAQs + Playbooks →</a></div>
</section>

<!-- BLOG TEASER — from DB, JSON-LD BlogPosting per post -->
@if(isset($blogs) && $blogs->count())
<section class="bg-bg border-y border-line">
  <div class="max-w-[1160px] mx-auto px-4 sm:px-6 py-10">
    <div class="flex flex-wrap items-end justify-between gap-3">
      <div><div class="text-[11px] font-bold tracking-widest uppercase text-brand">Playbooks • SEO & AEO ready</div><h2 class="text-[22px] font-black tracking-tight">Ship faster with proven SOPs</h2></div>
      <a href="{{ route('blog.index') }}" class="h-9 px-4 rounded-full border border-line bg-white font-bold text-[13px] inline-flex items-center">View all blogs →</a>
    </div>
    <div class="mt-6 grid md:grid-cols-3 gap-4">
      @foreach($blogs as $b)
      <a href="{{ route('blog.show', $b->slug) }}" class="bg-white border border-line rounded-2xl overflow-hidden hover:shadow-md transition group">
        <img src="{{ filter_var($b->cover, FILTER_VALIDATE_URL) ? $b->cover : asset('storage/'.$b->cover) }}" alt="{{ $b->cover_alt ?: $b->title }}" class="h-[160px] w-full object-cover">
        <div class="p-4"><div class="text-[11px] font-bold tracking-widest uppercase" style="color:{{ $b->category->color ?? '#2563EB' }}">{{ $b->category->name ?? 'Playbook' }} • {{ $b->reading_minutes }} min</div><div class="mt-1 text-[14px] font-black leading-tight group-hover:text-brand line-clamp-2">{{ $b->title }}</div><div class="mt-1 text-[12px] leading-5 text-muted line-clamp-2">{{ $b->excerpt }}</div></div>
      </a>
      @endforeach
    </div>
  </div>
</section>
@endif

<!-- FINAL CTA -->
<section class="bg-ink text-white">
  <div class="max-w-[1160px] mx-auto px-4 sm:px-6 py-10 flex flex-col md:flex-row items-center justify-between gap-6">
    <div>
      <div class="text-[11px] font-bold tracking-widest uppercase text-white/60">Ready in 45 seconds</div>
      <div class="text-[24px] font-black leading-tight">Your next reel is 12 minutes away.</div>
      <div class="text-[13px] font-medium text-white/70 mt-1">India’s First Quick Content Delivery — Hostinger-ready, escrow-safe, creator-verified.</div>
    </div>
    <div class="flex gap-3 shrink-0">
      <a href="{{ route('onboarding.business') }}" class="h-11 px-7 rounded-full bg-white text-ink font-extrabold text-[14px] inline-flex items-center">Start as Business</a>
      <a href="{{ route('onboarding.creator') }}" class="h-11 px-7 rounded-full bg-white/10 border border-white/20 text-white font-bold text-[14px] inline-flex items-center">Join as Creator</a>
    </div>
  </div>
</section>

<footer class="bg-white border-t border-line">
  <div class="max-w-[1160px] mx-auto px-4 sm:px-6 py-6 flex flex-col md:flex-row items-center justify-between gap-3 text-[12px] font-medium text-muted">
    <div>© {{ date('Y') }} QuickContent — India’s First Quick Content Delivery Platform. Built for Hostinger shared hosting (no Redis).</div>
    <div class="flex flex-wrap gap-4 font-bold"><a href="{{ route('onboarding.business') }}" class="hover:text-ink">Get Started</a><a href="{{ route('business.home') }}" class="hover:text-ink">Marketplace</a><a href="{{ route('blog.index') }}" class="hover:text-ink">Blog</a><a href="{{ route('creator.dashboard') }}" class="hover:text-ink">Creator</a><a href="{{ route('admin.dashboard') }}" class="hover:text-ink">Admin</a><a href="{{ route('sitemap') }}" class="hover:text-ink">Sitemap</a><a href="/health" class="hover:text-ink">Health</a></div>
  </div>
  <div class="max-w-[1160px] mx-auto px-4 sm:px-6 pb-4 text-[11px] text-muted font-medium text-center">SEO • AEO • JSON-LD Organization + FAQPage + BlogPosting + Breadcrumbs • Hostinger file cache + DB queue</div>
</footer>

</body>
</html>
