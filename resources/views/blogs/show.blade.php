<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('components.seo', ['seo'=>$seo, 'breadcrumbs'=>$breadcrumbs, 'blog'=>$blog])
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>*{font-family:Inter,sans-serif} .prose h2{font-size:20px;font-weight:800;margin:20px 0 8px} .prose h3{font-size:15px;font-weight:800;margin:16px 0 6px} .prose p{font-size:14px;line-height:1.7;color:#2b2b2b;margin:8px 0} .prose ul{list-style:disc;padding-left:20px;margin:8px 0} .prose pre{font-family:monospace} .prose a{color:#2563EB;text-decoration:underline}</style>
</head>
<body class="bg-white text-[#0F0F0F]">
<header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-[#E8E8E6]">
  <div class="max-w-[1160px] mx-auto px-4 sm:px-6 h-[64px] flex items-center justify-between">
    <a href="{{ route('landing') }}" class="flex items-center gap-3"><div class="w-9 h-9 rounded-xl bg-[#0F0F0F] text-white grid place-items-center font-black text-[13px]">QC</div><div class="leading-none"><div class="font-extrabold text-[14px]">QuickContent</div><div class="text-[11px] font-semibold text-[#7A7A78]">Playbooks</div></div></a>
    <div class="flex gap-2"><a href="{{ route('blog.index') }}" class="h-9 px-4 rounded-full border border-[#E8E8E6] font-bold text-[13px] inline-flex items-center">All posts</a><a href="{{ route('business.home') }}" class="h-9 px-5 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px] inline-flex items-center">Hire a Pro</a></div>
  </div>
</header>

<article class="max-w-[760px] mx-auto px-4 sm:px-6 py-8">
  <nav class="text-[11px] font-semibold text-[#7A7A78] flex flex-wrap gap-1.5 items-center">
    <a href="{{ route('landing') }}" class="hover:text-[#0F0F0F]">Home</a><span>›</span><a href="{{ route('blog.index') }}" class="hover:text-[#0F0F0F]">Blog</a><span>›</span><span class="text-[#0F0F0F]">{{ $blog->category->name ?? 'Post' }}</span>
  </nav>
  <div class="mt-3 inline-flex items-center gap-2 text-[11px] font-bold tracking-widest uppercase px-3 py-1 rounded-full text-white" style="background:{{ $blog->category->color ?? '#2563EB' }}">{{ $blog->category->name ?? 'General' }} • {{ $blog->reading_minutes }} MIN • {{ $blog->views }} VIEWS</div>
  <h1 class="mt-3 text-[26px] sm:text-[34px] font-black tracking-tight leading-[1.05]">{{ $blog->title }}</h1>
  <div class="mt-2 text-[13px] font-medium text-[#7A7A78] flex flex-wrap gap-3 items-center">
    <span class="inline-flex items-center gap-2"><img src="https://i.pravatar.cc/100?img=68" class="w-6 h-6 rounded-full"> {{ $blog->author->name ?? 'QuickContent Team' }}</span>
    <span>•</span><span>{{ $blog->published_at?->format('M d, Y') }}</span>
    @if($blog->tags)<span>•</span><span>{{ is_array($blog->tags) ? implode(', ', $blog->tags) : $blog->tags }}</span>@endif
  </div>
  @if($blog->excerpt)<p class="mt-4 text-[15px] leading-6 font-medium text-[#2b2b2b] bg-[#F8F8F7] border border-[#E8E8E6] rounded-2xl p-4">{{ $blog->excerpt }}</p>@endif
  <img src="{{ filter_var($blog->cover, FILTER_VALIDATE_URL) ? $blog->cover : asset('storage/'.$blog->cover) }}" alt="{{ $blog->cover_alt ?: $blog->title }}" class="mt-6 w-full h-[380px] object-cover rounded-2xl border border-[#E8E8E6]">

  {{-- AEO speakable --}}
  <div class="mt-6 prose max-w-none" id="speakable">
    {!! $blog->content !!}
  </div>

  @if(!empty($blog->faq_json) && is_array($blog->faq_json))
  <div class="mt-8 bg-[#F8F8F7] border border-[#E8E8E6] rounded-2xl p-5">
    <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Quick Answers (AEO)</div>
    @foreach($blog->faq_json as $fq)
      <div class="mt-3"><div class="text-[14px] font-bold">{{ $fq['q'] ?? $fq['question'] ?? '' }}</div><div class="text-[13px] leading-6 text-[#2b2b2b]">{{ $fq['a'] ?? $fq['answer'] ?? '' }}</div></div>
    @endforeach
  </div>
  <script type="application/ld+json">{!! json_encode(['@context'=>'https://schema.org','@type'=>'FAQPage','mainEntity'=> collect($blog->faq_json)->map(fn($f)=>['@type'=>'Question','name'=>$f['q']??$f['question'],'acceptedAnswer'=>['@type'=>'Answer','text'=>$f['a']??$f['answer']]])->values()->toArray()], JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE) !!}</script>
  @endif

  <div class="mt-8 flex flex-wrap gap-3">
    <a href="{{ route('business.home') }}" class="h-10 px-6 rounded-full bg-[#0F0F0F] text-white font-extrabold text-[13px] inline-flex items-center">Get this done in 1 day — From ₹1,299</a>
    <a href="{{ route('blog.index') }}" class="h-10 px-6 rounded-full border border-[#E8E8E6] font-bold text-[13px] inline-flex items-center">More playbooks</a>
  </div>

  @if($related->count())
  <div class="mt-10">
    <div class="text-[14px] font-black">Related playbooks</div>
    <div class="mt-3 grid sm:grid-cols-3 gap-4">
      @foreach($related as $r)
      <a href="{{ route('blog.show',$r->slug) }}" class="border border-[#E8E8E6] rounded-2xl overflow-hidden hover:border-[#0F0F0F]"><img src="{{ filter_var($r->cover, FILTER_VALIDATE_URL) ? $r->cover : asset('storage/'.$r->cover) }}" class="h-[120px] w-full object-cover"><div class="p-3"><div class="text-[12px] font-bold line-clamp-2">{{ $r->title }}</div><div class="text-[11px] text-[#7A7A78] font-semibold">{{ $r->reading_minutes }} min</div></div></a>
      @endforeach
    </div>
  </div>
  @endif
</article>

<footer class="border-t border-[#E8E8E6] mt-10 py-6 text-center text-[12px] font-semibold text-[#7A7A78]">© {{ date('Y') }} QuickContent • JSON-LD Article + Breadcrumbs + FAQ (Hostinger shared ready)</footer>
</body>
</html>
