@extends('layouts.site')

@section('content')
<section class="py-12">
  <div class="max-w-[900px] mx-auto px-5 lg:px-8">

    <a href="{{ route('creator.dashboard') }}" class="text-[12.5px] text-mut hover:text-white">← Back to studio</a>
    <h1 class="mt-3 font-display text-[30px] font-semibold">Creator profile</h1>
    <p class="mt-2 text-[14.5px] text-mut">A complete profile gets verified faster and ranks higher in matching.</p>

    @if($errors->any())
      <div class="mt-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-[13.5px] text-rose-200">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('creator.profile.update') }}" enctype="multipart/form-data" class="mt-7 glass rounded-3xl p-6 sm:p-7">
      @csrf

      <div class="flex flex-wrap items-center gap-5 pb-6 border-b border-white/8">
        <img src="{{ $creator->avatarUrl() }}" class="w-16 h-16 rounded-2xl object-cover border border-white/12" alt="">
        <div class="flex-1 min-w-[220px]">
          <label class="label" for="avatar">Profile photo</label>
          <input id="avatar" type="file" name="avatar" accept="image/*" class="block w-full text-[13px] text-mut file:mr-3 file:h-9 file:px-4 file:rounded-lg file:border-0 file:bg-white/10 file:text-white file:text-[12.5px]">
        </div>
        <div class="flex-1 min-w-[220px]">
          <label class="label" for="cover">Cover image</label>
          <input id="cover" type="file" name="cover" accept="image/*" class="block w-full text-[13px] text-mut file:mr-3 file:h-9 file:px-4 file:rounded-lg file:border-0 file:bg-white/10 file:text-white file:text-[12.5px]">
        </div>
      </div>

      <div class="mt-6 grid sm:grid-cols-2 gap-5">
        <div>
          <label class="label" for="name">Name *</label>
          <input id="name" name="name" value="{{ old('name', $creator->name) }}" required class="field">
        </div>
        <div>
          <label class="label" for="handle">Handle *</label>
          <input id="handle" name="handle" value="{{ old('handle', $creator->handle) }}" required class="field font-mono">
        </div>
        <div>
          <label class="label" for="email">Email</label>
          <input id="email" name="email" type="email" value="{{ old('email', $creator->email) }}" class="field">
        </div>
        <div>
          <label class="label" for="phone">Phone</label>
          <input id="phone" name="phone" value="{{ old('phone', $creator->phone) }}" class="field">
        </div>
        <div class="sm:col-span-2">
          <label class="label" for="headline">Headline</label>
          <input id="headline" name="headline" value="{{ old('headline', $creator->headline) }}" placeholder="Talking-head editing that holds attention past 60%" class="field">
        </div>
        <div class="sm:col-span-2">
          <label class="label" for="bio">Bio</label>
          <textarea id="bio" name="bio" rows="3" class="field" placeholder="What do you make, who for, and what results do clients get?">{{ old('bio', $creator->bio) }}</textarea>
        </div>
        <div>
          <label class="label" for="location">Location</label>
          <input id="location" name="location" value="{{ old('location', $creator->location) }}" placeholder="Ghaziabad, IN" class="field">
        </div>
        <div>
          <label class="label" for="price_from">Starting price (₹)</label>
          <input id="price_from" name="price_from" type="number" min="0" value="{{ old('price_from', $creator->price_from) }}" class="field font-mono">
        </div>
        <div>
          <label class="label" for="skills">Skills <span class="normal-case tracking-normal text-white/30">(comma separated)</span></label>
          <input id="skills" name="skills" value="{{ old('skills', is_array($creator->skills) ? implode(', ', $creator->skills) : $creator->skills) }}" placeholder="Reels, Retention editing, Captions" class="field">
        </div>
        <div>
          <label class="label" for="languages">Languages</label>
          <input id="languages" name="languages" value="{{ old('languages', is_array($creator->languages) ? implode(', ', $creator->languages) : $creator->languages) }}" placeholder="Hindi, English" class="field">
        </div>
        <div>
          <label class="label" for="profile_type">Profile type</label>
          <select id="profile_type" name="profile_type" class="field">
            @foreach(\App\Models\Creator::PROFILE_TYPES as $val => $label)
              <option value="{{ $val }}" @selected(old('profile_type', $creator->profile_type) === $val)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label" for="collab_type">Collaboration</label>
          <select id="collab_type" name="collab_type" class="field">
            @foreach(['paid' => 'Paid only', 'barter' => 'Barter only', 'both' => 'Paid or barter'] as $val => $label)
              <option value="{{ $val }}" @selected(old('collab_type', $creator->collab_type) === $val)>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="label" for="upi_id">UPI ID for payouts</label>
          <input id="upi_id" name="upi_id" value="{{ old('upi_id', $creator->upi_id) }}" placeholder="name@upi" class="field font-mono">
        </div>
        <div>
          <label class="label" for="followers_count">Followers</label>
          <input id="followers_count" name="followers_count" type="number" min="0" value="{{ old('followers_count', $creator->followers_count) }}" class="field">
        </div>
        <div>
          <label class="label" for="portfolio_url">Portfolio URL</label>
          <input id="portfolio_url" name="portfolio_url" value="{{ old('portfolio_url', $creator->portfolio_url) }}" placeholder="https://" class="field">
        </div>
        <div>
          <label class="label" for="instagram">Instagram</label>
          <input id="instagram" name="instagram" value="{{ old('instagram', $creator->instagram) }}" placeholder="https://instagram.com/…" class="field">
        </div>
      </div>

      <label class="mt-5 flex items-center gap-3 text-[13.5px] text-mut">
        <input type="checkbox" name="barter_available" value="1" @checked(old('barter_available', $creator->barter_available)) class="w-4 h-4 rounded border-white/20 bg-white/5 accent-violet">
        Open to barter collaborations
      </label>

      <button class="mt-7 h-12 px-7 rounded-xl btn-grad font-semibold text-[14.5px]">Save profile</button>
    </form>

    {{-- portfolio --}}
    <div class="mt-6 glass rounded-3xl p-6 sm:p-7">
      <div class="flex items-center justify-between">
        <h2 class="font-display text-[19px] font-semibold">Portfolio</h2>
        <span class="text-[12px] text-mut">{{ $portfolio->count() }} items</span>
      </div>

      <div class="mt-5 grid sm:grid-cols-3 gap-4">
        @forelse($portfolio as $item)
          <div class="rounded-2xl border border-white/10 overflow-hidden">
            <img src="{{ $item->cover && filter_var($item->cover, FILTER_VALIDATE_URL) ? $item->cover : ($item->cover ? asset('storage/'.$item->cover) : 'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=500&q=80') }}" class="h-[110px] w-full object-cover" alt="">
            <div class="p-3.5">
              <div class="text-[13px] font-medium line-clamp-1">{{ $item->title }}</div>
              <div class="mt-2 flex items-center justify-between">
                <span class="text-[11.5px] text-mut">{{ $item->category }}</span>
                <form method="POST" action="{{ route('creator.portfolio.destroy', $item->id) }}">
                  @csrf @method('DELETE')
                  <button class="text-[11.5px] text-rose-300 hover:text-rose-200">Remove</button>
                </form>
              </div>
            </div>
          </div>
        @empty
          <div class="sm:col-span-3 rounded-2xl border border-dashed border-white/12 p-8 text-center text-[13.5px] text-mut">
            No portfolio items yet — add your best three pieces below.
          </div>
        @endforelse
      </div>

      <form method="POST" action="{{ route('creator.portfolio.store') }}" enctype="multipart/form-data" class="mt-6 pt-6 border-t border-white/8 grid sm:grid-cols-2 gap-4">
        @csrf
        <div>
          <label class="label" for="p_title">Title *</label>
          <input id="p_title" name="title" required class="field" placeholder="Retention reel for a skincare brand">
        </div>
        <div>
          <label class="label" for="p_category">Category</label>
          <input id="p_category" name="category" class="field" placeholder="Reel">
        </div>
        <div>
          <label class="label" for="p_video">Video URL</label>
          <input id="p_video" name="video_url" class="field" placeholder="https://youtube.com/…">
        </div>
        <div>
          <label class="label" for="p_cover">Cover image</label>
          <input id="p_cover" type="file" name="cover" accept="image/*" class="block w-full text-[13px] text-mut file:mr-3 file:h-9 file:px-4 file:rounded-lg file:border-0 file:bg-white/10 file:text-white file:text-[12.5px]">
        </div>
        <div class="sm:col-span-2">
          <button class="h-12 px-6 rounded-xl glass font-medium text-[14px] hover:border-white/30 transition">Add portfolio item</button>
        </div>
      </form>
    </div>
  </div>
</section>
@endsection
