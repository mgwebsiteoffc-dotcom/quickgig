@extends('admin.layout')
@section('title', $creator->name)
@section('breadcrumb', 'Admin • Creators • '.$creator->handle)
@section('content')
<div class="max-w-[980px] mx-auto space-y-4">
  {{-- Header: check details --}}
  <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
    <div class="flex flex-wrap gap-5">
      <div class="relative shrink-0"><img src="{{ $creator->avatarUrl() }}" class="w-20 h-20 rounded-full object-cover border border-[#E8E8E6]"><span class="absolute -bottom-1 -right-1 w-7 h-7 rounded-full {{ $creator->is_verified ? 'bg-[#1D9BF0]' : 'bg-amber-400' }} text-white grid place-items-center border-2 border-white" style="aspect-ratio:1/1">@if($creator->is_verified)<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg>@else<span class="text-[11px] font-black text-ink">!</span>@endif</span></div>
      <div class="flex-1 min-w-[260px]">
        <div class="flex flex-wrap gap-2 items-center">
          <h1 class="text-[18px] font-black">{{ $creator->name }}</h1>
          <span class="text-[11px] font-black px-2.5 py-1 rounded-full border {{ $creator->profile_type==='ugc_creator' ? 'bg-blue-600 text-white border-blue-600' : ($creator->profile_type==='influencer' ? 'bg-amber-400 text-ink border-amber-400' : 'bg-ink text-white border-ink') }}">{{ $creator->profileLabel() }}</span>
          @if($creator->barter_available)<span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-green-50 border border-green-200 text-green-700">● Barter</span>@endif
          <span class="text-[11px] font-bold px-2.5 py-1 rounded-full {{ $creator->is_verified ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-amber-50 border border-amber-200 text-amber-800' }}">{{ $creator->is_verified ? 'Verified ✓' : 'Pending verify' }}</span>
          @if($creator->is_featured)<span class="text-[11px] font-bold px-2 py-1 rounded-full bg-amber-400 text-ink">Featured</span>@endif
          <span class="w-2 h-2 rounded-full {{ $creator->is_available ? 'bg-green-500' : 'bg-amber-400' }}"></span><span class="text-[11px] font-bold {{ $creator->is_available ? 'text-green-700' : 'text-amber-700' }}">{{ $creator->is_available ? 'Available' : 'Busy' }}</span>
        </div>
        <div class="text-[13px] font-semibold text-[#7A7A78]">{{ $creator->handle }} • {{ $creator->email }} • {{ $creator->phone }}</div>
        <div class="text-[13px] font-semibold">{{ $creator->headline }}</div>
        <div class="text-[13px] leading-6 mt-1">{{ $creator->bio }}</div>
        <div class="mt-2 flex flex-wrap gap-1.5">
          @if(is_array($creator->skills)) @foreach($creator->skills as $sk)<span class="text-[11px] font-bold bg-ink text-white px-2.5 py-1 rounded-full">{{ $sk }}</span>@endforeach @endif
          @if($creator->ugc_niches && is_array($creator->ugc_niches)) @foreach($creator->ugc_niches as $n)<span class="text-[11px] font-bold bg-blue-50 border border-blue-200 text-brand px-2 py-1 rounded-full">{{ $n }} UGC</span>@endforeach @endif
          @if($creator->barter_categories && is_array($creator->barter_categories)) @foreach($creator->barter_categories as $b)<span class="text-[11px] font-bold bg-green-50 border border-green-200 text-green-700 px-2 py-1 rounded-full">{{ $b }} barter</span>@endforeach @endif
        </div>
        @if($creator->followers_count)<div class="mt-2 text-[12px] font-bold">👥 {{ number_format($creator->followers_count) }} followers • Collab: {{ ucfirst($creator->collab_type) }} • ₹{{ number_format($creator->price_from) }} from</div>@endif
        <div class="mt-1 text-[11px] font-semibold text-[#7A7A78]">Joined {{ $creator->created_at->format('M d, Y') }} • {{ $creator->location }} • UPI: {{ $creator->upi_id ?: '—' }} • <a href="{{ $creator->portfolio_url }}" target="_blank" class="underline">{{ Str::limit($creator->portfolio_url ?: '', 30) }}</a></div>
        @if($creator->verification_notes)<div class="mt-2 bg-amber-50 border border-amber-200 rounded-xl p-3 text-[12px]"><b>Verification notes:</b> {{ $creator->verification_notes }}</div>@endif
        @if($creator->rejection_reason)<div class="mt-2 bg-red-50 border border-red-200 rounded-xl p-3 text-[12px]"><b>Rejection:</b> {{ $creator->rejection_reason }}</div>@endif
      </div>
      <div class="flex flex-col gap-2 shrink-0">
        <a href="{{ route('creator.public',$creator->id) }}" target="_blank" class="h-9 px-4 rounded-full border border-[#E8E8E6] bg-white font-bold text-[13px] inline-flex items-center justify-center">View public →</a>
        <a href="{{ route('admin.creators.index') }}" class="h-9 px-4 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] font-bold text-[13px] inline-flex items-center justify-center">← All creators</a>
      </div>
    </div>
  </div>

  <div class="grid lg:grid-cols-[1.2fr_0.8fr] gap-4">
    {{-- Left: portfolio + services + orders --}}
    <div class="space-y-4">
      <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
        <div class="flex items-center justify-between"><div class="font-black text-[14px]">Portfolio • {{ $creator->portfolio->count() }} items</div><span class="text-[11px] font-bold bg-[#F8F8F7] border border-[#E8E8E6] px-2.5 py-1 rounded-full">Shows on marketplace</span></div>
        <div class="mt-3 grid sm:grid-cols-2 gap-3">
          @forelse($creator->portfolio as $p)
            <div class="border border-[#E8E8E6] rounded-2xl overflow-hidden"><img src="{{ filter_var($p->cover, FILTER_VALIDATE_URL) ? $p->cover : asset('storage/'.$p->cover) }}" class="h-[120px] w-full object-cover"><div class="p-3"><div class="text-[13px] font-bold leading-tight">{{ $p->title }}</div><div class="text-[11px] font-semibold text-[#7A7A78]">{{ $p->category }} • {{ $p->views }} views</div><div class="text-[11px] text-[#7A7A78] line-clamp-2">{{ Str::limit($p->description ?: '', 80) }}</div></div></div>
          @empty
            <div class="col-span-2 text-center py-8 bg-[#F8F8F7] border border-dashed border-[#E8E8E6] rounded-2xl text-[13px] font-medium text-[#7A7A78]">No portfolio — creator hasn’t added yet.</div>
          @endforelse
        </div>
      </div>

      <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
        <div class="flex items-center justify-between"><div class="font-black text-[14px]">Services offered — managed by category</div><a href="{{ route('admin.services.index') }}?q={{ $creator->handle }}" class="text-[12px] font-bold text-brand hover:underline">Manage services →</a></div>
        <div class="mt-3 space-y-2">
          @forelse($services as $s)
            <div class="flex gap-3 p-3 rounded-xl border border-[#E8E8E6] hover:bg-[#F8F8F7]/50">
              <img src="{{ filter_var($s->cover, FILTER_VALIDATE_URL) ? $s->cover : ($s->cover ? asset('storage/'.$s->cover) : 'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=200&q=80') }}" class="w-14 h-14 rounded-xl object-cover border border-[#E8E8E6] shrink-0">
              <div class="flex-1 min-w-0"><div class="text-[13px] font-bold leading-tight">{{ $s->title }} <span class="ml-1 text-[10px] font-black px-2 py-0.5 rounded-full border {{ $s->price_type==='barter' ? 'bg-amber-50 border-amber-200 text-amber-800' : ($s->price_type==='hybrid' ? 'bg-blue-50 border-blue-200 text-brand' : 'bg-[#F8F8F7] border-[#E8E8E6]') }}">{{ strtoupper($s->price_type) }}</span></div><div class="text-[11px] font-semibold text-[#7A7A78]">{{ $s->category }} • {{ $s->profile_type }} • {{ $s->delivery_days }} days • {{ $s->displayPrice() }}</div><div class="text-[11px] text-[#7A7A78]">{{ Str::limit($s->description ?: '', 70) }}</div></div>
              <span class="text-[11px] font-bold px-2.5 py-1 rounded-full h-fit border {{ $s->is_active ? 'bg-green-50 border-green-200 text-green-700' : 'bg-[#F8F8F7] border-[#E8E8E6] text-[#7A7A78]' }}">{{ $s->is_active ? 'Active' : 'Hidden' }}</span>
            </div>
          @empty
            <div class="text-center py-6 text-[13px] font-medium text-[#7A7A78] bg-[#F8F8F7] border border-dashed rounded-xl">No services yet — add from Services → Create (pick UGC / Barter / Reel).</div>
          @endforelse
        </div>
      </div>

      <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
        <div class="font-black text-[14px]">Recent orders</div>
        <div class="mt-3 divide-y divide-[#F0F0EE]">
          @forelse($orders as $o)
            <div class="flex justify-between py-2 text-[13px]"><div><b>{{ $o->uid }}</b> • {{ $o->company->name ?? 'Company' }} • {{ $o->service->title ?? '' }}</div><span class="font-bold capitalize">{{ $o->status }}</span></div>
          @empty
            <div class="text-[13px] text-[#7A7A78]">No orders yet.</div>
          @endforelse
        </div>
      </div>
    </div>

    {{-- Right: verification actions --}}
    <div class="space-y-4">
      <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
        <div class="font-black text-[14px]">Verify / Check details</div>
        <div class="text-[11px] font-semibold text-[#7A7A78]">Blue tick = 14×14 perfect circle. Check avatar, handle, skills, portfolio, followers before verifying.</div>

        <form method="POST" action="{{ route('admin.creators.verify',$creator->id) }}" class="mt-4 space-y-3">@csrf
          <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Verification notes (visible to admin only)</label><textarea name="verification_notes" rows="2" placeholder="Checked ID, portfolio links valid, 3 sample videos OK..." class="mt-1 w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">{{ $creator->verification_notes }}</textarea></div>
          @if(!$creator->is_verified)
            <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Rejection reason (if rejecting)</label><input name="rejection_reason" placeholder="Poor portfolio, etc." value="{{ $creator->rejection_reason }}" class="mt-1 w-full h-9 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
          @endif
          <button class="w-full h-10 rounded-full font-bold text-[13px] {{ $creator->is_verified ? 'bg-white border-2 border-[#E8E8E6] text-ink' : 'bg-amber-400 text-ink border-2 border-amber-400' }}">{{ $creator->is_verified ? 'Unverify — remove blue tick' : 'Verify ✓ — give blue tick' }}</button>
        </form>

        <div class="mt-3 grid grid-cols-2 gap-2">
          <form method="POST" action="{{ route('admin.creators.availability',$creator->id) }}">@csrf<button class="w-full h-9 rounded-full border border-[#E8E8E6] bg-white font-bold text-[12px]">{{ $creator->is_available ? 'Set Busy' : 'Set Available' }}</button></form>
          <form method="POST" action="{{ route('admin.creators.featured',$creator->id) }}">@csrf<button class="w-full h-9 rounded-full border font-bold text-[12px] {{ $creator->is_featured ? 'bg-ink text-white border-ink' : 'bg-white border-[#E8E8E6]' }}">{{ $creator->is_featured ? '★ Featured' : 'Make Featured' }}</button></form>
        </div>
      </div>

      <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
        <div class="font-black text-[14px]">Profile type & collab — dynamic</div>
        <div class="text-[11px] font-semibold text-[#7A7A78]">Controls which services this creator gets matched for. UGC → UGC Video, Influencer → Barter Collab.</div>
        <form method="POST" action="{{ route('admin.creators.profileType',$creator->id) }}" class="mt-4 space-y-3">@csrf
          <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Profile Type</label><select name="profile_type" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold">
            @foreach(\App\Models\Creator::PROFILE_TYPES as $k=>$v)<option value="{{ $k }}" {{ $creator->profile_type==$k?'selected':'' }}>{{ $v }}</option>@endforeach
          </select></div>
          <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Collab Type</label><select name="collab_type" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold"><option value="paid" {{ $creator->collab_type=='paid'?'selected':'' }}>Paid</option><option value="barter" {{ $creator->collab_type=='barter'?'selected':'' }}>Barter</option><option value="both" {{ $creator->collab_type=='both'?'selected':'' }}>Both</option></select></div>
          <label class="flex items-center gap-2 text-[13px] font-bold"><input type="checkbox" name="barter_available" value="1" {{ $creator->barter_available?'checked':'' }}> Open to Barter</label>
          <button class="w-full h-10 rounded-full bg-ink text-white font-bold text-[13px]">Update type → re-categorize services</button>
        </form>
      </div>

      <form method="POST" action="{{ route('admin.creators.destroy',$creator->id) }}" onsubmit="return confirm('Delete creator? This cannot be undone.')" class="bg-red-50 border border-red-200 rounded-2xl p-4">@csrf @method('DELETE')<button class="w-full h-9 rounded-full bg-white border border-red-200 text-red-600 font-bold text-[13px]">Delete creator permanently</button></form>
    </div>
  </div>
</div>
@endsection
