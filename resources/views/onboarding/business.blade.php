<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
@include('components.seo', ['seo'=>$seo])
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>*{font-family:Inter,sans-serif} .step-line{height:2px}</style>
<script>tailwind.config={theme:{extend:{colors:{brand:'#2563EB', ink:'#0F0F0F', muted:'#6B7280', line:'#E8E8E6', bg:'#F8F8F7'}}}}</script>
</head>
<body class="bg-[#FDFDFC] text-ink antialiased">
<div class="min-h-screen flex flex-col lg:flex-row">
  <!-- Left — beautiful process story -->
  <div class="lg:w-[46%] bg-[#0F0F0F] text-white relative overflow-hidden flex flex-col">
    <div class="absolute inset-0 bg-gradient-to-br from-[#1a1a1a] via-[#0F0F0F] to-[#0F0F0F]"></div>
    <div class="absolute -top-24 -left-24 w-[520px] h-[520px] bg-[#2563EB]/20 rounded-full blur-[80px]"></div>
    <div class="absolute -bottom-32 -right-32 w-[560px] h-[560px] bg-amber-400/10 rounded-full blur-[80px]"></div>
    <div class="relative z-10 p-6 sm:p-8 lg:p-10 flex flex-col h-full">
      <a href="{{ route('landing') }}" class="flex items-center gap-3">
        <div class="w-9 h-9 rounded-xl bg-white text-[#0F0F0F] grid place-items-center font-black text-[13px]">QC</div>
        <div class="leading-none"><div class="font-black text-[14px]">QuickContent</div><div class="text-[11px] font-semibold text-white/60">India’s First Quick Content Delivery</div></div>
        <span class="ml-2 hidden sm:inline-flex items-center gap-1 text-[10px] font-bold tracking-widest uppercase bg-white text-[#0F0F0F] px-2.5 py-1 rounded-full">As easy as ordering food</span>
      </a>

      <div class="mt-10">
        <div class="inline-flex items-center gap-2 text-[11px] font-bold tracking-widest uppercase bg-white/10 border border-white/15 text-white px-3 py-1.5 rounded-full">For Businesses • 45 seconds to start</div>
        <h1 class="mt-4 text-[28px] sm:text-[34px] font-black leading-[0.95] tracking-tight">Get your next reel<br>as easy as<br><span class="text-amber-300">ordering food.</span></h1>
        <p class="mt-3 text-[14px] leading-6 text-white/70 font-medium max-w-[420px]">Tell us what you need → verified creator in ~12 minutes → live tracking, just like a food order. Pay only when you approve.</p>
      </div>

      <!-- Beautiful 3-step visual -->
      <div class="mt-8 space-y-4">
        <div class="flex gap-4 items-start bg-white/5 border border-white/10 rounded-2xl p-4 backdrop-blur">
          <div class="w-10 h-10 rounded-xl bg-amber-400 text-[#0F0F0F] grid place-items-center font-black text-[14px] shrink-0">1</div>
          <div><div class="text-[13px] font-black">Describe your need</div><div class="text-[12px] leading-5 text-white/60 font-medium">Category, brief, timeline — 20 seconds. No sales call. Hostinger-ready form.</div></div>
          <div class="ml-auto hidden sm:block text-[11px] font-bold bg-white text-[#0F0F0F] px-2.5 py-1 rounded-full shrink-0">20s</div>
        </div>
        <div class="flex gap-4 items-start bg-white/5 border border-white/10 rounded-2xl p-4 backdrop-blur">
          <div class="w-10 h-10 rounded-xl bg-white text-[#0F0F0F] grid place-items-center font-black text-[14px] shrink-0">2</div>
          <div><div class="text-[13px] font-black">Get matched in ~12 min</div><div class="text-[12px] leading-5 text-white/60 font-medium">We assign the best verified pro (blue tick) — live availability, not random.</div></div>
          <div class="ml-auto hidden sm:block text-[11px] font-bold bg-white/10 border border-white/15 text-white px-2.5 py-1 rounded-full shrink-0">12m</div>
        </div>
        <div class="flex gap-4 items-start bg-white border border-white/20 rounded-2xl p-4 text-[#0F0F0F]">
          <div class="w-10 h-10 rounded-xl bg-[#0F0F0F] text-white grid place-items-center font-black text-[14px] shrink-0">3</div>
          <div><div class="text-[13px] font-black">Track & approve</div><div class="text-[12px] leading-5 text-[#6B7280] font-medium">Live tracking like food delivery + chat. Escrow till you click Approve.</div></div>
          <div class="ml-auto hidden sm:block text-[11px] font-black bg-[#0F0F0F] text-white px-2.5 py-1 rounded-full shrink-0">1 day</div>
        </div>
      </div>

      <div class="mt-auto pt-8 hidden lg:block">
        <div class="flex items-center gap-3">
          <div class="flex -space-x-2"><img src="https://i.pravatar.cc/100?img=5" class="w-8 h-8 rounded-full border-2 border-[#0F0F0F]"><img src="https://i.pravatar.cc/100?img=12" class="w-8 h-8 rounded-full border-2 border-[#0F0F0F]"><img src="https://i.pravatar.cc/100?img=9" class="w-8 h-8 rounded-full border-2 border-[#0F0F0F]"></div>
          <div class="text-[12px] leading-tight"><div class="font-bold text-white">Trusted by 200+ teams</div><div class="text-white/60 font-medium">Avante • BrandScale • GrowthX • 4.8/5 (1.2k)</div></div>
        </div>
        <div class="mt-4 text-[11px] font-semibold text-white/40">Live tracking, escrow & verified tick — Hostinger shared-ready (no Redis).</div>
      </div>
    </div>
  </div>

  <!-- Right — beautiful multi-step form -->
  <div class="flex-1 bg-[#F8F8F7] flex flex-col" x-data="{ step: 1, cat: 'Reel', logoPreview: '' }">
    <div class="h-[56px] border-b border-[#E8E8E6] bg-white flex items-center justify-between px-6">
      <div class="flex items-center gap-2 text-[12px] font-bold">
        <a href="{{ route('landing') }}" class="text-[#6B7280] hover:text-ink">Home</a><span class="text-[#E8E8E6]">/</span><span class="text-ink">Business onboarding</span>
      </div>
      <div class="hidden sm:flex items-center gap-2 text-[11px] font-bold text-[#6B7280]"><span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span> Avg. setup 45s • No credit card</div>
    </div>

    <!-- Stepper -->
    <div class="px-6 sm:px-10 pt-6">
      <div class="flex items-center gap-3">
        <template x-for="i in [1,2,3]">
          <div class="flex items-center gap-3 flex-1" :class="i===3 ? 'flex-none' : 'flex-1'">
            <div class="w-8 h-8 rounded-full grid place-items-center text-[12px] font-black border shrink-0 transition" :class="step >= i ? 'bg-ink text-white border-ink' : 'bg-white text-muted border-line'"><span x-text="i"></span></div>
            <div class="hidden sm:block flex-1 step-line rounded-full transition" :class="step > i ? 'bg-ink' : 'bg-line'"></div>
          </div>
        </template>
      </div>
      <div class="flex justify-between mt-2 text-[11px] font-bold tracking-wide">
        <span :class="step===1?'text-ink':'text-muted'">Company</span>
        <span :class="step===2?'text-ink':'text-muted'">Your need</span>
        <span :class="step===3?'text-ink':'text-muted'">Launch</span>
      </div>
    </div>

    <form method="POST" action="{{ route('onboarding.business.store') }}" enctype="multipart/form-data" class="flex-1 px-6 sm:px-10 py-6 flex flex-col max-w-[560px] w-full mx-auto">
      @csrf
      <!-- STEP 1 -->
      <div x-show="step===1" x-transition class="space-y-4">
        <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-ink text-white grid place-items-center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/><path d="M12 11v2M9 15h6"/></svg></div>
            <div><div class="text-[14px] font-black">About your company</div><div class="text-[11px] font-semibold text-muted">Shown as <b class="text-ink">Company Name</b> on orders, tracking & invoices.</div></div>
          </div>
          <div class="mt-4 grid gap-3">
            <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Company Name *</label><input name="company_name" required placeholder="Avante Studio" value="{{ old('company_name') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[14px] font-semibold focus:bg-white outline-none"></div>
            <div class="grid grid-cols-2 gap-3">
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Your Name *</label><input name="person_name" required placeholder="Rohan Sharma" value="{{ old('person_name') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[14px]"></div>
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Phone *</label><input name="phone" required placeholder="98765 43210" value="{{ old('phone') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[14px]"></div>
            </div>
            <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Work Email *</label><input name="email" type="email" required placeholder="rohan@avante.studio" value="{{ old('email') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[14px]"></div>
            <div class="grid grid-cols-2 gap-3">
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Industry</label><input name="industry" placeholder="D2C, Agency, SaaS" value="{{ old('industry') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px]"></div>
              <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Team Size</label><input name="team_size" type="number" placeholder="14" value="{{ old('team_size') }}" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px]"></div>
            </div>
            <div>
              <label class="text-[11px] font-bold tracking-widest uppercase text-muted">Logo (optional) — shows next to Company Name</label>
              <div class="mt-1 flex gap-3 items-center">
                <div class="w-12 h-12 rounded-xl bg-bg border border-line grid place-items-center overflow-hidden shrink-0"><template x-if="logoPreview"><img :src="logoPreview" class="w-full h-full object-cover"></template><template x-if="!logoPreview"><span class="text-[10px] font-bold text-muted">LOGO</span></template></div>
                <input type="file" name="logo" accept="image/*" @change="logoPreview = URL.createObjectURL($event.target.files[0])" class="flex-1 text-[13px]">
              </div>
            </div>
          </div>
        </div>
        <button type="button" @click="if($el.form.checkValidity()) step=2; else $el.form.reportValidity()" class="w-full h-12 rounded-full bg-ink text-white font-extrabold text-[14px] hover:bg-black">Continue → Tell us your need</button>
        <div class="text-[11px] text-center font-semibold text-muted">Step 1 of 3 • Takes 15 seconds • Hostinger file upload</div>
      </div>

      <!-- STEP 2 -->
      <div x-show="step===2" x-transition class="space-y-4">
        <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-brand text-white grid place-items-center"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 3l2 5 5 2-5 2-2 5-2-5-5-2 5-2 2-5z"/></svg></div>
            <div><div class="text-[14px] font-black">What do you need?</div><div class="text-[11px] font-semibold text-muted">We match you in ~12 min — as easy as ordering food.</div></div>
          </div>
          <div class="mt-4">
            <div class="text-[11px] font-bold tracking-widest uppercase text-muted">Category * — dynamic by service & profile type</div>
            <div class="mt-2 grid grid-cols-2 gap-2">
              <template x-for="c in ['Reel','Thumbnail','AI Video','UGC Video','Barter Collab','Bundle']">
                <label class="cursor-pointer">
                  <input type="radio" name="category" :value="c" x-model="cat" class="sr-only" required>
                  <div class="h-[72px] rounded-2xl border-2 p-3 flex flex-col justify-between text-left" :class="cat===c ? 'border-ink bg-ink text-white' : 'border-line bg-bg text-ink'">
                    <div class="text-[12px] font-black leading-tight" x-text="c"></div><div class="text-[10px] font-semibold" :class="cat===c?'text-white/70':'text-muted'" x-text="c==='Reel'?'Talking-head, 1 day':c==='Thumbnail'?'CTR, 1 day':c==='AI Video'?'Veo 3, 2 days':c==='UGC Video'?'Unboxing, testimonial':c==='Barter Collab'?'Product exchange': 'Reel+Thumb'"></div>
                  </div>
                </label>
              </template>
            </div>
            <input type="hidden" name="category" :value="cat">
            <div class="mt-2 text-[11px] font-semibold px-2.5 py-1.5 rounded-xl border" :class="cat==='Barter Collab' ? 'bg-amber-50 border-amber-200 text-amber-800' : 'bg-blue-50 border-blue-100 text-brand'" x-show="cat">
              <span x-show="cat==='Barter Collab'">Barter: no cash — product value held. Flow same: request → creator accepts → deliver → approve. Admin manages barter value.</span>
              <span x-show="cat==='UGC Video'">UGC: natural, phone-shot style for ads. Matched to UGC creators (profile type).</span>
              <span x-show="cat!=='Barter Collab' && cat!=='UGC Video'">Flow: request → verified creator (~12 min) → live tracking, easy like ordering food → approve → payout.</span>
            </div>
          </div>
          <div class="mt-4"><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Brief — what should we deliver? *</label><textarea name="need" required rows="3" placeholder="e.g. 30-sec talking-head for launch — hook in 2s, gym b-roll, yellow captions, 9:16, SRT." class="mt-1 w-full px-3 py-3 rounded-xl border border-line bg-bg text-[13px] leading-6">{{ old('need') }}</textarea></div>
          <div class="mt-3 grid grid-cols-2 gap-3">
            <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Timeline *</label><select name="timeline" required class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px] font-semibold"><option>1 Day</option><option>2 Days</option><option>Flexible</option></select></div>
            <div><label class="text-[11px] font-bold tracking-widest uppercase text-muted">Budget (optional)</label><select name="budget" class="mt-1 w-full h-11 px-3 rounded-xl border border-line bg-bg text-[13px] font-semibold"><option value="">From ₹1,299</option><option>₹1,299 — Reel</option><option>₹6,499 — AI</option><option>₹8,999 — Bundle</option></select></div>
          </div>
        </div>
        <div class="flex gap-3">
          <button type="button" @click="step=1" class="flex-1 h-12 rounded-full border-2 border-line bg-white font-bold text-[14px]">Back</button>
          <button type="button" @click="if($el.form.checkValidity()) step=3; else $el.form.reportValidity()" class="flex-[1.5] h-12 rounded-full bg-brand text-white font-extrabold text-[14px] hover:bg-blue-700">Review & launch →</button>
        </div>
      </div>

      <!-- STEP 3 -->
      <div x-show="step===3" x-transition class="space-y-4">
        <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
          <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-amber-400 text-ink grid place-items-center font-black">✓</div>
            <div><div class="text-[14px] font-black">Ready to launch?</div><div class="text-[11px] font-semibold text-muted">Check once — then we match you in ~12 min. Live tracking, easy like ordering food.</div></div>
          </div>
          <div class="mt-4 bg-bg border border-line rounded-2xl p-4 text-[13px] leading-6">
            <div class="font-bold">Your workspace will be created instantly.</div>
            <div class="text-muted font-medium">• Verified creator assigned — you’ll see live tracking, just like a food order.<br>• Pay held in Razorpay escrow — releases only when you Approve.<br>• 2 free revisions + GST invoice to your Company Name.</div>
          </div>
          <div class="mt-3 flex items-start gap-2 text-[11px] font-semibold text-muted"><input type="checkbox" required class="mt-0.5"> I agree to escrow terms & 5% platform fee. I’ll pay only when I approve.</div>
        </div>
        <button type="submit" class="w-full h-12 rounded-full bg-amber-400 text-ink font-black text-[15px] hover:bg-amber-300">Launch my workspace — 45 sec ✓</button>
        <button type="button" @click="step=2" class="w-full h-10 rounded-full border border-line bg-white font-bold text-[13px]">Back to edit need</button>
        <div class="text-[11px] text-center font-semibold text-muted">By launching, you agree to our Terms. Need help? <a href="mailto:support@quickcontent.in" class="underline text-ink">support@quickcontent.in</a></div>
      </div>

      @if($errors->any())<div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-3 py-2 text-[13px] font-semibold">{{ $errors->first() }}</div>@endif
      <div class="mt-6 text-center text-[11px] font-semibold text-muted">Already have an account? <a href="{{ route('business.home') }}" class="text-ink underline">Go to marketplace</a> • <a href="{{ route('login') }}" class="text-ink underline">Login</a></div>
    </form>
  </div>
</div>
</body>
</html>
