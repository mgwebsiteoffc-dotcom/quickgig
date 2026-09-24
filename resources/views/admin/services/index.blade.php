@extends('admin.layout')
@section('title','Services — Dynamic')
@section('breadcrumb','Admin • Services • Dynamic by Profile Type')
@section('content')
<form method="GET" class="bg-white border border-[#E8E8E6] rounded-2xl p-4 space-y-3">
  <div class="flex flex-wrap gap-2 items-center">
    <div class="flex gap-1.5 flex-wrap">
      <a href="?category=all&profile_type={{ $profile }}&price_type={{ $priceType }}{{ $q? '&q='.$q : '' }}" class="px-3 py-1.5 rounded-full text-[12px] font-bold border {{ $cat=='all'?'bg-ink text-white border-ink':'bg-white border-line' }}">All</a>
      @foreach($categories as $c)
        <a href="?category={{ $c }}&profile_type={{ $profile }}&price_type={{ $priceType }}{{ $q? '&q='.$q : '' }}" class="px-3 py-1.5 rounded-full text-[12px] font-bold border {{ $cat==$c?'bg-ink text-white border-ink':'bg-white border-line hover:bg-bg' }}">{{ $c }}</a>
      @endforeach
    </div>
    <div class="ml-auto flex gap-2">
      <select name="profile_type" onchange="this.form.submit()" class="h-8 px-3 rounded-full border border-line bg-bg text-[12px] font-bold">
        <option value="all" {{ $profile=='all'?'selected':'' }}>All profile types</option>
        <option value="video_editor" {{ $profile=='video_editor'?'selected':'' }}>Video Editor</option>
        <option value="ugc_creator" {{ $profile=='ugc_creator'?'selected':'' }}>UGC Creator</option>
        <option value="influencer" {{ $profile=='influencer'?'selected':'' }}>Influencer (Barter)</option>
        <option value="designer" {{ $profile=='designer'?'selected':'' }}>Designer</option>
        <option value="hybrid" {{ $profile=='hybrid'?'selected':'' }}>Hybrid</option>
      </select>
      <select name="price_type" onchange="this.form.submit()" class="h-8 px-3 rounded-full border border-line bg-bg text-[12px] font-bold">
        <option value="all" {{ $priceType=='all'?'selected':'' }}>All pricing</option>
        <option value="paid" {{ $priceType=='paid'?'selected':'' }}>Paid</option>
        <option value="barter" {{ $priceType=='barter'?'selected':'' }}>Barter</option>
        <option value="hybrid" {{ $priceType=='hybrid'?'selected':'' }}>Hybrid</option>
      </select>
    </div>
  </div>
  <div class="flex gap-2">
    <input name="q" value="{{ $q }}" placeholder="Search title..." class="flex-1 h-10 px-3 rounded-full border border-line bg-bg text-[13px]">
    <button class="h-10 px-5 rounded-full bg-ink text-white font-bold text-[13px]">Search</button>
    <a href="{{ route('admin.services.create') }}" class="h-10 px-5 rounded-full bg-brand text-white font-bold text-[13px] inline-flex items-center gap-1">+ New Service</a>
  </div>
  <div class="text-[11px] font-semibold text-muted flex flex-wrap gap-1.5">
    <span>Flow same for all: business picks service → matched by <b>profile_type</b> → tracking, easy like ordering food → approve. Barter: price 0 + product value, no escrow.</span>
  </div>
</form>

@if(isset($isDb) && $isDb)
<div class="mt-4 bg-white border border-line rounded-2xl overflow-hidden">
  <div class="px-5 py-3 flex items-center justify-between border-b border-[#F0F0EE]"><div class="font-black text-[14px]">{{ $services->total() }} Services</div><div class="text-[11px] font-bold bg-bg border border-line px-2.5 py-1 rounded-full">Dynamic • {{ $cat }} • {{ $profile }} • {{ $priceType }}</div></div>
  <div class="divide-y divide-[#F0F0EE]">
    @forelse($services as $s)
    <div class="flex gap-4 p-4 hover:bg-bg/50">
      <img src="{{ filter_var($s->cover, FILTER_VALIDATE_URL) ? $s->cover : ($s->cover ? asset('storage/'.$s->cover) : 'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=200&q=80') }}" class="w-[84px] h-[64px] rounded-xl object-cover border border-line shrink-0">
      <div class="flex-1 min-w-0">
        <div class="text-[13px] font-black leading-tight">{{ $s->title }} <span class="ml-1 text-[10px] font-black px-2 py-0.5 rounded-full border {{ $s->is_active ? 'bg-green-50 border-green-200 text-green-700' : 'bg-bg border-line text-muted' }}">{{ $s->is_active ? 'Active' : 'Hidden' }}</span> <span class="text-[10px] font-bold px-2 py-0.5 rounded-full {{ $s->price_type==='barter' ? 'bg-amber-50 border border-amber-200 text-amber-800' : ($s->price_type==='hybrid' ? 'bg-blue-50 border border-blue-200 text-brand' : 'bg-ink text-white') }}">{{ strtoupper($s->price_type) }}</span></div>
        <div class="text-[11px] font-semibold text-muted">{{ $s->category }} • {{ $s->profile_type }} • {{ $s->delivery_days }} days • for {{ $s->creator->name ?? '—' }} ({{ $s->creator->profileLabel() ?? '' }})</div>
        <div class="mt-1 flex items-center gap-2"><span class="font-black text-[13px]">{{ $s->displayPrice() }}</span>@if($s->barter_value)<span class="text-[11px] font-semibold text-muted">• Barter value ₹{{ number_format($s->barter_value) }}</span>@endif<span class="text-[11px] text-muted">• ★ {{ $s->rating }} • {{ $s->sold_count }} sold</span></div>
        @if($s->deliverables)<div class="mt-1 text-[11px] font-medium text-muted">Deliver: {{ is_array($s->deliverables) ? implode(' • ', $s->deliverables) : $s->deliverables }}</div>@endif
      </div>
      <div class="flex flex-col gap-1.5 shrink-0">
        <a href="{{ route('admin.services.edit',$s->id) }}" class="h-8 px-3 rounded-full border border-line bg-white font-bold text-[12px] inline-flex items-center justify-center">Edit</a>
        <form method="POST" action="{{ route('admin.services.toggle',$s->id) }}">@csrf<button class="h-8 px-3 rounded-full border font-bold text-[12px] w-full {{ $s->is_active ? 'bg-white border-line' : 'bg-ink text-white border-ink' }}">{{ $s->is_active ? 'Hide' : 'Activate' }}</button></form>
        <form method="POST" action="{{ route('admin.services.destroy',$s->id) }}" onsubmit="return confirm('Delete?')">@csrf @method('DELETE')<button class="h-7 w-7 mx-auto rounded-full border border-red-200 bg-red-50 text-red-600 grid place-items-center text-[11px]">✕</button></form>
      </div>
    </div>
    @empty
      <div class="p-8 text-center"><div class="font-black">No services</div><div class="text-[13px] text-muted">Create your first: UGC Video or Barter Collab — dynamic, no code.</div></div>
    @endforelse
  </div>
  <div class="p-4 border-t border-[#F0F0EE]">{{ $services->links() }}</div>
</div>
@else
  <div class="mt-4 bg-white border border-line rounded-2xl p-8 text-center"><div class="font-black">Demo mode</div><div class="text-[13px] text-muted">Run <code class="bg-bg border px-1 py-0.5 rounded">php artisan migrate --seed</code> to see real services with UGC & Barter.</div></div>
@endif
@endsection
