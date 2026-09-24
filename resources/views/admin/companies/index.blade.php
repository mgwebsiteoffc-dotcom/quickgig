@extends('admin.layout')
@section('title','Companies')
@section('breadcrumb','Admin • Companies')
@section('content')
<form method="GET" class="bg-white border border-[#E8E8E6] rounded-2xl p-4 flex gap-3">
  <input name="q" value="{{ $q }}" placeholder="Search company or person..." class="flex-1 h-10 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
  <button class="h-10 px-5 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Search</button>
</form>

<div class="mt-4 bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left text-[13px]">
      <thead class="bg-[#F8F8F7] text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]"><tr><th class="px-4 py-3">Company</th><th class="px-4 py-3">Person</th><th class="px-4 py-3">Plan</th><th class="px-4 py-3">Orders</th><th class="px-4 py-3">Spent</th><th class="px-4 py-3">Joined</th></tr></thead>
      <tbody class="divide-y divide-[#F0F0EE]">
        @foreach($companies as $c)
        <tr class="hover:bg-[#F8F8F7]/50">
          <td class="px-4 py-3 flex items-center gap-3"><div class="w-9 h-9 rounded-xl bg-[#0F0F0F] text-white grid place-items-center font-black text-[11px]">{{ $c['initials'] }}</div><span class="font-bold">{{ $c['company'] }}</span></td>
          <td class="px-4 py-3"><div class="font-semibold">{{ $c['person'] }}</div><div class="text-[11px] text-[#7A7A78] font-medium">{{ $c['email'] }} • {{ $c['phone'] }}</div></td>
          <td class="px-4 py-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold border bg-white border-[#E8E8E6]">{{ $c['plan'] }}</span></td>
          <td class="px-4 py-3 font-black">{{ $c['orders'] }}</td>
          <td class="px-4 py-3 font-black">{{ $c['spent'] }}</td>
          <td class="px-4 py-3 text-[#7A7A78] font-semibold">{{ $c['joined'] }}</td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endsection
