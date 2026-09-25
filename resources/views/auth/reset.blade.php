@extends('layouts.site')

@section('content')
<section class="py-16 sm:py-24">
  <div class="max-w-[440px] mx-auto px-5">

    <div class="text-center">
      <h1 class="font-display text-[32px] font-semibold">Choose a new password</h1>
      <p class="mt-2 text-[14.5px] text-mut">At least 8 characters, with letters and numbers.</p>
    </div>

    @if($errors->any())
      <div class="mt-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-[13.5px] text-rose-200">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('password.update') }}" class="mt-7 glass rounded-3xl p-6 sm:p-7 space-y-5">
      @csrf
      <input type="hidden" name="token" value="{{ $token }}">

      <div>
        <label class="label" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email', $email) }}" required autocomplete="email" class="field" placeholder="you@company.com">
      </div>

      <div>
        <label class="label" for="password">New password</label>
        <input id="password" name="password" type="password" required autocomplete="new-password" class="field" placeholder="••••••••">
      </div>

      <div>
        <label class="label" for="password_confirmation">Confirm new password</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="field" placeholder="••••••••">
      </div>

      <button class="w-full h-12 rounded-xl btn-grad font-semibold text-[14.5px] shadow-lg shadow-ink/10">Update password</button>
    </form>

  </div>
</section>
@endsection
