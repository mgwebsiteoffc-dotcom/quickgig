@extends('admin.layout')
@section('title','Order '.$order['id'])
@section('breadcrumb','Admin • Orders • '.$order['id'])
@section('content')
<div class="grid lg:grid-cols-[1.7fr_0.9fr] gap-6">
  <div class="space-y-4">
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
          <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Order {{ $order['id'] }} • {{ $order['created'] }}</div>
          <div class="text-[18px] font-black mt-1">{{ $order['service'] }}</div>
          <div class="text-[13px] font-medium text-[#7A7A78]">For <b class="text-[#0F0F0F]">{{ $order['company'] }} • {{ $order['person'] }}</b> → Creator: <b class="text-[#0F0F0F]">{{ $order['creator'] }}</b></div>
        </div>
        <div class="text-right"><div class="text-[20px] font-black">₹{{ number_format($order['amount']) }}</div><div class="text-[11px] font-bold px-2.5 py-1 rounded-full inline-flex {{ $order['escrow']=='held' ? 'bg-amber-400 text-[#0F0F0F]' : 'bg-green-500 text-white' }}">Escrow: {{ ucfirst($order['escrow']) }}</div></div>
      </div>

      <div class="mt-5 grid grid-cols-3 gap-3 text-center">
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3"><div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Status</div><div class="font-black capitalize">{{ $order['status'] }}</div></div>
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3"><div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Escrow</div><div class="font-black">{{ ucfirst($order['escrow']) }}</div></div>
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3"><div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Delivery</div><div class="font-black">1 Day</div></div>
      </div>

      <div class="mt-5">
        <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Timeline (quick-delivery-style)</div>
        <div class="mt-3 space-y-3">
          @foreach($timeline as $t)
          <div class="flex gap-3">
            <div class="w-7 h-7 rounded-full grid place-items-center shrink-0 {{ $t['done'] ? 'bg-green-500 text-white' : 'bg-white border-2 border-[#E8E8E6]' }}">@if($t['done'])<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>@else<span class="w-2 h-2 bg-[#E8E8E6] rounded-full"></span>@endif</div>
            <div class="flex-1 pb-3 border-b border-[#F0F0EE] last:border-0"><div class="text-[13px] font-bold {{ $t['done'] ? '' : 'text-[#7A7A78]' }}">{{ $t['t'] }}</div><div class="text-[11px] font-semibold text-[#7A7A78]">{{ $t['time'] }}</div></div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="font-black text-[14px]">Brief from Company</div>
      <div class="mt-2 text-[13px] leading-6 font-medium text-[#2b2b2b] bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3">“Need a talking-head reel for Avante Studio — 30 sec, hook in first 2 sec, captions burned-in, 9:16, brand colors navy + white. Reference: @devtalksbusiness reel style. Deliver with SRT + thumbnail option.”</div>
      <div class="mt-3 flex gap-2 text-[12px] font-bold"><span class="px-2.5 py-1 rounded-full bg-white border border-[#E8E8E6]">1 Day</span><span class="px-2.5 py-1 rounded-full bg-white border border-[#E8E8E6]">9:16</span><span class="px-2.5 py-1 rounded-full bg-white border border-[#E8E8E6]">Captions required</span></div>
    </div>
  </div>

  <div class="space-y-4">
    @if(auth()->user()->hasAnyRole(['super_admin','admin','manager']))
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="font-black text-[13px]">Change Status</div>
      <form method="POST" action="{{ route('admin.orders.status',$order['id']) }}" class="mt-3 space-y-3">@csrf
        <select name="status" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold">
          @foreach(['pending','working','review','delivered','approved','cancelled'] as $s)<option value="{{ $s }}" {{ $order['status']==$s ? 'selected':'' }}>{{ ucfirst($s) }}</option>@endforeach
        </select>
        <button class="w-full h-10 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Update Status</button>
      </form>
      <form method="POST" action="{{ route('admin.orders.assign',$order['id']) }}" class="mt-3 flex gap-2">@csrf
        <select name="creator_id" class="flex-1 h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px] font-semibold"><option value="1">Priya Sharma (@priyaedits)</option><option value="2">Rahul Verma (@rahulcuts)</option><option value="4">Neha Jain (@nehacreates)</option></select>
        <button class="h-10 px-4 rounded-full border-2 border-[#0F0F0F] font-bold text-[13px]">Re-assign</button>
      </form>
    </div>
    @endif

    @if(auth()->user()->hasAnyRole(['super_admin','admin','finance']) && $order['escrow']=='held')
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
      <div class="font-black text-[13px]">Escrow Control</div>
      <div class="text-[12px] font-semibold text-[#7A7A78] mt-1">₹{{ number_format($order['amount']) }} held via Razorpay. Release only after Company approves.</div>
      <form method="POST" action="{{ route('admin.orders.release',$order['id']) }}" class="mt-3">@csrf<button class="w-full h-10 rounded-full bg-amber-400 text-[#0F0F0F] font-black text-[13px]">Release Escrow → Creator Payout</button></form>
    </div>
    @endif

    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="font-black text-[13px]">Chat (polling — Hostinger safe)</div>
      <div class="mt-3 space-y-2 max-h-[220px] overflow-y-auto">
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3"><div class="text-[11px] font-bold text-[#7A7A78]">Rohan Sharma (Avante) • 10:32 AM</div><div class="text-[13px] font-medium">Hi Priya, can we add a hook text at 0:02?</div></div>
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-3"><div class="text-[11px] font-bold text-blue-700">Priya Sharma • 10:38 AM</div><div class="text-[13px] font-medium">Sure! Adding now — will share draft in 1 hour.</div></div>
      </div>
      <form method="POST" action="{{ route('orders.message',$order['id']) }}" class="mt-3 flex gap-2">@csrf<input name="message" placeholder="Type as admin..." class="flex-1 h-10 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"><button class="h-10 px-5 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Send</button></form>
      <div class="mt-2 text-[11px] text-[#7A7A78] font-medium">No WebSockets needed — page polls every 15s. Works on shared hosting.</div>
    </div>
  </div>
</div>
@endsection
