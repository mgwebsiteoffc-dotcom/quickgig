@extends('admin.layout')
@section('title','Add Service — Dynamic')
@section('breadcrumb','Admin • Services • Create')
@section('content')
<div class="max-w-[760px] mx-auto">
<div class="bg-white border border-[#E8E8E6] rounded-2xl p-6">
  <div class="flex items-center justify-between"><div><div class="font-black text-[16px]">Add Service — dynamic by profile type</div><div class="text-[12px] font-medium text-[#7A7A78]">Appears instantly in marketplace. Same flow (assign in ~12 min → tracking, easy like ordering food → approve). For Barter: set price 0 + barter value + collab terms, creator matched by profile_type.</div></div><a href="{{ route('admin.services.index') }}" class="h-9 px-4 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] font-bold text-[13px] inline-flex items-center">← Back</a></div>

  <form method="POST" action="{{ route('admin.services.store') }}" enctype="multipart/form-data" class="mt-6 space-y-4" onsubmit="if(this.price_type.value==='barter'){ this.price.value=this.price.value||0; }">
    @csrf
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Title *</label><input name="title" required placeholder="UGC Unboxing 30s — Shows product in use" value="{{ old('title') }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold">@error('title')<div class="text-[12px] text-red-600">{{ $message }}</div>@enderror</div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Category * — controls where it shows</label><select name="category" id="category" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold">@foreach($categories as $c)<option value="{{ $c }}" {{ old('category')==$c?'selected':'' }}>{{ $c }}</option>@endforeach</select></div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Profile type * — who it matches to</label><select name="profile_type" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold">@foreach($profileTypes as $k=>$v)<option value="{{ $k }}" {{ old('profile_type')==$k?'selected':'' }}>{{ $v }}</option>@endforeach</select><div class="text-[11px] text-[#7A7A78] mt-1">UGC Video → ugc_creator, Barter Collab → influencer</div></div>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Price type *</label><select name="price_type" id="price_type" onchange="toggleBarter()" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold"><option value="paid" {{ old('price_type')=='paid'?'selected':'' }}>Paid</option><option value="barter" {{ old('price_type')=='barter'?'selected':'' }}>Barter</option><option value="hybrid" {{ old('price_type')=='hybrid'?'selected':'' }}>Hybrid (paid + barter)</option></select></div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Price ₹ (0 for barter-only)</label><input name="price" id="price" type="number" min="0" step="1" value="{{ old('price',500) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Compare price (strikethrough)</label><input name="compare_price" type="number" min="0" step="1" value="{{ old('compare_price') }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
    </div>

    <div id="barter-box" class="hidden p-4 rounded-2xl border border-amber-200 bg-amber-50 space-y-3">
      <div class="font-black text-[13px]">Barter details</div>
      <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-amber-800">Barter value ₹ — product value you give</label><input name="barter_value" id="barter_value" type="number" min="0" step="1" value="{{ old('barter_value') }}" placeholder="2000" class="mt-1 w-full h-10 px-3 rounded-xl border border-amber-200 bg-white text-[13px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-amber-800">Revisions</label><input name="revision_count" type="number" min="0" value="{{ old('revision_count',1) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-amber-200 bg-white text-[13px]"></div>
      </div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-amber-800">Collab terms — what business gives, what creator delivers, timeline</label><textarea name="collab_terms" rows="2" placeholder="Business ships 1 kurta worth ₹2000 + allows creator 7 days to post. Creator delivers 1 Reel 30s + keeps product. No cash exchange." class="mt-1 w-full px-3 py-2 rounded-xl border border-amber-200 bg-white text-[13px]">{{ old('collab_terms') }}</textarea></div>
      <div class="text-[11px] text-amber-800">Barter flow same order → creator assigned in ~12 min → Tracking, easy like ordering food → business marks approve (no escrow hold for barter).</div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Delivery — days</label><input name="delivery_days" type="number" min="1" value="{{ old('delivery_days',2) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Assign Creator (optional — else auto-matched)</label><select name="creator_id" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"><option value="">Auto-match by profile type</option>@foreach($creators as $c)<option value="{{ $c->id }}" {{ (string)old('creator_id')===(string)$c->id?'selected':'' }}>{{ $c->name }} — {{ $c->handle }} ({{ $c->profileLabel() }})</option>@endforeach</select></div>
    </div>

    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Description</label><textarea name="description" rows="2" placeholder="UGC video: unboxing + 3 UGC hooks + captions. Deliverable: 1×30s vertical mp4." class="mt-1 w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">{{ old('description') }}</textarea></div>
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Deliverables — one per line (e.g. 1×30s Reel )</label><textarea name="deliverables" rows="2" placeholder="1×30s vertical mp4&#10;Captions burned in&#10;2 size variants" class="mt-1 w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">{{ old('deliverables') }}</textarea></div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Badge (e.g. Best seller, UGC)</label><input name="badge" value="{{ old('badge') }}" placeholder="UGC" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Cover — upload or URL</label><input type="file" name="cover" accept="image/*" class="mt-1 w-full text-[13px]"><input name="cover_url" value="{{ old('cover_url') }}" placeholder="https://... or leave empty" class="mt-2 w-full h-9 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
    </div>

    <label class="flex items-center gap-2 text-[13px] font-bold"><input type="checkbox" name="is_active" value="1" checked> Active (shows in marketplace)</label>

    <button class="w-full h-11 rounded-full bg-ink text-white font-black text-[14px]">Create Service — goes live instantly</button>
  </form>
</div>
</div>
<script>
function toggleBarter(){ const t=document.getElementById('price_type').value; const b=document.getElementById('barter-box'); const p=document.getElementById('price'); if(t==='barter'||t==='hybrid'){ b.classList.remove('hidden'); if(t==='barter') p.placeholder='0 for barter-only'; } else { b.classList.add('hidden'); } }
toggleBarter();
document.getElementById('category')?.addEventListener('change', function(){
  if(this.value==='Barter Collab'){ document.getElementById('price_type').value='barter'; toggleBarter(); document.querySelector('[name=profile_type]').value='influencer'; }
  if(this.value==='UGC Video'){ document.querySelector('[name=profile_type]').value='ugc_creator'; }
});
</script>
@endsection
