@extends('layouts.site')

@section('content')
@php
  $person = [
    '@context' => 'https://schema.org',
    '@type' => 'Person',
    'name' => $c->name,
    'alternateName' => $c->handle,
    'image' => $c->avatarUrl(),
    'description' => $c->bio,
    'url' => route('creator.public', $c->id),
    'jobTitle' => $c->headline,
    'knowsAbout' => $c->skills ?: ['Video editing'],
    'aggregateRating' => ['@type' => 'AggregateRating', 'ratingValue' => $c->rating, 'reviewCount' => max(1, (int) $c->reviews_count)],
  ];
@endphp

<section class="py-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8">

    <div class="glass rounded-3xl overflow-hidden">
      <div class="h-[160px] sm:h-[200px] bg-tint relative">
        @if($c->cover)
          <img src="{{ filter_var($c->cover, FILTER_VALIDATE_URL) ? $c->cover : asset('storage/'.$c->cover) }}" class="w-full h-full object-cover opacity-70" alt="">
        @endif
      </div>

      <div class="p-6 sm:p-8 -mt-14">
        <div class="flex flex-wrap items-end gap-5">
          <img src="{{ $c->avatarUrl() }}" class="w-24 h-24 rounded-3xl object-cover border-4 border-white" alt="{{ $c->name }}">
          <div class="flex-1 min-w-[240px]">
            <h1 class="font-display text-[28px] font-semibold flex items-center gap-2.5">
              {{ $c->name }}
              @if($c->is_verified)<span class="text-[10.5px] font-semibold rounded-full bg-mint-wash text-mint-deep px-2.5 py-1">Verified</span>@endif
            </h1>
            <div class="text-[13.5px] text-mut mt-1">{{ $c->handle }} · {{ $c->headline }}</div>
          </div>
          <div class="flex items-center gap-2.5">
            <span class="text-[12px] font-semibold rounded-full px-3 py-1.5 {{ $c->is_available ? 'bg-mint-wash text-mint-deep' : 'bg-amber-400/15 text-amber-300' }}">
              {{ $c->is_available ? 'Available now' : 'Busy — free soon' }}
            </span>
            <a href="{{ route('marketplace', ['q' => ltrim($c->handle, '@')]) }}" class="h-11 px-5 rounded-xl btn-grad inline-flex items-center text-[13.5px] font-semibold">See gigs</a>
          </div>
        </div>

        @if($c->bio)
          <p class="mt-6 text-[14.5px] leading-7 text-mut max-w-[720px]">{{ $c->bio }}</p>
        @endif

        <div class="mt-6 flex flex-wrap gap-2">
          @foreach((array) ($c->skills ?? []) as $skill)
            <span class="text-[12px] rounded-full border border-line px-3 py-1.5 text-mut">{{ $skill }}</span>
          @endforeach
        </div>

        <div class="mt-7 grid grid-cols-2 sm:grid-cols-4 gap-4">
          @foreach([
            ['Rating', number_format((float) $c->rating, 1).' ★'],
            ['Gigs delivered', $c->orders_count ?: 0],
            ['Response', ($c->response_minutes ?: 8).' min'],
            ['From', '₹'.number_format($c->price_from)],
          ] as [$k, $v])
            <div class="rounded-2xl border border-line bg-tint px-4 py-3.5">
              <div class="text-[11px] text-mut">{{ $k }}</div>
              <div class="font-display text-[19px] font-semibold mt-0.5">{{ $v }}</div>
            </div>
          @endforeach
        </div>
      </div>
    </div>

    @if($c->portfolio->count())
      <h2 class="mt-12 font-display text-[22px] font-semibold">Recent work</h2>
      <div class="mt-6 grid sm:grid-cols-2 lg:grid-cols-3 gap-5">
        @foreach($c->portfolio as $p)
          <div class="glass rounded-3xl overflow-hidden card-hover">
            <img src="{{ $p->cover && filter_var($p->cover, FILTER_VALIDATE_URL) ? $p->cover : ($p->cover ? asset('storage/'.$p->cover) : 'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=600&q=80') }}" class="h-[170px] w-full object-cover" alt="{{ $p->title }}">
            <div class="p-5">
              <div class="text-[14.5px] font-semibold leading-snug">{{ $p->title }}</div>
              <div class="mt-1.5 text-[12.5px] text-mut">{{ $p->category }}</div>
            </div>
          </div>
        @endforeach
      </div>
    @endif
  </div>
</section>
@endsection

@push('scripts')
<script type="application/ld+json">{!! json_encode($person, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
@endpush
