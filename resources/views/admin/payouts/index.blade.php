@extends('admin.layout')
@section('title','Payouts')
@section('breadcrumb','Admin • Finance • Payouts')
@section('content')
<div class="grid sm:grid-cols-3 gap-4">
  <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4"><div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Held (escrow)</div><div class="text-[18px] font-black">{{ $stats['hold'] }}</div></div>
  <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4"><div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Ready to pay</div><div class="text-[18px] font-black">{{ $stats['ready'] }}</div></div>
  <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4"><div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Paid this month</div><div class="text-[18px] font-black">{{ $stats['paid'] }}</div></div>
</div>

<div class="mt-6 bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
  <div class="px-5 py-4 border-b border-[#F0F0EE] flex items-center justify-between"><div class="font-black text-[14px]">Payout Queue</div><div class="text-[12px] font-semibold text-[#7A7A78]">RazorpayX • UPI • Cron moves held → ready after {{ 48 }}h</div></div>
  <div class="divide-y divide-[#F0F0EE]">
    @foreach($queue as $q)
    <div class="flex flex-wrap items-center gap-4 px-5 py-4 hover:bg-[#F8F8F7]/50">
      <img src="https://i.pravatar.cc/100?img={{ $q['id'] + 4 }}" class="w-10 h-10 rounded-full object-cover border border-[#E8E8E6]">
      <div class="flex-1 min-w-[160px]"><div class="text-[13px] font-black">{{ $q['creator'] }} <span class="text-[#7A7A78] font-semibold">{{ $q['handle'] }}</span></div><div class="text-[11px] font-semibold text-[#7A7A78]">UPI: {{ $q['upi'] }} • {{ $q['orders'] }} orders • Hold till {{ $q['hold_until'] }}</div></div>
      <div class="text-right"><div class="text-[14px] font-black">₹{{ number_format($q['amount']) }}</div><span class="text-[11px] font-bold px-2.5 py-1 rounded-full border
        @if($q['status']=='hold') bg-amber-50 border-amber-200 text-amber-700
        @elseif($q['status']=='ready') bg-green-50 border-green-200 text-green-700
        @else bg-[#F8F8F7] border-[#E8E8E6] text-[#7A7A78] @endif
      ">{{ ucfirst($q['status']) }}</span></div>
      <div class="flex gap-2">
        @if($q['status']=='ready')<form method="POST" action="{{ route('admin.payouts.paid',$q['id']) }}">@csrf<button class="h-9 px-5 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Mark Paid</button></form>@endif
        @if($q['status']!='paid')<form method="POST" action="{{ route('admin.payouts.hold',$q['id']) }}">@csrf<button class="h-9 px-4 rounded-full border border-[#E8E8E6] bg-white font-bold text-[13px]">Hold</button></form>@endif
      </div>
    </div>
    @endforeach
  </div>
</div>

<div class="mt-4 bg-blue-50 border border-blue-200 rounded-2xl p-4 text-[13px] leading-5"><b>How payouts work:</b> Payments are held in Razorpay escrow. A daily cron runs <code class="bg-white border border-blue-200 px-1 py-0.5 rounded font-mono text-[11px]">php artisan payouts:release</code> (you create this command) to move eligible holds to ready. Finance clicks Mark Paid → calls RazorpayX payout API → creator gets UPI. Support cron logs to <code class="bg-white border border-blue-200 px-1 py-0.5 rounded">storage/logs/payouts.log</code>.</div>
@endsection
