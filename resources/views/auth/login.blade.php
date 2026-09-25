@extends('layouts.site')

@section('content')
<section class="py-16 sm:py-24">
  <div class="max-w-[440px] mx-auto px-5">

    <div class="text-center">
      <h1 class="font-display text-[32px] font-semibold">Welcome back</h1>
      <p class="mt-2 text-[14.5px] text-mut">Log in to track gigs, releases and payouts.</p>
    </div>

    @if($errors->any())
      <div class="mt-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-[13.5px] text-rose-200">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('login.post') }}" class="mt-7 glass rounded-3xl p-6 sm:p-7 space-y-5">
      @csrf

      <div>
        <label class="label" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="field" placeholder="you@company.com">
      </div>

      <div>
        <div class="flex items-center justify-between">
          <label class="label" for="password">Password</label>
          <a href="#" class="text-[11.5px] text-mut hover:text-ink mb-[7px]">Forgot?</a>
        </div>
        <input id="password" name="password" type="password" required autocomplete="current-password" class="field" placeholder="••••••••">
      </div>

      <label class="flex items-center gap-2.5 text-[13.5px] text-mut">
        <input type="checkbox" name="remember" value="1" class="w-4 h-4 rounded border-line bg-tint accent-violet">
        Keep me logged in
      </label>

      <button class="w-full h-12 rounded-xl btn-grad font-semibold text-[14.5px] shadow-lg shadow-ink/10">Log in</button>

      <div class="text-center text-[13.5px] text-mut">
        New to Quick GIGS? <a href="{{ route('register') }}" class="text-ink font-medium underline decoration-mint decoration-2 underline-offset-4 hover:text-mint-deep">Create a free account</a>
      </div>
    </form>

    {{-- demo accounts --}}
    <div x-data="{ fill(e,p){ document.getElementById('email').value=e; document.getElementById('password').value=p; } }" class="mt-5 glass rounded-3xl p-5">
      <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Demo accounts — tap to fill</div>
      <div class="mt-3 space-y-2">
        @foreach([
          ['label' => 'Business', 'email' => 'business@quickgigs.in', 'pass' => 'Business@123'],
          ['label' => 'Freelancer',  'email' => 'creator@quickgigs.in',  'pass' => 'Freelancer@123'],
          ['label' => 'Admin',    'email' => 'admin@quickgigs.in',    'pass' => 'Admin@12345'],
        ] as $d)
          <button type="button" x-on:click="fill(@js($d['email']), @js($d['pass']))"
                  class="w-full flex items-center justify-between gap-3 rounded-2xl border border-line bg-tint px-4 py-2.5 hover:border-mint transition text-left">
            <span class="text-[12.5px] font-mono text-body truncate">{{ $d['email'] }}</span>
            <span class="text-[11px] font-semibold rounded-full bg-mint-wash text-mint-deep px-2.5 py-1 shrink-0">{{ $d['label'] }}</span>
          </button>
        @endforeach
      </div>
      <div class="mt-3 text-[11.5px] text-mut">Seeded by <span class="font-mono text-faint">php artisan migrate --seed</span>.</div>
    </div>
  </div>
</section>
@endsection
