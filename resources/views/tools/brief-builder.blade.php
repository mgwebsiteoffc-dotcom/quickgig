@extends('layouts.site')

@section('content')

<section class="pt-16 pb-10">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[720px]">
      <div class="inline-flex items-center gap-2.5 glass rounded-full pl-2 pr-3.5 py-1.5 text-[12px] font-medium">
        <span class="btn-grad text-ink text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-full">Free tool</span>
        <span class="text-body">No account, no card, no email wall</span>
      </div>
      <h1 class="mt-5 font-display text-[38px] sm:text-[50px] font-semibold leading-[1.04]">
        One line in.<br><span class="grad-text">A production brief</span> out.
      </h1>
      <p class="mt-5 text-[16px] leading-7 text-mut max-w-[620px]">
        Bad briefs cause most revisions. Describe the job the way you would say it out loud and get back
        hook options, a timed beat sheet, deliverables, the technical spec and the quality checks your
        delivery will be scored against.
      </p>
    </div>
  </div>
</section>

{{-- ── the form ── --}}
<section class="pb-8">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <form id="brief-form" x-on:submit.prevent="$dispatch('brief-generate', new FormData($event.target))"
          x-data="{ format: @js(old('format', $input['format'] ?? 'reel')), urgency: @js(old('urgency', $input['urgency'] ?? 'standard')) }"
          class="glass rounded-3xl p-6 sm:p-8">
      @csrf

      @if($errors->any())
        <div class="mb-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-[13.5px] text-rose-200">{{ $errors->first() }}</div>
      @endif

      <div class="grid lg:grid-cols-[1.25fr_1fr] gap-8">
        <div>
          <label class="label" for="idea">What do you need? <span class="normal-case tracking-normal text-faint">one sentence</span></label>
          <textarea id="idea" name="idea" rows="3" required class="field"
                    placeholder="A reel announcing our ₹999 protein bar launch, founder on camera, shot at the factory">{{ old('idea', $input['idea'] ?? '') }}</textarea>

          <div class="mt-5">
            <span class="label">Format</span>
            <div class="grid sm:grid-cols-3 gap-2.5">
              @foreach($formats as $key => $f)
                <label class="rounded-2xl p-3.5 border cursor-pointer transition block"
                       :class="format === '{{ $key }}' ? 'border-mint bg-mint-wash' : 'border-line bg-tint hover:border-line'">
                  <input type="radio" name="format" value="{{ $key }}" x-model="format" class="sr-only">
                  <div class="text-[13.5px] font-semibold">{{ $f['label'] }}</div>
                  <div class="text-[11.5px] text-mut mt-0.5">from ₹{{ number_format($f['price']) }} · {{ $f['length'] }}</div>
                </label>
              @endforeach
            </div>
          </div>

          <div class="mt-5">
            <label class="label" for="audience">Who is it for? <span class="normal-case tracking-normal text-faint">optional</span></label>
            <input id="audience" name="audience" value="{{ old('audience', $input['audience'] ?? '') }}" class="field"
                   placeholder="gym-goers in metros who buy supplements online">
          </div>
        </div>

        <div class="space-y-5">
          <div>
            <label class="label" for="goal">Goal</label>
            <select id="goal" name="goal" class="field">
              @foreach($goals as $key => $desc)
                <option value="{{ $key }}" @selected(old('goal', $input['goal'] ?? 'conversion') === $key)>{{ ucfirst($key) }} — {{ $desc }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <label class="label" for="tone">Tone</label>
            <select id="tone" name="tone" class="field">
              @foreach($tones as $key => $t)
                <option value="{{ $key }}" @selected(old('tone', $input['tone'] ?? 'confident') === $key)>{{ ucfirst($key) }} — {{ $t[0] }}</option>
              @endforeach
            </select>
          </div>

          <div>
            <span class="label">Speed</span>
            <div class="space-y-2">
              @foreach(['express' => 'Express · 3 hours (+60%)', 'standard' => 'Standard · 24 hours', 'relaxed' => 'Relaxed · 48 hours (−15%)'] as $key => $label)
                <label class="flex items-center gap-3 rounded-2xl px-4 py-3 border cursor-pointer transition"
                       :class="urgency === '{{ $key }}' ? 'border-mint bg-mint-wash' : 'border-line bg-tint hover:border-line'">
                  <input type="radio" name="urgency" value="{{ $key }}" x-model="urgency" class="accent-cyan">
                  <span class="text-[13.5px] font-medium">{{ $label }}</span>
                </label>
              @endforeach
            </div>
          </div>

          <button type="submit" class="w-full py-3.5 rounded-xl btn-grad font-semibold text-[15px] shadow-lg shadow-ink/10">
            Generate my brief →
          </button>
          <div class="text-center text-[11.5px] text-mut">Runs instantly. Nothing is stored against your name.</div>
        </div>
      </div>
    </form>
  </div>
</section>

{{-- ── the result ── --}}
{{-- ── result (updates in place, no page load) ── --}}
<div x-data="briefTool({{ $brief ? 'true' : 'false' }})" x-on:brief-reset.window="reset()">

  {{-- skeleton --}}
  <section x-show="busy" x-cloak class="py-12">
    <div class="max-w-shell mx-auto px-5 lg:px-8">
      <div class="glass rounded-3xl p-7 space-y-4">
        <div class="h-5 w-1/3 rounded skeleton"></div>
        <div class="h-4 w-2/3 rounded skeleton"></div>
        <div class="grid sm:grid-cols-3 gap-4 pt-2">
          <div class="h-20 rounded-2xl skeleton"></div>
          <div class="h-20 rounded-2xl skeleton"></div>
          <div class="h-20 rounded-2xl skeleton"></div>
        </div>
        <div class="h-40 rounded-2xl skeleton"></div>
      </div>
      <div class="mt-4 text-center text-[12.5px] text-mut">Composing hooks, beats and the QA gate…</div>
    </div>
  </section>

  {{-- rendered brief --}}
  <div id="brief-result" x-ref="result" x-show="!busy" x-transition.opacity.duration.400ms>
@if($brief)
@include('tools.partials.result')
@endif
  </div>

  {{-- refine bar stays put, so the page never jumps --}}
  <section x-show="!busy && refinable" x-cloak class="pb-4">
    <div class="max-w-shell mx-auto px-5 lg:px-8">
      <form x-on:submit.prevent="refine()" class="glass rounded-3xl p-4 sm:p-5">
        <div class="flex flex-col sm:flex-row gap-3">
          <div class="flex-1">
            <label class="label" for="instruction">Not quite right? Tell it what to change</label>
            <input id="instruction" x-model="instruction" required maxlength="300" class="field"
                   placeholder="Make the hooks shorter and mention the ₹999 price in beat two">
          </div>
          <button class="sm:self-end h-12 px-6 rounded-xl btn-grad font-semibold text-[14px] shrink-0" :disabled="busy">Refine brief</button>
        </div>
        <div class="mt-2.5 text-[11.5px] text-mut">Updates the brief above in place — the model keeps the reasoning from the first pass.</div>
      </form>
    </div>
  </section>
</div>


{{-- ── empty state: why it matters ── --}}
@unless($brief)
<section class="py-14" x-data x-show="true">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid md:grid-cols-3 gap-5">
    @foreach([
      ['Fewer revisions', 'Gigs ordered with a generated brief come back right the first time far more often — because the freelancer gets beats and a spec, not a vibe.'],
      ['Comparable quotes', 'A structured brief means every freelancer prices the same scope. No more "it depends".'],
      ['Yours to keep', 'Copy it into any tool, send it to your own editor, or order it here. No lock-in, no email gate.'],
    ] as [$t, $b])
      <div class="glass rounded-3xl p-6 reveal">
        <div class="text-[15px] font-semibold">{{ $t }}</div>
        <p class="mt-2 text-[13.5px] leading-6 text-mut">{{ $b }}</p>
      </div>
    @endforeach
  </div>
</section>
@endunless

@include('partials.cta', [
  'eyebrow'   => 'After the brief',
  'title'     => 'Matched, escrowed and QA-checked — in the same tab.',
  'body'      => 'Turn the brief into a live gig and watch it move through the pipeline.',
  'primary'   => ['Browse the marketplace', route('marketplace')],
  'secondary' => ['See how the engine works', route('ai')],
])

@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('briefTool', (hasBrief) => ({
    busy: false,
    refinable: hasBrief,
    instruction: '',

    init() {
      window.addEventListener('brief-generate', (e) => this.generate(e.detail));
    },

    async send(url, body, isForm) {
      const headers = {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      };
      if (!isForm) headers['Content-Type'] = 'application/json';
      const res = await fetch(url, { method: 'POST', headers, body: isForm ? body : JSON.stringify(body) });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.json();
    },

    async generate(formData) {
      this.busy = true;
      // keep the form in view, then reveal the result where it already sits
      try {
        const data = await this.send('{{ route('brief-builder.generate') }}', formData, true);
        this.paint(data);
        window.qg.toast(data.meta.source === 'ai' ? 'Brief written by ' + data.meta.model : 'Brief ready.');
      } catch (e) {
        window.qg.toast('Could not build that brief — check the form and try again.');
      }
      this.busy = false;
    },

    async refine() {
      if (!this.instruction.trim()) return;
      this.busy = true;
      try {
        const data = await this.send('{{ route('brief-builder.refine') }}', { instruction: this.instruction }, false);
        this.paint(data);
        this.instruction = '';
        window.qg.toast(data.meta.refine_error || 'Brief updated.');
      } catch (e) {
        window.qg.toast('Refine failed — try again.');
      }
      this.busy = false;
    },

    paint(data) {
      const host = this.$refs.result;
      host.innerHTML = data.html;
      this.refinable = !!data.meta.refinable;
      this.$nextTick(() => {
        host.querySelectorAll('.reveal, .reveal-s, .reveal-l').forEach(el => el.classList.add('in'));
        const top = host.getBoundingClientRect().top + window.scrollY - 80;
        window.scrollTo({ top, behavior: 'smooth' });
      });
    },

    async reset() {
      this.$refs.result.innerHTML = '';
      this.refinable = false;
      try { await this.send('{{ route('brief-builder.reset') }}', {}, false); } catch (e) {}
      window.scrollTo({ top: 0, behavior: 'smooth' });
    },
  }));
});
</script>
@endpush
