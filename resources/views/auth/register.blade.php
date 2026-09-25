@extends('layouts.site')

@section('content')
<section class="py-16 sm:py-20">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[1fr_0.85fr] gap-12 items-start">

    {{-- form --}}
    <div class="max-w-[520px] w-full mx-auto lg:mx-0 order-2 lg:order-1"
         x-data="{ type: @js(old('account_type', $type)) }">

      <h1 class="font-display text-[32px] sm:text-[38px] font-semibold leading-tight">Create your free account</h1>
      <p class="mt-2.5 text-[15px] text-mut">Takes about 30 seconds. No card required.</p>

      {{-- account type switch --}}
      <div class="mt-7 grid grid-cols-2 gap-2.5">
        <button type="button" x-on:click="type='business'"
                class="rounded-2xl p-4 text-left border transition"
                :class="type==='business' ? 'border-mint bg-mint-wash' : 'border-line bg-tint hover:border-line'">
          <div class="text-[14px] font-semibold">I need work done</div>
          <div class="text-[12px] text-mut mt-0.5">Hire verified freelancers</div>
        </button>
        <button type="button" x-on:click="type='creator'"
                class="rounded-2xl p-4 text-left border transition"
                :class="type==='creator' ? 'border-mint bg-mint-wash' : 'border-line bg-tint hover:border-line'">
          <div class="text-[14px] font-semibold">I do the work</div>
          <div class="text-[12px] text-mut mt-0.5">Freelancer or creator</div>
        </button>
      </div>

      @if($errors->any())
        <div class="mt-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3.5 text-[13.5px] text-rose-200">
          <div class="font-semibold mb-1">Please fix the following:</div>
          <ul class="list-disc list-inside space-y-0.5">
            @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
          </ul>
        </div>
      @endif

      <form method="POST" action="{{ route('register.post') }}" class="mt-6 glass rounded-3xl p-6 sm:p-7 space-y-5">
        @csrf
        <input type="hidden" name="account_type" :value="type">
        @if($plan)<input type="hidden" name="plan" value="{{ $plan }}">@endif

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="label" for="name">Full name</label>
            <input id="name" name="name" value="{{ old('name') }}" required class="field" placeholder="Rohan Sharma">
          </div>
          <div x-show="type==='business'" x-cloak>
            <label class="label" for="company_name">Company / brand</label>
            <input id="company_name" name="company_name" value="{{ old('company_name') }}" class="field" placeholder="Avante Studio">
          </div>
          <div x-show="type==='creator'" x-cloak>
            <label class="label" for="handle">Handle</label>
            <input id="handle" name="handle" value="{{ old('handle') }}" class="field" placeholder="priyaedits">
          </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="label" for="email">Work email</label>
            <input id="email" name="email" type="email" value="{{ old('email') }}" required class="field" placeholder="you@company.com">
          </div>
          <div>
            <label class="label" for="phone">Phone <span class="normal-case tracking-normal text-faint">(optional)</span></label>
            <input id="phone" name="phone" value="{{ old('phone') }}" class="field" placeholder="+91 98765 43210">
          </div>
        </div>

        <div x-show="type==='creator'" x-cloak>
          <label class="label" for="skills">Your skills <span class="normal-case tracking-normal text-faint">(comma separated)</span></label>
          <input id="skills" name="skills" value="{{ old('skills') }}" class="field" placeholder="Reels, Retention editing, Captions">
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="label" for="password">Password</label>
            <input id="password" name="password" type="password" required class="field" placeholder="At least 8 characters">
          </div>
          <div>
            <label class="label" for="password_confirmation">Confirm password</label>
            <input id="password_confirmation" name="password_confirmation" type="password" required class="field" placeholder="Repeat password">
          </div>
        </div>

        <label class="flex items-start gap-3 text-[13px] text-mut leading-5">
          <input type="checkbox" name="terms" value="1" {{ old('terms') ? 'checked' : '' }} class="mt-0.5 w-4 h-4 rounded border-line bg-tint accent-violet shrink-0">
          <span>I agree to the Quick GIGS terms of service and escrow policy.</span>
        </label>

        <button class="w-full h-12 rounded-xl btn-grad font-semibold text-[14.5px] shadow-lg shadow-ink/10">
          <span x-show="type==='business'">Create account & post a gig</span>
          <span x-show="type==='creator'" x-cloak>Create talent account</span>
        </button>

        <div class="text-center text-[13.5px] text-mut">
          Already have an account? <a href="{{ route('login') }}" class="text-ink font-medium underline decoration-mint decoration-2 underline-offset-4 hover:text-mint-deep">Log in</a>
        </div>
      </form>
    </div>

    {{-- side panel --}}
    <aside class="order-1 lg:order-2 lg:sticky lg:top-24">
      <div class="glass-strong rounded-3xl p-7 ring-glow">
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">What you get</div>
        <ul class="mt-5 space-y-4">
          @foreach([
            ['Matched in minutes', 'Our engine ranks verified pros by skill, speed and rating — no bidding, no proposals.'],
            ['Escrow on every gig', 'Money is held safely and released only when you approve the delivery.'],
            ['Live production tracking', 'Watch progress, chat and preview files without chasing anyone.'],
            ['Flat 10% platform fee', 'Freelancers keep 90%. No connects, no listing fees, no subscriptions.'],
          ] as [$title, $body])
            <li class="flex gap-3.5">
              <span class="w-6 h-6 rounded-lg bg-mint-wash grid place-items-center shrink-0 mt-0.5">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#A78BFA" stroke-width="3"><path d="M20 6 9 17l-5-5"/></svg>
              </span>
              <span>
                <span class="block text-[14px] font-semibold">{{ $title }}</span>
                <span class="block text-[13px] leading-6 text-mut mt-0.5">{{ $body }}</span>
              </span>
            </li>
          @endforeach
        </ul>

        <div class="mt-7 pt-6 border-t border-line flex items-center gap-4">
          <div class="flex -space-x-2.5">
            @foreach([5,12,9,15] as $i)
              <img src="https://i.pravatar.cc/80?img={{ $i }}" class="w-8 h-8 rounded-full border-2 border-white object-cover" alt="">
            @endforeach
          </div>
          <div class="text-[12.5px] text-mut leading-tight">
            <span class="block text-ink font-medium">1,284 freelancers online</span>
            average match time 4 min
          </div>
        </div>
      </div>
    </aside>
  </div>
</section>
@endsection
