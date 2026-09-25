@extends('admin.layout')
@section('title','Leads')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
  <div>
    <h1 class="text-[22px] font-black tracking-tight">Leads</h1>
    <p class="text-[13px] font-medium text-[#7A7A78]">Contact and enterprise enquiries from the website.</p>
  </div>
  <span class="text-[12px] font-bold bg-white border border-[#E8E8E6] px-3 py-1.5 rounded-full">{{ $leads->total() }} total</span>
</div>

<div class="mt-5 bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full min-w-[820px] text-left">
      <thead class="bg-[#F8F8F7] border-b border-[#E8E8E6]">
        <tr>
          @foreach(['Who','Type','Details','Received','Status',''] as $h)
            <th class="px-4 py-3 text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">{{ $h }}</th>
          @endforeach
        </tr>
      </thead>
      <tbody>
        @forelse($leads as $lead)
          <tr class="border-b border-[#F0F0EE] last:border-0 align-top">
            <td class="px-4 py-4">
              <div class="font-bold text-[13.5px]">{{ $lead->name }}</div>
              <div class="text-[12px] text-[#7A7A78]">{{ $lead->email }}</div>
              @if($lead->phone)<div class="text-[12px] text-[#7A7A78]">{{ $lead->phone }}</div>@endif
            </td>
            <td class="px-4 py-4">
              <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $lead->type === 'enterprise' ? 'bg-violet-100 text-violet-700' : 'bg-[#F8F8F7] border border-[#E8E8E6]' }}">{{ ucfirst($lead->type) }}</span>
              @if($lead->company)<div class="text-[12px] text-[#7A7A78] mt-1.5">{{ $lead->company }}</div>@endif
              @if($lead->volume)<div class="text-[12px] text-[#7A7A78]">{{ $lead->volume }}</div>@endif
            </td>
            <td class="px-4 py-4 max-w-[340px]">
              <div class="text-[13px] leading-6 text-[#2b2b2b]">{{ $lead->message ?: '—' }}</div>
            </td>
            <td class="px-4 py-4 text-[12px] text-[#7A7A78] whitespace-nowrap">{{ $lead->created_at->diffForHumans() }}</td>
            <td class="px-4 py-4">
              <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $lead->is_handled ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">
                {{ $lead->is_handled ? 'Handled' : 'Open' }}
              </span>
            </td>
            <td class="px-4 py-4 text-right">
              @unless($lead->is_handled)
                <form method="POST" action="{{ route('admin.leads.handled', $lead->id) }}">
                  @csrf
                  <button class="h-9 px-4 rounded-full bg-[#0F0F0F] text-white font-bold text-[12px]">Mark handled</button>
                </form>
              @endunless
            </td>
          </tr>
        @empty
          <tr><td colspan="6" class="px-4 py-12 text-center">
            <div class="font-black">No leads yet</div>
            <div class="text-[13px] text-[#7A7A78] font-medium">Enquiries from the contact and teams pages will appear here.</div>
          </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="mt-5">{{ $leads->links() }}</div>
@endsection
