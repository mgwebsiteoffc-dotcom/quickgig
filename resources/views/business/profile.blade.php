@extends('layouts.app')
@section('content')
<div class="max-w-[760px] mx-auto">
  <div class="px-4 sm:px-6 py-5">
    <a href="{{ route('business.home') }}" class="text-[12px] font-bold text-[#7A7A78] hover:text-[#0F0F0F]">← Back to Marketplace</a>
    <h1 class="mt-2 text-[22px] font-black tracking-tight">Business Profile</h1>
    <p class="text-[13px] font-medium text-[#7A7A78]">This is your <b class="text-[#0F0F0F]">Company Name</b> shown everywhere — orders, invoices, chat, tracking. No dummy — saved to DB.</p>
  </div>

  @if(session('toast'))<div class="mx-4 sm:mx-6 mb-4 bg-[#0F0F0F] text-white rounded-full px-4 py-2.5 text-[13px] font-bold">{{ session('toast') }}</div>@endif

  <form method="POST" action="{{ route('business.profile.update') }}" enctype="multipart/form-data" class="bg-white border-y sm:border border-[#E8E8E6] sm:rounded-2xl sm:mx-6 overflow-hidden">
    @csrf
    <div class="p-5 sm:p-6">
      <div class="flex gap-4 items-start">
        <img src="{{ $company->logoUrl() }}" class="w-16 h-16 rounded-2xl object-cover border border-[#E8E8E6] bg-[#F8F8F7]">
        <div class="flex-1">
          <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Company Logo (Hostinger: file upload, no S3)</div>
          <input type="file" name="logo" accept="image/*" class="mt-1 w-full text-[13px] font-medium">
          <div class="text-[11px] text-[#7A7A78] font-medium">PNG/JPG, max 2MB. Stored in <code class="bg-[#F8F8F7] border border-[#E8E8E6] px-1 py-0.5 rounded">storage/companies</code> or <code class="bg-[#F8F8F7] px-1 py-0.5 rounded">public/uploads</code> fallback.</div>
        </div>
      </div>

      <div class="mt-6 grid sm:grid-cols-2 gap-4">
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Company Name *</label><input name="name" value="{{ old('name',$company->name) }}" required class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px] font-semibold focus:bg-white outline-none"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Your Name *</label><input name="person_name" value="{{ old('person_name',$company->person_name) }}" required class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px] font-semibold"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Email</label><input name="email" type="email" value="{{ old('email',$company->email) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Phone</label><input name="phone" value="{{ old('phone',$company->phone) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Website</label><input name="website" value="{{ old('website',$company->website) }}" placeholder="https://..." class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">GSTIN</label><input name="gstin" value="{{ old('gstin',$company->gstin) }}" placeholder="09ABCDE1234F1Z5" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px] font-mono"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Industry</label><input name="industry" value="{{ old('industry',$company->industry) }}" placeholder="D2C, Agency, SaaS..." class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Team Size</label><input name="team_size" type="number" value="{{ old('team_size',$company->team_size) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px]"></div>
      </div>

      <div class="mt-4"><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Bio</label><textarea name="bio" rows="3" class="mt-1 w-full px-3 py-2.5 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] leading-6" placeholder="What does your company do? Shown to creators when you post a brief.">{{ old('bio',$company->bio) }}</textarea></div>

      <div class="mt-4"><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Address</label><input name="address" value="{{ old('address',$company->address) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
      <div class="mt-3 grid grid-cols-3 gap-3">
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">City</label><input name="city" value="{{ old('city',$company->city) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">State</label><input name="state" value="{{ old('state',$company->state) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
        <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Pincode</label><input name="pincode" value="{{ old('pincode',$company->pincode) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
      </div>

      @if($errors->any())<div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-3 py-2 text-[13px] font-semibold">{{ $errors->first() }}</div>@endif

      <button class="mt-6 w-full h-11 rounded-full bg-[#0F0F0F] text-white font-extrabold text-[14px]">Save Profile → shows as Company Name everywhere</button>
      <div class="mt-2 text-[11px] text-center font-semibold text-[#7A7A78]">After save, topbar, checkout, invoices, and tracking will show “{{ $company->name }} • {{ $company->person_name }}”</div>
    </div>
  </form>
</div>
@endsection
