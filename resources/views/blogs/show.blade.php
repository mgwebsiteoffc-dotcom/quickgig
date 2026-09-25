@extends('layouts.site')

@push('styles')
<style>
  .article h2 { font-family:"Space Grotesk",sans-serif; font-size:23px; font-weight:600; margin:34px 0 12px; letter-spacing:-0.02em; }
  .article h3 { font-family:"Space Grotesk",sans-serif; font-size:18px; font-weight:600; margin:26px 0 10px; }
  .article p  { font-size:15.5px; line-height:1.85; color:#B6BCCE; margin:14px 0; }
  .article ul { list-style:disc; padding-left:22px; margin:14px 0; color:#B6BCCE; font-size:15.5px; line-height:1.8; }
  .article ol { list-style:decimal; padding-left:22px; margin:14px 0; color:#B6BCCE; font-size:15.5px; line-height:1.8; }
  .article strong { color:#F2F3F8; font-weight:600; }
  .article a { color:#A78BFA; text-decoration:underline; }
  .article pre { background:rgba(255,255,255,.05); border:1px solid rgba(255,255,255,.1); padding:16px; border-radius:16px; white-space:pre-wrap; font-size:13px; color:#D5DAE8; margin:18px 0; }
  .article img { border-radius:20px; margin:20px 0; }
</style>
@endpush

@section('content')
<article class="py-12">
  <div class="max-w-[760px] mx-auto px-5 lg:px-8">

    <nav class="flex items-center gap-2 text-[12.5px] text-mut">
      <a href="{{ route('blog.index') }}" class="hover:text-ink">Insights</a>
      <span class="opacity-40">/</span>
      <span class="text-body">{{ $blog->category->name ?? 'Post' }}</span>
    </nav>

    <h1 class="mt-5 font-display text-[32px] sm:text-[42px] font-semibold leading-[1.08]">{{ $blog->title }}</h1>

    <div class="mt-5 flex flex-wrap items-center gap-4 text-[13px] text-mut">
      <span class="flex items-center gap-2.5">
        <span class="w-7 h-7 rounded-lg btn-grad grid place-items-center font-display text-[12px] font-bold text-white">{{ substr($blog->author->name ?? 'Q', 0, 1) }}</span>
        {{ $blog->author->name ?? 'Quick GIGS team' }}
      </span>
      <span class="opacity-40">·</span>
      <span>{{ $blog->published_at?->format('d M Y') }}</span>
      <span class="opacity-40">·</span>
      <span>{{ $blog->reading_minutes }} min read</span>
    </div>

    @if($blog->excerpt)
      <p class="mt-7 glass rounded-2xl p-5 text-[15px] leading-7 text-body">{{ $blog->excerpt }}</p>
    @endif

    @if($blog->cover)
      <img src="{{ filter_var($blog->cover, FILTER_VALIDATE_URL) ? $blog->cover : asset('storage/'.$blog->cover) }}" alt="{{ $blog->cover_alt ?: $blog->title }}" class="mt-7 w-full h-[300px] sm:h-[380px] object-cover rounded-3xl border border-line">
    @endif

    <div class="article mt-8" id="speakable">{!! $blog->content !!}</div>

    @if(!empty($blog->faq_json) && is_array($blog->faq_json))
      <div class="mt-10 glass rounded-3xl p-6">
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Quick answers</div>
        <div class="mt-4 space-y-4">
          @foreach($blog->faq_json as $f)
            <div>
              <div class="text-[14.5px] font-semibold">{{ $f['q'] ?? $f['question'] ?? '' }}</div>
              <div class="mt-1.5 text-[13.5px] leading-6 text-mut">{{ $f['a'] ?? $f['answer'] ?? '' }}</div>
            </div>
          @endforeach
        </div>
      </div>
    @endif

    {{-- CTA --}}
    <div class="mt-12 rounded-3xl glass-strong p-7 text-center">
      <div class="font-display text-[22px] font-semibold">Skip the how-to — hire the pro.</div>
      <p class="mt-2 text-[14px] text-mut">Matched in minutes, escrow protected, from ₹1,299.</p>
      <div class="mt-5 flex flex-wrap justify-center gap-3">
        <a href="{{ route('marketplace') }}" class="h-11 px-5 rounded-xl btn-grad inline-flex items-center text-[13.5px] font-semibold">Browse gigs</a>
        <a href="{{ route('register') }}?type=business" class="h-11 px-5 rounded-xl glass inline-flex items-center text-[13.5px] font-medium">Create free account</a>
      </div>
    </div>

    @if(isset($faqs) && $faqs->count())
      <div class="mt-12" x-data="{ open: -1 }">
        <h2 class="font-display text-[20px] font-semibold">Common questions</h2>
        <div class="mt-4 space-y-2.5">
          @foreach($faqs as $i => $f)
            <div class="glass rounded-2xl overflow-hidden">
              <button type="button" x-on:click="open = open === {{ $i }} ? -1 : {{ $i }}" class="w-full flex items-center justify-between gap-4 p-4 text-left">
                <span class="text-[14px] font-medium">{{ $f->question }}</span>
                <span class="w-6 h-6 rounded-full grid place-items-center shrink-0 transition" :class="open === {{ $i }} ? 'bg-mint text-ink rotate-180' : 'bg-tint text-faint'">
                  <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6"/></svg>
                </span>
              </button>
              <div x-show="open === {{ $i }}" x-collapse x-cloak><div class="px-4 pb-4 text-[13.5px] leading-6 text-body">{{ $f->answer }}</div></div>
            </div>
          @endforeach
        </div>
        <a href="{{ route('faq') }}" class="mt-4 inline-block text-[13px] font-medium text-mint-deep hover:underline">See all answers →</a>
      </div>
    @endif

    @if($related->count())
      <h2 class="mt-14 font-display text-[22px] font-semibold">Read next</h2>
      <div class="mt-5 grid sm:grid-cols-3 gap-4">
        @foreach($related as $r)
          <a href="{{ route('blog.show', $r->slug) }}" class="glass rounded-2xl overflow-hidden card-hover group">
            <img src="{{ filter_var($r->cover, FILTER_VALIDATE_URL) ? $r->cover : asset('storage/'.$r->cover) }}" class="h-[110px] w-full object-cover opacity-80 group-hover:opacity-100 transition" alt="">
            <div class="p-4 text-[13.5px] font-medium leading-snug line-clamp-2">{{ $r->title }}</div>
          </a>
        @endforeach
      </div>
    @endif
  </div>
</article>
@endsection
