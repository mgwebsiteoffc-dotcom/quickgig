@extends('layouts.site')

@section('content')
<section class="py-16 sm:py-24">
  <div class="max-w-[440px] mx-auto px-5">

    <div class="text-center">
      <h1 class="font-display text-[32px] font-semibold">Reset your password</h1>
      <p class="mt-2 text-[14.5px] text-mut">We will email you a secure link to choose a new one.</p>
    </div>

    @if(session('toast'))
      <div class="mt-6 rounded-2xl border border-mint/30 bg-mint-wash px-4 py-3 text-[13.5px] text-mint-deep">
        {{ session('toast') }}
      </div>
    @endif

    @if($errors->any())
      <div class="mt-6 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-[13.5px] text-rose-200">
        {{ $errors->first() }}
      </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}" class="mt-7 glass rounded-3xl p-6 sm:p-7 space-y-5">
      @csrf

      <div>
        <label class="label" for="email">Email</label>
        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus autocomplete="email" class="field" placeholder="you@company.com">
      </div>

      <button class="w-full h-12 rounded-xl btn-grad font-semibold text-[14.5px] shadow-lg shadow-ink/10">Send reset link</button>

      <div class="text-center text-[13.5px] text-mut">
        Remembered it? <a href="{{ route('login') }}" class="text-ink font-medium underline decoration-mint decoration-2 underline-offset-4 hover:text-mint-deep">Back to log in</a>
      </div>
    </form>

  </div>
</section>
@endsection
