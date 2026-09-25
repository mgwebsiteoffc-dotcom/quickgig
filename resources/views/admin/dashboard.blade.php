@extends('admin.layout')
@section('title','Dashboard')
@section('breadcrumb','Admin • Overview')
@section('content')
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
  @foreach($stats as $s)
  <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4">
    <div class="flex items-center justify-between">
      <div class="w-9 h-9 rounded-xl bg-[#F8F8F7] border border-[#E8E8E6] grid place-items-center">
        @if($s['icon']=='shopping-bag')<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        @elseif($s['icon']=='wallet')<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0E8A4B" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M16 14a2 2 0 0 0 0-4"/></svg>
        @elseif($s['icon']=='users')<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        @else<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>@endif
      </div>
      <span class="text-[11px] font-bold px-2 py-1 rounded-full bg-[#F8F8F7] border border-[#E8E8E6]">{{ $s['change'] }}</span>
    </div>
    <div class="mt-3 text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">{{ $s['label'] }}</div>
    <div class="text-[22px] font-black tracking-tight">{{ $s['value'] }}</div>
  </div>
  @endforeach
</div>

<div class="mt-6 grid lg:grid-cols-[1.6fr_0.9fr] gap-6">
  <!-- Recent orders -->
  <div class="bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
    <div class="px-5 py-4 flex items-center justify-between border-b border-[#F0F0EE]">
      <div class="font-black text-[14px]">Recent Orders</div>
      <a href="{{ route('admin.orders.index') }}" class="h-8 px-3 rounded-full border border-[#E8E8E6] font-bold text-[12px] inline-flex items-center hover:bg-[#F8F8F7]">View all</a>
    </div>
    <div class="divide-y divide-[#F0F0EE]">
      @foreach($recentOrders as $o)
      <a href="{{ route('admin.orders.show',$o['id']) }}" class="flex items-center gap-3 px-5 py-3 hover:bg-[#F8F8F7]/70">
        <div class="w-9 h-9 rounded-xl bg-[#0F0F0F] text-white grid place-items-center font-black text-[10px]">{{ substr($o['id'],0,2) }}</div>
        <div class="flex-1 min-w-0">
          <div class="text-[13px] font-bold truncate">{{ $o['id'] }} • {{ $o['service'] }}</div>
          <div class="text-[11px] font-semibold text-[#7A7A78] truncate">{{ $o['company'] }} → {{ $o['creator'] }} • {{ $o['time'] }}</div>
        </div>
        <div class="text-right">
          <div class="text-[13px] font-black">₹{{ number_format($o['amount']) }}</div>
          <span class="text-[11px] font-bold px-2 py-0.5 rounded-full border
            @if($o['status']=='pending') bg-amber-50 border-amber-200 text-amber-700
            @elseif($o['status']=='working') bg-blue-50 border-blue-200 text-blue-700
            @elseif($o['status']=='review') bg-violet-50 border-violet-200 text-violet-700
            @elseif($o['status']=='delivered') bg-green-50 border-green-200 text-green-700
            @else bg-[#F8F8F7] border-[#E8E8E6] @endif
          ">{{ ucfirst($o['status']) }}</span>
        </div>
      </a>
      @endforeach
    </div>
  </div>

  <!-- Payout queue + quick actions -->
  <div class="space-y-6">
    <div class="bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
      <div class="px-5 py-4 border-b border-[#F0F0EE] flex items-center justify-between">
        <div class="font-black text-[14px]">Payout Queue</div>
        <span class="text-[11px] font-bold bg-amber-400 text-[#0F0F0F] px-2 py-1 rounded-full">{{ count($payoutQueue) }} in queue</span>
      </div>
      <div class="p-3 space-y-3">
        @foreach($payoutQueue as $p)
        <div class="flex items-center gap-3 p-3 rounded-xl bg-[#F8F8F7] border border-[#E8E8E6]">
          <div class="w-9 h-9 rounded-full bg-[#0F0F0F] text-white grid place-items-center font-black text-[11px] shrink-0">{{ strtoupper(substr($p['creator'],0,1)) }}</div>
          <div class="flex-1 min-w-0"><div class="text-[13px] font-bold truncate">{{ $p['creator'] }}</div><div class="text-[11px] font-semibold text-[#7A7A78] truncate">{{ $p['handle'] }} • {{ $p['orders'] }} orders</div></div>
          <div class="text-right"><div class="text-[13px] font-black">₹{{ number_format($p['amount']) }}</div><div class="text-[11px] font-semibold text-[#7A7A78]">{{ $p['upi'] }}</div></div>
        </div>
        @endforeach
        <a href="{{ route('admin.payouts.index') }}" class="h-9 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px] grid place-items-center">Go to Payouts</a>
      </div>
    </div>

    <div class="bg-[#0F0F0F] text-white rounded-2xl p-5">
      <div class="text-[11px] font-bold tracking-widest uppercase text-white/60">Quick Actions</div>
      <div class="mt-3 grid grid-cols-2 gap-2">
        <a href="{{ route('admin.orders.index') }}?status=pending" class="h-10 rounded-xl bg-white text-[#0F0F0F] font-bold text-[13px] grid place-items-center">Pending</a>
        <a href="{{ route('admin.orders.index') }}?status=review" class="h-10 rounded-xl bg-white/10 border border-white/20 font-bold text-[13px] grid place-items-center">In Review</a>
        <a href="{{ route('admin.creators.index') }}?filter=pending" class="h-10 rounded-xl bg-white/10 border border-white/20 font-bold text-[13px] grid place-items-center">Verify Creators</a>
        <a href="{{ route('admin.users.index') }}" class="h-10 rounded-xl bg-amber-400 text-[#0F0F0F] font-black text-[13px] grid place-items-center">Manage Roles</a>
      </div>
      <div class="mt-3 text-[11px] font-medium text-white/60">Role: <b class="text-white">{{ $role }}</b> • Super Admin sees everything. Manager: orders+creators. Support: orders view. Finance: payouts.</div>
    </div>
  </div>
</div>

<div class="mt-6 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex gap-3">
  <div class="w-8 h-8 rounded-full bg-amber-400 text-[#0F0F0F] grid place-items-center font-black shrink-0">!</div>
  <div class="text-[13px] leading-5"><b>Hostinger cron setup (required for queue):</b> In hPanel → Cron Jobs → Add: <code class="bg-white border border-amber-200 px-1.5 py-0.5 rounded font-mono text-[11px]">* * * * * /usr/bin/php /home/u123456789/domains/yourdomain.com/artisan queue:work --stop-when-empty >> /dev/null 2>&1</code> and <code class="bg-white border border-amber-200 px-1.5 py-0.5 rounded font-mono text-[11px]">* * * * * /usr/bin/php /home/u123456789/domains/yourdomain.com/artisan schedule:run >> /dev/null 2>&1</code></div>
</div>
@endsection
