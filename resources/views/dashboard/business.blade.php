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

    {{-- ── natural-language task capture ── --}}
    @php $task = session('task.state'); @endphp
    <div class="mt-6 glass rounded-3xl p-6 sm:p-7">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <div class="flex items-center gap-2.5">
            <h2 class="font-display text-[19px] font-semibold">Describe a task</h2>
            @if($task && ($task['source'] ?? '') === 'ai')
              <span class="text-[10.5px] font-semibold rounded-full bg-violet/15 text-violet-soft px-2.5 py-1">{{ $task['model'] }}</span>
            @else
              <span class="text-[10.5px] font-semibold rounded-full bg-white/8 text-mut px-2.5 py-1">Rule-based</span>
            @endif
          </div>
          <p class="mt-1 text-[13px] text-mut">Plain English in, a structured task out — title, dates, description and client.</p>
        </div>
        @if($task)
          <form method="POST" action="{{ route('tasks.clear') }}">@csrf
            <button class="text-[12.5px] text-mut hover:text-white transition">Clear ✕</button>
          </form>
        @endif
      </div>

      <form method="POST" action="{{ route('tasks.parse') }}" class="mt-5 flex flex-col sm:flex-row gap-3">
        @csrf
        <input name="prompt" required maxlength="600" class="field flex-1"
               value="{{ old('prompt') }}"
               placeholder="create task to build a mobile app, delivery date is 29 aug 2026">
        <button class="h-12 px-6 rounded-xl btn-grad font-semibold text-[14px] shrink-0">Parse task</button>
      </form>
      @error('prompt')<div class="mt-2 text-[12.5px] text-rose-300">{{ $message }}</div>@enderror

      @if($task)
        <div class="mt-6 grid lg:grid-cols-[1fr_300px] gap-5 items-start">
          <div class="rounded-2xl border border-white/10 bg-black/25 p-5">
            <div class="grid sm:grid-cols-2 gap-4">
              @foreach([
                ['title', $task['title']],
                ['client', $task['client'] ?: '—'],
                ['start_date', $task['start_date']],
                ['end_date', $task['end_date']],
              ] as [$k, $v])
                <div>
                  <div class="text-[10.5px] font-mono text-white/40">{{ $k }}</div>
                  <div class="text-[14px] font-medium mt-0.5">{{ $v }}</div>
                </div>
              @endforeach
            </div>
            <div class="mt-4 pt-4 border-t border-white/8">
              <div class="text-[10.5px] font-mono text-white/40">description</div>
              <p class="mt-1 text-[13.5px] leading-6 text-mut">{{ $task['description'] }}</p>
            </div>

            @if(!empty($task['warnings']))
              <div class="mt-4 space-y-1.5">
                @foreach($task['warnings'] as $w)
                  <div class="flex gap-2 text-[12.5px] text-amber-200">
                    <span class="shrink-0">!</span><span>{{ $w }}</span>
                  </div>
                @endforeach
              </div>
            @endif

            @if(!empty($task['refine_error']))
              <div class="mt-4 text-[12.5px] text-amber-200">{{ $task['refine_error'] }}</div>
            @endif

            <form method="POST" action="{{ route('tasks.refine') }}" class="mt-5 flex flex-col sm:flex-row gap-2.5">
              @csrf
              <input name="instruction" required maxlength="300" class="field flex-1" placeholder="Actually the deadline is 29 aug 2027 and the client is Nova Foods">
              <button class="h-12 px-5 rounded-xl glass font-medium text-[13.5px] shrink-0 hover:border-white/30 transition">Refine</button>
            </form>
          </div>

          <div class="space-y-3">
            @if(!empty($task['gig_id']))
              <a href="{{ route('gigs.show', $task['gig_id']) }}" class="h-12 rounded-xl btn-grad grid place-items-center font-semibold text-[14px]">Order the matching gig →</a>
            @endif
            <a href="{{ route('brief-builder') }}?idea={{ urlencode($task['title']) }}" class="h-12 rounded-xl glass grid place-items-center font-medium text-[13.5px] hover:border-white/30 transition">Write a full brief</a>
            <div class="rounded-2xl border border-white/8 bg-white/3 p-4">
              <div class="text-[10.5px] font-semibold tracking-[.12em] uppercase text-white/40">Same result as JSON</div>
              <pre class="mt-2 text-[11px] leading-5 text-mut overflow-x-auto">POST /tasks/parse
Accept: application/json

{"prompt": "…"}</pre>
            </div>
          </div>
        </div>
      @endif
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
