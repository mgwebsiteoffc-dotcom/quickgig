@extends('admin.layout')
@section('title','Payouts')
@section('breadcrumb','Admin • Finance • Payouts')

@section('content')
<div class="flex flex-wrap items-center justify-between gap-3">
  <div>
    <h1 class="text-[22px] font-black tracking-tight">Payout queue</h1>
    <p class="text-[13px] font-medium text-[#7A7A78]">
      Raised automatically when a buyer approves a delivery.
      {{ $autoPayouts ? 'RazorpayX is connected — Pay sends the transfer.' : 'Manual mode — record the UTR after you transfer.' }}
    </p>
  </div>
  <div class="flex gap-1.5">
    @foreach(['open' => 'Open', 'ready' => 'Ready', 'paid' => 'Paid', 'failed' => 'Failed', 'all' => 'All'] as $key => $label)
      <a href="{{ route('admin.payouts.index', ['status' => $key]) }}"
         class="h-9 px-3.5 rounded-xl text-[12.5px] font-bold inline-flex items-center border
                {{ $status === $key ? 'bg-[#0F0F0F] text-white border-[#0F0F0F]' : 'bg-white border-[#E8E8E6] hover:bg-[#F8F8F7]' }}">{{ $label }}</a>
    @endforeach
  </div>
</div>

<div class="mt-5 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
  @foreach([
    ['In escrow hold', '₹' . number_format($stats['held']), 'released after the hold window'],
    ['Ready to pay', '₹' . number_format($stats['ready']), 'approved and past the hold'],
    ['Paid this month', '₹' . number_format($stats['paid']), 'transferred to freelancers'],
    ['Platform fees', '₹' . number_format($stats['fees']), 'lifetime, across all payouts'],
  ] as [$label, $value, $hint])
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4">
      <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">{{ $label }}</div>
      <div class="mt-1 text-[20px] font-black">{{ $value }}</div>
      <div class="text-[11.5px] text-[#7A7A78]">{{ $hint }}</div>
    </div>
  @endforeach
</div>

@if($stats['failed'])
  <div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-2.5 text-[13px] font-semibold">
    {{ $stats['failed'] }} payout{{ $stats['failed'] > 1 ? 's' : '' }} failed — open the Failed tab and retry.
  </div>
@endif

<div class="mt-5 bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
  <div class="divide-y divide-[#F0F0EE]">
    @forelse($payouts as $payout)
      <div class="px-5 py-4 flex flex-wrap items-center gap-4" x-data="{ pay: false }">
        <img src="{{ $payout->creator?->avatarUrl() }}" class="w-10 h-10 rounded-full object-cover border border-[#E8E8E6]" alt="">

        <div class="flex-1 min-w-[190px]">
          <div class="text-[13.5px] font-black">{{ $payout->creator->name ?? 'Freelancer' }}</div>
          <div class="text-[11.5px] font-semibold text-[#7A7A78]">
            <span class="font-mono">{{ $payout->uid }}</span>
            @if($payout->order) · order <span class="font-mono">{{ $payout->order->uid }}</span> @endif
            · {{ $payout->method === 'upi' ? 'UPI ' . $payout->destination : ucfirst($payout->method) }}
          </div>
          @if($payout->reference)
            <div class="text-[11px] text-[#7A7A78]">ref <span class="font-mono">{{ $payout->reference }}</span></div>
          @endif
          @if($payout->notes)
            <div class="text-[11px] text-red-600">{{ $payout->notes }}</div>
          @endif
        </div>

        <div class="text-right">
          <div class="text-[15px] font-black">₹{{ number_format($payout->amount) }}</div>
          <div class="text-[11px] text-[#7A7A78]">of ₹{{ number_format($payout->gross) }} · fee ₹{{ number_format($payout->fee) }}</div>
        </div>

        <div class="w-[124px] text-center">
          @php $tone = ['paid' => 'bg-green-50 border-green-200 text-green-700', 'failed' => 'bg-red-50 border-red-200 text-red-700', 'processing' => 'bg-blue-50 border-blue-200 text-blue-700', 'on_hold' => 'bg-amber-50 border-amber-200 text-amber-700'][$payout->status] ?? 'bg-[#F8F8F7] border-[#E8E8E6] text-[#7A7A78]'; @endphp
          <span class="text-[11px] font-bold px-2.5 py-1 rounded-full border {{ $tone }}">{{ $payout->statusLabel() }}</span>
          <div class="mt-1 text-[10.5px] text-[#7A7A78]">
            @if($payout->status === 'paid') {{ $payout->processed_at?->format('d M') }}
            @elseif($payout->available_at && $payout->available_at->isFuture()) holds till {{ $payout->available_at->format('d M, H:i') }}
            @else ready @endif
          </div>
        </div>

        <div class="flex flex-wrap gap-2">
          @if(in_array($payout->status, ['pending', 'on_hold', 'failed'], true))
            <button x-on:click="pay = !pay" class="h-9 px-4 rounded-full bg-[#0F0F0F] text-white font-bold text-[12.5px]">
              {{ $autoPayouts ? 'Pay via RazorpayX' : 'Record payment' }}
            </button>
            <form method="POST" action="{{ route('admin.payouts.hold', $payout->id) }}">@csrf
              <button class="h-9 px-4 rounded-full border border-[#E8E8E6] bg-white font-bold text-[12.5px]">{{ $payout->status === 'on_hold' ? 'Unhold' : 'Hold' }}</button>
            </form>
          @endif
          @if($payout->status === 'failed')
            <form method="POST" action="{{ route('admin.payouts.retry', $payout->id) }}">@csrf
              <button class="h-9 px-4 rounded-full border border-[#E8E8E6] bg-white font-bold text-[12.5px]">Retry</button>
            </form>
          @endif
        </div>

        <form x-show="pay" x-cloak method="POST" action="{{ route('admin.payouts.paid', $payout->id) }}"
              class="w-full flex flex-wrap items-center gap-2 pt-1">
          @csrf
          <input name="reference" placeholder="{{ $autoPayouts ? 'Leave blank to send via RazorpayX, or paste a UTR' : 'UTR / transaction reference' }}"
                 class="h-9 px-3 rounded-lg border border-[#E8E8E6] bg-[#F8F8F7] text-[12.5px] flex-1 min-w-[220px] font-mono">
          <input name="notes" placeholder="Note (optional)" class="h-9 px-3 rounded-lg border border-[#E8E8E6] bg-[#F8F8F7] text-[12.5px] flex-1 min-w-[160px]">
          <button class="h-9 px-4 rounded-lg bg-[#0F0F0F] text-white font-bold text-[12.5px]">Confirm</button>
          <button type="button" x-on:click="pay = false" class="text-[12px] text-[#7A7A78]">Cancel</button>
        </form>
      </div>
    @empty
      <div class="px-5 py-14 text-center">
        <div class="font-black">Nothing in this queue</div>
        <div class="text-[13px] text-[#7A7A78] font-medium">Payouts appear here the moment a buyer approves a delivery.</div>
      </div>
    @endforelse
  </div>
</div>

<div class="mt-5">{{ $payouts->links() }}</div>
@endsection
