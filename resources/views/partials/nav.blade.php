@php
  $user = auth()->user();
  $dash = $user ? ($user->isAdmin() ? route('admin.dashboard') : ($user->hasRole('creator') ? route('creator.dashboard') : route('business.home'))) : null;
  $nav = [
    ['label' => 'Marketplace',   'href' => route('marketplace')],
    ['label' => 'How it works',  'href' => route('landing').'#how'],
    ['label' => 'Pricing',       'href' => route('landing').'#pricing'],
    ['label' => 'Insights',      'href' => route('blog.index')],
  ];
@endphp

<header x-data="{ open:false, scrolled:false }" x-on:scroll.window="scrolled = window.scrollY > 12"
        class="sticky top-0 z-50 transition-colors duration-300"
        :class="scrolled || open ? 'glass-strong border-b border-white/10' : 'border-b border-transparent'">
  <div class="max-w-shell mx-auto px-5 lg:px-8 h-[70px] flex items-center justify-between gap-6">

    {{-- Brand --}}
    <a href="{{ route('landing') }}" class="flex items-center gap-2.5 shrink-0 group">
      <span class="w-9 h-9 rounded-xl grid place-items-center btn-grad shadow-lg shadow-violet/25 group-hover:scale-105 transition">
        <svg width="16" height="16" viewBox="0 0 24 24" fill="#06060B"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z"/></svg>
      </span>
      <span class="font-display text-[17px] tracking-tight leading-none">
        <span class="font-medium text-white/90">Quick</span><span class="font-bold text-white"> GIGS</span>
      </span>
    </a>

    {{-- Desktop menu — 4 links, nothing else --}}
    <nav class="hidden lg:flex items-center gap-1 text-[14px]">
      @foreach($nav as $item)
        <a href="{{ $item['href'] }}" class="px-3.5 py-2 rounded-lg text-mut hover:text-white hover:bg-white/5 transition font-medium">{{ $item['label'] }}</a>
      @endforeach
    </nav>

    {{-- Right side --}}
    <div class="flex items-center gap-2.5">
      @auth
        <a href="{{ $dash }}" class="hidden sm:inline-flex h-10 px-4 rounded-xl border border-white/12 hover:bg-white/5 items-center text-[13.5px] font-medium transition">Dashboard</a>
        <div x-data="{ m:false }" class="relative hidden sm:block">
          <button x-on:click="m=!m" class="w-10 h-10 rounded-xl btn-grad grid place-items-center font-display font-bold text-[13px] text-ink">{{ strtoupper(substr($user->name ?? 'U',0,1)) }}</button>
          <div x-show="m" x-cloak x-on:click.outside="m=false" x-transition class="absolute right-0 mt-2 w-56 glass-strong rounded-2xl p-2 shadow-2xl">
            <div class="px-3 py-2">
              <div class="text-[13px] font-semibold truncate">{{ $user->name }}</div>
              <div class="text-[11.5px] text-mut truncate">{{ $user->email }}</div>
            </div>
            <div class="h-px bg-white/10 my-1"></div>
            <a href="{{ $dash }}" class="block px-3 py-2 rounded-xl text-[13.5px] hover:bg-white/8">Dashboard</a>
            <a href="{{ $user->hasRole('creator') ? route('creator.profile') : route('business.profile') }}" class="block px-3 py-2 rounded-xl text-[13.5px] hover:bg-white/8">Profile settings</a>
            <form method="POST" action="{{ route('logout') }}">@csrf
              <button class="w-full text-left px-3 py-2 rounded-xl text-[13.5px] text-rose-300 hover:bg-rose-500/10">Log out</button>
            </form>
          </div>
        </div>
      @else
        <a href="{{ route('login') }}" class="hidden sm:inline-flex h-10 px-4 rounded-xl text-mut hover:text-white items-center text-[13.5px] font-medium transition">Log in</a>
        <a href="{{ route('register') }}" class="inline-flex h-10 px-5 rounded-xl btn-grad items-center gap-1.5 text-[13.5px] font-semibold shadow-lg shadow-violet/20 transition">
          Get started
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
      @endauth

      {{-- Mobile toggle --}}
      <button x-on:click="open=!open" class="lg:hidden w-10 h-10 rounded-xl border border-white/12 grid place-items-center" aria-label="Menu">
        <svg x-show="!open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18M3 12h18M3 18h18"/></svg>
        <svg x-show="open" x-cloak width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 6l12 12M18 6L6 18"/></svg>
      </button>
    </div>
  </div>

  {{-- Mobile drawer --}}
  <div x-show="open" x-cloak x-transition.origin.top class="lg:hidden border-t border-white/10 px-5 py-4 glass-strong">
    <div class="flex flex-col gap-1">
      @foreach($nav as $item)
        <a href="{{ $item['href'] }}" x-on:click="open=false" class="px-3 py-3 rounded-xl text-[15px] font-medium text-white/85 hover:bg-white/5">{{ $item['label'] }}</a>
      @endforeach
      <div class="h-px bg-white/10 my-2"></div>
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
