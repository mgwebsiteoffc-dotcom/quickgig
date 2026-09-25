@extends('layouts.site')

@section('content')
<section class="py-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8">

    <div class="flex flex-wrap items-center justify-between gap-5">
      <div class="flex items-center gap-4">
        <div class="relative">
          <img src="{{ $creator->avatarUrl() }}" class="w-12 h-12 rounded-2xl object-cover border border-line" alt="">
          <span class="absolute -bottom-1 -right-1 w-4 h-4 rounded-full border-2 border-white {{ $creator->is_available ? 'bg-mint' : 'bg-amber-400' }}"></span>
        </div>
        <div>
          <h1 class="font-display text-[26px] sm:text-[30px] font-semibold leading-tight flex items-center gap-2">
            {{ $creator->name }}
            @if($creator->is_verified)
              <span class="text-[10.5px] font-semibold rounded-full bg-mint-wash text-mint-deep px-2 py-0.5">Verified</span>
            @else
              <span class="text-[10.5px] font-semibold rounded-full bg-amber-400/15 text-amber-300 px-2 py-0.5">Pending review</span>
            @endif
          </h1>
          <div class="text-[13px] text-mut mt-0.5">{{ $creator->handle }} · {{ $creator->profileLabel() }}</div>
        </div>
      </div>

      <div class="flex gap-2.5">
        <form method="POST" action="{{ route('creator.availability') }}">
          @csrf
          <button class="h-11 px-5 rounded-xl glass inline-flex items-center gap-2.5 text-[13.5px] font-medium hover:border-line transition">
            <span class="w-2 h-2 rounded-full {{ $creator->is_available ? 'bg-mint' : 'bg-amber-400' }}"></span>
            {{ $creator->is_available ? 'Available for gigs' : 'Marked busy' }}
          </button>
        </form>
        <a href="{{ route('creator.profile') }}" class="h-11 px-5 rounded-xl btn-grad inline-flex items-center text-[13.5px] font-semibold">Edit profile</a>
      </div>
    </div>

    {{-- stats --}}
    <div class="mt-9 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach([
        ['Active gigs', $stats['active'], 'assigned to you'],
        ['Pending escrow', '₹'.number_format($stats['pending']), 'releases on approval'],
        ['Earned', '₹'.number_format($stats['earned']), 'after 10% fee'],
        ['Rating', $stats['rating'].' ★', ($creator->reviews_count ?: 0).' reviews'],
      ] as [$label, $value, $hint])
        <div class="glass rounded-3xl p-5">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">{{ $label }}</div>
          <div class="mt-2.5 font-display text-[28px] font-semibold tracking-tight">{{ $value }}</div>
          <div class="text-[12px] text-mut mt-0.5">{{ $hint }}</div>
        </div>
      @endforeach
    </div>

    <div class="mt-6 grid lg:grid-cols-[1fr_330px] gap-6 items-start">

      <div class="glass rounded-3xl p-6 sm:p-7">
        <h2 class="font-display text-[19px] font-semibold">Assigned gigs</h2>

        @forelse($orders as $o)
          <div class="mt-4 rounded-2xl border border-line bg-tint p-4">
            <div class="flex flex-wrap items-center gap-4">
              <div class="min-w-0 flex-1">
                <div class="text-[14px] font-medium truncate">{{ $o->service->title ?? 'Custom gig' }}</div>
                <div class="text-[12px] text-mut mt-0.5 font-mono">{{ $o->uid }} · {{ $o->company->name ?? 'Client' }} · {{ $o->turnaround }}</div>
              </div>
              <span class="text-[11.5px] font-semibold rounded-full px-2.5 py-1 shrink-0
                {{ $o->status === 'delivered' ? 'bg-mint-wash text-mint-deep' : ($o->status === 'review' ? 'bg-mint-wash text-mint-deep' : 'bg-mint-wash text-mint-deep') }}">
                {{ ucfirst($o->status) }}
              </span>
              <div class="text-right shrink-0">
                <div class="text-[14px] font-semibold font-mono">₹{{ number_format($o->total - $o->fee) }}</div>
                <div class="text-[11px] text-mut">your payout</div>
              </div>
            </div>

            <p class="mt-3 text-[13px] leading-6 text-mut line-clamp-2">{{ $o->brief }}</p>

            @if($o->status !== 'delivered')
              <form method="POST" action="{{ route('creator.deliver', $o->uid) }}" class="mt-4 flex flex-col sm:flex-row gap-2.5">
                @csrf
                <input name="delivery_url" type="url" required class="field flex-1" placeholder="https://drive.google.com/… delivery link">
                <button class="h-12 px-5 rounded-xl btn-grad font-semibold text-[13.5px] shrink-0">Deliver for review</button>
              </form>
            @endif
          </div>
        @empty
          <div class="mt-5 rounded-2xl border border-dashed border-line p-10 text-center">
            <div class="font-display text-[17px] font-semibold">No gigs assigned yet</div>
            <p class="mt-1.5 text-[13.5px] text-mut">Complete your profile and stay available — the matching engine prioritises verified, online creators.</p>
            <a href="{{ route('creator.profile') }}" class="mt-5 inline-flex h-11 px-5 rounded-xl btn-grad items-center text-[13.5px] font-semibold">Complete profile</a>
          </div>
        @endforelse
      </div>

      <aside class="space-y-5">
        <div class="glass rounded-3xl p-5">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Profile strength</div>
          @php
            $checks = [
              'Avatar uploaded'  => (bool) $creator->avatar,
              'Bio written'      => (bool) $creator->bio,
              'Skills added'     => ! empty($creator->skills),
              'UPI for payouts'  => (bool) $creator->upi_id,
              'Portfolio items'  => $portfolio->count() > 0,
            ];
            $done = count(array_filter($checks));
            $pct  = (int) round($done / max(1, count($checks)) * 100);
          @endphp
          <div class="mt-3 flex items-baseline gap-2">
            <span class="font-display text-[26px] font-semibold">{{ $pct }}%</span>
            <span class="text-[12px] text-mut">{{ $done }} of {{ count($checks) }} complete</span>
          </div>
          <div class="mt-3 h-1.5 rounded-full bg-tint overflow-hidden"><div class="h-full btn-grad" style="width: {{ max(4, $pct) }}%"></div></div>
          <ul class="mt-4 space-y-2">
            @foreach($checks as $label => $ok)
              <li class="flex items-center gap-2.5 text-[13px] {{ $ok ? 'text-mut' : 'text-body' }}">
                <span class="w-4 h-4 rounded-md grid place-items-center shrink-0 {{ $ok ? 'bg-mint-wash text-mint-deep' : 'bg-tint text-faint' }}">
                  <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3.4"><path d="M20 6 9 17l-5-5"/></svg>
                </span>
                {{ $label }}
              </li>
            @endforeach
          </ul>
        </div>

        <div class="glass rounded-3xl p-5">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Payouts</div>
          <div class="mt-3 text-[13px] text-mut leading-6">
            Escrow is released to <span class="font-mono text-body">{{ $creator->upi_id ?: 'your UPI ID' }}</span> as soon as the client approves. Payouts usually land within minutes.
          </div>
          <a href="{{ route('creator.profile') }}" class="mt-4 h-10 rounded-xl glass grid place-items-center text-[13px] font-medium hover:border-line transition">Update payout details</a>
        </div>
      </aside>
    </div>
  </div>
</section>
@endsection
