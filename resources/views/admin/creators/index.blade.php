@extends('admin.layout')
@section('title','Creators')
@section('breadcrumb','Admin • Creators • Verify & Manage')
@section('content')
{{-- Filters — profile type + barter + verification + search — dynamic --}}
<form method="GET" class="bg-white border border-[#E8E8E6] rounded-2xl p-4 space-y-3">
  <div class="flex flex-wrap gap-2 items-center">
    <div class="flex gap-1.5 flex-wrap">
      @foreach(['all'=>'All','available'=>'Available','pending'=>'Pending Verify','verified'=>'Verified','barter'=>'Barter'] as $k=>$v)
        <a href="?filter={{ $k }}&profile_type={{ $profile ?? 'all' }}{{ $q ? '&q='.$q : '' }}" class="px-3.5 py-1.5 rounded-full text-[13px] font-bold border {{ ($filter ?? 'all')==$k ? 'bg-[#0F0F0F] text-white border-[#0F0F0F]' : 'bg-white border-[#E8E8E6] hover:bg-[#F8F8F7]' }}">{{ $v }}</a>
      @endforeach
    </div>
    <div class="ml-auto flex gap-2">
      <select name="profile_type" onchange="this.form.submit()" class="h-9 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[12px] font-bold">
        <option value="all" {{ ($profile ?? 'all')=='all' ? 'selected':'' }}>All types</option>
        @foreach($profileTypes ?? [] as $rk=>$rv)
          <option value="{{ $rk }}" {{ ($profile ?? '')==$rk ? 'selected':'' }}>{{ $rv }}</option>
        @endforeach
      </select>
    </div>
  </div>
  <div class="flex gap-2">
    <input name="q" value="{{ $q }}" placeholder="Search name, handle, email, skill..." class="flex-1 h-10 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
    <button class="h-10 px-5 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Search</button>
    @if($q || ($filter??'all')!='all' || ($profile??'all')!='all')<a href="{{ route('admin.creators.index') }}" class="h-10 px-4 rounded-full border border-[#E8E8E6] bg-white font-bold text-[13px] inline-flex items-center">Clear</a>@endif
  </div>
  <div class="text-[11px] font-semibold text-[#7A7A78] flex flex-wrap gap-2">
    <span>Profile type decides matching:</span>
    <span class="px-2 py-1 rounded-full bg-[#F8F8F7] border border-[#E8E8E6]"><b>Video Editor</b> → Reels</span>
    <span class="px-2 py-1 rounded-full bg-blue-50 border border-blue-200 text-brand"><b>UGC Creator</b> → UGC Video</span>
    <span class="px-2 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800"><b>Influencer</b> → Barter Collab</span>
  </div>
</form>

@if(isset($isDb) && $isDb)
  {{-- Real DB --}}
  <div class="mt-4 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($creators as $c)
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4 hover:shadow-sm transition">
      <div class="flex gap-3">
        <div class="relative shrink-0"><img src="{{ $c->avatarUrl() }}" class="w-14 h-14 rounded-full object-cover border border-[#E8E8E6]"><span class="absolute -bottom-1 -right-1 w-[20px] h-[20px] rounded-full {{ $c->is_verified ? 'bg-[#1D9BF0]' : 'bg-amber-400' }} text-white grid place-items-center border-2 border-white" style="aspect-ratio:1/1">@if($c->is_verified)<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>@else<span class="text-[10px] font-black text-[#0F0F0F]">!</span>@endif</span></div>
        <div class="min-w-0 flex-1">
          <div class="text-[14px] font-black leading-tight flex items-center gap-1.5">{{ $c->name }} <span class="w-2 h-2 rounded-full {{ $c->is_available ? 'bg-green-500' : 'bg-amber-400' }}"></span></div>
          <div class="text-[11px] font-semibold text-[#7A7A78]">{{ $c->handle }} • {{ $c->email }}</div>
          <div class="mt-1 flex flex-wrap gap-1.5">
            <span class="text-[10px] font-black px-2 py-1 rounded-full border tracking-wide {{ $c->profile_type==='ugc_creator' ? 'bg-blue-600 text-white border-blue-600' : ($c->profile_type==='influencer' ? 'bg-amber-400 text-[#0F0F0F] border-amber-400' : ($c->profile_type==='hybrid' ? 'bg-[#0F0F0F] text-white border-[#0F0F0F]' : 'bg-[#F8F8F7] border-[#E8E8E6]')) }}">{{ $c->profileLabel() }}</span>
            @if($c->barter_available)<span class="text-[10px] font-bold px-2 py-1 rounded-full bg-green-50 border border-green-200 text-green-700">● Barter</span>@endif
            <span class="text-[10px] font-bold px-2 py-1 rounded-full {{ $c->is_verified ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-amber-50 border border-amber-200 text-amber-800' }}">{{ $c->is_verified ? 'Verified' : 'Pending' }}</span>
            @if($c->is_featured)<span class="text-[10px] font-bold px-2 py-1 rounded-full bg-amber-400 text-[#0F0F0F]">Featured</span>@endif
          </div>
        </div>
      </div>
      <div class="mt-2 text-[11px] font-medium text-[#7A7A78] line-clamp-1">{{ is_array($c->skills) ? implode(' • ', array_slice($c->skills,0,3)) : $c->skills }} @if($c->ugc_niches && is_array($c->ugc_niches)) • UGC: {{ implode(',', array_slice($c->ugc_niches,0,2)) }} @endif</div>
      @if($c->followers_count)<div class="text-[11px] font-bold">👥 {{ number_format($c->followers_count) }} followers • {{ $c->collab_type }}</div>@endif
      <div class="mt-3 grid grid-cols-3 gap-2 text-center">
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl py-2"><div class="text-[13px] font-black">{{ $c->orders_count }}</div><div class="text-[10px] font-bold tracking-widest uppercase text-[#7A7A78]">Orders</div></div>
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl py-2"><div class="text-[13px] font-black">★ {{ $c->rating }}</div><div class="text-[10px] font-bold tracking-widest uppercase text-[#7A7A78]">Rating</div></div>
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl py-2"><div class="text-[11px] font-bold">{{ $c->created_at->format('M d') }}</div><div class="text-[10px] font-bold tracking-widest uppercase text-[#7A7A78]">Joined</div></div>
      </div>
      <div class="mt-3 flex gap-2 flex-wrap">
        <a href="{{ route('admin.creators.show',$c->id) }}" class="flex-1 h-8 rounded-full bg-ink text-white font-bold text-[12px] grid place-items-center">Check details →</a>
        <form method="POST" action="{{ route('admin.creators.verify',$c->id) }}">@csrf<button class="h-8 px-3 rounded-full border font-bold text-[12px] {{ $c->is_verified ? 'bg-white border-[#E8E8E6]' : 'bg-amber-400 border-amber-400 text-ink' }}">{{ $c->is_verified ? 'Unverify' : 'Verify ✓' }}</button></form>
      </div>
      <div class="mt-2 flex gap-1.5">
        <form method="POST" action="{{ route('admin.creators.availability',$c->id) }}">@csrf<button class="flex-1 h-7 rounded-full border border-[#E8E8E6] bg-white font-bold text-[11px]">{{ $c->is_available ? 'Set Busy' : 'Set Available' }}</button></form>
        <form method="POST" action="{{ route('admin.creators.featured',$c->id) }}">@csrf<button class="flex-1 h-7 rounded-full border font-bold text-[11px] {{ $c->is_featured ? 'bg-amber-400 border-amber-400 text-ink' : 'bg-white border-[#E8E8E6]' }}">{{ $c->is_featured ? '★ Featured' : 'Feature' }}</button></form>
      </div>
    </div>
    @empty
      <div class="col-span-3 text-center py-12 bg-white border border-dashed border-[#E8E8E6] rounded-2xl"><div class="font-black">No creators found</div><div class="text-[13px] text-[#7A7A78] font-medium">Try clearing the filters, or invite a creator to sign up.</div></div>
    @endforelse
  </div>
  <div class="mt-4">{{ $creators->links() }}</div>
@else
  {{-- Fallback dummy --}}
  <div class="mt-4 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($creators as $c)
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4">
      <div class="flex gap-3"><div class="relative shrink-0"><img src="{{ $c['img'] }}" class="w-14 h-14 rounded-full object-cover border border-[#E8E8E6]"><span class="absolute -bottom-1 -right-1 w-[20px] h-[20px] rounded-full {{ $c['verified'] ? 'bg-[#1D9BF0]' : 'bg-amber-400' }} text-white grid place-items-center border-2 border-white">@if($c['verified'])<svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>@else<span class="text-[10px] font-black text-ink">!</span>@endif</span></div><div class="min-w-0 flex-1"><div class="text-[14px] font-black">{{ $c['name'] }}</div><div class="text-[11px] font-semibold text-[#7A7A78]">{{ $c['handle'] }} • {{ $c['email'] }}</div><div class="text-[11px] font-medium text-[#7A7A78]">{{ $c['skills'] }}</div></div></div>
      <div class="mt-3 flex gap-2"><a href="{{ route('admin.creators.index') }}" class="flex-1 h-8 rounded-full bg-ink text-white font-bold text-[12px] grid place-items-center">Check details →</a></div>
    </div>
    @endforeach
  </div>
@endif
@endsection
