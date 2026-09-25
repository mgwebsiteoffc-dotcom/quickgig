@php
  $user = auth()->user();
  $dash = $user ? ($user->isAdmin() ? route('admin.dashboard') : ($user->hasRole('creator') ? route('creator.dashboard') : route('business.home'))) : null;

  $platform = [
    ['How it works',  'The 7-stage delivery pipeline', route('how-it-works')],
    ['The engine',    'Explainable matching, QA gate', route('ai')],
    ['Brief builder', 'Free tool — one line to brief', route('brief-builder')],
    ['Compare',       'Quick GIGS vs the alternatives', route('compare')],
  ];
  $company = [
    ['For business', 'Task board, seats, one invoice', route('for-business')],
    ['For freelancers', 'Keep 90%, no bidding',        route('for-creators')],
    ['For teams',    'Pods, SLAs, one invoice',     route('enterprise')],
    ['Insights',     'Playbooks and case studies',  route('blog.index')],
    ['FAQ',          'Answers without a sales call', route('faq')],
    ['About',        'Why we built this',           route('about')],
  ];
@endphp

<header x-data="{ open:false, menu:null, scrolled:false }" x-on:scroll.window="scrolled = window.scrollY > 12"
        x-on:keydown.escape.window="menu=null; open=false"
        class="sticky top-0 z-50 transition-colors duration-300 bg-white/85 backdrop-blur"
        :class="scrolled || open || menu ? 'border-b border-line shadow-sm' : 'border-b border-transparent'">
  <div class="max-w-shell mx-auto px-5 lg:px-8 h-[70px] flex items-center justify-between gap-6">

    {{-- Brand --}}
    <a href="{{ route('landing') }}" class="flex items-center gap-2.5 shrink-0 group">
      <span class="w-9 h-9 rounded-xl grid place-items-center bg-ink group-hover:scale-105 transition">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="#00C48C"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z"/></svg>
      </span>
      <span class="font-display text-[17px] tracking-tight leading-none">
        <span class="font-medium">Quick</span><span class="font-bold text-mint-deep"> GIGS</span>
      </span>
    </a>

    {{-- Desktop menu: 2 dropdowns + 2 links --}}
    <nav class="hidden lg:flex items-center gap-1 text-[14px]">
      @foreach(['platform' => ['Platform', $platform], 'company' => ['Company', $company]] as $key => [$label, $items])
        {{-- each trigger owns its panel, so the pointer never leaves the hover area --}}
        <div class="relative" x-on:mouseenter="menu='{{ $key }}'" x-on:mouseleave="menu=null">
          <button type="button" x-on:click="menu = menu==='{{ $key }}' ? null : '{{ $key }}'"
                  class="px-3.5 py-2 rounded-lg transition font-medium inline-flex items-center gap-1.5 hover:text-ink hover:bg-tint"
                  :class="menu==='{{ $key }}' ? 'text-ink bg-tint' : 'text-mut'">
            {{ $label }}
            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"
                 class="transition-transform duration-200" :class="menu==='{{ $key }}' ? 'rotate-180' : ''"><path d="m6 9 6 6 6-6"/></svg>
          </button>

          {{-- pt-3 is the bridge between button and panel --}}
          <div x-show="menu==='{{ $key }}'" x-cloak
               x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0 -translate-y-1"
               x-transition:leave="transition ease-in duration-100" x-transition:leave-end="opacity-0 -translate-y-1"
               class="absolute left-0 top-full pt-3 w-[440px] z-50">
            <div class="bg-white border border-line rounded-2xl p-2 shadow-xl shadow-ink/10">
              <div class="grid grid-cols-2 gap-1">
                @foreach($items as [$itemLabel, $desc, $href])
                  <a href="{{ $href }}" class="rounded-xl p-3 hover:bg-tint transition group">
                    <div class="text-[13.5px] font-semibold text-ink group-hover:text-mint-deep transition">{{ $itemLabel }}</div>
                    <div class="text-[12px] text-faint mt-0.5 leading-snug">{{ $desc }}</div>
                  </a>
                @endforeach
              </div>
            </div>
          </div>
        </div>

        @if($key === 'platform')
          <a href="{{ route('marketplace') }}" class="px-3.5 py-2 rounded-lg text-mut hover:text-ink hover:bg-tint transition font-medium">Marketplace</a>
          <a href="{{ route('pricing') }}" class="px-3.5 py-2 rounded-lg text-mut hover:text-ink hover:bg-tint transition font-medium">Pricing</a>
        @endif
      @endforeach
    </nav>

    {{-- Right side --}}
    <div class="flex items-center gap-2.5">
      @auth
        <a href="{{ $dash }}" class="hidden sm:inline-flex h-10 px-4 rounded-xl border border-line hover:border-ink/30 hover:bg-tint items-center text-[13.5px] font-medium transition">Dashboard</a>
        <div x-data="{ m:false }" class="relative hidden sm:block">
          <button x-on:click="m=!m" class="w-10 h-10 rounded-xl btn-grad grid place-items-center font-display font-bold text-[13px] text-ink">{{ strtoupper(substr($user->name ?? 'U',0,1)) }}</button>
          <div x-show="m" x-cloak x-on:click.outside="m=false" x-transition class="absolute right-0 mt-2 w-56 bg-white border border-line rounded-2xl p-2 shadow-xl shadow-ink/10 z-50">
            <div class="px-3 py-2">
              <div class="text-[13px] font-semibold truncate">{{ $user->name }}</div>
              <div class="text-[11.5px] text-mut truncate">{{ $user->email }}</div>
            </div>
            <div class="h-px bg-tint my-1"></div>
            <a href="{{ $dash }}" class="block px-3 py-2 rounded-xl text-[13.5px] hover:bg-tint">Dashboard</a>
            <a href="{{ $user->hasRole('creator') ? route('creator.profile') : route('business.profile') }}" class="block px-3 py-2 rounded-xl text-[13.5px] hover:bg-tint">Profile settings</a>
            <a href="{{ route('brief-builder') }}" class="block px-3 py-2 rounded-xl text-[13.5px] hover:bg-tint">Brief builder</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
              <button class="w-full text-left px-3 py-2 rounded-xl text-[13.5px] text-rose-300 hover:bg-rose-500/10">Log out</button>
            </form>
          </div>
        </div>
      @else
        <a href="{{ route('login') }}" class="hidden sm:inline-flex h-10 px-4 rounded-xl text-mut hover:text-ink hover:bg-tint items-center text-[13.5px] font-medium transition">Log in</a>
        <a href="{{ route('register') }}" class="inline-flex h-10 px-5 rounded-xl btn-grad items-center gap-1.5 text-[13.5px] font-semibold shadow-lg shadow-ink/10 transition">
          Get started
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
      @endauth

      <button x-on:click="open=!open" class="lg:hidden w-10 h-10 rounded-xl border border-line grid place-items-center hover:bg-tint transition" aria-label="Menu">
        <svg x-show="!open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        <svg x-show="open" x-cloak width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>
  </div>

  {{-- Mobile drawer --}}
  <div x-show="open" x-cloak x-transition.origin.top class="lg:hidden border-t border-line px-5 py-4 bg-white max-h-[80vh] overflow-y-auto">
    <div class="flex flex-col gap-1">
      <a href="{{ route('marketplace') }}" class="px-3 py-3 rounded-xl text-[15px] font-medium text-ink hover:bg-tint">Marketplace</a>
      <a href="{{ route('pricing') }}" class="px-3 py-3 rounded-xl text-[15px] font-medium text-ink hover:bg-tint">Pricing</a>

      <div class="mt-2 px-3 text-[10.5px] font-semibold tracking-[.16em] uppercase text-faint">Platform</div>
      @foreach($platform as [$label, $desc, $href])
        <a href="{{ $href }}" class="px-3 py-2.5 rounded-xl text-[14.5px] text-body hover:bg-tint hover:text-ink">{{ $label }}</a>
      @endforeach

      <div class="mt-2 px-3 text-[10.5px] font-semibold tracking-[.16em] uppercase text-faint">Company</div>
      @foreach($company as [$label, $desc, $href])
        <a href="{{ $href }}" class="px-3 py-2.5 rounded-xl text-[14.5px] text-body hover:bg-tint hover:text-ink">{{ $label }}</a>
      @endforeach

      <div class="h-px bg-tint my-3"></div>
      @auth
        <a href="{{ $dash }}" class="px-3 py-3 rounded-xl text-[15px] font-medium">Dashboard</a>
        <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full text-left px-3 py-3 rounded-xl text-[15px] text-rose-300">Log out</button></form>
      @else
        <a href="{{ route('login') }}" class="px-3 py-3 rounded-xl text-[15px] font-medium">Log in</a>
        <a href="{{ route('register') }}" class="mt-1 h-12 rounded-xl btn-grad grid place-items-center text-[15px] font-semibold">Get started free</a>
      @endauth
    </div>
  </div>
</header>
