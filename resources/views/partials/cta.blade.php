{{--
  Reusable CTA band.
  @include('partials.cta', ['eyebrow'=>'', 'title'=>'', 'body'=>'', 'primary'=>['label','href'], 'secondary'=>['label','href'], 'note'=>'', 'tone'=>'violet|cyan'])
--}}
@php
  $tone      = $tone ?? 'violet';
  $eyebrow   = $eyebrow ?? null;
  $note      = $note ?? null;
  $primary   = $primary ?? ['Get started free', route('register')];
  $secondary = $secondary ?? null;
@endphp

<section class="py-14">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="relative overflow-hidden rounded-[32px] glass-strong px-7 sm:px-12 py-12 sm:py-14">
      <div class="absolute -top-28 left-1/3 w-[34rem] h-[34rem] {{ $tone === 'cyan' ? 'bg-violet/20' : 'bg-violet/25' }} blur-[120px] rounded-full -z-10"></div>

      <div class="flex flex-col lg:flex-row lg:items-center gap-8 justify-between">
        <div class="max-w-[620px]">
          @if($eyebrow)
            <div class="text-[11px] font-semibold tracking-[.16em] uppercase {{ $tone === 'cyan' ? 'text-violet-soft' : 'text-violet-soft' }}">{{ $eyebrow }}</div>
          @endif
          <h2 class="mt-3 font-display text-[28px] sm:text-[36px] font-semibold leading-[1.1]">{!! $title !!}</h2>
          @isset($body)
            <p class="mt-4 text-[15px] leading-7 text-mut">{{ $body }}</p>
          @endisset
        </div>

        <div class="shrink-0">
          <div class="flex flex-wrap gap-3">
            <a href="{{ $primary[1] }}" class="h-12 px-6 rounded-xl btn-grad font-semibold text-[14.5px] inline-flex items-center gap-2 shadow-xl shadow-violet/25">
              {{ $primary[0] }}
              <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
            </a>
            @if($secondary)
              <a href="{{ $secondary[1] }}" class="h-12 px-6 rounded-xl glass font-medium text-[14.5px] inline-flex items-center hover:border-white/30 transition">{{ $secondary[0] }}</a>
            @endif
          </div>
          @if($note)
            <div class="mt-3.5 text-[12.5px] text-mut">{{ $note }}</div>
          @endif
        </div>
      </div>
    </div>
  </div>
</section>
