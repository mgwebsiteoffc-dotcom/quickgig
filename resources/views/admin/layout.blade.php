<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Admin') — Quick GIGS Admin</title>
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>*{font-family:Inter,sans-serif} .sb::-webkit-scrollbar{width:6px;height:6px} .sb::-webkit-scrollbar-thumb{background:#E8E8E6;border-radius:999px}</style>
</head>
<body class="bg-[#F8F8F7] text-[#0F0F0F]" x-data="{open:false}">
<div class="min-h-screen flex">
  <!-- Sidebar -->
  <aside class="hidden lg:flex w-[260px] shrink-0 bg-white border-r border-[#E8E8E6] flex-col sticky top-0 h-screen">
    <div class="h-[64px] px-5 flex items-center gap-3 border-b border-[#F0F0EE] shrink-0">
      <div class="w-8 h-8 rounded-xl text-white grid place-items-center" style="background:linear-gradient(100deg,#7C5CFF,#22D3EE)"><svg width="14" height="14" viewBox="0 0 24 24" fill="#06060B"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z"/></svg></div>
      <div class="leading-none">
        <div class="font-black text-[13px]">Quick GIGS</div>
        <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">ADMIN • {{ strtoupper(auth()->user()->role ?? 'admin') }}</div>
      </div>
    </div>
    <nav class="flex-1 overflow-y-auto sb p-3 space-y-1">
      @php
        $nav = [
          ['label'=>'Dashboard','icon'=>'layout-dashboard','route'=>'admin.dashboard','roles'=>['super_admin','admin','manager','support','finance']],
          ['label'=>'Orders','icon'=>'shopping-bag','route'=>'admin.orders.index','roles'=>['super_admin','admin','manager','support','finance'],'badge'=>'23'],
          ['label'=>'Creators','icon'=>'users','route'=>'admin.creators.index','roles'=>['super_admin','admin','manager']],
          ['label'=>'Companies','icon'=>'building','route'=>'admin.companies.index','roles'=>['super_admin','admin','manager','support']],
          ['label'=>'Leads','icon'=>'inbox','route'=>'admin.leads.index','roles'=>['super_admin','admin','manager','support']],
          ['label'=>'Skills','icon'=>'tag','route'=>'admin.skills.index','roles'=>['super_admin','admin','manager']],
          ['label'=>'Services','icon'=>'layers','route'=>'admin.services.index','roles'=>['super_admin','admin','manager']],
          ['label'=>'Blogs','icon'=>'file-text','route'=>'admin.blogs.index','roles'=>['super_admin','admin','manager']],
          ['label'=>'FAQs','icon'=>'help','route'=>'admin.faqs.index','roles'=>['super_admin','admin','manager']],
          ['label'=>'Payouts','icon'=>'wallet','route'=>'admin.payouts.index','roles'=>['super_admin','admin','finance']],
          ['label'=>'Users & Roles','icon'=>'shield','route'=>'admin.users.index','roles'=>['super_admin','admin']],
          ['label'=>'Settings','icon'=>'settings','route'=>'admin.settings.index','roles'=>['super_admin']],
        ];
        $current = Route::currentRouteName();
      @endphp
      @foreach($nav as $n)
        @if(!auth()->user() || auth()->user()->isSuperAdmin() || in_array(auth()->user()->role, $n['roles']))
        <a href="{{ Route::has($n['route']) ? route($n['route']) : '#' }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl text-[13px] font-semibold {{ str_starts_with($current, explode('.',$n['route'])[0].'.'.explode('.',$n['route'])[1]) ? 'bg-[#0F0F0F] text-white' : 'hover:bg-[#F8F8F7] text-[#0F0F0F]' }}">
          <span class="w-7 h-7 rounded-lg grid place-items-center {{ str_starts_with($current, explode('.',$n['route'])[0].'.'.explode('.',$n['route'])[1]) ? 'bg-white/15' : 'bg-[#F8F8F7] border border-[#E8E8E6]' }}">
            @if($n['icon']=='layout-dashboard')<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>
            @elseif($n['icon']=='shopping-bag')<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
            @elseif($n['icon']=='users')<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            @elseif($n['icon']=='building')<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="4" y="2" width="16" height="20" rx="2"/><path d="M9 22V12h6v10"/><path d="M8 6h.01M12 6h.01M16 6h.01M8 10h.01M12 10h.01M16 10h.01"/></svg>
            @elseif($n['icon']=='layers')<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/></svg>
            @elseif($n['icon']=='wallet')<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M16 14a2 2 0 0 0 0-4"/></svg>
            @elseif($n['icon']=='shield')<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
            @elseif($n['icon']=='file-text')<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
            @elseif($n['icon']=='help')<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
            @else<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M12 1v4M12 19v4M4.2 4.2l2.8 2.8M17 17l2.8 2.8M1 12h4M19 12h4M4.2 19.8l2.8-2.8M17 7l2.8-2.8"/></svg>@endif
          </span>
          <span class="flex-1">{{ $n['label'] }}</span>
          @if(isset($n['badge']))<span class="text-[11px] font-black bg-amber-400 text-[#0F0F0F] px-2 py-0.5 rounded-full">{{ $n['badge'] }}</span>@endif
        </a>
        @endif
      @endforeach
    </nav>
    <div class="p-3 border-t border-[#F0F0EE] space-y-2">
      <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3">
        <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">System healthy</div>
        <div class="text-[12px] font-semibold leading-tight mt-1">No Redis • File cache • DB queue (cron)</div>
        <div class="text-[11px] text-[#7A7A78] font-medium">Tailwind CDN • No build needed</div>
      </div>
      <div class="flex items-center gap-3 px-2">
        <img src="https://i.pravatar.cc/100?img=68" class="w-8 h-8 rounded-full border border-[#E8E8E6]">
        <div class="flex-1 min-w-0"><div class="text-[13px] font-bold truncate">{{ auth()->user()->name ?? 'Admin' }}</div><div class="text-[11px] font-semibold text-[#7A7A78] truncate">{{ auth()->user()->email ?? 'admin@quickcontent.in' }}</div></div>
      </div>
      <form method="POST" action="{{ route('logout') }}">@csrf<button class="w-full h-9 rounded-full border border-[#E8E8E6] bg-white font-bold text-[13px] hover:bg-[#F8F8F7]">Logout</button></form>
    </div>
  </aside>

  <!-- Main -->
  <div class="flex-1 min-w-0 flex flex-col">
    <!-- Topbar -->
    <div class="h-[64px] bg-white border-b border-[#E8E8E6] flex items-center justify-between px-4 sm:px-6 gap-4 sticky top-0 z-30">
      <div class="flex items-center gap-3">
        <button @click="open=!open" class="lg:hidden w-9 h-9 rounded-full border border-[#E8E8E6] grid place-items-center"><svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg></button>
        <div>
          <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">@yield('breadcrumb','Admin')</div>
          <div class="text-[16px] font-black tracking-tight">@yield('title','Dashboard')</div>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <a href="{{ route('landing') }}" class="hidden sm:inline-flex h-9 px-4 rounded-full border border-[#E8E8E6] font-bold text-[13px] items-center hover:bg-[#F8F8F7]">View Site</a>
        <a href="{{ route('business.home') }}" class="h-9 px-4 rounded-full bg-[#2563EB] text-white font-bold text-[13px] inline-flex items-center">Marketplace</a>
      </div>
    </div>

    <!-- Mobile drawer -->
    <div x-show="open" x-transition class="lg:hidden fixed inset-0 z-40 bg-black/40" @click="open=false"></div>
    <div x-show="open" x-transition class="lg:hidden fixed left-0 top-0 bottom-0 w-[280px] bg-white border-r border-[#E8E8E6] z-50 overflow-y-auto p-3">
      <div class="flex items-center justify-between h-[56px] px-2 border-b border-[#F0F0EE]"><span class="font-black">Quick GIGS Admin</span><button @click="open=false" class="w-8 h-8 rounded-full bg-[#F8F8F7] border border-[#E8E8E6] grid place-items-center">✕</button></div>
      <div class="mt-3 space-y-1">
        <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-[#0F0F0F] text-white font-semibold text-[13px]">Dashboard</a>
        <a href="{{ route('admin.orders.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#F8F8F7] font-semibold text-[13px] border border-transparent">Orders</a>
        <a href="{{ route('admin.creators.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#F8F8F7] font-semibold text-[13px]">Creators</a>
        <a href="{{ route('admin.payouts.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#F8F8F7] font-semibold text-[13px]">Payouts</a>
        <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#F8F8F7] font-semibold text-[13px]">Users & Roles</a>
      </div>
    </div>

    <main class="flex-1 p-4 sm:p-6">
      @if(session('toast'))<div class="mb-4 bg-[#0F0F0F] text-white rounded-full px-4 py-2.5 text-[13px] font-bold inline-flex gap-2"><span class="w-6 h-6 rounded-full bg-white text-[#0F0F0F] grid place-items-center text-[12px]">✓</span> {{ session('toast') }}</div>@endif
      @if(session('error'))<div class="mb-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-[13px] font-semibold">{{ session('error') }}</div>@endif
      @yield('content')
    </main>

    <div class="px-6 py-4 text-center text-[11px] font-semibold text-[#7A7A78] border-t border-[#F0F0EE] bg-white">© {{ date('Y') }} Quick GIGS • Admin console</div>
  </div>
</div>
</body>
</html>
