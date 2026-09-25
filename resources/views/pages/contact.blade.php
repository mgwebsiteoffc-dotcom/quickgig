@extends('layouts.site')

@section('content')

<section class="py-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[1fr_1fr] gap-12 items-start">

    <div>
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Contact</div>
      <h1 class="mt-4 font-display text-[36px] sm:text-[44px] font-semibold leading-[1.06]">Talk to a human.</h1>
      <p class="mt-5 text-[16px] leading-7 text-mut max-w-[520px]">
        Questions about a gig, freelancer verification, a pod for your team, or something that broke —
        this form reaches the same small team that builds the product.
      </p>

      <div class="mt-10 space-y-4">
        @foreach([
          ['Order or delivery issue', 'Reply within 4 working hours. Escrow stays held until it is resolved.', 'support@quickgigs.in'],
          ['Freelancer verification', 'Usually reviewed within one working day of applying.', 'freelancers@quickgigs.in'],
          ['Teams and volume', 'Pod proposals, SLAs and consolidated billing.', 'teams@quickgigs.in'],
        ] as [$t, $b, $mail])
          <div class="glass rounded-3xl p-5">
            <div class="text-[14.5px] font-semibold">{{ $t }}</div>
            <p class="mt-1 text-[13px] leading-6 text-mut">{{ $b }}</p>
            <a href="mailto:{{ $mail }}" class="mt-2 inline-block text-[13px] font-mono text-mint-deep hover:text-white transition">{{ $mail }}</a>
          </div>
        @endforeach
      </div>

      <div class="mt-8 glass rounded-3xl p-5">
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Office</div>
        <div class="mt-2 text-[14px] leading-7 text-mut">
          Quick GIGS<br>
          Ghaziabad, Uttar Pradesh, India<br>
          Mon–Sat, 10:00–19:00 IST
        </div>
      </div>
    </div>

    <div class="glass-strong rounded-3xl p-6 sm:p-8 ring-glow lg:sticky lg:top-24">
      @if($errors->any())
        <div class="mb-5 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-[13px] text-rose-200">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('leads.store') }}" class="space-y-4">
        @csrf
        <input type="hidden" name="type" value="contact">
        <input type="hidden" name="source" value="contact">

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="label" for="c_name">Name</label>
            <input id="c_name" name="name" required value="{{ old('name') }}" class="field" placeholder="Rohan Sharma">
          </div>
          <div>
            <label class="label" for="c_email">Email</label>
            <input id="c_email" name="email" type="email" required value="{{ old('email') }}" class="field" placeholder="you@company.com">
          </div>
        </div>

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="label" for="c_company">Company <span class="normal-case tracking-normal text-faint">optional</span></label>
            <input id="c_company" name="company" value="{{ old('company') }}" class="field">
          </div>
          <div>
            <label class="label" for="c_phone">Phone <span class="normal-case tracking-normal text-faint">optional</span></label>
            <input id="c_phone" name="phone" value="{{ old('phone') }}" class="field">
          </div>
        </div>

        <div>
          <label class="label" for="c_message">How can we help?</label>
          <textarea id="c_message" name="message" rows="5" class="field" placeholder="Tell us what you are trying to get made, or what went wrong.">{{ old('message') }}</textarea>
        </div>

        <button class="w-full h-12 rounded-xl btn-grad font-semibold text-[14.5px] shadow-lg shadow-ink/10">Send message</button>
        <div class="text-center text-[11.5px] text-mut">We reply within one working day. No newsletter, no sequence.</div>
      </form>
    </div>
  </div>
</section>

@endsection
