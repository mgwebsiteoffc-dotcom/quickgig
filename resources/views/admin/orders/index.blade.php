@extends('admin.layout')
@section('title','Orders')
@section('breadcrumb','Admin • Orders')
@section('content')
<form method="GET" class="bg-white border border-[#E8E8E6] rounded-2xl p-4 flex flex-wrap gap-3 items-center">
  <div class="flex gap-1.5 flex-wrap">
    @foreach(['all'=>'All','pending'=>'Pending','working'=>'Working','review'=>'Review','delivered'=>'Delivered'] as $k=>$v)
      <a href="?status={{ $k }}{{ request('q') ? '&q='.request('q') : '' }}" class="px-3.5 py-1.5 rounded-full text-[13px] font-bold border {{ ($status ?? 'all')==$k ? 'bg-[#0F0F0F] text-white border-[#0F0F0F]' : 'bg-white border-[#E8E8E6] hover:bg-[#F8F8F7]' }}">{{ $v }} <span class="opacity-60">({{ $counts[$k] ?? 0 }})</span></a>
    @endforeach
  </div>
  <div class="flex-1 min-w-[220px] flex gap-2 ml-auto">
    <input name="q" value="{{ $q }}" placeholder="Search order, company, service..." class="flex-1 h-10 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-medium outline-none focus:bg-white">
    <button class="h-10 px-5 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Search</button>
  </div>
</form>

<div class="mt-4 bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left text-[13px]">
      <thead class="bg-[#F8F8F7] text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">
        <tr><th class="px-4 py-3">Order</th><th class="px-4 py-3">Company</th><th class="px-4 py-3">Service</th><th class="px-4 py-3">Creator</th><th class="px-4 py-3">Amount</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Escrow</th><th class="px-4 py-3"></th></tr>
      </thead>
      <tbody class="divide-y divide-[#F0F0EE]">
        @forelse($orders as $o)
        <tr class="hover:bg-[#F8F8F7]/50">
          <td class="px-4 py-3 font-black">{{ $o['id'] }}<div class="text-[11px] font-medium text-[#7A7A78]">{{ $o['created'] }}</div></td>
          <td class="px-4 py-3"><div class="font-bold">{{ $o['company'] }}</div><div class="text-[11px] text-[#7A7A78] font-semibold">{{ $o['person'] }}</div></td>
          <td class="px-4 py-3 font-semibold">{{ $o['service'] }}</td>
          <td class="px-4 py-3 font-semibold">{{ $o['creator'] }}</td>
          <td class="px-4 py-3 font-black">₹{{ number_format($o['amount']) }}</td>
          <td class="px-4 py-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold border
            @if($o['status']=='pending') bg-amber-50 border-amber-200 text-amber-700
            @elseif($o['status']=='working') bg-blue-50 border-blue-200 text-blue-700
            @elseif($o['status']=='review') bg-violet-50 border-violet-200 text-violet-700
            @elseif($o['status']=='delivered') bg-green-50 border-green-200 text-green-700
            @elseif($o['status']=='approved') bg-green-100 border-green-300 text-green-800
            @else bg-white border-[#E8E8E6] @endif
          ">{{ ucfirst($o['status']) }}</span></td>
          <td class="px-4 py-3"><span class="text-[11px] font-bold px-2 py-1 rounded-full {{ $o['escrow']=='held' ? 'bg-amber-400 text-[#0F0F0F]' : 'bg-green-500 text-white' }}">{{ $o['escrow']=='held' ? 'Held' : 'Released' }}</span></td>
          <td class="px-4 py-3"><a href="{{ route('admin.orders.show',$o['id']) }}" class="h-8 px-3 rounded-full border border-[#E8E8E6] bg-white font-bold text-[12px] inline-flex items-center hover:bg-[#0F0F0F] hover:text-white">View</a></td>
        </tr>
        @empty
        <tr><td colspan="8" class="px-4 py-12 text-center text-[#7A7A78] font-semibold">No orders found</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
