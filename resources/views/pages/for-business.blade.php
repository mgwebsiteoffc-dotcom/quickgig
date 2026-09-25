@extends('layouts.site')

@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="pt-14 pb-16 relative overflow-hidden">
  <div class="absolute inset-0 -z-10 bg-gradient-to-br from-violet/[0.12] via-pink/[0.07] to-transparent"></div>
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[760px] reveal-l">
      <div class="inline-flex items-center gap-2.5 glass rounded-full pl-2 pr-3.5 py-1.5 text-[12px] font-medium">
        <span class="btn-grad text-white text-[10px] font-bold tracking-wider uppercase px-2 py-0.5 rounded-full">For business</span>
        <span class="text-white/75">Task board · team seats · one invoice</span>
      </div>

      <h1 class="mt-6 font-display text-[40px] sm:text-[56px] leading-[1.03] font-semibold">
        Stop hiring for creative work.<br><span class="grad-text">Queue the task instead.</span>
      </h1>

      <p class="mt-5 text-[16.5px] leading-7 text-mut max-w-[600px]">
        Your team drops work on a shared board. We match a verified specialist within minutes, run it
        through the QA gate and hand it back for approval. No recruiting, no agency retainers, no chasing.
      </p>

      <div class="mt-8 flex flex-wrap items-center gap-3">
        <a href="#demo-form" class="h-12 px-6 rounded-xl btn-grad font-semibold text-[14.5px] inline-flex items-center gap-2 shadow-xl shadow-pink/20">
          Book a 20-minute demo
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M5 12h14M13 6l6 6-6 6"/></svg>
        </a>
        <a href="{{ route('register') }}?type=business" class="h-12 px-6 rounded-xl glass btn-ghost font-medium text-[14.5px] inline-flex items-center hover:border-white/30">Start free — no card</a>
      </div>

      <div class="mt-8 flex flex-wrap gap-x-8 gap-y-3 text-[13px] text-mut">
        <span>✓ Tasks start in minutes</span>
        <span>✓ Escrow on every order</span>
        <span>✓ Cancel any month</span>
      </div>
    </div>

    {{-- live board preview — a real, working mock, not a screenshot --}}
    <div class="mt-14 reveal-s" x-data="boardDemo()" x-intersect.once="play()">
      <div class="glass-strong rounded-[26px] p-4 sm:p-5 ring-glow">
        <div class="flex items-center justify-between px-1 pb-3">
          <div class="flex items-center gap-2.5">
            <span class="flex gap-1.5">
              <span class="w-2.5 h-2.5 rounded-full bg-pink/70"></span>
              <span class="w-2.5 h-2.5 rounded-full bg-amber/70"></span>
              <span class="w-2.5 h-2.5 rounded-full bg-lime/70"></span>
            </span>
            <span class="text-[12.5px] text-mut">Avante Studio · task board</span>
          </div>
          <div class="flex items-center gap-2 text-[11.5px] text-mut">
            <span class="w-1.5 h-1.5 rounded-full bg-lime pulse-dot text-lime"></span>
            <span x-text="statusLine"></span>
          </div>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
          <template x-for="col in columns" :key="col.key">
            <div class="rounded-2xl border p-2.5" :class="col.ring">
              <div class="flex items-center justify-between px-1.5 py-1">
                <span class="text-[11.5px] font-semibold" :class="col.text" x-text="col.label"></span>
                <span class="text-[11px] font-mono text-white/30" x-text="cards.filter(c => c.col === col.key).length"></span>
              </div>
              <div class="mt-1.5 space-y-2 min-h-[120px]">
                <template x-for="card in cards.filter(c => c.col === col.key)" :key="card.id">
                  <div class="glass rounded-xl p-3 transition-all duration-500 animate-popIn">
                    <div class="flex items-center justify-between">
                      <span class="text-[9.5px] font-semibold tracking-wider uppercase rounded-full px-1.5 py-0.5" :class="card.tone" x-text="card.priority"></span>
                      <span class="text-[10px] font-mono text-white/25" x-text="card.uid"></span>
                    </div>
                    <div class="mt-2 text-[12.5px] font-medium leading-snug" x-text="card.title"></div>
                    <div class="mt-2.5 flex items-center justify-between">
                      <div class="flex items-center gap-1.5">
                        <img :src="card.img" class="w-5 h-5 rounded-full object-cover border border-white/15" alt="">
                        <span class="text-[10.5px] text-mut" x-text="card.who"></span>
                      </div>
                      <span class="text-[10.5px] font-mono text-mut" x-text="card.due"></span>
                    </div>
                  </div>
                </template>
              </div>
            </div>
          </template>
        </div>
      </div>
      <div class="mt-3 text-center text-[12px] text-mut">Watch a task move across the board — this is the real layout your team gets.</div>
    </div>
  </div>
</section>

{{-- ═══════════════ COST COMPARISON (light) ═══════════════ --}}
<section class="band-light py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8" x-data="hireCalc()">
    <div class="max-w-[640px] reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-deep">The maths</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1] text-deep">Why spend months hiring for work that ships this week?</h2>
      <p class="mt-4 text-[15px] leading-7 text-mut">Tick the roles you would otherwise hire. The comparison updates live.</p>
    </div>

    <div class="mt-12 grid lg:grid-cols-2 gap-5 items-start">
      {{-- hiring --}}
      <div class="glass rounded-3xl p-7">
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">Traditional hiring</div>
        <div class="mt-5 space-y-2.5">
          <template x-for="role in roles" :key="role.name">
            <label class="flex items-center justify-between gap-4 rounded-2xl border border-white/10 px-4 py-3 cursor-pointer transition"
                   :class="role.on ? 'bg-pink/6 border-pink/30' : 'hover:border-white/25'">
              <span class="flex items-center gap-3">
                <input type="checkbox" x-model="role.on" class="w-4 h-4 rounded accent-violet">
                <span class="text-[13.5px] font-medium text-deep" x-text="role.name"></span>
              </span>
              <span class="text-[13px] font-mono text-mut" x-text="'₹' + role.cost.toLocaleString('en-IN') + '/mo'"></span>
            </label>
          </template>
          <div class="flex items-center justify-between rounded-2xl border border-white/10 px-4 py-3">
            <span class="text-[13.5px] text-mut">Management, tools and downtime</span>
            <span class="text-[13px] font-mono text-mut">₹18,000/mo</span>
          </div>
        </div>

        <div class="mt-6 pt-5 border-t border-white/10 flex items-end justify-between">
          <div>
            <div class="text-[12px] text-mut">Monthly cost</div>
            <div class="font-display text-[34px] font-semibold text-deep" x-text="'₹' + hiring.toLocaleString('en-IN')"></div>
          </div>
          <div class="text-right text-[12.5px] text-mut">Time to productive<br><span class="text-deep font-medium">6–10 weeks</span></div>
        </div>
      </div>

      {{-- quick gigs --}}
      <div class="glass-strong rounded-3xl p-7 ring-glow-pink relative">
        <span class="absolute -top-3 left-7 btn-grad text-white text-[10.5px] font-bold tracking-wider uppercase px-3 py-1 rounded-full shadow-lg">With Quick GIGS</span>
        <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45 mt-1">Subscription</div>

        <ol class="mt-5 space-y-3">
          @foreach([
            ['Queue the task', 'Anyone on your team, 30 seconds, from the board.'],
            ['We assign the specialist', 'Scored match from the verified pool, within minutes.'],
            ['Approve the delivery', 'QA gate runs first. Escrow releases only when you say so.'],
          ] as $i => [$t, $b])
            <li class="flex gap-3.5">
              <span class="w-7 h-7 rounded-xl btn-grad grid place-items-center text-[12px] font-bold text-white shrink-0">{{ $i + 1 }}</span>
              <span>
                <span class="block text-[14px] font-semibold text-deep">{{ $t }}</span>
                <span class="block text-[13px] leading-6 text-mut">{{ $b }}</span>
              </span>
            </li>
          @endforeach
        </ol>

        <div class="mt-6 pt-5 border-t border-white/10 flex items-end justify-between">
          <div>
            <div class="text-[12px] text-mut">Starting at</div>
            <div class="font-display text-[34px] font-semibold text-violet-deep">₹24,999<span class="text-[15px] text-mut">/mo</span></div>
          </div>
          <div class="text-right text-[12.5px] text-mut">Time to first delivery<br><span class="text-deep font-medium">3 hours</span></div>
        </div>

        <div class="mt-5 rounded-2xl bg-gradient-to-br from-lime/15 to-transparent border border-lime/25 p-4">
          <div class="text-[12.5px] text-mut">You keep</div>
          <div class="font-display text-[26px] font-semibold text-[#3B7D12]" x-text="'₹' + saved.toLocaleString('en-IN') + ' every month'"></div>
        </div>
      </div>
    </div>
  </div>
</section>

{{-- ═══════════════ CATEGORIES ═══════════════ --}}
<section class="py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px] reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-amber-soft">One platform</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1]">Every creative task your team requests.</h2>
      <p class="mt-4 text-[15px] leading-7 text-mut">Pick a category when you create the task — the engine matches a specialist who does exactly that.</p>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
      @foreach($categories as $i => [$name, $desc, $grad])
        <div class="reveal-s rounded-3xl p-5 bg-gradient-to-br {{ $grad }} text-white shadow-lg hover:-translate-y-1.5 transition-transform duration-300" data-delay="{{ $i * 60 }}">
          <div class="font-display text-[17px] font-semibold">{{ $name }}</div>
          <div class="mt-1.5 text-[12.5px] text-white/80">{{ $desc }}</div>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════ WORKSPACE FEATURES (lavender) ═══════════════ --}}
<section class="band-lav py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="max-w-[620px] reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-deep">The workspace</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1] text-deep">Everything your team needs, nothing it doesn't.</h2>
    </div>

    <div class="mt-12 grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
      @foreach([
        ['Shared task board', 'Five columns from queued to delivered. Anyone with a seat can add work; priorities and due dates are visible to everyone.'],
        ['Auto-assignment', 'Every new task is scored against the verified pool and assigned in minutes — with the reasons shown.'],
        ['One-click escrow orders', 'Turn any board task into a funded order. Money is held until your approver signs off.'],
        ['Seats and approvals', 'Requesters raise tasks, one approver releases payment. Every action is attributed.'],
        ['Monthly credits', 'Your plan includes a task allowance. Overflow is charged at plain per-gig pricing — never a surprise retainer.'],
        ['One invoice', 'GST-compliant, consolidated monthly, with a per-task breakdown your finance team will accept.'],
      ] as $i => [$t, $b])
        <div class="reveal glass rounded-3xl p-6 card-hover" data-delay="{{ $i * 70 }}">
          <div class="w-10 h-10 rounded-2xl bg-gradient-to-br from-violet to-pink grid place-items-center text-white shadow-lg">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="M20 6 9 17l-5-5"/></svg>
          </div>
          <div class="mt-4 text-[16px] font-semibold text-deep">{{ $t }}</div>
          <p class="mt-2 text-[13.5px] leading-6 text-mut">{{ $b }}</p>
        </div>
      @endforeach
    </div>
  </div>
</section>

{{-- ═══════════════ PLANS ═══════════════ --}}
<section class="py-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8">
    <div class="text-center max-w-[620px] mx-auto reveal">
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-teal">Business plans</div>
      <h2 class="mt-3 font-display text-[32px] sm:text-[40px] font-semibold leading-[1.1]">Pick an allowance. Overflow stays at list price.</h2>
      <p class="mt-4 text-[15px] leading-7 text-mut">Each plan includes monthly task credits, team seats and a delivery SLA. Cancel or switch any month.</p>
    </div>

    <div class="mt-12 grid md:grid-cols-3 gap-5">
      @foreach(['starter', 'growth', 'scale'] as $i => $key)
        @php $t = $tiers[$key]; $featured = $key === 'growth'; @endphp
        <div class="reveal rounded-3xl p-7 card-hover relative {{ $featured ? 'glass-strong ring-glow' : 'glass' }}" data-delay="{{ $i * 90 }}">
          @if($featured)
            <span class="absolute -top-3 left-7 btn-grad text-white text-[10.5px] font-bold tracking-wider uppercase px-3 py-1 rounded-full shadow-lg">Most teams pick this</span>
          @endif
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">{{ $t['label'] }}</div>
          <div class="mt-4 flex items-baseline gap-1.5">
            <span class="font-display text-[36px] font-semibold tracking-tight">₹{{ number_format($t['price']) }}</span>
            <span class="text-[13px] text-mut">/ month</span>
          </div>
          <div class="mt-2 text-[13.5px] text-mut">{{ $t['credits'] }} task credits · {{ $t['seats'] }} seats</div>

          <ul class="mt-6 space-y-3">
            @foreach([
              $t['credits'] . ' delivered tasks included',
              $t['seats'] . ' team seats with roles',
              $key === 'starter' ? 'Next-day SLA' : ($key === 'growth' ? 'Same-day SLA on priority tasks' : '3-hour express SLA'),
              $key === 'scale' ? 'Dedicated creator pod + manager' : 'Auto-assignment from the verified pool',
              'Escrow, QA gate and consolidated invoice',
            ] as $f)
              <li class="flex gap-2.5 text-[13.5px] leading-5">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="{{ $featured ? '#FF4D8D' : '#7C5CFF' }}" stroke-width="2.6" class="shrink-0 mt-0.5"><path d="M20 6 9 17l-5-5"/></svg>
                <span class="text-white/80">{{ $f }}</span>
              </li>
            @endforeach
          </ul>

          <a href="#demo-form" class="mt-7 h-12 rounded-xl grid place-items-center font-semibold text-[14px] transition {{ $featured ? 'btn-grad shadow-lg shadow-pink/25' : 'glass btn-ghost hover:border-white/30' }}">
            Talk to us about {{ $t['label'] }}
          </a>
        </div>
      @endforeach
    </div>

    <div class="mt-6 text-center text-[13px] text-mut">
      Not ready to commit? <a href="{{ route('marketplace') }}" class="text-pink-soft hover:text-white transition">Order single gigs from ₹1,299</a> — the board works on pay-as-you-go too.
    </div>
  </div>
</section>

{{-- ═══════════════ FAQ + DEMO FORM ═══════════════ --}}
<section id="demo-form" class="band-light py-24 scroll-mt-20">
  <div class="max-w-shell mx-auto px-5 lg:px-8 grid lg:grid-cols-[1fr_1fr] gap-12 items-start">
    <div>
      <div class="text-[11px] font-semibold tracking-[.16em] uppercase text-violet-deep">Questions teams ask</div>
      <h2 class="mt-3 font-display text-[30px] sm:text-[36px] font-semibold leading-[1.1] text-deep">Before you book the call.</h2>

      <div class="mt-8 space-y-3" x-data="{ open: 0 }">
        @foreach([
          ['Who actually does the work?', 'Verified Quick GIGS creators — ID checked, portfolio reviewed and scored on on-time delivery. You see who is assigned, their rating and their record, on every task.'],
          ['Can we talk to the specialist directly?', 'Yes. Every task has a thread. For plans with a pod, the same creators stay on your account so they learn your brand.'],
          ['What is the delivery timeline?', 'Express tasks land in about three hours, standard next-day, larger packs in two days. The SLA is attached to your plan and credited back if missed.'],
          ['Can we rebook the same creator?', 'Yes — request them by name on a task, or lock a pod on the Scale plan.'],
          ['How many revisions are included?', 'Two free rounds on every task, translated into timestamped notes so the re-cut lands right.'],
          ['What happens if we do not use our credits?', 'Unused credits roll over for one month. You can also downgrade or pause with no exit fee.'],
        ] as $i => [$q, $a])
          <div class="glass rounded-2xl overflow-hidden">
            <button type="button" x-on:click="open = open === {{ $i }} ? -1 : {{ $i }}" class="w-full flex items-center justify-between gap-5 p-5 text-left">
              <span class="text-[14.5px] font-medium text-deep">{{ $q }}</span>
              <span class="w-7 h-7 rounded-full grid place-items-center shrink-0 transition-all duration-300" :class="open === {{ $i }} ? 'btn-grad text-white rotate-180' : 'bg-deep/6 text-mut'">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4"><path d="m6 9 6 6 6-6"/></svg>
              </span>
            </button>
            <div x-show="open === {{ $i }}" x-collapse x-cloak>
              <div class="px-5 pb-5 text-[14px] leading-7 text-mut">{{ $a }}</div>
            </div>
          </div>
        @endforeach
      </div>
    </div>

    <div class="glass-strong rounded-3xl p-6 sm:p-8 ring-glow lg:sticky lg:top-24">
      <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-white/45">Book a demo</div>
      <h3 class="mt-2 font-display text-[22px] font-semibold text-deep">See the board with your own tasks in it</h3>
      <p class="mt-2 text-[13.5px] leading-6 text-mut">Twenty minutes. We set up your workspace live, queue two real tasks and you keep the output either way.</p>

      @if($errors->any())
        <div class="mt-5 rounded-2xl border border-rose-500/30 bg-rose-500/10 px-4 py-3 text-[13px] text-rose-300">{{ $errors->first() }}</div>
      @endif

      <form method="POST" action="{{ route('leads.store') }}" class="mt-6 space-y-4">
        @csrf
        <input type="hidden" name="type" value="enterprise">
        <input type="hidden" name="source" value="for-business">

        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="label" for="b_name">Your name</label>
            <input id="b_name" name="name" required value="{{ old('name') }}" class="field" placeholder="Rohan Sharma">
          </div>
          <div>
            <label class="label" for="b_email">Work email</label>
            <input id="b_email" name="email" type="email" required value="{{ old('email') }}" class="field" placeholder="you@company.com">
          </div>
        </div>
        <div class="grid sm:grid-cols-2 gap-4">
          <div>
            <label class="label" for="b_company">Company</label>
            <input id="b_company" name="company" value="{{ old('company') }}" class="field" placeholder="Avante Studio">
          </div>
          <div>
            <label class="label" for="b_volume">Tasks per month</label>
            <select id="b_volume" name="volume" class="field">
              @foreach(['Under 10 tasks', '10–25 tasks', '25–60 tasks', '60+ tasks'] as $v)
                <option value="{{ $v }}">{{ $v }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div>
          <label class="label" for="b_message">What would you queue first?</label>
          <textarea id="b_message" name="message" rows="3" class="field" placeholder="Weekly reels for two brands, plus thumbnails and a monthly AI ad.">{{ old('message') }}</textarea>
        </div>

        <button class="w-full h-12 rounded-xl btn-grad font-semibold text-[14.5px] shadow-lg shadow-pink/20">Book the demo</button>
        <div class="text-center text-[11.5px] text-mut">One reply from a human, within a working day.</div>
      </form>
    </div>
  </div>
</section>

@include('partials.cta', [
  'eyebrow'   => 'Or skip the call',
  'title'     => 'Create a workspace and queue your first task in 60 seconds.',
  'body'      => 'Free to start, pay-as-you-go pricing, and you can move to a plan whenever the volume justifies it.',
  'primary'   => ['Start free', route('register') . '?type=business'],
  'secondary' => ['See the marketplace', route('marketplace')],
])

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@alpinejs/intersect@3.x.x/dist/cdn.min.js" defer></script>
<script>
document.addEventListener('alpine:init', () => {

  /* the hero board: a task walks across the columns on a loop */
  Alpine.data('boardDemo', () => ({
    statusLine: 'idle',
    columns: [
      { key: 'queued',     label: 'Queued',        ring: 'border-white/12',  text: 'text-mut' },
      { key: 'assigned',   label: 'Assigned',      ring: 'border-violet/30', text: 'text-violet-soft' },
      { key: 'production', label: 'In production', ring: 'border-pink/30',   text: 'text-pink-soft' },
      { key: 'done',       label: 'Delivered',     ring: 'border-lime/30',   text: 'text-lime' },
    ],
    cards: [
      { id: 1, uid: 'T-7K2A', col: 'queued',     title: 'Diwali campaign — hero reel',   who: 'Matching…',    img: 'https://i.pravatar.cc/60?img=5',  due: '2 Oct', priority: 'Urgent', tone: 'bg-pink/20 text-pink-soft' },
      { id: 2, uid: 'T-M31C', col: 'queued',     title: 'Amazon A+ banner set',          who: 'Matching…',    img: 'https://i.pravatar.cc/60?img=9',  due: '4 Oct', priority: 'Normal', tone: 'bg-white/10 text-mut' },
      { id: 3, uid: 'T-B84P', col: 'assigned',   title: 'UGC testimonial — protein bar', who: 'Riya M.',      img: 'https://i.pravatar.cc/60?img=32', due: '1 Oct', priority: 'High',   tone: 'bg-amber/20 text-amber-soft' },
      { id: 4, uid: 'T-Q19X', col: 'production', title: 'Podcast — 5 vertical shorts',   who: 'Rahul V.',     img: 'https://i.pravatar.cc/60?img=12', due: '3 Oct', priority: 'High',   tone: 'bg-amber/20 text-amber-soft' },
      { id: 5, uid: 'T-D55L', col: 'done',       title: 'AI product ad — serum',         who: 'Priya S.',     img: 'https://i.pravatar.cc/60?img=5',  due: 'Sent',  priority: 'Normal', tone: 'bg-white/10 text-mut' },
    ],
    play() {
      const flow = ['queued', 'assigned', 'production', 'done'];
      const labels = {
        queued: 'task queued', assigned: 'specialist assigned',
        production: 'in production', done: 'delivered for approval',
      };
      let i = 0;
      const step = () => {
        const card = this.cards[0];
        const next = flow[Math.min(flow.length - 1, flow.indexOf(card.col) + 1)];
        card.col = next;
        card.who = next === 'queued' ? 'Matching…' : 'Priya S.';
        this.statusLine = labels[next];
        if (next === 'done') {
          setTimeout(() => { card.col = 'queued'; card.who = 'Matching…'; this.statusLine = 'task queued'; }, 2600);
        }
        i++;
        setTimeout(step, next === 'done' ? 4200 : 2200);
      };
      setTimeout(step, 700);
    },
  }));

  /* hiring vs subscription */
  Alpine.data('hireCalc', () => ({
    roles: [
      { name: 'Graphic designer', cost: 40000, on: true },
      { name: 'Video editor',     cost: 45000, on: true },
      { name: 'UI/UX designer',   cost: 55000, on: false },
      { name: 'AI content lead',  cost: 50000, on: false },
    ],
    get hiring() { return this.roles.filter(r => r.on).reduce((n, r) => n + r.cost, 0) + 18000; },
    get saved()  { return Math.max(0, this.hiring - 24999); },
  }));
});
</script>
@endpush
