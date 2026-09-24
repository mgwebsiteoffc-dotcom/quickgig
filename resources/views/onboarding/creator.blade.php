<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('components.seo', ['seo'=>$seo])
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>*{font-family:Inter,sans-serif}</style>
<script>tailwind.config={theme:{extend:{colors:{brand:'#2563EB', ink:'#0F0F0F', muted:'#6B7280', line:'#E8E8E6', bg:'#F8F8F7', accent:'#FFC300'}}}}</script>
</head>
<body class="bg-[#F8F8F7] text-ink antialiased">
<div class="min-h-screen flex flex-col lg:flex-row">
  <!-- Left — beautiful creator promise -->
  <div class="lg:w-[46%] bg-white border-r border-line relative overflow-hidden flex flex-col">
    <div class="absolute inset-0 bg-gradient-to-br from-blue-50 via-white to-amber-50/60"></div>
    <div class="absolute -top-20 -right-20 w-[420px] h-[420px] bg-brand/10 rounded-full blur-[50px]"></div>
    <div class="absolute -bottom-20 -left-20 w-[420px] h-[420px] bg-amber-300/20 rounded-full blur-[50px]"></div>
    <div class="relative z-10 p-6 sm:p-8 lg:p-10 flex flex-col h-full">
      <a href="{{ route('landing') }}" class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-ink text-white grid place-items-center font-black text-[13px]">QC</div>
        <div class="leading-none"><div class="font-black text-[14px]">QuickContent</div><div class="text-[11px] font-semibold text-muted">For Creators</div></div>
        <span class="ml-2 hidden sm:inline-flex text-[10px] font-bold tracking-widest uppercase bg-amber-400 text-ink px-2.5 py-1 rounded-full">Earn 90% • Weekly UPI</span>
      </a>

      <div class="mt-10">
        <div class="inline-flex items-center gap-2 text-[11px] font-bold tracking-widest uppercase bg-ink text-white px-3 py-1.5 rounded-full">Creator onboarding • 60 seconds</div>
        <h1 class="mt-4 text-[28px] sm:text-[34px] font-black leading-[0.95] tracking-tight">Turn your<br>editing craft into<br><span class="text-brand">weekly UPI.</span></h1>
        <p class="mt-3 text-[14px] leading-6 text-muted font-medium max-w-[420px]">Verified profile, portfolio, and live availability — get matched in ~12 min, deliver, and get paid. No bidding, no Connects. 90% is yours.</p>
      </div>

      <div class="mt-8 grid gap-3">
        <div class="bg-white border border-line rounded-2xl p-4 flex gap-3 items-start shadow-sm">
          <div class="w-10 h-10 rounded-xl bg-ink text-white grid place-items-center shrink-0"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/><path d="M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg></div>
          <div><div class="text-[13px] font-black">1. Build your verified profile</div><div class="text-[12px] leading-5 text-muted font-medium">Avatar, handle, skills, bio — perfect 14px circle blue tick after review. Shows everywhere.</div></div>
        </div>
        <div class="bg-white border border-line rounded-2xl p-4 flex gap-3 items-start shadow-sm">
          <div class="w-10 h-10 rounded-xl bg-brand text-white grid place-items-center shrink-0"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M16 11l-4 4-4-4"/></svg></div>
          <div><div class="text-[13px] font-black">2. Showcase portfolio</div><div class="text-[12px] leading-5 text-muted font-medium">Add 2–3 best reels/thumbs. Companies pick you from Top 3 — no spam.</div></div>
        </div>
        <div class="bg-ink text-white rounded-2xl p-4 flex gap-3 items-start">
          <div class="w-10 h-10 rounded-xl bg-amber-400 text-ink grid place-items-center font-black shrink-0">3</div>
          <div><div class="text-[13px] font-black text-white">3. Get matched & paid</div><div class="text-[12px] leading-5 text-white/70 font-medium">Toggle Available → get orders → deliver → escrow releases to UPI weekly.</div></div>
        </div>
      </div>

      <div class="mt-6 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex gap-3">
        <img src="https://i.pravatar.cc/100?img=5" class="w-10 h-10 rounded-full border border-white shrink-0">
        <div class="text-[12px] leading-5"><b class="text-ink">Priya, @priyaedits</b> — “I went from hunting clients to 9 orders/week. Availability toggle = I work when I want. Payouts every Monday.”<div class="text-[11px] font-bold text-amber-700">★ 4.9 • 1,200 orders • ₹3.2L earned</div></div>
      </div>

      <div class="mt-auto pt-8 hidden lg:block text-[11px] font-semibold text-muted">Perfect 14×14 circle verified tick • Live green dot • File uploads work on Hostinger (no S3)</div>
    </div>
  </div>

  <!-- Right — beautiful form -->
  <div class="flex-1 flex flex-col bg-[#FDFDFC]" x-data="{ step: 1, avatarPreview: '', skills: '' }">
    <div class="h-[56px] border-b border-line bg-white flex items-center justify-between px-6">
      <div class="flex items-center gap-2 text-[12px] font-bold"><a href="{{ route('landing') }}" class="text-muted hover:text-ink">Home</a><span class="text-line">/</span><span class="text-ink">Creator onboarding</span></div>
      <div class="hidden sm:flex items-center gap-2 text-[11px] font-bold bg-green-50 border border-green-200 text-green-700 px-3 py-1.5 rounded-full"><span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> 60s • No fees to join</div>
    </div>

    <div class="px-6 sm:px-10 pt-6">
      <div class="flex items-center gap-3">
        <template x-for="i in [1,2,3]">
          <div class="flex items-center gap-3 flex-1" :class="i===3 ? 'flex-none' : 'flex-1'">
            <div class="w-8 h-8 rounded-full grid place-items-center text-[12px] font-black border transition" :class="step >= i ? 'bg-ink text-white border-ink' : 'bg-white text-muted border-line'"><span x-text="i"></span></div>
            <div class="hidden sm:block flex-1 h-[2px] rounded-full transition" :class="step > i ? 'bg-ink' : 'bg-line'"></div>
          </div>
        </template>
      </div>
      <div class="flex justify-between mt-2 text-[11px] font-bold"><span :class="step===1?'text-ink':'text-muted'">Profile</span><span :class="step===2?'text-ink':'text-muted'">Skills</span><span :class="step===3?'text-ink':'text-muted'">Go live</span></div>
    </div>

    <form method="POST" action="{{ route('onboarding.creator.store') }}" enctype="multipart/form-data" class="flex-1 px-6 sm:px-10 py-6 flex flex-col max-w-[560px] w-full mx-auto">
      @csrf
      <!-- STEP 1 -->
      <div x-show="step===1" x-transition class="space-y-4">
        <div class="bg-white border border-line rounded-2xl p-5 shadow-sm">
          <div class="flex gap-4 items-start">
            <div class="w-16 h-16 rounded-full bg-bg border border-line grid place-items-center overflow-hidden shrink-0"><template x-if="avatarPreview"><img :src="avatarPreview" class="w-full h-full object-cover"></template><template x-if="!avatarPreview"><span class="text-[10px] font-bold text-muted">AVATAR</span></template></div>
            <div class="flex-1"><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Avatar (optional) • perfect circle crop</label><input type="file" name="avatar" accept="image/*" @change="avatarPreview = URL.createObjectURL($event.target.files[0])" class="mt-1 w-full text-[13px]"><div class="text-[11px] text-muted font-medium">Max 2MB • Shows as 14px verified tick everywhere after approval.</div></div>
          </div>
          <div class="mt-4 grid gap-3">
            <div class="grid grid-cols-2 gap-3">
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Your Name *</label><input name="name" required placeholder="Priya Sharma" value="{{ old('name') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[14px] font-bold focus:bg-white outline-none"></div>
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Handle *</label><input name="handle" required placeholder="@priyaedits" value="{{ old('handle') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px] font-mono"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Email *</label><input name="email" type="email" required placeholder="priya@..." value="{{ old('email') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px]"></div>
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Phone *</label><input name="phone" required placeholder="98765 00001" value="{{ old('phone') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px]"></div>
            </div>
            <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Headline</label><input name="headline" placeholder="Talking-Head • Retention • For @devtalksbusiness" value="{{ old('headline') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px] font-semibold"></div>
            <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Bio</label><textarea name="bio" rows="2" placeholder="I obsess over hooks that hold 70%+ watch-time..." class="mt-1 w-full px-3 py-2.5 rounded-xl border border-line bg-bg text-[13px] leading-6">{{ old('bio') }}</textarea></div>
          </div>
        </div>
        <button type="button" @click="if($el.form.checkValidity()) step=2; else $el.form.reportValidity()" class="w-full h-12 rounded-full bg-ink text-white font-extrabold text-[14px] hover:bg-black">Continue → Skills & pricing</button>
        <div class="text-[11px] text-center font-semibold text-muted">Step 1 of 3 • Private until verified • Blue tick after review</div>
      </div>

      <!-- STEP 2 -->
      <div x-show="step===2" x-transition class="space-y-4" x-data="{ ptype: '{{ old('profile_type','video_editor') }}', collab: '{{ old('collab_type','paid') }}' }">
        <div class="bg-white border border-line rounded-2xl p-5 shadow-sm">
          <div class="text-[14px] font-black flex items-center gap-2"><div class="w-8 h-8 rounded-xl bg-brand text-white grid place-items-center"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 20l3-3H9l3 3z"/><path d="M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg></div> Skills, profile type & pricing</div>
          <div class="mt-4 grid gap-3">
            <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Profile Type * — decides which services you get</label>
              <select name="profile_type" x-model="ptype" required class="mt-1 w-full h-11 px-3 rounded-xl border-2 font-bold text-[13px]" :class="ptype?'border-ink bg-ink text-white':'border-line bg-bg'">
                <option value="video_editor">🎬 Video Editor — Reels, Retention, Captions</option>
                <option value="ugc_creator">📱 UGC Creator — Unboxing, Testimonial, Natural ads</option>
                <option value="influencer">🤝 Influencer — Barter Collabs, Reviews, Stories</option>
                <option value="designer">🎨 Designer — Thumbnails, CTR, Pack</option>
                <option value="hybrid">⭐ Hybrid — All types (UGC + Edit + Barter)</option>
              </select>
              <div class="mt-1 text-[11px] font-semibold px-2.5 py-1 rounded-xl" :class="ptype==='ugc_creator'?'bg-blue-50 border border-blue-200 text-brand':ptype==='influencer'?'bg-amber-50 border border-amber-200 text-amber-800':'bg-bg border border-line text-muted'"><span x-show="ptype==='ugc_creator'">UGC: you’ll get UGC Video requests — flow same: brief → deliver 30s → approve.</span><span x-show="ptype==='influencer'">Barter: product exchange, no escrow cash — flow same: brief → ship product → deliver Reel → approve.</span><span x-show="ptype==='video_editor' || ptype==='designer'">Paid services — escrow till Approve. Admin assigns by profile type.</span><span x-show="ptype==='hybrid'">All services — you’ll be matched across Reels, UGC & Barter by admin.</span></div>
            </div>
            <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Skills * (comma separated)</label><input name="skills" x-model="skills" required placeholder="Talking-Head, Retention, Captions, Hook Writing" value="{{ old('skills') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px]"></div>
            <div class="flex flex-wrap gap-1.5"><template x-for="t in skills.split(',').filter(s=>s.trim())"><span class="text-[11px] font-bold bg-ink text-white px-2.5 py-1 rounded-full" x-text="t.trim()"></span></template></div>
            <div class="grid grid-cols-2 gap-3">
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Starting Price (₹) *</label><input name="price_from" type="number" required placeholder="1299 — 0 for barter-only" value="{{ old('price_from',1299) }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px] font-bold"></div>
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Followers (if influencer/UGC)</label><input name="followers_count" type="number" placeholder="45000" value="{{ old('followers_count') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px]"></div>
            </div>
            <div class="grid grid-cols-2 gap-3">
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Collab Type</label><select name="collab_type" x-model="collab" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px] font-semibold"><option value="paid">Paid only</option><option value="barter">Barter only</option><option value="both">Both (Paid + Barter)</option></select></div>
              <div class="flex items-end"><label class="flex items-center gap-2 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px] font-bold cursor-pointer"><input type="checkbox" name="barter_available" value="1" :checked="collab==='barter' || collab==='both'" @change="collab = $event.target.checked ? 'both' : 'paid'"> <span>Open to Barter</span></label></div>
            </div>
            <div x-show="ptype==='ugc_creator' || ptype==='hybrid'"><label class="text-[11px] font-bold tracking-widest uppercase text-muted">UGC Niches (comma)</label><input name="ugc_niches" placeholder="Beauty, Lifestyle, Tech, Fashion" value="{{ old('ugc_niches') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px]"></div>
            <div class="grid grid-cols-2 gap-3">
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">UPI for payouts</label><input name="upi_id" placeholder="priya@upi" value="{{ old('upi_id') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px] font-mono"></div>
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Portfolio URL (optional)</label><input name="portfolio_url" type="url" placeholder="https://instagram.com/..." value="{{ old('portfolio_url') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px]"></div>
            </div>
          </div>
        </div>
        <div class="flex gap-3">
          <button type="button" @click="step=1" class="flex-1 h-12 rounded-full border-2 border-line bg-white font-bold text-[14px]">Back</button>
          <button type="button" @click="if($el.form.checkValidity()) step=3; else $el.form.reportValidity()" class="flex-[1.5] h-12 rounded-full bg-brand text-white font-extrabold text-[14px] hover:bg-blue-700">Almost done →</button>
        </div>
      </div>

      <!-- STEP 3 -->
      <div x-show="step===3" x-transition class="space-y-4">
        <div class="bg-ink text-white rounded-2xl p-5">
          <div class="text-[11px] font-bold tracking-widest uppercase text-white/60">You’re about to go live</div>
          <div class="mt-2 text-[16px] font-black leading-tight">We’ll review your tick — then<br>you’re discoverable in ~1 hour.</div>
          <div class="mt-3 text-[13px] leading-6 text-white/70 font-medium">• No bidding — companies see Top 3 and pick.<br>• Toggle Available when you can work — green dot shows.<br>• Earn 90% • Weekly UPI after escrow release.</div>
          <div class="mt-4 flex items-start gap-2 text-[11px] font-semibold"><input type="checkbox" required class="mt-0.5 accent-white"><span> I confirm my work is original and I’ll deliver on time.</span></div>
        </div>
        <button type="submit" class="w-full h-12 rounded-full bg-amber-400 text-ink font-black text-[15px] hover:bg-amber-300">Create my creator profile → go live ✓</button>
        <button type="button" @click="step=2" class="w-full h-10 rounded-full border border-line bg-white font-bold text-[13px]">Back to edit skills</button>
        <div class="text-[11px] text-center font-semibold text-muted">By creating, you agree to 10% platform fee. Need help? <a href="mailto:creators@quickcontent.in" class="underline text-ink">creators@quickcontent.in</a></div>
      </div>

      @if($errors->any())<div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-3 py-2 text-[13px] font-semibold">{{ $errors->first() }}</div>@endif
      <div class="mt-6 text-center text-[11px] font-semibold text-muted">Already verified? <a href="{{ route('creator.dashboard') }}" class="text-ink underline">Go to dashboard</a> • <a href="{{ route('login') }}" class="text-ink underline">Login</a></div>
    </form>
  </div>
</div>
</body>
</html>
