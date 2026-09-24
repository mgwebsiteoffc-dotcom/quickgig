@extends('layouts.app')
@section('content')
<style>
  .section{padding:18px 16px 0}
  @media(min-width:900px){.section{padding:20px 24px 0}}
  .section-head{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:12px;gap:12px}
  .section-title{font-size:13.5px;font-weight:700;display:flex;align-items:center;gap:7px}
  .section-title svg{width:14px;height:14px;color:var(--muted)}
  .section-link{font-size:12px;font-weight:600;color:var(--muted);cursor:pointer}
  .section-sub{font-size:11.5px;color:var(--muted);font-weight:500;margin-top:2px}
  .search{height:42px;background:var(--card);border:1px solid var(--line);border-radius:12px;display:flex;align-items:center;gap:10px;padding:0 12px}
  .search input{flex:1;border:none;background:transparent;outline:none;font-size:13.5px;font-weight:500}
  .search input::placeholder{color:#9A9A96}
  .search-action{font-size:11px;font-weight:700;background:var(--text);color:white;padding:6px 10px;border-radius:8px;cursor:pointer;display:flex;align-items:center;gap:5px;white-space:nowrap}
  .chips{display:flex;gap:8px;overflow-x:auto;scrollbar-width:none;padding-top:12px}
  .chips::-webkit-scrollbar{display:none}
  .chip{font-size:12px;font-weight:600;padding:7px 12px;border-radius:20px;white-space:nowrap;cursor:pointer;border:1px solid var(--line);background:var(--card);color:var(--muted)}
  .chip.active{background:var(--text);color:white;border-color:var(--text)}
  .creator-row{display:flex;gap:12px;overflow-x:auto;scrollbar-width:none;padding-top:4px}
  .creator-row::-webkit-scrollbar{display:none}
  .creator-card{min-width:124px;background:var(--card);border:1px solid var(--line);border-radius:14px;padding:12px;cursor:pointer;flex-shrink:0}
  .creator-top{display:flex;align-items:center;gap:9px}
  .creator-av{width:40px;height:40px;border-radius:50%;overflow:hidden;position:relative;border:1px solid var(--line);flex-shrink:0}
  .creator-av img{width:100%;height:100%;object-fit:cover}
  .creator-check{position:absolute;bottom:-1px;right:-1px;width:16px;height:16px;min-width:16px;aspect-ratio:1/1;background:var(--blue);border:2px solid white;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;color:white}
  .creator-name{font-size:12.5px;font-weight:700;line-height:1.1;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .creator-handle{font-size:11px;color:var(--muted);font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
  .creator-stats{display:flex;gap:8px;margin-top:10px;padding-top:10px;border-top:1px solid var(--line2)}
  .creator-stat{flex:1;text-align:center}
  .creator-stat-num{font-size:12px;font-weight:750}
  .creator-stat-label{font-size:9px;color:var(--muted);font-weight:650;letter-spacing:0.04em;text-transform:uppercase;margin-top:1px}
  .banner{margin-top:14px;background:var(--card);border:1px solid var(--line);border-radius:12px;padding:12px 14px;display:flex;align-items:center;justify-content:space-between;cursor:pointer}
  .banner-left{display:flex;gap:10px;align-items:center}
  .banner-icon{width:32px;height:32px;border-radius:10px;background:var(--text);color:white;display:flex;align-items:center;justify-content:center;flex-shrink:0}
  .banner-title{font-size:12.5px;font-weight:700}
  .banner-desc{font-size:11px;color:var(--muted);font-weight:500;margin-top:1px}
  .banner-arrow{width:28px;height:28px;border-radius:50%;background:var(--bg);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;flex-shrink:0}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-top:12px}
  @media(min-width:900px){.grid{grid-template-columns:repeat(3,1fr);gap:14px}}
  @media(min-width:1200px){.grid{grid-template-columns:repeat(4,1fr)}}
  .card{background:var(--card);border:1px solid var(--line);border-radius:14px;overflow:hidden;cursor:pointer;display:flex;flex-direction:column}
  .card-img{height:112px;background:#ECECEA;overflow:hidden;position:relative;flex-shrink:0}
  .card-img img{width:100%;height:100%;object-fit:cover}
  .card-badge{position:absolute;top:8px;left:8px;font-size:10px;font-weight:700;background:rgba(15,15,15,0.86);color:white;padding:4px 7px;border-radius:6px}
  .card-play{position:absolute;left:50%;top:50%;transform:translate(-50%,-50%);width:32px;height:32px;background:rgba(255,255,255,0.96);border-radius:50%;display:flex;align-items:center;justify-content:center;box-shadow:0 2px 10px rgba(0,0,0,0.15)}
  .card-body{padding:10px 11px 11px;display:flex;flex-direction:column;flex:1}
  .card-title{font-size:12.5px;font-weight:650;line-height:1.35;min-height:34px;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
  .card-creator{display:flex;align-items:center;gap:6px;margin-top:8px}
  .card-creator img{width:16px;height:16px;border-radius:50%;object-fit:cover;flex-shrink:0}
  .card-creator span{font-size:11px;color:var(--muted);font-weight:500;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;flex:1}
  .card-creator .tick{width:14px;height:14px;min-width:14px;aspect-ratio:1/1;border-radius:50%;background:var(--blue);color:white;display:inline-flex;align-items:center;justify-content:center;flex:0 0 14px;padding:0;line-height:0}
  .card-bottom{display:flex;align-items:baseline;justify-content:space-between;margin-top:8px}
  .card-price{font-size:13.5px;font-weight:750}
  .card-rating{font-size:11px;font-weight:650;display:flex;align-items:center;gap:3px}
  .card-meta{font-size:11px;color:var(--muted);font-weight:500;margin-top:4px;display:flex;align-items:center;gap:6px}
  .card-add{margin-top:10px;width:100%;height:32px;border-radius:9px;background:var(--text);color:white;border:none;font-size:12px;font-weight:650;cursor:pointer;display:flex;align-items:center;justify-content:center;gap:5px}
  .reorder{background:var(--card);border:1px solid var(--line);border-radius:12px;padding:12px;display:flex;align-items:center;gap:12px;cursor:pointer;margin-top:12px}
  .reorder-img{width:42px;height:42px;border-radius:10px;overflow:hidden;flex-shrink:0}
  .reorder-img img{width:100%;height:100%;object-fit:cover}
  .reorder-title{font-size:12.5px;font-weight:650}
  .reorder-sub{font-size:11px;color:var(--muted);font-weight:500;margin-top:2px}
  .reorder-cta{margin-left:auto;font-size:12px;font-weight:700;background:var(--text);color:white;padding:7px 12px;border-radius:20px;white-space:nowrap}
  .sheet{position:fixed;bottom:0;left:0;right:0;background:var(--card);border-radius:20px 20px 0 0;transform:translateY(100%);transition:transform 0.32s;max-height:90vh;max-width:480px;margin:0 auto;z-index:41;display:flex;flex-direction:column}
  .sheet.show{transform:none}
  @media(min-width:900px){.sheet{left:50%;right:auto;bottom:auto;top:50%;transform:translate(-50%,-50%) scale(0.96);border-radius:20px;max-height:85vh;width:92%;opacity:0;pointer-events:none}.sheet.show{transform:translate(-50%,-50%) scale(1);opacity:1;pointer-events:auto}}
  .overlay{position:fixed;inset:0;background:rgba(15,15,15,0.32);backdrop-filter:blur(4px);z-index:40;display:none}
  .overlay.show{display:block}
</style>

<div style="padding:14px 16px 0">
  <div class="search">
    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" style="color:var(--muted)"><circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/></svg>
    <input id="searchInput" placeholder="Search services or type '3 reels for launch'..." onkeydown="if(event.key==='Enter') handlePrompt(this.value)">
    <div class="search-action" onclick="handlePrompt(document.getElementById('searchInput').value)"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3l2 5 5 2-5 2-2 5-2-5-5-2 5-2 2-5z"/></svg> Prompt Team</div>
  </div>
  <div class="chips" id="chips">
    <div class="chip active" data-cat="all" onclick="filterCat('all', this)">All</div><div class="chip" data-cat="Reel" onclick="filterCat('Reel', this)">Reel</div><div class="chip" data-cat="Thumbnail" onclick="filterCat('Thumbnail', this)">Thumbnail</div><div class="chip" data-cat="AI Video" onclick="filterCat('AI Video', this)">AI Video</div><div class="chip" data-cat="UGC Video" onclick="filterCat('UGC Video', this)"><span style="background:var(--blue);color:white;padding:1px 5px;border-radius:6px;font-size:10px">NEW</span> UGC</div><div class="chip" data-cat="Barter Collab" onclick="filterCat('Barter Collab', this)"><span style="background:#FFC300;color:var(--text);padding:1px 5px;border-radius:6px;font-size:10px">Barter</span> Collab</div>
  </div>
  <div class="text-[11px] font-semibold" style="color:var(--muted);margin-top:6px">UGC = creators film with your product • Barter = product-for-post (₹0 + product value) — same flow: describe → assigned ~12 min → tracking, easy like ordering food → approve</div>
</div>

<div class="section">
  <div class="section-head">
    <div>
      <div class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 2l2.4 7.2H22l-6.2 4.5 2.4 7.2L12 16 5.8 20.9 8.2 13.7 2 9.2h7.6z"/></svg> Creator partners — verified</div>
      <div class="section-sub">Businesses like yours hire these creators daily</div>
    </div>
    <div class="section-link">View all</div>
  </div>
  <div class="creator-row">
    @foreach($creators as $c)
    <div class="creator-card">
      <div class="creator-top">
        <div class="creator-av"><img src="{{ $c['img'] }}" alt=""><span class="creator-check"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5l10-10"/></svg></span></div>
        <div><div class="creator-name">{{ $c['name'] }}</div><div class="creator-handle">{{ $c['handle'] }}</div></div>
      </div>
      <div class="creator-stats"><div class="creator-stat"><div class="creator-stat-num">{{ $c['followers'] }}</div><div class="creator-stat-label">Followers</div></div><div class="creator-stat"><div class="creator-stat-num">{{ $c['posts'] }}</div><div class="creator-stat-label">Posts</div></div></div>
    </div>
    @endforeach
  </div>
</div>

<div class="section">
  <div class="section-head">
    <div class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="12" r="3"/><path d="M12 2v3M12 19v3M2 12h3M19 12h3M4.9 4.9l2.1 2.1M16.9 16.9l2.1 2.1M4.9 19.1l2.1-2.1M16.9 7l2.1-2.1"/></svg> Available now</div>
    <div class="section-link" onclick="openChoosePro()">See all</div>
  </div>
  <div class="creator-row">
    @foreach($pros as $p)
    <div style="min-width:92px;background:var(--card);border:1px solid var(--line);border-radius:14px;padding:10px;text-align:center;cursor:pointer;flex-shrink:0" onclick="openChoosePro()">
      <div style="width:44px;height:44px;border-radius:50%;margin:0 auto;overflow:hidden;position:relative;border:1px solid var(--line)"><img src="{{ $p['img'] }}" style="width:100%;height:100%;object-fit:cover"><span style="position:absolute;bottom:0;right:0;width:11px;height:11px;border-radius:50%;border:2px solid white;background:{{ $p['avail']=='on' ? 'var(--green)' : '#EAB308' }};display:block"></span></div>
      <div style="font-size:11.5px;font-weight:700;margin-top:6px">{{ explode(' ', $p['name'])[0] }}</div><div style="font-size:10px;color:var(--muted)">{{ $p['handle'] }}</div><div style="font-size:10px;font-weight:700;margin-top:4px;background:{{ $p['avail']=='on' ? 'var(--green-bg)' : 'var(--bg)' }};color:{{ $p['avail']=='on' ? 'var(--green)' : 'var(--muted)' }};padding:2px 6px;border-radius:20px;display:inline-block;border:1px solid {{ $p['avail']=='on' ? 'var(--green-line)' : 'var(--line)' }}">{{ $p['label'] }}</div>
    </div>
    @endforeach
  </div>
  <div class="banner" onclick="handlePrompt('3 reels + thumbnails for {{ $current['company'] }} launch')">
    <div class="banner-left"><div class="banner-icon"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8"><path d="M12 3l2 5 5 2-5 2-2 5-2-5-5-2 5-2 2-5z"/></svg></div><div><div class="banner-title">Prompt a team for {{ $current['company'] }}</div><div class="banner-desc">Describe outcome → get team & fixed price in seconds</div></div></div>
    <div class="banner-arrow"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 18l6-6-6-6"/></svg></div>
  </div>
</div>

<div class="section" id="reorderWrap" style="display:none">
  <div class="section-head"><div class="section-title"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 12a9 9 0 1 1-2.6-6.4"/><path d="M21 3v7h-7"/></svg> Reorder for {{ $current['company'] }}</div></div>
  <div class="reorder"><div class="reorder-img"><img id="reorderImg" src="https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=200&q=80"></div><div><div class="reorder-title" id="reorderTitle">Engaging Talking-Head Reel</div><div class="reorder-sub" id="reorderSub">Last for {{ $current['company'] }} • Priya S. • ★5.0</div></div><div class="reorder-cta">Reorder</div></div>
</div>

<div class="section" id="bestSection">
  <div class="section-head"><div class="section-title">Best sellers <span style="font-weight:700;font-size:11px;background:var(--yellow);color:var(--text);padding:2px 7px;border-radius:20px;margin-left:6px">Reel • Thumbnail • AI • UGC • Barter</span></div><div class="section-link" onclick="filterCat('all', document.querySelector('.chip.active'))">View all</div></div>
  <div class="grid" id="serviceGrid">
    @foreach($services as $s)
    <div class="card" data-category="{{ $s['category'] ?? 'Reel' }}" data-price-type="{{ $s['price_type'] ?? 'paid' }}" onclick="openSheet('{{ $s['id'] }}')">
      <div class="card-img"><img src="{{ $s['img'] }}" loading="lazy"><span class="card-badge" style="{{ ($s['price_type'] ?? '')=='barter' ? 'background:#FFC300;color:#0F0F0F' : (($s['category']??'')=='UGC Video' ? 'background:var(--blue);color:white' : '') }}">{{ $s['badge'] ?? ($s['category'] ?? 'Best seller') }}</span><span class="card-play"><svg width="12" height="12" viewBox="0 0 24 24" fill="var(--text)"><path d="M8 5v14l11-7z"/></svg></span></div>
      <div class="card-body">
        <div class="card-title">{{ $s['title'] }}</div>
        <div class="text-[11px] font-semibold" style="color:var(--muted)">{{ $s['category'] ?? '' }} @if(isset($s['price_type']) && $s['price_type']=='barter') • <span style="background:#FFF7CC;border:1px solid #FFE27A;padding:1px 6px;border-radius:20px;font-size:10px;font-weight:800;color:#0F0F0F">Barter</span> @elseif(isset($s['price_type']) && $s['price_type']=='hybrid') • Hybrid @endif</div>
        <div class="card-creator"><img src="{{ $s['cimg'] }}"><span>{{ $s['creator'] }} • {{ $s['handle'] }}</span><span class="tick"><svg width="7" height="7" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5l10-10"/></svg></span></div>
        <div class="card-bottom"><span class="card-price">{{ $s['display_price'] ?? (isset($s['price']) ? '₹'.number_format($s['price']) : '—') }}</span><span class="card-rating"><svg width="11" height="11" viewBox="0 0 24 24" fill="var(--text)"><path d="M12 2l2.4 7.2H22l-6.2 4.5 2.4 7.2L12 16 5.8 20.9 8.2 13.7 2 9.2h7.6z"/></svg> {{ $s['rating'] }}</span></div>
        <div class="card-meta"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg> {{ $s['time'] }} <span>•</span> {{ $s['sold'] }} sold</div>
        <button class="card-add" onclick="event.stopPropagation();openSheet('{{ $s['id'] }}')">@if(($s['price_type']??'paid')=='barter')<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg> Collab for {{ explode(' ', $current['company'])[0] }} @else<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg> Add for {{ explode(' ', $current['company'])[0] }} @endif</button>
      </div>
    </div>
    @endforeach
  </div>
</div>

<div class="section" style="padding-bottom:20px">
  <div class="section-head"><div class="section-title"><span style="background:var(--blue);color:white;width:20px;height:20px;border-radius:50%;display:inline-grid;place-items:center;font-size:11px">★</span> UGC & Barter — same flow, new growth channel</div><div class="section-link" onclick="filterCat('UGC Video', document.querySelector('[data-cat=\'UGC Video\']'))">View UGC →</div></div>
  <div class="bg-white border border-line rounded-2xl p-4 flex flex-col sm:flex-row gap-4 items-start sm:items-center mb-4">
    <div class="flex-1"><div class="text-[13px] font-black">UGC Video — real people, real product</div><div class="text-[12px] font-medium" style="color:var(--muted)">A UGC creator films unboxing/review at home (30s). Same 12-min assign & live tracking, easy like ordering food. Brands send product, creator keeps it + delivers video.</div><div class="mt-2 text-[11px] font-bold inline-flex gap-2"><span class="px-2.5 py-1 rounded-full bg-blue-50 border border-blue-200 text-blue-700">Paid: ₹1,999</span><span class="px-2.5 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800">Barter: ₹0 + product ₹2k</span></div></div>
    <div class="flex-1"><div class="text-[13px] font-black">Barter Collab — product-for-post</div><div class="text-[12px] font-medium" style="color:var(--muted)">An influencer posts a Reel to 45K followers in exchange for your product. No cash (or hybrid). Admin sets barter_value + collab_terms per service.</div><div class="mt-2 text-[11px] font-bold">How: Business describes collab → matched influencer → ships product → creator posts → Approve. No escrow for pure barter.</div></div>
  </div>
  <div class="grid">
    @php $ugcServices = array_filter($services, fn($s)=>($s['category']??'')=='UGC Video' || ($s['category']??'')=='Barter Collab' || ($s['price_type']??'')=='barter'); if(empty($ugcServices)) $ugcServices = array_slice($services,0,3); @endphp
    @foreach(array_slice($ugcServices,0,3) as $s)
    <div class="card" data-category="{{ $s['category'] ?? '' }}" onclick="openSheet('{{ $s['id'] }}')">
      <div class="card-img"><img src="{{ $s['img'] }}"><span class="card-badge" style="{{ ($s['price_type']??'')=='barter'?'background:#FFC300;color:#0F0F0F':'background:var(--blue);color:white' }}">{{ $s['badge'] ?? (($s['category']??'')=='Barter Collab' ? 'Barter' : 'UGC') }}</span><span class="card-play"><svg width="12" height="12" viewBox="0 0 24 24" fill="var(--text)"><path d="M8 5v14l11-7z"/></svg></span></div>
      <div class="card-body">
        <div class="card-title">{{ $s['title'] }}</div>
        <div class="card-creator"><img src="{{ $s['cimg'] }}"><span>{{ $s['creator'] }} • {{ $s['handle'] }}</span><span class="tick"><svg width="7" height="7" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M5 12l5 5l10-10"/></svg></span></div>
        <div class="card-bottom"><span class="card-price">{{ $s['display_price'] ?? (isset($s['price'])?'₹'.number_format($s['price']):'—') }}</span><span class="card-rating"><svg width="11" height="11" viewBox="0 0 24 24" fill="var(--text)"><path d="M12 2l2.4 7.2H22l-6.2 4.5 2.4 7.2L12 16 5.8 20.9 8.2 13.7 2 9.2h7.6z"/></svg> {{ $s['rating'] }}</span></div>
        <div class="card-meta">{{ $s['category'] ?? 'UGC' }} <span>•</span> {{ $s['time'] }} <span>•</span> {{ $s['sold'] }} sold</div>
        <button class="card-add" onclick="event.stopPropagation();openSheet('{{ $s['id'] }}')">@if(($s['price_type']??'')=='barter') Collab free — ship product @else Add for {{ explode(' ', $current['company'])[0] }} @endif</button>
      </div>
    </div>
    @endforeach
  </div>
</div>

{{-- Sheet --}}
<div class="overlay" id="overlay" onclick="closeSheet()"></div>
<div class="sheet" id="sheet" style="background:white">
  <div style="width:32px;height:4px;background:var(--line);border-radius:10px;margin:10px auto 0"></div>
  <div style="flex:1;overflow-y:auto;padding-bottom:0" id="sheetContent">
    {{-- Filled by JS using Blade data --}}
  </div>
</div>

<script>
const services = @json($services);
const current = @json($current);
let sel = services[0];
function openSheet(id){
  sel = services.find(s=>s.id===id)||services[0];
  document.getElementById('sheetContent').innerHTML = `
    <div style="height:176px;background:#111;position:relative;overflow:hidden"><img src="${sel.img}" style="width:100%;height:100%;object-fit:cover"><div style="position:absolute;inset:0;background:linear-gradient(to top,rgba(0,0,0,0.5),transparent 55%)"></div><div style="position:absolute;bottom:12px;left:12px;display:flex;gap:6px"><span style="font-size:11px;font-weight:650;padding:5px 9px;border-radius:20px;background:rgba(255,255,255,0.96)">${sel.time}</span><span style="font-size:11px;font-weight:650;padding:5px 9px;border-radius:20px;background:rgba(15,15,15,0.78);color:white">3 pros available</span></div></div>
    <div style="padding:16px">
      <div style="font-size:17px;font-weight:800;line-height:1.25">${sel.title}</div>
      <div style="margin-top:8px;display:flex;gap:8px;align-items:center;font-size:12px;color:var(--muted)"><span>★ ${sel.rating} • 1,243 reviews</span><span style="font-size:11px;font-weight:700;background:var(--green-bg);color:var(--green);padding:3px 7px;border-radius:6px;border:1px solid var(--green-line)">${sel.sold} sold</span><span style="color:var(--green);font-weight:650;display:flex;align-items:center;gap:4px"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12l5 5l10-10"/></svg> Paisa Safe</span></div>
      <div style="display:flex;align-items:center;gap:10px;margin-top:14px;padding:12px;background:var(--bg);border:1px solid var(--line2);border-radius:12px">
        <div style="width:36px;height:36px;border-radius:50%;overflow:hidden;border:1px solid var(--line)"><img src="${sel.cimg}" style="width:100%;height:100%;object-fit:cover"></div>
        <div><div style="font-size:13px;font-weight:700;display:flex;gap:4px;align-items:center">${sel.creator} <span style="width:14px;height:14px;min-width:14px;aspect-ratio:1/1;border-radius:50%;background:var(--blue);display:inline-flex;align-items:center;justify-content:center"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5l10-10"/></svg></span></div><div style="font-size:11px;color:var(--muted)">${sel.handle} • Verified • For ${current.company}</div></div>
        <div style="margin-left:auto;font-size:12px;font-weight:650;background:white;border:1px solid var(--line);padding:7px 12px;border-radius:20px;cursor:pointer">View</div>
      </div>
      <form method="POST" action="{{ route('orders.store') }}" style="margin-top:16px">
        @csrf
        <input type="hidden" name="service_id" value="${sel.id}">
        <input type="hidden" name="company_id" value="${current.id}">
        <input type="hidden" name="turnaround" value="1 Day">
        <label style="font-size:11px;font-weight:700;letter-spacing:0.06em;text-transform:uppercase;color:var(--muted)">Brief for your company</label>
        <textarea name="brief" rows="3" style="width:100%;margin-top:8px;border:1px solid var(--line);border-radius:10px;padding:11px 12px;font-size:13.5px;background:var(--bg)" required>30 sec talking head for ${current.company}. Hook first 2 sec, gym b-roll, yellow captions.</textarea>
        <div style="margin-top:8px;font-size:11px;font-weight:700;color:var(--muted)">${sel.category || ''} ${sel.price_type==='barter' ? '• <span style=\'background:#FFF7CC;border:1px solid #FFE27A;padding:2px 7px;border-radius:20px\'>Barter: product value ₹'+(sel.barter_value||2000).toLocaleString('en-IN')+'</span> • Collab terms in service' : (sel.price_type==='hybrid' ? '• Hybrid: cash + product' : '')}</div>
        <div style="margin-top:12px;display:flex;gap:8px">
          <button type="submit" style="flex:1;height:44px;border-radius:12px;background:var(--yellow);border:none;font-size:13.5px;font-weight:750;cursor:pointer">${sel.price_type==='barter' ? 'Collab for '+current.company.split(' ')[0]+' — Barter (ship product) →' : 'Book for '+current.company.split(' ')[0]+' — '+(sel.display_price || ('₹'+(sel.price||0).toLocaleString('en-IN')))+' →'}</button>
        </div>
        <div style="font-size:11px;color:var(--muted);text-align:center;margin-top:6px">${sel.price_type==='barter' ? 'No escrow for pure barter • You ship product, creator posts' : 'Held in escrow • GST invoice to company'}</div>
      </form>
    </div>
  `;
  document.getElementById('overlay').classList.add('show');
  document.getElementById('sheet').classList.add('show');
}
function closeSheet(){document.getElementById('overlay').classList.remove('show');document.getElementById('sheet').classList.remove('show')}
function filterCat(cat, el){
  document.querySelectorAll('#chips .chip').forEach(c=>c.classList.remove('active'));
  if(el) el.classList.add('active');
  document.querySelectorAll('#serviceGrid .card').forEach(card=>{
    if(cat==='all') card.style.display='';
    else card.style.display = (card.dataset.category===cat ? '' : 'none');
  });
  // scroll to grid
  if(cat!=='all') document.getElementById('bestSection')?.scrollIntoView({behavior:'smooth', block:'start'});
}
function handlePrompt(v){ const prompt=v||`Launch for ${current.company} — 3 reels`; showToast('Prompt Team: “'+prompt+'” — team ₹6,144'); }
function showToast(m){const t=document.getElementById('toast');t.textContent=m;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2200)}
</script>
@endsection
