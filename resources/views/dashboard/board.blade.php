@extends('layouts.site')

@php
  $tone = [
    'queued'     => ['dot' => 'bg-white/35',  'ring' => 'border-line',  'text' => 'text-mut'],
    'assigned'   => ['dot' => 'bg-mint',    'ring' => 'border-mint', 'text' => 'text-mint-deep'],
    'production' => ['dot' => 'bg-pink',      'ring' => 'border-pink/35',   'text' => 'text-mint-deep'],
    'review'     => ['dot' => 'bg-amber',     'ring' => 'border-amber/35',  'text' => 'text-amber'],
    'done'       => ['dot' => 'bg-mint',      'ring' => 'border-mint/35',   'text' => 'text-mint-deep'],
  ];
  $priorityTone = [
    'urgent' => 'bg-pink/10 text-mint-deep',
    'high'   => 'bg-amber/10 text-amber',
    'normal' => 'bg-tint text-mut',
    'low'    => 'bg-mint-wash text-mint-deep',
  ];
@endphp

@section('content')
<section class="py-10">
  <div class="max-w-[1400px] mx-auto px-5 lg:px-8" x-data="board()">

    {{-- header --}}
    <div class="flex flex-wrap items-start justify-between gap-5">
      <div>
        <div class="flex items-center gap-3">
          <img src="{{ $company->logoUrl() }}" class="w-10 h-10 rounded-xl object-cover border border-line" alt="">
          <div>
            <h1 class="font-display text-[26px] font-semibold leading-tight">Task board</h1>
            <div class="text-[13px] text-mut mt-0.5">{{ $company->name }} · queue the work, we assign the specialist</div>
          </div>
        </div>
      </div>

      <div class="flex flex-wrap items-center gap-2.5">
        <div class="glass rounded-2xl px-4 py-2.5">
          <div class="text-[10.5px] font-semibold tracking-[.12em] uppercase text-faint">{{ $company->tier()['label'] }} plan</div>
          <div class="mt-0.5 flex items-center gap-2">
            <div class="w-24 h-1.5 rounded-full bg-tint overflow-hidden">
              <div class="h-full btn-grad" style="width: {{ $company->monthly_credits ? min(100, round($company->credits_used / max(1,$company->monthly_credits) * 100)) : 0 }}%"></div>
            </div>
            <span class="text-[12px] font-mono">{{ $company->credits_used }}/{{ $company->monthly_credits ?: '∞' }}</span>
          </div>
        </div>
        <button x-on:click="composing = !composing" class="h-11 px-5 rounded-xl btn-grad font-semibold text-[13.5px] inline-flex items-center gap-2">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M12 5v14M5 12h14"/></svg>
          New task
        </button>
      </div>
    </div>

    <div class="mt-6">@include('partials.panel-tabs')</div>

    {{-- stats --}}
    <div class="mt-6 grid grid-cols-2 lg:grid-cols-4 gap-3">
      @foreach([
        ['Open tasks', $counts['open'], 'in the queue or in progress', '/14'],
        ['Overdue', $counts['overdue'], 'past the requested date', '/10'],
        ['Delivered', $counts['done'], 'all time', 'from-lime/14'],
        ['Credits left', $company->creditsLeft(), 'renews ' . ($company->renews_on?->format('d M') ?? '—'), 'from-amber/12'],
      ] as [$label, $value, $hint, $grad])
        <div class="glass rounded-2xl p-4 {{ $grad }} to-transparent">
          <div class="text-[10.5px] font-semibold tracking-[.12em] uppercase text-faint">{{ $label }}</div>
          <div class="mt-1.5 font-display text-[26px] font-semibold">{{ $value }}</div>
          <div class="text-[11.5px] text-mut">{{ $hint }}</div>
        </div>
      @endforeach
    </div>

    {{-- composer (inline, no reload) --}}
    <div x-show="composing" x-cloak x-transition.duration.300ms class="mt-5 glass-strong rounded-3xl p-5 sm:p-6">
      <div class="flex items-center justify-between">
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">New task</div>
        <button x-on:click="composing = false" class="text-[12.5px] text-mut hover:text-ink">Close ✕</button>
      </div>

      <form x-on:submit.prevent="create()" class="mt-4 space-y-4">
        <div>
          <label class="label" for="prompt">Describe it in one line — we fill the rest</label>
          <input id="prompt" x-model="form.prompt" class="field"
                 placeholder="Diwali offer reel for Instagram, founder on camera, delivery by 20 oct">
        </div>

        <div class="grid sm:grid-cols-4 gap-3">
          <div class="sm:col-span-2">
            <label class="label" for="title">Title <span class="normal-case tracking-normal text-faint">(optional if described above)</span></label>
            <input id="title" x-model="form.title" class="field" placeholder="Diwali offer reel">
          </div>
          <div>
            <label class="label" for="category">Category</label>
            <select id="category" x-model="form.category" class="field">
              @foreach($categories as $cat)<option value="{{ $cat }}">{{ $cat }}</option>@endforeach
            </select>
          </div>
          <div>
            <label class="label" for="priority">Priority</label>
            <select id="priority" x-model="form.priority" class="field">
              @foreach($priorities as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
            </select>
          </div>
        </div>

        <div class="grid sm:grid-cols-4 gap-3">
          <div class="sm:col-span-2">
            <label class="label" for="brief">Brief <span class="normal-case tracking-normal text-faint">(optional)</span></label>
            <input id="brief" x-model="form.brief" class="field" placeholder="Hook in 2s, captions, 30 seconds, upbeat">
          </div>
          <div>
            <label class="label" for="due">Due date</label>
            <input id="due" type="date" x-model="form.due_on" class="field">
          </div>
          <div>
            <label class="label" for="link">Reference link</label>
            <input id="link" x-model="form.link" class="field" placeholder="https://drive.google.com/…">
          </div>
        </div>

        <div class="flex flex-wrap items-center gap-3">
          <button class="h-11 px-6 rounded-xl btn-grad font-semibold text-[13.5px]" :disabled="busy">
            <span x-show="!busy">Queue task</span><span x-show="busy" x-cloak>Queuing…</span>
          </button>
          <span class="text-[12px] text-mut">A specialist is matched the moment it lands on the board.</span>
        </div>
      </form>
    </div>

    {{-- board --}}
    <div class="mt-6 grid lg:grid-cols-5 gap-4 items-start">
      @foreach($columns as $key => $label)
        <div class="rounded-3xl border {{ $tone[$key]['ring'] }} bg-tint p-3" x-data>
          <div class="flex items-center justify-between px-2 py-1.5">
            <div class="flex items-center gap-2">
              <span class="w-2 h-2 rounded-full {{ $tone[$key]['dot'] }}"></span>
              <span class="text-[12.5px] font-semibold {{ $tone[$key]['text'] }}">{{ $label }}</span>
            </div>
            <span class="text-[11.5px] font-mono text-faint" x-text="count('{{ $key }}')">{{ ($tasks[$key] ?? collect())->count() }}</span>
          </div>

          <div class="mt-2 space-y-2.5 min-h-[80px]" data-column="{{ $key }}">
            @forelse($tasks[$key] ?? [] as $task)
              <div class="glass rounded-2xl p-3.5 card-hover group"
                   x-show="!isHidden({{ $task->id }})" x-data="{ open:false }" id="task-{{ $task->id }}">
                <div class="flex items-start justify-between gap-2">
                  <span class="text-[10px] font-semibold tracking-wider uppercase rounded-full px-2 py-0.5 {{ $priorityTone[$task->priority] }}">{{ $task->priorityLabel() }}</span>
                  <span class="text-[10.5px] font-mono text-faint">{{ $task->uid }}</span>
                </div>

                <div class="mt-2.5 text-[13.5px] font-medium leading-snug">{{ $task->title }}</div>
                @if($task->brief)
                  <p class="mt-1.5 text-[12px] leading-5 text-mut line-clamp-2">{{ $task->brief }}</p>
                @endif

                <div class="mt-3 flex items-center gap-2.5 text-[11px] text-mut">
                  <span class="rounded-md bg-tint px-1.5 py-0.5">{{ $task->category }}</span>
                  @if($task->due_on)
                    <span class="inline-flex items-center gap-1 {{ $task->isOverdue() ? 'text-mint-deep' : '' }}">
                      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="5" width="18" height="16" rx="2"/><path d="M16 3v4M8 3v4M3 11h18"/></svg>
                      {{ $task->due_on->format('d M') }}
                    </span>
                  @endif
                  @if(count($task->links ?? []))
                    <span class="inline-flex items-center gap-1">
                      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1"/><path d="M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/></svg>
                      {{ count($task->links) }}
                    </span>
                  @endif
                  @if($task->comments_count)
                    <span class="inline-flex items-center gap-1">
                      <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 11.5a8.5 8.5 0 0 1-12.5 7.5L3 21l2-5.5A8.5 8.5 0 1 1 21 11.5z"/></svg>
                      {{ $task->comments_count }}
                    </span>
                  @endif
                </div>

                <div class="mt-3 pt-3 border-t border-line flex items-center justify-between">
                  @if($task->creator)
                    <div class="flex items-center gap-2 min-w-0">
                      <img src="{{ $task->creator->avatarUrl() }}" class="w-6 h-6 rounded-full object-cover border border-line" alt="">
                      <span class="text-[11.5px] text-mut truncate">{{ $task->creator->name }}</span>
                    </div>
                  @else
                    <span class="text-[11.5px] text-mut">Matching…</span>
                  @endif

                  <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                    @if($key !== 'done')
                      <button x-on:click="move({{ $task->id }}, '{{ $key }}', 1)" title="Move forward"
                              class="w-7 h-7 rounded-lg bg-tint hover:bg-tint grid place-items-center transition">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                      </button>
                    @endif
                    @if(!$task->order_id && $key !== 'done')
                      <button x-on:click="convert({{ $task->id }})" title="Create escrow order"
                              class="w-7 h-7 rounded-lg bg-mint-wash hover:bg-mint/35 grid place-items-center transition">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                      </button>
                    @endif
                    @if($task->order)
                      <a href="{{ route('orders.show', $task->order->uid) }}" title="Open order"
                         class="w-7 h-7 rounded-lg bg-mint-wash hover:bg-mint/35 grid place-items-center transition">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><path d="M15 3h6v6M10 14 21 3"/></svg>
                      </a>
                    @endif
                    <button x-on:click="remove({{ $task->id }})" title="Delete"
                            class="w-7 h-7 rounded-lg bg-tint hover:bg-pink/30 grid place-items-center transition">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M3 6h18M8 6V4h8v2M6 6l1 14h10l1-14"/></svg>
                    </button>
                  </div>
                </div>
              </div>
            @empty
              <div class="rounded-2xl border border-dashed border-line px-3 py-6 text-center text-[11.5px] text-mut">
                Nothing here
              </div>
            @endforelse

            {{-- cards added during this session land here --}}
            <template x-for="t in added.filter(t => t.status === '{{ $key }}')" :key="t.id">
              <div class="glass rounded-2xl p-3.5 animate-popIn">
                <div class="flex items-start justify-between gap-2">
                  <span class="text-[10px] font-semibold tracking-wider uppercase rounded-full px-2 py-0.5 bg-tint text-mut" x-text="t.priority"></span>
                  <span class="text-[10.5px] font-mono text-faint" x-text="t.uid"></span>
                </div>
                <div class="mt-2.5 text-[13.5px] font-medium leading-snug" x-text="t.title"></div>
                <p class="mt-1.5 text-[12px] leading-5 text-mut line-clamp-2" x-text="t.brief"></p>
                <div class="mt-3 flex items-center gap-2.5 text-[11px] text-mut">
                  <span class="rounded-md bg-tint px-1.5 py-0.5" x-text="t.category"></span>
                  <span x-show="t.due_label" x-text="t.due_label"></span>
                </div>
                <div class="mt-3 pt-3 border-t border-line flex items-center justify-between">
                  <div class="flex items-center gap-2 min-w-0">
                    <template x-if="t.creator">
                      <img :src="t.creator.img" class="w-6 h-6 rounded-full object-cover border border-line" alt="">
                    </template>
                    <span class="text-[11.5px] text-mut truncate" x-text="t.creator ? t.creator.name : 'Matching…'"></span>
                  </div>
                  <div class="flex items-center gap-1">
                    <button x-on:click="move(t.id, t.status, 1)" class="w-7 h-7 rounded-lg bg-tint hover:bg-tint grid place-items-center transition">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
                    </button>
                    <button x-on:click="convert(t.id)" class="w-7 h-7 rounded-lg bg-mint-wash hover:bg-mint/35 grid place-items-center transition">
                      <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2"><path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </button>
                  </div>
                </div>
              </div>
            </template>
          </div>
        </div>
      @endforeach
    </div>

    <div class="mt-5 text-[12.5px] text-mut">
      Tip: the arrow moves a task to the next stage, the ₹ icon turns it into an escrow-backed order, and everything updates without reloading.
    </div>
  </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  const FLOW = ['queued', 'assigned', 'production', 'review', 'done'];

  Alpine.data('board', () => ({
    composing: false,
    busy: false,
    added: [],
    hidden: [],
    origins: @js($tasks->flatten()->mapWithKeys(fn ($t) => [$t->id => $t->status])),
    form: { prompt: '', title: '', brief: '', category: 'Reel', priority: 'normal', due_on: '', link: '' },

    count(status) {
      const onServer = Object.entries(this.origins)
        .filter(([id, st]) => st === status && !this.hidden.includes(Number(id))).length;
      return onServer + this.added.filter(t => t.status === status).length;
    },
    isHidden(id) { return this.hidden.includes(id) || this.added.some(t => t.id === id); },

    async create() {
      if (!this.form.prompt.trim() && !this.form.title.trim()) {
        window.qg.toast('Describe the task or give it a title.');
        return;
      }
      this.busy = true;
      try {
        const res = await window.qg.post('{{ route('board.store') }}', this.form);
        this.added.unshift(res.task);
        this.form = { prompt: '', title: '', brief: '', category: 'Reel', priority: 'normal', due_on: '', link: '' };
        this.composing = false;
        window.qg.toast(res.message);
      } catch (e) { window.qg.toast('Could not create that task.'); }
      this.busy = false;
    },

    async move(id, from, step) {
      const next = FLOW[Math.min(FLOW.length - 1, Math.max(0, FLOW.indexOf(from) + step))];
      if (next === from) return;
      try {
        const res = await window.qg.post(`/business/board/${id}/status`, { status: next });
        this.place(res.task);
        window.qg.toast(res.message);
      } catch (e) { window.qg.toast('Could not move that task.'); }
    },

    async convert(id) {
      try {
        const res = await window.qg.post(`/business/board/${id}/convert`, {});
        this.place(res.task);
        window.qg.toast(res.message);
      } catch (e) { window.qg.toast('Could not create the order.'); }
    },

    async remove(id) {
      try {
        await window.qg.post(`/business/board/${id}`, { _method: 'DELETE' });
        this.hidden.push(id);
        this.added = this.added.filter(t => t.id !== id);
        window.qg.toast('Task removed.');
      } catch (e) { window.qg.toast('Could not remove that task.'); }
    },

    /* move a card between columns without a reload */
    place(task) {
      if (!this.hidden.includes(task.id)) this.hidden.push(task.id);   // hide the server-rendered copy
      this.added = this.added.filter(t => t.id !== task.id);
      this.added.unshift(task);                                        // re-draw it in its new column
    },
  }));
});
</script>
@endpush
