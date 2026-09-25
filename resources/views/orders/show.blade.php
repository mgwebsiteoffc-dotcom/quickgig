@extends('layouts.site')

@section('content')
@php
  $steps = [
    ['key' => 'placed',   'label' => 'Order placed',        'at' => 5],
    ['key' => 'matched',  'label' => 'Freelancer assigned',    'at' => 15],
    ['key' => 'working',  'label' => 'In production',       'at' => 45],
    ['key' => 'review',   'label' => 'Delivered for review','at' => 100],
    ['key' => 'released', 'label' => 'Approved · escrow released', 'at' => 101],
  ];
  $progress = (int) $order->progress;
  $released = $order->escrow_status === 'released';
  $current  = $released ? 4 : ($order->status === 'review' ? 3 : ($progress >= 40 ? 2 : 1));
  $payout   = $order->total - $order->fee;
  $thread   = session('thread.' . $order->uid, []);
@endphp

<section class="py-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8">

    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <a href="{{ route('business.home') }}" class="text-[12.5px] text-mut hover:text-white">← Back to dashboard</a>
        <h1 class="mt-2 font-display text-[28px] sm:text-[32px] font-semibold">{{ $order->service->title ?? 'Gig' }}</h1>
        <div class="mt-1.5 flex flex-wrap items-center gap-3 text-[13px] text-mut">
          <span class="font-mono">{{ $order->uid }}</span>
          <span class="opacity-40">·</span>
          <span>Ordered {{ $order->created_at->diffForHumans() }}</span>
          @if($order->due_at)<span class="opacity-40">·</span><span>Due {{ $order->due_at->format('d M, H:i') }}</span>@endif
        </div>
      </div>

      <span class="rounded-full px-3.5 py-1.5 text-[12px] font-semibold
        {{ $released ? 'bg-mint-wash text-mint-deep' : ($order->status === 'review' ? 'bg-mint-wash text-mint-deep' : 'bg-mint-wash text-mint-deep') }}">
        {{ $released ? 'Completed' : ($order->status === 'review' ? 'Waiting for your approval' : 'In production') }}
      </span>
    </div>

    <div class="mt-9 grid lg:grid-cols-[1fr_360px] gap-6 items-start">

      {{-- pipeline --}}
      <div class="space-y-5">
        <div class="glass rounded-3xl p-6 sm:p-7"
             x-data="orderPipeline({ progress: {{ $progress }}, status: @js($order->status), released: {{ $released ? 'true' : 'false' }}, payout: {{ $payout }}, creator: @js($order->creator->name ?? 'A verified pro'), uid: @js($order->uid) })">
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-2 text-[11px] font-semibold tracking-[.14em] uppercase text-faint">
              <span class="w-1.5 h-1.5 rounded-full transition-colors" :class="released ? 'bg-mint' : 'bg-mint pulse-dot text-mint-deep'"></span>
              Live pipeline
            </div>
            <div class="text-[12px] font-mono text-mut"><span x-text="progress"></span>% complete</div>
          </div>

          <div class="mt-4 h-1.5 rounded-full bg-tint overflow-hidden">
            <div class="h-full btn-grad transition-all duration-700 ease-out" :style="`width:${Math.max(5, progress)}%`"></div>
          </div>

          <div class="mt-7 space-y-5">
            <template x-for="(s, i) in steps" :key="i">
              <div class="flex gap-4 transition-all duration-500" :class="i <= current ? 'opacity-100' : 'opacity-40'">
                <div class="flex flex-col items-center">
                  <div class="w-8 h-8 rounded-xl grid place-items-center shrink-0 transition-all duration-500"
                       :class="i < current ? 'bg-mint-wash text-mint-deep' : (i === current ? 'btn-grad text-white scale-110' : 'bg-tint text-faint')">
                    <template x-if="i < current"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg></template>
                    <template x-if="i >= current"><span class="text-[11px] font-mono" x-text="i + 1"></span></template>
                  </div>
                  <div x-show="i < steps.length - 1" class="w-px flex-1 my-1 transition-colors duration-500" :class="i < current ? 'bg-mint/35' : 'bg-tint'"></div>
                </div>
                <div class="pb-1">
                  <div class="text-[14.5px] font-medium" x-text="s.label"></div>
                  <div class="text-[12.5px] text-mut mt-0.5" x-text="s.detail(this)"></div>
                </div>
              </div>
            </template>
          </div>

          {{-- actions: everything happens here, no page change --}}
          <div class="mt-7 pt-6 border-t border-line flex flex-wrap items-center gap-3">
            <template x-if="!released">
              <div class="flex flex-wrap gap-3">
                <button x-show="status === 'review'" x-cloak x-on:click="approve()" :disabled="busy"
                        class="h-11 px-6 rounded-xl btn-grad font-semibold text-[14px] disabled:opacity-60">
                  <span x-show="!busy">Approve & release ₹<span x-text="payout.toLocaleString('en-IN')"></span></span>
                  <span x-show="busy" x-cloak>Releasing…</span>
                </button>
                <button x-on:click="advance()" :disabled="busy"
                        class="h-11 px-5 rounded-xl glass btn-ghost font-medium text-[13.5px] hover:border-line disabled:opacity-60">
                  <span x-show="!busy">▸ Advance demo pipeline</span>
                  <span x-show="busy" x-cloak>Working…</span>
                </button>
              </div>
            </template>

            <template x-if="released">
              <div class="flex items-center gap-2.5 text-[13.5px] text-mint-deep animate-popIn">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6"><path d="M20 6 9 17l-5-5"/></svg>
                Escrow released — ₹<span x-text="payout.toLocaleString('en-IN')"></span> paid to <span x-text="creator"></span>.
              </div>
            </template>
          </div>
        </div>

        {{-- brief --}}
        <div class="glass rounded-3xl p-6 sm:p-7">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">The brief</div>
          <p class="mt-3.5 text-[14.5px] leading-7 text-body whitespace-pre-line">{{ $order->brief }}</p>
        </div>

        {{-- chat --}}
        <div class="glass rounded-3xl p-6 sm:p-7">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Messages</div>

          <div class="mt-4 space-y-3 max-h-[320px] overflow-y-auto">
            <div class="flex gap-3">
              <img src="{{ $order->creator?->avatarUrl() ?? 'https://i.pravatar.cc/80?img=5' }}" class="w-8 h-8 rounded-full object-cover shrink-0" alt="">
              <div class="rounded-2xl rounded-tl-sm bg-tint px-4 py-2.5 text-[13.5px] max-w-[80%]">
                Hi! I have the brief — starting now. I'll share the first cut before the deadline.
              </div>
            </div>

            @foreach($thread as $m)
              @if($m['from'] === 'you')
                <div class="flex gap-3 justify-end">
                  <div class="rounded-2xl rounded-tr-sm bg-mint/25 border border-mint px-4 py-2.5 text-[13.5px] max-w-[80%]">{{ $m['text'] }}</div>
                </div>
              @else
                <div class="flex gap-3">
                  <img src="{{ $order->creator?->avatarUrl() ?? 'https://i.pravatar.cc/80?img=5' }}" class="w-8 h-8 rounded-full object-cover shrink-0" alt="">
                  <div class="rounded-2xl rounded-tl-sm bg-tint px-4 py-2.5 text-[13.5px] max-w-[80%]">{{ $m['text'] }}</div>
                </div>
              @endif
            @endforeach
          </div>

          <form method="POST" action="{{ route('orders.message', $order->uid) }}" class="mt-4 flex gap-2.5">
            @csrf
            <input name="message" required maxlength="500" class="field flex-1" placeholder="Send a note to the freelancer…">
            <button class="h-12 px-5 rounded-xl btn-grad font-semibold text-[13.5px] shrink-0">Send</button>
          </form>
        </div>
      </div>

      {{-- summary --}}
      <aside class="space-y-5 lg:sticky lg:top-24">
        <div class="glass-strong rounded-3xl p-6">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Payment</div>
          <div class="mt-4 space-y-2.5 text-[13.5px]">
            <div class="flex justify-between"><span class="text-mut">Gig total</span><span class="font-mono">₹{{ number_format($order->total) }}</span></div>
            <div class="flex justify-between"><span class="text-mut">Platform fee</span><span class="font-mono">₹{{ number_format($order->fee) }}</span></div>
            <div class="flex justify-between"><span class="text-mut">Freelancer payout</span><span class="font-mono text-mint-deep">₹{{ number_format($payout) }}</span></div>
            <div class="pt-3 mt-3 border-t border-line flex justify-between items-center">
              <span class="text-mut">Escrow</span>
              <span class="text-[12px] font-semibold rounded-full px-2.5 py-1 {{ $released ? 'bg-mint-wash text-mint-deep' : 'bg-mint-wash text-mint-deep' }}">
                {{ $released ? 'Released' : 'Held' }}
              </span>
            </div>
          </div>
        </div>

        @if($order->creator)
          <div class="glass rounded-3xl p-6">
            <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Your freelancer</div>
            <div class="mt-4 flex items-center gap-3">
              <img src="{{ $order->creator->avatarUrl() }}" class="w-12 h-12 rounded-2xl object-cover border border-line" alt="">
              <div class="min-w-0">
                <a href="{{ route('creator.public', $order->creator->id) }}" class="text-[14.5px] font-semibold hover:text-mint-deep transition">{{ $order->creator->name }}</a>
                <div class="text-[12px] text-mut truncate">{{ $order->creator->handle }} · {{ number_format((float) $order->creator->rating, 1) }} ★</div>
              </div>
            </div>
          </div>
        @endif

        <div class="glass rounded-3xl p-6">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Order details</div>
          <div class="mt-4 space-y-2.5 text-[13px]">
            <div class="flex justify-between"><span class="text-mut">Workspace</span><span class="truncate max-w-[55%] text-right">{{ $order->company->name ?? '—' }}</span></div>
            <div class="flex justify-between"><span class="text-mut">Speed lane</span><span>{{ $order->turnaround }}</span></div>
            <div class="flex justify-between"><span class="text-mut">Revisions left</span><span>2</span></div>
          </div>
        </div>
      </aside>
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('orderPipeline', (init) => ({
    ...init,
    busy: false,
    steps: [
      { label: 'Order placed',        detail: () => 'Brief received and queued for matching.' },
      { label: 'Freelancer assigned',    detail: (c) => c.creator + ' accepted this gig.' },
      { label: 'In production',       detail: () => 'Cutting, sound, captions and export.' },
      { label: 'Delivered for review',detail: (c) => (c.status === 'review' || c.released) ? 'Files are ready — review and approve.' : 'You will get a preview link here.' },
      { label: 'Approved · escrow released', detail: (c) => c.released ? '₹' + c.payout.toLocaleString('en-IN') + ' paid out to the freelancer.' : 'Escrow releases the moment you approve.' },
    ],
    get current() {
      if (this.released) return 4;
      if (this.status === 'review') return 3;
      return this.progress >= 40 ? 2 : 1;
    },
    async call(path) {
      this.busy = true;
      try {
        const res = await window.qg.post(path, {});
        this.progress = res.progress;
        this.status = res.status;
        this.released = res.escrow_status === 'released';
        window.qg.toast(res.message);
      } catch (e) {
        window.qg.toast('Something went wrong — refresh and try again.');
      }
      this.busy = false;
    },
    advance() { return this.call('/orders/' + this.uid + '/simulate'); },
    approve() { return this.call('/orders/' + this.uid + '/approve'); },
  }));
});
</script>
@endpush
