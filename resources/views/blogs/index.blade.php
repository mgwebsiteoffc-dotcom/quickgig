@extends('layouts.site')

@section('content')
<section class="pt-14 pb-10 border-b border-white/8">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="flex flex-wrap items-end justify-between gap-6">
      <div class="max-w-[620px]">
        <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-soft">Insights</div>
        <h1 class="mt-3 font-display text-[34px] sm:text-[42px] font-semibold leading-[1.08]">Playbooks you can ship tomorrow.</h1>
        <p class="mt-4 text-[15px] leading-7 text-mut">Short, practical notes from verified creators — retention editing, thumbnails that get clicked, AI video workflows.</p>
      </div>

      <form method="GET" class="flex gap-2.5 w-full sm:w-auto">
        <input name="q" value="{{ $q }}" placeholder="Search insights…" class="field sm:w-[260px]">
        <button class="h-12 px-5 rounded-xl btn-grad font-semibold text-[14px] shrink-0">Search</button>
      </form>
    </div>

    <div class="mt-7 flex flex-wrap gap-2">
      <a href="{{ route('blog.index') }}" class="rounded-full px-3.5 py-1.5 text-[12.5px] font-medium border transition {{ !$cat ? 'border-violet bg-violet/15 text-white' : 'border-white/10 text-mut hover:text-white hover:border-white/25' }}">All</a>
      @foreach($categories as $c)
        <a href="{{ route('blog.index', ['category' => $c->slug]) }}" class="rounded-full px-3.5 py-1.5 text-[12.5px] font-medium border transition {{ $cat == $c->slug ? 'border-violet bg-violet/15 text-white' : 'border-white/10 text-mut hover:text-white hover:border-white/25' }}">
          {{ $c->name }} <span class="opacity-50">{{ $c->blogs_count }}</span>
        </a>
      @endforeach
    </div>
  </div>
</section>

<section class="py-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    @if($blogs->count())
      <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($blogs as $b)
          <a href="{{ route('blog.show', $b->slug) }}" class="glass rounded-3xl overflow-hidden card-hover group flex flex-col">
            <img src="{{ filter_var($b->cover, FILTER_VALIDATE_URL) ? $b->cover : asset('storage/'.$b->cover) }}" alt="{{ $b->cover_alt ?: $b->title }}" class="h-[180px] w-full object-cover opacity-85 group-hover:opacity-100 transition">
            <div class="p-5 flex-1 flex flex-col">
              <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-cyan">{{ $b->category->name ?? 'Playbook' }} · {{ $b->reading_minutes }} min</div>
              <div class="mt-2 text-[16px] font-semibold leading-snug line-clamp-2">{{ $b->title }}</div>
              <div class="mt-2 text-[13px] leading-6 text-mut line-clamp-3">{{ $b->excerpt }}</div>
              <div class="mt-auto pt-4 text-[12px] text-mut">{{ $b->published_at?->format('d M Y') }} · {{ number_format($b->views) }} reads</div>
            </div>
          </a>
        @endforeach
      </div>

      <div class="mt-10">{{ $blogs->links('vendor.pagination.quickgigs') }}</div>
    @else
      <div class="glass rounded-3xl p-14 text-center">
        <div class="font-display text-[20px] font-semibold">Nothing published yet</div>
        <p class="mt-2 text-[14px] text-mut">New playbooks land here every week.</p>
      </div>
    @endif
  </div>
</section>
@endsection
