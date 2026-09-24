<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@php
  $seo = ['title'=>$c->seoTitle(),'description'=>Str::limit(strip_tags($c->bio ?: $c->headline),155),'canonical'=>route('creator.public',$c->id),'image'=>$c->avatarUrl(),'type'=>'profile'];
  $breadcrumbs = [['name'=>'Home','url'=>url('/')],['name'=>'Creators','url'=>url('/creator')],['name'=>$c->name,'url'=>route('creator.public',$c->id)]];
@endphp
@include('components.seo', ['seo'=>$seo, 'breadcrumbs'=>$breadcrumbs])
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>*{font-family:Inter,sans-serif}</style>
<script type="application/ld+json">
{!! json_encode(['@context'=>'https://schema.org','@type'=>'Person','name'=>$c->name,'alternateName'=>$c->handle,'image'=>$c->avatarUrl(),'description'=>$c->bio,'url'=>route('creator.public',$c->id),'jobTitle'=>$c->headline,'knowsAbout'=> $c->skills ?: ['Video Editing'],'aggregateRating'=>['@type'=>'AggregateRating','ratingValue'=>$c->rating,'reviewCount'=>$c->reviews_count]], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}
</script>
</head>
<body class="bg-[#F8F8F7] text-[#0F0F0F]">
<div class="max-w-[960px] mx-auto px-4 sm:px-6 py-6">
  <a href="{{ route('landing') }}" class="text-[12px] font-bold text-[#7A7A78]">← Home</a>
  <div class="mt-4 bg-white border border-[#E8E8E6] rounded-2xl p-6 flex gap-5 flex-wrap">
    <div class="relative shrink-0"><img src="{{ $c->avatarUrl() }}" class="w-24 h-24 rounded-full object-cover border border-[#E8E8E6]"><span class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full {{ $c->is_verified?'bg-[#1D9BF0]':'bg-amber-400' }} text-white grid place-items-center border-2 border-white" style="aspect-ratio:1/1">@if($c->is_verified)<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>@else<span class="text-[10px] font-black text-[#0F0F0F]">!</span>@endif</span></div>
    <div class="flex-1 min-w-[240px]">
      <h1 class="text-[22px] font-black leading-tight">{{ $c->name }} <span class="text-[#7A7A78] font-semibold text-[14px]">{{ $c->handle }}</span></h1>
      <div class="text-[13px] font-semibold text-[#7A7A78]">{{ $c->headline }}</div>
      <div class="mt-2 text-[13px] leading-6">{{ $c->bio }}</div>
      <div class="mt-3 flex flex-wrap gap-2">
        <span class="px-3 py-1.5 rounded-full text-[12px] font-bold border {{ $c->is_available ? 'bg-green-50 border-green-200 text-green-700' : 'bg-amber-50 border-amber-200 text-amber-700' }}">{{ $c->is_available ? '● Available now' : '● Busy' }}</span>
        <span class="px-3 py-1.5 rounded-full bg-[#F8F8F7] border border-[#E8E8E6] text-[12px] font-bold">★ {{ $c->rating }} • {{ $c->reviews_count }} reviews</span>
        <span class="px-3 py-1.5 rounded-full bg-[#F8F8F7] border border-[#E8E8E6] text-[12px] font-bold">{{ $c->orders_count }} orders • {{ $c->on_time_rate }}% on-time</span>
        @if($c->skills)<span class="px-3 py-1.5 rounded-full bg-[#0F0F0F] text-white text-[12px] font-bold">{{ is_array($c->skills) ? implode(' • ', array_slice($c->skills,0,3)) : $c->skills }}</span>@endif
      </div>
      <div class="mt-4 flex gap-2">
        <a href="{{ route('business.home') }}" class="h-10 px-6 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px] inline-flex items-center">Hire {{ explode(' ',$c->name)[0] }} — From ₹{{ number_format($c->price_from) }}</a>
        @if($c->portfolio_url)<a href="{{ $c->portfolio_url }}" target="_blank" class="h-10 px-6 rounded-full border border-[#E8E8E6] bg-white font-bold text-[13px] inline-flex items-center">Portfolio</a>@endif
      </div>
    </div>
  </div>
  <div class="mt-6 grid md:grid-cols-3 gap-4">
    @forelse($c->portfolio()->where('is_published',true)->limit(6)->get() as $p)
      <div class="bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden"><img src="{{ filter_var($p->cover, FILTER_VALIDATE_URL) ? $p->cover : asset('storage/'.$p->cover) }}" class="h-[150px] w-full object-cover"><div class="p-3"><div class="text-[13px] font-bold">{{ $p->title }}</div><div class="text-[11px] font-semibold text-[#7A7A78]">{{ $p->category }} • {{ $p->views }} views</div></div></div>
    @empty
      <div class="md:col-span-3 text-center py-8 bg-white border border-dashed border-[#E8E8E6] rounded-2xl text-[#7A7A78] font-medium">No portfolio published yet.</div>
    @endforelse
  </div>
</div>
</body>
</html>
