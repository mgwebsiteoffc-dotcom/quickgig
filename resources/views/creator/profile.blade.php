@extends('layouts.app')
@section('content')
<div class="max-w-[760px] mx-auto">
  <div class="px-4 sm:px-6 py-5">
    <a href="{{ route('creator.dashboard') }}" class="text-[12px] font-bold text-[#7A7A78] hover:text-[#0F0F0F]">← Back to Dashboard</a>
    <h1 class="mt-2 text-[22px] font-black tracking-tight">Creator Profile</h1>
    <p class="text-[13px] font-medium text-[#7A7A78]">Real DB — your verified tick, portfolio, UPI, and live availability (green dot) come from here. No dummy.</p>
  </div>

  @if(session('toast'))<div class="mx-4 sm:mx-6 mb-4 bg-[#0F0F0F] text-white rounded-full px-4 py-2.5 text-[13px] font-bold">{{ session('toast') }}</div>@endif

  <form method="POST" action="{{ route('creator.profile.update') }}" enctype="multipart/form-data" class="bg-white border-y sm:border border-[#E8E8E6] sm:rounded-2xl sm:mx-6 overflow-hidden">
    @csrf
    <div class="p-5 sm:p-6">
      <div class="flex gap-4 flex-wrap">
        <div class="text-center">
          <img src="{{ $creator->avatarUrl() }}" class="w-20 h-20 rounded-full object-cover border border-[#E8E8E6]">
          <div class="mt-2 text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Avatar</div>
          <input type="file" name="avatar" accept="image/*" class="mt-1 w-[160px] text-[11px]">
        </div>
        <div class="flex-1 min-w-[220px]">
          <div class="w-full h-[96px] rounded-2xl bg-[#F8F8F7] border border-[#E8E8E6] grid place-items-center text-[11px] font-bold text-[#7A7A78]">Cover image (optional) <input type="file" name="cover" accept="image/*" class="ml-2 text-[11px]"></div>
          <div class="mt-2 text-[11px] font-medium text-[#7A7A78]">Hostinger file upload — stored in <code class="bg-[#F8F8F7] border px-1 py-0.5 rounded">storage/creators</code> or <code class="bg-[#F8F8F7] px-1 py-0.5 rounded">public/uploads</code>.</div>
        </div>
      </div>

      <div class="mt-6 grid sm:grid-cols-2 gap-4">
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Name *</label><input name="name" value="{{ old('name',$creator->name) }}" required class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px] font-bold"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Handle *</label><input name="handle" value="{{ old('handle',$creator->handle) }}" required class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px] font-mono"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Email</label><input name="email" type="email" value="{{ old('email',$creator->email) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Phone</label><input name="phone" value="{{ old('phone',$creator->phone) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px]"></div>
        <div class="sm:col-span-2"><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Headline</label><input name="headline" value="{{ old('headline',$creator->headline) }}" placeholder="Talking-Head • Retention • For @devtalksbusiness" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold"></div>
        <div class="sm:col-span-2"><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Bio</label><textarea name="bio" rows="3" class="mt-1 w-full px-3 py-2.5 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] leading-6">{{ old('bio',$creator->bio) }}</textarea></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Location</label><input name="location" value="{{ old('location',$creator->location) }}" placeholder="Ghaziabad, IN" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Starting Price (₹)</label><input name="price_from" type="number" value="{{ old('price_from',$creator->price_from) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Skills (comma separated)</label><input name="skills" value="{{ old('skills', is_array($creator->skills) ? implode(', ', $creator->skills) : $creator->skills) }}" placeholder="Talking-Head, Retention, Captions" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Languages (comma)</label><input name="languages" value="{{ old('languages', is_array($creator->languages) ? implode(', ', $creator->languages) : $creator->languages) }}" placeholder="Hindi, English" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Profile type — controls matching</label><select name="profile_type" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold">@foreach(['video_editor'=>'Video Editor','ugc_creator'=>'UGC Creator','influencer'=>'Influencer (Barter)','designer'=>'Designer','hybrid'=>'Hybrid — All'] as $k=>$v)<option value="{{ $k }}" {{ (old('profile_type',$creator->profile_type)==$k?'selected':'') }}>{{ $v }}</option>@endforeach</select><div class="text-[11px] text-[#7A7A78] mt-1">UGC → UGC Video, Influencer → Barter Collab</div></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Collab type</label><select name="collab_type" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold"><option value="paid" {{ old('collab_type',$creator->collab_type)=='paid'?'selected':'' }}>Paid</option><option value="barter" {{ old('collab_type',$creator->collab_type)=='barter'?'selected':'' }}>Barter</option><option value="both" {{ old('collab_type',$creator->collab_type)=='both'?'selected':'' }}>Both</option></select></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Followers</label><input name="followers_count" type="number" min="0" value="{{ old('followers_count',$creator->followers_count) }}" placeholder="85000" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
        <div class="flex items-center gap-2 pt-6"><label class="flex items-center gap-2 text-[13px] font-bold"><input type="checkbox" name="barter_available" value="1" {{ old('barter_available',$creator->barter_available)?'checked':'' }}> Open to Barter</label><span class="text-[11px] font-semibold text-[#7A7A78]">Shows in Barter</span></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">UGC Niches (comma)</label><input name="ugc_niches" value="{{ old('ugc_niches', is_array($creator->ugc_niches)?implode(', ', $creator->ugc_niches):$creator->ugc_niches) }}" placeholder="Beauty, Skincare, Fashion" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">UPI ID (for payouts)</label><input name="upi_id" value="{{ old('upi_id',$creator->upi_id) }}" placeholder="name@upi" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-mono"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Portfolio URL</label><input name="portfolio_url" value="{{ old('portfolio_url',$creator->portfolio_url) }}" placeholder="https://..." class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Instagram</label><input name="instagram" value="{{ old('instagram',$creator->instagram) }}" placeholder="https://instagram.com/..." class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">YouTube</label><input name="youtube" value="{{ old('youtube',$creator->youtube) }}" placeholder="https://youtube.com/..." class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
      </div>

      @if($errors->any())<div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-3 py-2 text-[13px] font-semibold">{{ $errors->first() }}</div>@endif

      <button class="mt-6 w-full h-11 rounded-full bg-[#0F0F0F] text-white font-extrabold text-[14px]">Save Creator Profile ✓</button>
      <div class="mt-2 text-[11px] text-center font-semibold text-[#7A7A78]">Shows as verified 14px circle tick + green availability dot across marketplace & landing</div>
    </div>
  </form>

  {{-- Portfolio manager --}}
  <div class="mt-6 bg-white border-y sm:border border-[#E8E8E6] sm:rounded-2xl sm:mx-6 p-5 sm:p-6">
    <div class="flex items-center justify-between"><div class="font-black text-[14px]">Portfolio</div><span class="text-[11px] font-bold bg-[#F8F8F7] border border-[#E8E8E6] px-2.5 py-1 rounded-full">{{ $portfolio->count() }} items</span></div>
    <div class="mt-4 grid sm:grid-cols-3 gap-3">
      @forelse($portfolio as $p)
        <div class="border border-[#E8E8E6] rounded-2xl overflow-hidden">
          <img src="{{ filter_var($p->cover, FILTER_VALIDATE_URL) ? $p->cover : asset('storage/'.$p->cover) }}" class="h-[120px] w-full object-cover">
          <div class="p-3"><div class="text-[13px] font-bold leading-tight">{{ $p->title }}</div><div class="text-[11px] font-semibold text-[#7A7A78]">{{ $p->category }} • {{ $p->views }} views</div>
          <form method="POST" action="{{ route('creator.portfolio.destroy',$p->id) }}" onsubmit="return confirm('Remove?')" class="mt-2">@csrf @method('DELETE')<button class="w-full h-8 rounded-full border border-red-200 bg-red-50 text-red-700 font-bold text-[12px]">Remove</button></form></div>
        </div>
      @empty
        <div class="sm:col-span-3 text-center py-8 bg-[#F8F8F7] border border-dashed border-[#E8E8E6] rounded-2xl"><div class="font-bold">No portfolio yet</div><div class="text-[12px] text-[#7A7A78] font-medium">Add your best reel/thumb below — shows on your public profile & marketplace</div></div>
      @endforelse
    </div>

    <form method="POST" action="{{ route('creator.portfolio.store') }}" enctype="multipart/form-data" class="mt-5 bg-[#F8F8F7] border border-[#E8E8E6] rounded-2xl p-4">
      @csrf
      <div class="font-bold text-[13px]">Add Portfolio Item</div>
      <div class="mt-3 grid sm:grid-cols-2 gap-3">
        <input name="title" required placeholder="Title — e.g. Hook that held 71%" class="h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px]">
        <input name="category" placeholder="Category — Reel / Thumbnail / AI" class="h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px]">
        <input name="video_url" placeholder="Video URL (YouTube/Drive)" class="h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px]">
        <input name="tags" placeholder="Tags comma — Retention, CTR" class="h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px]">
        <textarea name="description" rows="2" placeholder="Description (optional)" class="sm:col-span-2 w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-white text-[13px]"></textarea>
        <input type="file" name="cover" accept="image/*" class="sm:col-span-2 text-[13px]">
      </div>
      <button class="mt-3 w-full h-10 rounded-full bg-[#2563EB] text-white font-bold text-[13px]">Add to Portfolio</button>
    </form>
  </div>
</div>
@endsection
