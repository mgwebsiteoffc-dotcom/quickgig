@extends('layouts.site')

@section('content')

<section class="pt-16 pb-12">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[1.05fr_0.95fr] gap-12 items-start">
    <div>
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">For teams</div>
      <h1 class="mt-4 font-display text-[38px] sm:text-[50px] font-semibold leading-[1.04]">
        Always-on content, <span class="grad-text">without the headcount</span>.
      </h1>
      <p class="mt-5 text-[16px] leading-7 text-mut max-w-[560px]">
        A dedicated pod of verified freelancers who learn your brand, an SLA that carries a credit when it
        slips, seat-based approvals for your marketers, and one invoice at the end of the month.
      </p>

      <div class="mt-9 grid sm:grid-cols-2 gap-4">
        @foreach([
          ['Dedicated pod', 'The same 3–6 freelancers on your account, so nobody re-learns your brand every week.'],
          ['Brand-locked briefs', 'Your tone, banned words, logo rules and spec pre-loaded into every generated brief.'],
          ['SLA with credits', 'Miss the window and the express premium comes back automatically — no support ticket.'],
          ['Seats and approvals', 'Marketers raise gigs, one approver releases escrow. Full audit trail per order.'],
          ['Consolidated billing', 'One GST invoice monthly instead of forty card charges.'],
          ['Volume pricing', 'Committed monthly volume moves you onto retainer rates, up to 20% below list.'],
        ] as $i => [$t, $b])
          <div class="reveal glass rounded-3xl p-5" style="transition-delay: {{ $i * 60 }}ms">
            <div class="text-[14.5px] font-semibold">{{ $t }}</div>
            <p class="mt-1.5 text-[13px] leading-6 text-mut">{{ $b }}</p>
          </div>
        @endforeach
      </div>
    </div>

    {{-- lead form --}}
    <aside class="lg:sticky lg:top-24">
      <div class="glass-strong rounded-3xl p-6 sm:p-7 ring-glow">
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">Talk to us</div>
        <h2 class="mt-2 font-display text-[22px] font-semibold">Get a pod proposal in one working day</h2>

        @if($errors->any())
          <div class="mt-5 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-[13px] text-rose-200">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('leads.store') }}" class="mt-6 space-y-4">
          @csrf
          <input type="hidden" name="type" value="enterprise">
          <input type="hidden" name="source" value="enterprise">

          <div>
            <label class="label" for="e_name">Your name</label>
            <input id="e_name" name="name" required value="{{ old('name') }}" class="field" placeholder="Priya Kapoor">
          </div>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="label" for="e_email">Work email</label>
              <input id="e_email" name="email" type="email" required value="{{ old('email') }}" class="field" placeholder="you@company.com">
            </div>
            <div>
              <label class="label" for="e_phone">Phone</label>
              <input id="e_phone" name="phone" value="{{ old('phone') }}" class="field" placeholder="+91 …">
            </div>
          </div>
          <div class="grid sm:grid-cols-2 gap-4">
            <div>
              <label class="label" for="e_company">Company</label>
              <input id="e_company" name="company" value="{{ old('company') }}" class="field" placeholder="BrandScale Media">
            </div>
            <div>
              <label class="label" for="e_volume">Monthly volume</label>
              <select id="e_volume" name="volume" class="field">
                @foreach(['10–25 deliverables', '25–60 deliverables', '60–150 deliverables', '150+ deliverables'] as $v)
                  <option value="{{ $v }}">{{ $v }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div>
            <label class="label" for="e_message">What are you producing?</label>
            <textarea id="e_message" name="message" rows="3" class="field" placeholder="Weekly reels for two brands, plus thumbnails and a monthly AI ad.">{{ old('message') }}</textarea>
          </div>

          <button class="w-full h-12 rounded-xl btn-grad font-semibold text-[14.5px] shadow-lg shadow-ink/10">Request a pod proposal</button>
          <div class="text-center text-[11.5px] text-mut">No sales sequence. One reply from a human.</div>
        </form>
      </div>
    </aside>
  </div>
</section>

{{-- ── operating model ── --}}
<section class="band-light py-20">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px]">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Operating model</div>
      <h2 class="mt-3 font-display text-[30px] sm:text-[38px] font-semibold leading-[1.1]">How a pod runs.</h2>
    </div>

    <div class="mt-12 grid md:grid-cols-4 gap-4">
      @foreach([
        ['Week 0', 'Calibration', 'We load your brand rules, past winners and banned claims into the brief engine, then run two test gigs.'],
        ['Week 1', 'Pod assembled', 'Three to six freelancers are matched to your formats and locked to your account.'],
        ['Ongoing', 'Request → deliver', 'Your team raises gigs from the dashboard. Everything runs the standard pipeline with your SLA attached.'],
        ['Monthly', 'Review + invoice', 'Delivery report, QA pass-rate, spend by format, and a single GST invoice.'],
      ] as $i => [$when, $t, $b])
        <div class="reveal glass rounded-3xl p-6" style="transition-delay: {{ $i * 70 }}ms">
          <div class="font-mono text-[11.5px] text-mint-deep">{{ $when }}</div>
          <div class="mt-2.5 text-[16px] font-semibold">{{ $t }}</div>
          <p class="mt-2 text-[13px] leading-6 text-mut">{{ $b }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ── trust ── --}}
<section class="py-16">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[0.9fr_1.1fr] gap-10">
    <div>
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-mint-deep">Controls</div>
      <h2 class="mt-3 font-display text-[28px] sm:text-[34px] font-semibold leading-[1.1]">Things procurement will ask about.</h2>
    </div>
    <div class="grid sm:grid-cols-2 gap-4">
      @foreach([
        ['Rights and licensing', 'Full commercial rights transfer on approval. Music and stock are licensed for paid media by default.'],
        ['Confidentiality', 'Briefs and assets are visible only to your workspace and the assigned freelancer. Mutual NDA available.'],
        ['Access control', 'Role-based seats: requester, approver, finance. Every escrow release is attributed and timestamped.'],
        ['Data handling', 'Files stay on your workspace storage. Deletion on request, and nothing is used for model training.'],
      ] as [$t, $b])
        <div class="glass rounded-3xl p-6">
          <div class="text-[14.5px] font-semibold">{{ $t }}</div>
          <p class="mt-1.5 text-[13px] leading-6 text-mut">{{ $b }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

@include('partials.cta', [
  'eyebrow'   => 'Start smaller if you prefer',
  'title'     => 'You do not need a contract to test us.',
  'body'      => 'Run three gigs on the standard plan this week. If the pass-rate and turnaround hold up, we will talk about a pod.',
  'primary'   => ['Browse the marketplace', route('marketplace')],
  'secondary' => ['See pricing', route('pricing')],
])

@endsection
