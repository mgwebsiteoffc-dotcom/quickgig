@extends('admin.layout')
@section('title','Edit Service — '.$service->title)
@section('breadcrumb','Admin • Services • Edit')
@section('content')
<div class="max-w-[760px] mx-auto">
<div class="bg-white border border-[#E8E8E6] rounded-2xl p-6">
  <div class="flex items-center justify-between"><div><div class="font-black text-[16px]">Edit — {{ $service->title }}</div><div class="text-[12px] font-medium text-[#7A7A78]">{{ $service->category }} • {{ $service->profile_type }} • {{ $service->price_type }} • {{ $service->displayPrice() }}</div></div><a href="{{ route('admin.services.index') }}" class="h-9 px-4 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] font-bold text-[13px] inline-flex items-center">← Services</a></div>

  <form method="POST" action="{{ route('admin.services.update',$service->id) }}" enctype="multipart/form-data" class="mt-6 space-y-4">
    @csrf @method('PUT')
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Title</label><input name="title" required value="{{ old('title',$service->title) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold"></div>
    <div class="grid sm:grid-cols-2 gap-4">
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Category</label><select name="category" id="category" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold">@foreach($categories as $c)<option value="{{ $c }}" {{ (old('category',$service->category))==$c?'selected':'' }}>{{ $c }}</option>@endforeach</select></div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Profile type</label><select name="profile_type" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold">@foreach($profileTypes as $k=>$v)<option value="{{ $k }}" {{ (old('profile_type',$service->profile_type))==$k?'selected':'' }}>{{ $v }}</option>@endforeach</select></div>
    </div>

    <div class="grid sm:grid-cols-3 gap-4">
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Price type</label><select name="price_type" id="price_type" onchange="toggleBarter()" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold"><option value="paid" {{ old('price_type',$service->price_type)=='paid'?'selected':'' }}>Paid</option><option value="barter" {{ old('price_type',$service->price_type)=='barter'?'selected':'' }}>Barter</option><option value="hybrid" {{ old('price_type',$service->price_type)=='hybrid'?'selected':'' }}>Hybrid</option></select></div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Price ₹</label><input name="price" id="price" type="number" min="0" value="{{ old('price',$service->price) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Compare price</label><input name="compare_price" type="number" min="0" value="{{ old('compare_price',$service->compare_price) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
    </div>

    <div id="barter-box" class="p-4 rounded-2xl border border-amber-200 bg-amber-50 space-y-3">
      <div class="font-black text-[13px]">Barter details</div>
      <div class="grid sm:grid-cols-2 gap-4">
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-amber-800">Barter value ₹</label><input name="barter_value" type="number" min="0" value="{{ old('barter_value',$service->barter_value) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-amber-200 bg-white text-[13px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-amber-800">Revisions</label><input name="revision_count" type="number" min="0" value="{{ old('revision_count',$service->revision_count) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-amber-200 bg-white text-[13px]"></div>
      </div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-amber-800">Collab terms</label><textarea name="collab_terms" rows="3" class="mt-1 w-full px-3 py-2 rounded-xl border border-amber-200 bg-white text-[13px]">{{ old('collab_terms',$service->collab_terms) }}</textarea></div>
    </div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Delivery days</label><input name="delivery_days" type="number" min="1" value="{{ old('delivery_days',$service->delivery_days) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Creator</label><select name="creator_id" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"><option value="">Auto-match</option>@foreach($creators as $c)<option value="{{ $c->id }}" {{ (string)old('creator_id',$service->creator_id)===(string)$c->id?'selected':'' }}>{{ $c->name }} — {{ $c->handle }} ({{ $c->profileLabel() }})</option>@endforeach</select></div>
    </div>
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Description</label><textarea name="description" rows="2" class="mt-1 w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">{{ old('description',$service->description) }}</textarea></div>
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Deliverables (one per line)</label><textarea name="deliverables" rows="2" class="mt-1 w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">{{ old('deliverables', is_array($service->deliverables)?implode("\n",$service->deliverables):$service->deliverables) }}</textarea></div>

    <div class="grid sm:grid-cols-2 gap-4">
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Badge</label><input name="badge" value="{{ old('badge',$service->badge) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
      <div>
        <label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Cover</label>
        @if($service->cover)<img src="{{ filter_var($service->cover, FILTER_VALIDATE_URL)?$service->cover:asset('storage/'.$service->cover) }}" class="w-full h-28 object-cover rounded-xl border border-line mt-1">@endif
        <input type="file" name="cover" accept="image/*" class="mt-2 w-full text-[13px]"><input name="cover_url" placeholder="Or new URL" class="mt-2 w-full h-9 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
      </div>
    </div>

    <label class="flex items-center gap-2 text-[13px] font-bold"><input type="checkbox" name="is_active" value="1" {{ old('is_active',$service->is_active)?'checked':'' }}> Active</label>

    <div class="flex gap-3">
      <button class="flex-1 h-11 rounded-full bg-ink text-white font-black text-[14px]">Save changes</button>
      <a href="{{ route('admin.services.index') }}" class="h-11 px-6 rounded-full border border-[#E8E8E6] bg-white font-bold text-[13px] inline-flex items-center">Cancel</a>
    </div>
  </form>

  <form method="POST" action="{{ route('admin.services.destroy',$service->id) }}" onsubmit="return confirm('Delete service?')" class="mt-4">@csrf @method('DELETE')<button class="w-full h-9 rounded-full border border-red-200 bg-red-50 text-red-600 font-bold text-[13px]">Delete service permanently</button></form>
</div>
</div>
<script>
function toggleBarter(){ const v=document.getElementById('price_type').value; const b=document.getElementById('barter-box'); if(v==='barter'||v==='hybrid') b.style.display=''; else b.style.display='none'; }
toggleBarter();
</script>
@endsection
