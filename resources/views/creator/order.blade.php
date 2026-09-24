@extends('layouts.site')

@section('content')
<section class="py-12">
  <div class="max-w-[820px] mx-auto px-5 lg:px-8">

    <a href="{{ route('creator.dashboard') }}" class="text-[12.5px] text-mut hover:text-white">← Back to studio</a>

    <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
      <div>
        <h1 class="font-display text-[28px] font-semibold">{{ $o->service->title ?? 'Custom gig' }}</h1>
        <div class="mt-1.5 text-[13px] text-mut font-mono">{{ $o->uid }} · {{ $o->company->name ?? 'Client' }}</div>
      </div>
      <span class="rounded-full px-3.5 py-1.5 text-[12px] font-semibold
        {{ $o->status === 'delivered' ? 'bg-lime/15 text-lime' : ($o->status === 'review' ? 'bg-cyan/15 text-cyan' : 'bg-violet/15 text-violet-soft') }}">
        {{ ucfirst($o->status) }}
      </span>
    </div>

    <div class="mt-7 glass rounded-3xl p-6 sm:p-7">
      <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">Client brief</div>
      <p class="mt-3.5 text-[14.5px] leading-7 text-white/80 whitespace-pre-line">{{ $o->brief }}</p>

      <div class="mt-6 grid sm:grid-cols-3 gap-4">
        @foreach([
          ['Your payout', '₹'.number_format($o->total - $o->fee)],
          ['Speed lane', $o->turnaround],
          ['Due', $o->due_at?->format('d M, H:i') ?? '—'],
        ] as [$k, $v])
          <div class="rounded-2xl border border-white/8 bg-white/3 px-4 py-3.5">
            <div class="text-[11px] text-mut">{{ $k }}</div>
            <div class="text-[15px] font-semibold mt-0.5">{{ $v }}</div>
          </div>
        @endforeach
      </div>
    </div>

    @if($o->status !== 'delivered')
      <form method="POST" action="{{ route('creator.deliver', $o->uid) }}" class="mt-5 glass rounded-3xl p-6 sm:p-7">
        @csrf
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">Deliver this gig</div>
        <div class="mt-4 grid sm:grid-cols-[1fr_auto] gap-3">
          <input name="delivery_url" type="url" required class="field" placeholder="https://drive.google.com/… delivery link">
          <button class="h-12 px-6 rounded-xl btn-grad font-semibold text-[14px]">Send for review</button>
        </div>
        <textarea name="note" rows="2" maxlength="500" class="field mt-3" placeholder="Optional note for the client…"></textarea>
      </form>
    @endif
  </div>
</section>
@endsection
