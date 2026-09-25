@extends('layouts.site')

@section('content')
<section class="py-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8">

    <div class="flex flex-wrap items-center justify-between gap-5">
      <div class="flex items-center gap-4">
        <img src="{{ $company->logoUrl() }}" class="w-12 h-12 rounded-2xl object-cover border border-line" alt="">
        <div>
          <h1 class="font-display text-[26px] sm:text-[30px] font-semibold leading-tight">{{ $company->name }}</h1>
          <div class="text-[13px] text-mut mt-0.5">{{ $company->person_name }} · {{ $company->plan ?: 'Starter' }} workspace</div>
        </div>
      </div>
      <div class="flex gap-2.5">
        <a href="{{ route('business.profile') }}" class="h-11 px-5 rounded-xl glass inline-flex items-center text-[13.5px] font-medium hover:border-line transition">Workspace settings</a>
        <a href="{{ route('marketplace') }}" class="h-11 px-5 rounded-xl btn-grad inline-flex items-center text-[13.5px] font-semibold">Order a gig</a>
      </div>
    </div>

    <div class="mt-6">@include('partials.panel-tabs')</div>

    {{-- stats --}}
    <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach([
        ['Active gigs', $stats['active'], 'in production or review'],
        ['In escrow', '₹'.number_format($stats['escrow']), 'released on approval'],
        ['Delivered', $stats['delivered'], 'completed gigs'],
        ['Total spend', '₹'.number_format($stats['spend']), 'lifetime'],
      ] as [$label, $value, $hint])
        <div class="glass rounded-3xl p-5">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">{{ $label }}</div>
          <div class="mt-2.5 font-display text-[28px] font-semibold tracking-tight">{{ $value }}</div>
          <div class="text-[12px] text-mut mt-0.5">{{ $hint }}</div>
        </div>
      @endforeach
    </div>

    {{-- ── task board snapshot ── --}}
    <div class="mt-6 glass rounded-3xl p-6 sm:p-7">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <h2 class="font-display text-[19px] font-semibold">Task board</h2>
          <p class="mt-1 text-[13px] text-mut">{{ $board['open'] }} open · {{ $board['overdue'] }} overdue · {{ $company->creditsLeft() }} credits left this month</p>
        </div>
        <a href="{{ route('board') }}" class="h-11 px-5 rounded-xl btn-grad inline-flex items-center gap-2 text-[13.5px] font-semibold">
          Open the board
          <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
      </div>

      <div class="mt-5 grid sm:grid-cols-5 gap-2.5">
        @foreach(\App\Models\Task::STATUSES as $key => $label)
          @php $tint = ['queued'=>'from-white/8','assigned'=>'/14','production'=>'/12','review'=>'from-amber/12','done'=>'from-lime/14'][$key]; @endphp
          <div class="rounded-2xl border border-line bg-gradient-to-br {{ $tint }} to-transparent p-4">
            <div class="text-[11px] text-mut">{{ $label }}</div>
            <div class="mt-1 font-display text-[22px] font-semibold">{{ $board['by_status'][$key] ?? 0 }}</div>
          </div>
        @endforeach
      </div>

      @if($board['next'])
        <a href="{{ route('board') }}" class="mt-4 flex items-center gap-3 rounded-2xl border border-line bg-tint p-4 hover:border-mint transition">
          <span class="text-[10px] font-semibold tracking-wider uppercase rounded-full px-2 py-0.5 bg-pink/10 text-mint-deep shrink-0">Next up</span>
          <span class="text-[13.5px] font-medium truncate">{{ $board['next']->title }}</span>
          <span class="ml-auto text-[12px] text-mut shrink-0">{{ $board['next']->due_on?->format('d M') }}</span>
        </a>
      @endif
    </div>

    {{-- ── natural-language task capture (inline, never reloads) ── --}}
    <div class="mt-6 glass rounded-3xl p-6 sm:p-7" x-data="taskCapture(@js(session('task.state')))">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
          <div class="flex items-center gap-2.5">
            <h2 class="font-display text-[19px] font-semibold">Describe a task</h2>
            <span class="text-[10.5px] font-semibold rounded-full px-2.5 py-1 transition-colors"
                  :class="meta.source === 'ai' ? 'bg-mint-wash text-mint-deep' : 'bg-tint text-mut'"
                  x-text="meta.source === 'ai' ? meta.model : 'Rule-based'"></span>
          </div>
          <p class="mt-1 text-[13px] text-mut">Plain English in, a structured task out — title, dates, description and client.</p>
        </div>
        <button x-show="task" x-cloak x-on:click="clear()" class="text-[12.5px] text-mut hover:text-ink transition">Clear ✕</button>
      </div>

      <form x-on:submit.prevent="parse()" class="mt-5 flex flex-col sm:flex-row gap-3">
        <input x-model="prompt" required maxlength="600" class="field flex-1"
               placeholder="create task to build a mobile app, delivery date is 29 aug 2026">
        <button type="submit" class="h-12 px-6 rounded-xl btn-grad font-semibold text-[14px] shrink-0 disabled:opacity-60" :disabled="busy">
          <span x-show="!busy">Parse task</span>
          <span x-show="busy" x-cloak>Parsing…</span>
        </button>
      </form>

      <div class="mt-3 flex flex-wrap gap-2">
        <template x-for="s in samples" :key="s">
          <button type="button" x-on:click="prompt = s; parse()"
                  class="rounded-full border border-line px-3 py-1.5 text-[11.5px] text-mut hover:text-ink hover:border-pink/50 transition" x-text="s"></button>
        </template>
      </div>

      <div x-show="error" x-cloak class="mt-4 text-[12.5px] text-rose-300" x-text="error"></div>

      {{-- skeleton while parsing --}}
      <div x-show="busy" x-cloak class="mt-6 grid lg:grid-cols-[1fr_300px] gap-5">
        <div class="rounded-2xl border border-line bg-tint p-5 space-y-3">
          <div class="h-4 w-1/3 rounded skeleton"></div>
          <div class="h-4 w-1/2 rounded skeleton"></div>
          <div class="h-12 w-full rounded skeleton"></div>
        </div>
        <div class="h-12 rounded-xl skeleton"></div>
      </div>

      {{-- result, rendered in place --}}
      <div x-show="task && !busy" x-cloak x-transition.duration.400ms class="mt-6 grid lg:grid-cols-[1fr_300px] gap-5 items-start">
        <div class="rounded-2xl border border-line bg-tint p-5">
          <div class="grid sm:grid-cols-2 gap-4">
            <template x-for="f in ['title','client','start_date','end_date']" :key="f">
              <div>
                <div class="text-[10.5px] font-mono text-faint" x-text="f"></div>
                <div class="text-[14px] font-medium mt-0.5" x-text="task?.[f] || '—'"></div>
              </div>
            </template>
          </div>
          <div class="mt-4 pt-4 border-t border-line">
            <div class="text-[10.5px] font-mono text-faint">description</div>
            <p class="mt-1 text-[13.5px] leading-6 text-mut" x-text="task?.description"></p>
          </div>

          <template x-if="task?.warnings?.length">
            <div class="mt-4 space-y-1.5">
              <template x-for="w in task.warnings" :key="w">
                <div class="flex gap-2 text-[12.5px] text-mint-deep"><span class="shrink-0">!</span><span x-text="w"></span></div>
              </template>
            </div>
          </template>

          <form x-on:submit.prevent="refine()" class="mt-5 flex flex-col sm:flex-row gap-2.5">
            <input x-model="instruction" required maxlength="300" class="field flex-1" placeholder="Actually the deadline is 29 aug 2027 and the client is Nova Foods">
            <button class="h-12 px-5 rounded-xl glass btn-ghost font-medium text-[13.5px] shrink-0 hover:border-line" :disabled="busy">Refine</button>
          </form>
        </div>

        <div class="space-y-3">
          <template x-if="task?.gig_url">
            <a :href="task.gig_url" class="h-12 rounded-xl btn-grad grid place-items-center font-semibold text-[14px]">Order the matching gig →</a>
          </template>
          <a :href="task?.brief_url" class="h-12 rounded-xl glass btn-ghost grid place-items-center font-medium text-[13.5px] hover:border-line">Write a full brief</a>
          <div class="rounded-2xl border border-line bg-tint p-4">
            <div class="text-[10.5px] font-semibold tracking-[.12em] uppercase text-faint">Same result as JSON</div>
            <pre class="mt-2 text-[11px] leading-5 text-mut overflow-x-auto">POST /tasks/parse
Accept: application/json

{"prompt": "…"}</pre>
          </div>
        </div>
      </div>
    </div>

    <div class="mt-6 grid lg:grid-cols-[1fr_330px] gap-6 items-start">

      {{-- orders --}}
      <div class="glass rounded-3xl p-6 sm:p-7">
        <div class="flex items-center justify-between">
          <h2 class="font-display text-[19px] font-semibold">Your gigs</h2>
          <a href="{{ route('marketplace') }}" class="text-[12.5px] text-mut hover:text-ink">Browse marketplace →</a>
        </div>

        @forelse($orders as $o)
          <a href="{{ route('orders.show', $o->uid) }}" class="mt-4 flex flex-wrap items-center gap-4 rounded-2xl border border-line bg-tint p-4 hover:border-mint transition group">
            <img src="{{ $o->creator?->avatarUrl() ?? 'https://i.pravatar.cc/80?img=5' }}" class="w-10 h-10 rounded-xl object-cover border border-line" alt="">
            <div class="min-w-0 flex-1">
              <div class="text-[14px] font-medium truncate group-hover:text-mint-deep transition">{{ $o->service->title ?? 'Custom gig' }}</div>
              <div class="text-[12px] text-mut mt-0.5 font-mono">{{ $o->uid }} · {{ $o->creator->name ?? 'Matching…' }} · {{ $o->turnaround }}</div>
            </div>
            <div class="w-full sm:w-[120px]">
              <div class="h-1.5 rounded-full bg-tint overflow-hidden"><div class="h-full btn-grad" style="width: {{ max(5, (int) $o->progress) }}%"></div></div>
              <div class="mt-1.5 text-[11px] text-mut">{{ $o->progress }}%</div>
            </div>
            <div class="text-right shrink-0">
              <div class="text-[14px] font-semibold font-mono">₹{{ number_format($o->total) }}</div>
              <div class="text-[11px] {{ $o->escrow_status === 'released' ? 'text-mint-deep' : 'text-mint-deep' }}">{{ $o->escrow_status === 'released' ? 'released' : 'in escrow' }}</div>
            </div>
          </a>
        @empty
          <div class="mt-5 rounded-2xl border border-dashed border-line p-10 text-center">
            <div class="font-display text-[17px] font-semibold">No gigs yet</div>
            <p class="mt-1.5 text-[13.5px] text-mut">Order your first gig — matched to a verified freelancer in minutes.</p>
            <a href="{{ route('marketplace') }}" class="mt-5 inline-flex h-11 px-5 rounded-xl btn-grad items-center text-[13.5px] font-semibold">Browse the marketplace</a>
          </div>
        @endforelse
      </div>

      {{-- sidebar --}}
      <aside class="space-y-5">
        <div class="glass rounded-3xl p-5">
          <div class="flex items-center gap-2 text-[11px] font-semibold tracking-[.14em] uppercase text-faint">
            <span class="w-1.5 h-1.5 rounded-full bg-mint pulse-dot text-mint-deep"></span> Freelancers online
          </div>
          <div class="mt-4 space-y-3.5">
            @forelse($availableNow as $c)
              <a href="{{ route('creator.public', $c->id) }}" class="flex items-center gap-3 group">
                <img src="{{ $c->avatarUrl() }}" class="w-9 h-9 rounded-xl object-cover border border-line" alt="">
                <div class="min-w-0 flex-1">
                  <div class="text-[13px] font-medium truncate group-hover:text-mint-deep transition">{{ $c->name }}</div>
                  <div class="text-[11.5px] text-mut truncate">{{ $c->headline ?: $c->profileLabel() }}</div>
                </div>
              </a>
            @empty
              <div class="text-[13px] text-mut">Nobody online right now.</div>
            @endforelse
          </div>
        </div>

        <div class="glass rounded-3xl p-5">
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Recommended for you</div>
          <div class="mt-4 space-y-3">
            @foreach($recommended as $r)
              <a href="{{ route('gigs.show', $r->id) }}" class="flex gap-3 group">
                <img src="{{ $r->coverUrl() }}" class="w-14 h-12 rounded-xl object-cover border border-line" alt="">
                <div class="min-w-0">
                  <div class="text-[13px] font-medium leading-snug line-clamp-2 group-hover:text-mint-deep transition">{{ $r->title }}</div>
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

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('taskCapture', (initial) => ({
    prompt: '', instruction: '', busy: false, error: '',
    task: initial ? {
      title: initial.title, client: initial.client, start_date: initial.start_date,
      end_date: initial.end_date, description: initial.description,
      warnings: initial.warnings || [], gig_url: null, brief_url: null,
    } : null,
    meta: { source: initial?.source || 'rules', model: initial?.model || null },
    samples: [
      'create task to build a mobile app, delivery date is 29 aug 2026',
      '5 instagram reels for client Nova Foods in 2 weeks',
      'thumbnail pack for the launch video, due tomorrow',
    ],

    async parse() {
      if (!this.prompt.trim()) return;
      this.busy = true; this.error = '';
      try {
        const res = await window.qg.post('{{ route('tasks.parse') }}', { prompt: this.prompt });
        this.task = res.task; this.meta = res.meta;
        window.qg.toast(res.meta.source === 'ai' ? 'Parsed by ' + res.meta.model : 'Task parsed.');
      } catch (e) { this.error = 'Could not parse that — try rephrasing.'; }
      this.busy = false;
    },

    async refine() {
      if (!this.instruction.trim()) return;
      this.busy = true; this.error = '';
      try {
        const res = await window.qg.post('{{ route('tasks.refine') }}', { instruction: this.instruction });
        this.task = res.task; this.meta = res.meta;
        this.instruction = '';
        window.qg.toast(res.meta.error || 'Task updated.');
      } catch (e) { this.error = 'Refine failed — try again.'; }
      this.busy = false;
    },

    async clear() {
      this.task = null; this.prompt = '';
      try { await window.qg.post('{{ route('tasks.clear') }}', {}); } catch (e) {}
    },
  }));
});
</script>
@endpush
