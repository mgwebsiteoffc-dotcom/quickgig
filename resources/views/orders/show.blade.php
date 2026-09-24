@extends('layouts.app')
@section('content')
<div class="max-w-[760px] mx-auto px-4 sm:px-6 py-6">
  <a href="{{ route('business.home') }}" class="text-[12px] font-bold text-[#7A7A78] hover:text-ink">← Back to Marketplace</a>
  <div class="mt-3 bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
    <div class="px-5 py-4 flex flex-wrap items-center justify-between gap-3 border-b border-[#F0F0EE]">
      <div>
        <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">ORDER • {{ $order['id'] }}</div>
        <div class="text-[16px] font-black">{{ $order['service']['title'] ?? 'Service' }} @if(($order['service']['price_type']??'')=='barter')<span class="ml-2 text-[11px] font-black px-2.5 py-1 rounded-full bg-amber-400 text-ink">BARTER • Ship product</span>@endif</div>
        <div class="text-[12px] font-semibold text-[#7A7A78]">For {{ $order['company']['name'] ?? '' }} • {{ $order['service']['category'] ?? '' }} • {{ $order['service']['time'] ?? '' }}</div>
      </div>
      <div class="text-right">
        <div class="text-[13px] font-black">{{ $order['service']['display_price'] ?? (isset($order['service']['price']) ? '₹'.number_format($order['service']['price']) : '—') }}</div>
        <div class="text-[11px] font-bold px-2.5 py-1 rounded-full inline-block border {{ $order['status']=='working' ? 'bg-amber-50 border-amber-200 text-amber-800' : ($order['status']=='review' ? 'bg-blue-50 border-blue-200 text-blue-700' : 'bg-green-50 border-green-200 text-green-700') }}">{{ ucfirst($order['status']) }}</div>
        @if(($order['escrow_status']??'')==='barter')<div class="text-[11px] font-semibold text-[#7A7A78]">No escrow — product exchange</div>@else<div class="text-[11px] font-semibold text-[#7A7A78]">Escrow: {{ $order['escrow_status'] ?? 'held' }} • Total ₹{{ number_format($order['total']) }}</div>@endif
      </div>
    </div>

    {{-- Tracker like food delivery --}}
    <div class="px-5 py-5">
      <div class="flex items-center justify-between text-center">
        @php $steps = [['Ordered','10:30 AM', $order['progress']>=10],['Assigned','10:42 AM', $order['progress']>=30],['Working','Now', $order['progress']>=25 && $order['progress']<90],['Review','~6 PM', $order['progress']>=90]]; @endphp
        @foreach($steps as $i=>$st)
          <div class="flex-1">
            <div class="w-8 h-8 mx-auto rounded-full grid place-items-center font-black text-[11px] border-2 {{ $st[2] ? 'bg-green-500 border-green-500 text-white' : 'bg-white border-[#E8E8E6] text-[#7A7A78]' }}">@if($st[2]) ✓ @else {{ $i+1 }} @endif</div>
            <div class="text-[11px] font-bold mt-1 {{ $st[2] ? 'text-ink' : 'text-[#7A7A78]' }}">{{ $st[0] }}</div><div class="text-[10px] text-[#7A7A78]">{{ $st[1] }}</div>
          </div>
          @if($i<3)<div class="flex-1 h-0.5 {{ $st[2] ? 'bg-green-500' : 'bg-[#E8E8E6]' }} mt-4"></div>@endif
        @endforeach
      </div>
      <div class="mt-4 h-1.5 bg-[#F8F8F7] rounded-full overflow-hidden border border-[#E8E8E6]"><div class="h-full bg-ink" style="width:{{ $order['progress'] }}%"></div></div>
      <div class="mt-2 text-[11px] text-center font-semibold text-[#7A7A78]">Live tracking — easy like ordering food • Assigned in ~12 min avg.</div>
    </div>

    <div class="px-5 pb-5 grid sm:grid-cols-2 gap-4">
      <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-2xl p-4">
        <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Creator — verified</div>
        <div class="mt-2 flex gap-3 items-center">
          <div class="relative"><img src="{{ $order['creator']['img'] }}" class="w-12 h-12 rounded-full object-cover border border-[#E8E8E6]"><span class="absolute -bottom-1 -right-1 w-5 h-5 rounded-full bg-[#1D9BF0] text-white grid place-items-center border-2 border-white" style="aspect-ratio:1/1"><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg></span></div>
          <div><div class="text-[14px] font-black flex items-center gap-1">{{ $order['creator']['name'] }} <span class="w-2 h-2 bg-green-500 rounded-full"></span></div><div class="text-[12px] font-semibold text-[#7A7A78]">{{ $order['creator']['handle'] }} • {{ $order['creator']['rating'] }}★ • Verified</div></div>
        </div>
        <div class="mt-3 text-[11px] font-semibold text-[#7A7A78]">Chat available • Response in ~4 min</div>
      </div>
      <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-2xl p-4">
        <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Brief from {{ $order['company']['name'] ?? 'Company' }}</div>
        <div class="mt-2 text-[13px] leading-6 bg-white border border-[#E8E8E6] rounded-xl p-3">{{ $order['brief'] ?? '—' }}</div>
        <div class="mt-2 text-[11px] font-semibold text-[#7A7A78]">Company: {{ $order['company']['name'] ?? '' }} • {{ $order['company']['person'] ?? '' }}</div>
      </div>
    </div>

    <div class="px-5 pb-5 flex gap-3">
      @if(($order['escrow_status']??'')==='barter')
        <form method="POST" action="{{ route('orders.approve',$order['id']) }}" class="flex-1">@csrf<button class="w-full h-11 rounded-full bg-amber-400 text-ink font-black text-[13px]">Mark Collab Done ✓</button></form>
        <div class="flex-1 h-11 rounded-full border border-[#E8E8E6] bg-white grid place-items-center font-bold text-[13px]">Ship product to creator</div>
      @else
        <form method="POST" action="{{ route('orders.approve',$order['id']) }}" class="flex-1">@csrf<button class="w-full h-11 rounded-full bg-ink text-white font-black text-[13px]">Approve & Release ₹{{ number_format($order['total']) }} →</button></form>
        <form method="POST" action="{{ route('orders.message',$order['id']) }}" class="flex-1 flex gap-2">@csrf<input name="message" placeholder="Ask for revision..." class="flex-1 h-11 px-4 rounded-full border border-[#E8E8E6] bg-white text-[13px]"><button class="h-11 px-5 rounded-full border border-[#E8E8E6] bg-white font-bold text-[13px]">Send</button></form>
      @endif
    </div>
    <div class="px-5 pb-4 text-[11px] text-center font-medium text-[#7A7A78]">@if(($order['escrow_status']??'')==='barter')Barter: no payment held. Business ships product, creator posts. Approve when post is live. @else Held in Razorpay escrow • Releases only when you Approve • 2 free revisions @endif</div>
  </div>
</div>
@endsection
