@extends('admin.layout')
@section('title','Edit creator profile')
@section('content')
<div class="max-w-[900px] mx-auto">
  <div class="flex items-center justify-between gap-3"><div><h1 class="text-[22px] font-black">Edit creator profile</h1><p class="text-[13px] text-[#7A7A78]">Admin can correct any public profile, availability and billing identity.</p></div><a href="{{ route('admin.creators.show',$creator->id) }}" class="h-9 px-4 rounded-full border border-[#E8E8E6] bg-white font-bold text-[13px] inline-flex items-center">← Back</a></div>
  @if($errors->any())<div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-2.5 text-[13px] font-semibold">{{ $errors->first() }}</div>@endif
  <form method="POST" action="{{ route('admin.creators.update',$creator->id) }}" class="mt-5 bg-white border border-[#E8E8E6] rounded-2xl p-5 space-y-5">@csrf @method('PUT')
    <div class="grid sm:grid-cols-2 gap-4">
      @foreach([['name','Name'],['email','Email'],['phone','Phone'],['handle','Handle'],['headline','Headline'],['location','Location'],['price_from','Starting price'],['portfolio_url','Portfolio URL']] as [$field,$label])
        <label><span class="text-[12px] font-bold text-[#7A7A78]">{{ $label }}</span><input name="{{ $field }}" value="{{ old($field,$creator->{$field}) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]" {{ $field==='name' || $field==='email' ? 'required' : '' }}></label>
      @endforeach
    </div>
    <label class="block"><span class="text-[12px] font-bold text-[#7A7A78]">Skills, comma separated</span><input name="skills" value="{{ old('skills',implode(', ',(array)$creator->skills)) }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></label>
    <label class="block"><span class="text-[12px] font-bold text-[#7A7A78]">Bio</span><textarea name="bio" rows="5" class="mt-1 w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">{{ old('bio',$creator->bio) }}</textarea></label>
    <div class="flex flex-wrap gap-5 text-[13px] font-semibold"><label><input type="checkbox" name="is_available" value="1" @checked(old('is_available',$creator->is_available))> Available</label><label><input type="checkbox" name="is_verified" value="1" @checked(old('is_verified',$creator->is_verified))> Verified</label></div>
    <button class="h-11 px-6 rounded-xl bg-[#0F0F0F] text-white font-black text-[13.5px]">Save profile</button>
  </form>
</div>
@endsection