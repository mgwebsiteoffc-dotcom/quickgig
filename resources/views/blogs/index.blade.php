<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('components.seo', ['seo'=>$seo])
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>*{font-family:Inter,sans-serif}</style>
</head>
<body class="bg-white text-[#0F0F0F] antialiased">
<header class="sticky top-0 z-40 bg-white/90 backdrop-blur border-b border-[#E8E8E6]">
  <div class="max-w-[1160px] mx-auto px-4 sm:px-6 h-[64px] flex items-center justify-between gap-4">
    <a href="{{ route('landing') }}" class="flex items-center gap-3">
      <div class="w-9 h-9 rounded-xl bg-[#0F0F0F] text-white grid place-items-center font-black text-[13px]">QC</div>
      <div class="leading-none"><div class="font-extrabold text-[14px]">QuickContent</div><div class="text-[11px] font-semibold text-[#7A7A78]">Blog • Playbooks</div></div>
    </a>
    <nav class="hidden md:flex items-center gap-5 text-[13px] font-semibold text-[#7A7A78]">
      <a href="{{ route('landing') }}" class="hover:text-[#0F0F0F]">Home</a>
      <a href="{{ route('business.home') }}" class="hover:text-[#0F0F0F]">Marketplace</a>
      <a href="{{ route('blog.index') }}" class="text-[#0F0F0F]">Blog</a>
    </nav>
    <a href="{{ route('business.home') }}" class="h-9 px-5 rounded-full bg-[#2563EB] text-white font-bold text-[13px] inline-flex items-center">Hire a Pro</a>
  </div>
</header>

<div class="max-w-[1160px] mx-auto px-4 sm:px-6 py-8">
  <div class="flex flex-wrap items-end justify-between gap-4">
    <div>
      <div class="inline-flex items-center gap-2 text-[11px] font-bold tracking-widest uppercase bg-blue-50 border border-blue-100 text-[#2563EB] px-3 py-1 rounded-full">AEO • SEO • Hostinger-ready SOPs</div>
      <h1 class="mt-2 text-[28px] sm:text-[36px] font-black tracking-tight leading-none">Playbooks you can ship tomorrow.</h1>
      <p class="mt-2 text-[14px] leading-6 text-[#7A7A78] font-medium max-w-[640px]">No fluff. SOPs from verified pros — retention cuts, CTR thumbs, Veo 3 UGC ads. Each post has JSON-LD (Article + FAQ) for answer engines.</p>
    </div>
    <form method="GET" class="flex gap-2 w-full sm:w-auto">
      <input name="q" value="{{ $q }}" placeholder="Search — e.g. retention, CTR, Veo" class="flex-1 sm:w-[280px] h-10 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
      <button class="h-10 px-5 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Search</button>
    </form>
  </div>

  <div class="mt-6 flex flex-wrap gap-2">
    <a href="{{ route('blog.index') }}" class="px-3.5 py-1.5 rounded-full text-[13px] font-bold border {{ !$cat ? 'bg-[#0F0F0F] text-white border-[#0F0F0F]' : 'bg-white border-[#E8E8E6]' }}">All</a>
    @foreach($categories as $c)
      <a href="{{ route('blog.index',['category'=>$c->slug]) }}" class="px-3.5 py-1.5 rounded-full text-[13px] font-bold border {{ $cat==$c->slug ? 'bg-[#0F0F0F] text-white border-[#0F0F0F]' : 'bg-white border-[#E8E8E6]' }}">{{ $c->name }} <span class="opacity-60">({{ $c->blogs_count }})</span></a>
    @endforeach
  </div>

  @if($featured->count() && !$q && !$cat)
  <div class="mt-6 grid md:grid-cols-3 gap-4">
    @foreach($featured as $b)
    <a href="{{ route('blog.show',$b->slug) }}" class="group bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden hover:shadow-md transition">
      <img src="{{ filter_var($b->cover, FILTER_VALIDATE_URL) ? $b->cover : asset('storage/'.$b->cover) }}" alt="{{ $b->cover_alt ?: $b->title }}" class="h-[180px] w-full object-cover">
      <div class="p-4">
        <div class="text-[11px] font-bold tracking-widest uppercase" style="color:{{ $b->category->color ?? '#2563EB' }}">{{ $b->category->name ?? 'General' }} • {{ $b->reading_minutes }} min • {{ $b->views }} views</div>
        <div class="mt-1 text-[15px] font-black leading-tight group-hover:text-[#2563EB]">{{ $b->title }}</div>
        <div class="mt-1 text-[13px] leading-5 text-[#7A7A78] font-medium line-clamp-2">{{ $b->excerpt }}</div>
        <div class="mt-3 text-[12px] font-bold text-[#0F0F0F]">Read →</div>
      </div>
    </a>
    @endforeach
  </div>
  @endif

  <div class="mt-8 grid md:grid-cols-2 lg:grid-cols-3 gap-5">
    @forelse($blogs as $b)
    <a href="{{ route('blog.show',$b->slug) }}" class="bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden hover:border-[#0F0F0F] transition group">
      <img src="{{ filter_var($b->cover, FILTER_VALIDATE_URL) ? $b->cover : asset('storage/'.$b->cover) }}" alt="{{ $b->cover_alt ?: $b->title }}" class="h-[180px] w-full object-cover">
      <div class="p-4">
        <div class="flex items-center gap-2 text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]"><span class="w-2 h-2 rounded-full" style="background:{{ $b->category->color ?? '#2563EB' }}"></span>{{ $b->category->name ?? 'General' }} • {{ $b->reading_minutes }} min</div>
        <div class="mt-1 text-[14px] font-black leading-tight group-hover:text-[#2563EB]">{{ $b->title }}</div>
        <div class="mt-1 text-[12px] leading-5 text-[#7A7A78] font-medium line-clamp-2">{{ $b->excerpt }}</div>
        <div class="mt-3 flex items-center justify-between text-[11px] font-semibold text-[#7A7A78]"><span>{{ $b->published_at?->format('M d, Y') }}</span><span>{{ $b->views }} views</span></div>
      </div>
    </a>
    @empty
    <div class="col-span-3 text-center py-12 bg-[#F8F8F7] border border-[#E8E8E6] rounded-2xl"><div class="font-black">No posts yet</div><div class="text-[13px] text-[#7A7A78] font-medium">Admin → Blogs → Create your first post (Quill editor, Hostinger file uploads).</div></div>
    @endforelse
  </div>
  <div class="mt-6">{{ $blogs->links() }}</div>
</div>

<footer class="border-t border-[#E8E8E6] mt-8 py-6 text-center text-[12px] font-semibold text-[#7A7A78]">© {{ date('Y') }} QuickContent • <a href="{{ route('sitemap') }}" class="underline">Sitemap</a> • <a href="{{ route('robots') }}" class="underline">Robots</a> • JSON-LD + AEO ready</footer>
</body>
</html>
