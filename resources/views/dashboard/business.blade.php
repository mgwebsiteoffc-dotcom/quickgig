@extends('layouts.site')

@section('content')
<section class="py-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8">

    <div class="flex flex-wrap items-center justify-between gap-5">
      <div class="flex items-center gap-4">
        <img src="{{ $company->logoUrl() }}" class="w-12 h-12 rounded-2xl object-cover border border-white/12" alt="">
        <div>
          <h1 class="font-display text-[26px] sm:text-[30px] font-semibold leading-tight">{{ $company->name }}</h1>
          <div class="text-[13px] text-mut mt-0.5">{{ $company->person_name }} · {{ $company->plan ?: 'Starter' }} workspace</div>
        </div>
      </div>
      <div class="flex gap-2.5">
        <a href="{{ route('business.profile') }}" class="h-11 px-5 rounded-xl glass inline-flex items-center text-[13.5px] font-medium hover:border-white/25 transition">Workspace settings</a>
        <a href="{{ route('marketplace') }}" class="h-11 px-5 rounded-xl btn-grad inline-flex items-center text-[13.5px] font-semibold">Order a gig</a>
      </div>
    </div>

    {{-- stats --}}
    <div class="mt-9 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach([
        ['Active gigs', $stats['active'], 'in production or review'],
        ['In escrow', '₹'.number_format($stats['escrow']), 'released on approval'],
        ['Delivered', $stats['delivered'], 'completed gigs'],
        ['Total spend', '₹'.number_format($stats['spend']), 'lifetime'],
      ] as [$label, $value, $hint])
        <div class="glass rounded-3xl p-5">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">{{ $label }}</div>
          <div class="mt-2.5 font-display text-[28px] font-semibold tracking-tight">{{ $value }}</div>
          <div class="text-[12px] text-mut mt-0.5">{{ $hint }}</div>
        </div>
      @endforeach
    </div>

    <div class="mt-6 grid lg:grid-cols-[1fr_330px] gap-6 items-start">

      {{-- orders --}}
      <div class="glass rounded-3xl p-6 sm:p-7">
        <div class="flex items-center justify-between">
          <h2 class="font-display text-[19px] font-semibold">Your gigs</h2>
          <a href="{{ route('marketplace') }}" class="text-[12.5px] text-mut hover:text-white">Browse marketplace →</a>
        </div>

        @forelse($orders as $o)
          <a href="{{ route('orders.show', $o->uid) }}" class="mt-4 flex flex-wrap items-center gap-4 rounded-2xl border border-white/8 bg-white/3 p-4 hover:border-violet/40 transition group">
            <img src="{{ $o->creator?->avatarUrl() ?? 'https://i.pravatar.cc/80?img=5' }}" class="w-10 h-10 rounded-xl object-cover border border-white/12" alt="">
            <div class="min-w-0 flex-1">
              <div class="text-[14px] font-medium truncate group-hover:text-violet-soft transition">{{ $o->service->title ?? 'Custom gig' }}</div>
              <div class="text-[12px] text-mut mt-0.5 font-mono">{{ $o->uid }} · {{ $o->creator->name ?? 'Matching…' }} · {{ $o->turnaround }}</div>
            </div>
            <div class="w-full sm:w-[120px]">
              <div class="h-1.5 rounded-full bg-white/8 overflow-hidden"><div class="h-full btn-grad" style="width: {{ max(5, (int) $o->progress) }}%"></div></div>
              <div class="mt-1.5 text-[11px] text-mut">{{ $o->progress }}%</div>
            </div>
            <div class="text-right shrink-0">
              <div class="text-[14px] font-semibold font-mono">₹{{ number_format($o->total) }}</div>
              <div class="text-[11px] {{ $o->escrow_status === 'released' ? 'text-lime' : 'text-violet-soft' }}">{{ $o->escrow_status === 'released' ? 'released' : 'in escrow' }}</div>
            </div>
          </a>
        @empty
          <div class="mt-5 rounded-2xl border border-dashed border-white/12 p-10 text-center">
            <div class="font-display text-[17px] font-semibold">No gigs yet</div>
            <p class="mt-1.5 text-[13.5px] text-mut">Order your first gig — matched to a verified creator in minutes.</p>
            <a href="{{ route('marketplace') }}" class="mt-5 inline-flex h-11 px-5 rounded-xl btn-grad items-center text-[13.5px] font-semibold">Browse the marketplace</a>
          </div>
        @endforelse
      </div>

      {{-- sidebar --}}
      <aside class="space-y-5">
        <div class="glass rounded-3xl p-5">
          <div class="flex items-center gap-2 text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">
            <span class="w-1.5 h-1.5 rounded-full bg-lime pulse-dot text-lime"></span> Creators online
          </div>
          <div class="mt-4 space-y-3.5">
            @forelse($availableNow as $c)
              <a href="{{ route('creator.public', $c->id) }}" class="flex items-center gap-3 group">
                <img src="{{ $c->avatarUrl() }}" class="w-9 h-9 rounded-xl object-cover border border-white/12" alt="">
                <div class="min-w-0 flex-1">
                  <div class="text-[13px] font-medium truncate group-hover:text-violet-soft transition">{{ $c->name }}</div>
                  <div class="text-[11.5px] text-mut truncate">{{ $c->headline ?: $c->handle }}</div>
                </div>
              </a>
            @empty
              <div class="text-[13px] text-mut">Nobody online right now.</div>
            @endforelse
          </div>
        </div>

        <div class="glass rounded-3xl p-5">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">Recommended for you</div>
          <div class="mt-4 space-y-3">
            @foreach($recommended as $r)
              <a href="{{ route('gigs.show', $r->id) }}" class="flex gap-3 group">
                <img src="{{ $r->coverUrl() }}" class="w-14 h-12 rounded-xl object-cover border border-white/10" alt="">
                <div class="min-w-0">
                  <div class="text-[13px] font-medium leading-snug line-clamp-2 group-hover:text-violet-soft transition">{{ $r->title }}</div>
                  <div class="text-[11.5px] text-mut mt-0.5">{{ $r->displayPrice() }} · {{ $r->deliveryLabel() }}</div>
                </div>
              </a>
            @endforeach
          </div>
        </div>
      </aside>
    </div>
  </div>
</section>
@endsection
