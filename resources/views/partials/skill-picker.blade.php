{{--
  Admin-managed skill picker.

  @include('partials.skill-picker', [
      'selected' => array,      // currently chosen skill names
      'groups'   => array,      // ['Video' => ['Reels', …], …]  (Skill::grouped())
      'name'     => 'skills',   // form field name, posts as skills[]
      'max'      => 12,
  ])
--}}
@php
  $selected = array_values(array_filter((array) ($selected ?? [])));
  $groups   = $groups ?? [];
  $name     = $name ?? 'skills';
  $max      = $max ?? 12;
  $known    = collect($groups)->flatten()->all();
  $custom   = array_values(array_diff($selected, $known));   // legacy values no longer in the library
@endphp

<div x-data="skillPicker(@js($selected), @js($groups), {{ $max }})" class="relative" x-on:keydown.escape="open=false">
  {{-- chips --}}
  <div class="rounded-2xl border border-line bg-white p-2.5 min-h-[52px] flex flex-wrap gap-1.5 items-center cursor-text"
       x-on:click="open = true; $refs.search.focus()">
    <template x-for="s in chosen" :key="s">
      <span class="inline-flex items-center gap-1.5 rounded-lg bg-mint-wash text-mint-deep px-2.5 py-1.5 text-[12.5px] font-medium">
        <span x-text="s"></span>
        <button type="button" x-on:click.stop="remove(s)" class="w-4 h-4 rounded grid place-items-center hover:bg-mint/30" aria-label="Remove">
          <svg width="9" height="9" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M6 6l12 12M18 6L6 18"/></svg>
        </button>
      </span>
    </template>

    <input x-ref="search" x-model="query" x-on:focus="open = true" x-on:input="open = true"
           class="flex-1 min-w-[140px] h-8 px-1 text-[13.5px] outline-none bg-transparent placeholder:text-faint"
           aria-label="Search skills"
           :placeholder="chosen.length ? 'Search for another skill…' : 'Type to search skills — reels, Figma, copywriting…'">

    <span class="text-[11px] font-mono text-faint shrink-0 pr-1" x-text="chosen.length + '/' + max"></span>
  </div>

  {{-- posted values --}}
  <template x-for="s in chosen" :key="'in-' + s">
    <input type="hidden" name="{{ $name }}[]" :value="s">
  </template>

  {{-- dropdown --}}
  <div x-show="open" x-cloak x-transition.opacity.duration.150ms x-on:click.outside="open = false"
       class="absolute z-40 left-0 right-0 mt-2 max-h-[320px] overflow-y-auto bg-white border border-line rounded-2xl shadow-xl shadow-ink/10 p-2">

    <template x-for="(list, group) in filtered" :key="group">
      <div class="mb-1.5">
        <div class="px-2.5 py-1.5 text-[10.5px] font-semibold tracking-[.14em] uppercase text-faint" x-text="group"></div>
        <div class="flex flex-wrap gap-1.5 px-1.5 pb-1.5">
          <template x-for="s in list" :key="group + s">
            <button type="button" x-on:click="toggle(s)"
                    class="rounded-lg px-2.5 py-1.5 text-[12.5px] border transition"
                    :class="chosen.includes(s) ? 'border-mint bg-mint-wash text-mint-deep font-medium' : 'border-line text-body hover:border-ink/30 hover:bg-tint'">
              <span x-text="s"></span>
            </button>
          </template>
        </div>
      </div>
    </template>

    <div x-show="!Object.keys(filtered).length" x-cloak class="px-3 py-4 text-[13px] text-faint">
      No skill matches “<span x-text="query"></span>”. The list is curated by the Quick GIGS team —
      <a href="{{ route('contact') }}" class="text-mint-deep font-medium hover:underline">ask us to add it</a>.
    </div>
  </div>

  @if(count($custom))
    <div class="mt-2 text-[11.5px] text-faint">
      Kept from your old profile: {{ implode(', ', $custom) }} — these stay until you remove them.
    </div>
  @endif
</div>

@once
  @push('scripts')
  <script>
    document.addEventListener('alpine:init', () => {
      Alpine.data('skillPicker', (selected, groups, max) => ({
        chosen: [...selected],
        groups,
        max,
        query: '',
        open: false,

        get filtered() {
          const q = this.query.trim().toLowerCase();
          const out = {};
          for (const [group, list] of Object.entries(this.groups)) {
            const hits = list.filter(s => !q || s.toLowerCase().includes(q));
            if (hits.length) out[group] = hits;
          }
          return out;
        },
        toggle(s) {
          if (this.chosen.includes(s)) return this.remove(s);
          if (this.chosen.length >= this.max) {
            window.qg?.toast('That is the maximum of ' + this.max + ' skills.');
            return;
          }
          this.chosen.push(s);
          this.query = '';
        },
        remove(s) { this.chosen = this.chosen.filter(x => x !== s); },
      }));
    });
  </script>
  @endpush
@endonce
