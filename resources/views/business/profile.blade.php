@extends('layouts.site')

@section('content')
<section class="py-12">
  <div class="max-w-[820px] mx-auto px-5 lg:px-8">

    <a href="{{ route('business.home') }}" class="text-[12.5px] text-mut hover:text-white">← Back to dashboard</a>
    <h1 class="mt-3 font-display text-[30px] font-semibold">Workspace settings</h1>
    <p class="mt-2 text-[14.5px] text-mut">This is the name creators, invoices and order pages will show.</p>

    @if($errors->any())
      <div class="mt-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-[13.5px] text-rose-200">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('business.profile.update') }}" enctype="multipart/form-data" class="mt-7 glass rounded-3xl p-6 sm:p-7">
      @csrf

      <div class="flex flex-wrap items-center gap-5 pb-6 border-b border-white/8">
        <img src="{{ $company->logoUrl() }}" class="w-16 h-16 rounded-2xl object-cover border border-white/12" alt="">
        <div class="flex-1 min-w-[240px]">
          <label class="label" for="logo">Company logo</label>
          <input id="logo" type="file" name="logo" accept="image/*" class="block w-full text-[13px] text-mut file:mr-3 file:h-9 file:px-4 file:rounded-lg file:border-0 file:bg-white/10 file:text-white file:text-[12.5px] file:font-medium">
          <div class="mt-1.5 text-[11.5px] text-mut">PNG or JPG, up to 2 MB.</div>
        </div>
      </div>

      <div class="mt-6 grid sm:grid-cols-2 gap-5">
        <div>
          <label class="label" for="name">Company name *</label>
          <input id="name" name="name" value="{{ old('name', $company->name) }}" required class="field">
        </div>
        <div>
          <label class="label" for="person_name">Your name *</label>
          <input id="person_name" name="person_name" value="{{ old('person_name', $company->person_name) }}" required class="field">
        </div>
        <div>
          <label class="label" for="email">Billing email</label>
          <input id="email" name="email" type="email" value="{{ old('email', $company->email) }}" class="field">
        </div>
        <div>
          <label class="label" for="phone">Phone</label>
          <input id="phone" name="phone" value="{{ old('phone', $company->phone) }}" class="field">
        </div>
        <div>
          <label class="label" for="website">Website</label>
          <input id="website" name="website" value="{{ old('website', $company->website) }}" placeholder="https://" class="field">
        </div>
        <div>
          <label class="label" for="gstin">GSTIN</label>
          <input id="gstin" name="gstin" value="{{ old('gstin', $company->gstin) }}" placeholder="09ABCDE1234F1Z5" class="field font-mono">
        </div>
        <div>
          <label class="label" for="industry">Industry</label>
          <input id="industry" name="industry" value="{{ old('industry', $company->industry) }}" placeholder="D2C, agency, SaaS…" class="field">
        </div>
        <div>
          <label class="label" for="team_size">Team size</label>
          <input id="team_size" name="team_size" type="number" min="1" value="{{ old('team_size', $company->team_size) }}" class="field">
        </div>
      </div>

      <div class="mt-5">
        <label class="label" for="bio">About the company</label>
        <textarea id="bio" name="bio" rows="3" class="field" placeholder="What do you do? Creators see this with every brief.">{{ old('bio', $company->bio) }}</textarea>
      </div>

      <div class="mt-5 grid sm:grid-cols-[2fr_1fr_1fr_1fr] gap-4">
        <div>
          <label class="label" for="address">Address</label>
          <input id="address" name="address" value="{{ old('address', $company->address) }}" class="field">
        </div>
        <div>
          <label class="label" for="city">City</label>
          <input id="city" name="city" value="{{ old('city', $company->city) }}" class="field">
        </div>
        <div>
          <label class="label" for="state">State</label>
          <input id="state" name="state" value="{{ old('state', $company->state) }}" class="field">
        </div>
        <div>
          <label class="label" for="pincode">PIN</label>
          <input id="pincode" name="pincode" value="{{ old('pincode', $company->pincode) }}" class="field">
        </div>
      </div>

      <button class="mt-7 h-12 px-7 rounded-xl btn-grad font-semibold text-[14.5px]">Save workspace</button>
    </form>
  </div>
</section>
@endsection
