<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
@include('components.seo', ['seo' => $seo ?? [], 'faqs' => $faqs ?? null, 'blog' => $blog ?? null, 'breadcrumbs' => $breadcrumbs ?? null])
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/collapse@3.x.x/dist/cdn.min.js"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='26' fill='%237C5CFF'/><path d='M54 18 30 56h18l-4 26 26-40H52z' fill='white'/></svg>">
<script>
tailwind.config = {
  theme: {
    extend: {
      colors: {
        ink:    '#0A0A0B',
        ink2:   '#141416',
        ink3:   '#1E1E21',
        cream:  '#FAFAFA',
        lav:    '#F4F4F5',
        deep:   '#0A0A0B',
        txt:    '#0A0A0B',
        mut:    '#71717A',
        /* the only brand colour — unjob-style mint */
        mint:   { DEFAULT:'#00C48C', deep:'#00A276', soft:'#7FE7C6', wash:'#E8FAF3' },
        violet: { DEFAULT:'#00C48C', soft:'#00A276', deep:'#00A276' },
        /* status only */
        lime:   { DEFAULT:'#00C48C' },
        amber:  { DEFAULT:'#B45309', soft:'#D97706' },
        pink:   { DEFAULT:'#BE123C', soft:'#E11D48' },
        teal:   { DEFAULT:'#00C48C' },
        cyan:   { DEFAULT:'#00C48C' },
      },
      fontFamily: {
        display: ['"Space Grotesk"','system-ui','sans-serif'],
        sans: ['Inter','system-ui','sans-serif'],
      },
      borderRadius: { '4xl':'2rem' },
      maxWidth: { shell: '1180px' },
      keyframes: {
        floaty:  { '0%,100%':{ transform:'translateY(0) rotate(0deg)' }, '50%':{ transform:'translateY(-14px) rotate(2deg)' } },
        drift:   { '0%,100%':{ transform:'translate3d(0,0,0) scale(1)' }, '50%':{ transform:'translate3d(3%,-4%,0) scale(1.08)' } },
        shimmer: { '0%':{ backgroundPosition:'0% 50%' }, '100%':{ backgroundPosition:'200% 50%' } },
        popIn:   { '0%':{ opacity:'0', transform:'scale(.92)' }, '100%':{ opacity:'1', transform:'scale(1)' } },
      },
      animation: {
        floaty:  'floaty 6s ease-in-out infinite',
        drift:   'drift 18s ease-in-out infinite',
        shimmer: 'shimmer 3s linear infinite',
        popIn:   'popIn .4s cubic-bezier(.2,.8,.3,1) both',
      },
    }
  }
}
</script>
<style>
  :root {
    color-scheme: light;
    --ink:#0A0A0B; --paper:#FFFFFF; --mint:#00C48C; --line:rgba(10,10,11,.10);
    --ease: cubic-bezier(.22,.8,.26,1);
  }
  html { scroll-behavior:smooth; }
  body { font-family: Inter, system-ui, sans-serif; background:var(--paper); color:var(--ink); -webkit-font-smoothing:antialiased; overflow-x:hidden; }
  h1,h2,h3,h4,.font-display { font-family:"Space Grotesk", system-ui, sans-serif; letter-spacing:-0.02em; }
  ::selection { background:#00C48C; color:#04120D; }

  /* page enters softly */
  body { animation: pageIn .5s var(--ease) both; }
  @keyframes pageIn { from { opacity:0; transform:translateY(6px) } to { opacity:1; transform:none } }

  /* ── ambient colour field ── */
  .aurora { display:none; }
  @keyframes drift { 0%,100%{ transform:translate3d(0,0,0) scale(1) } 50%{ transform:translate3d(2%,-3%,0) scale(1.05) } }

  /* faint isometric lattice, top of page only */
  .grid-bg { position:fixed; inset:0; z-index:-1; pointer-events:none;
    background-image:linear-gradient(30deg, rgba(10,10,11,.045) 1px, transparent 1px), linear-gradient(-30deg, rgba(10,10,11,.045) 1px, transparent 1px);
    background-size:56px 32px; mask-image:linear-gradient(to bottom, #000 0%, transparent 46%); }

  /* ── surfaces ── */
  .glass { background:#fff; border:1px solid var(--line); }
  .glass-strong { background:#fff; border:1px solid rgba(10,10,11,.14); box-shadow:0 20px 50px -34px rgba(10,10,11,.45); }

  /* helper text tones */
  .text-body  { color:#3F3F46; }
  .text-faint { color:#8A8A93; }
  .bg-tint    { background:rgba(10,10,11,.035); }
  .border-line{ border-color:var(--line); }

  /* the few deliberately black sections */
  .band-dark { background:var(--ink); color:#FAFAFA; }
  .band-dark .glass { background:rgba(255,255,255,.06); border-color:rgba(255,255,255,.12); }
  .band-dark .glass-strong { background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.16); box-shadow:none; }
  .band-dark .text-body { color:rgba(250,250,250,.82); }
  .band-dark .text-faint, .band-dark .text-mut { color:rgba(250,250,250,.55); }
  .band-dark .bg-tint { background:rgba(255,255,255,.07); }
  .band-dark .border-line { border-color:rgba(255,255,255,.14); }
  .band-dark .btn-grad { background:#fff; color:#0A0A0B; }
  .band-dark .btn-grad:hover { background:#E8FAF3; }
  .band-dark .field { background:rgba(255,255,255,.08); border-color:rgba(255,255,255,.18); color:#fff; }
  .band-dark .label { color:rgba(250,250,250,.6); }
  .band-dark .text-ink, .band-dark .text-deep { color:#FAFAFA; }
  .band-dark .hover\:text-ink:hover, .band-dark .group:hover .group-hover\:text-ink { color:#fff; }
  .band-dark .text-mint-deep { color:#3BE0AE; }
  .band-dark .bg-ink { background:rgba(255,255,255,.10); }
  .band-dark .bg-mint-wash { background:rgba(0,196,140,.16); }
  .band-dark .badge-off { background:rgba(0,196,140,.18); color:#7FE7C6; }
  .band-dark .price-strike { color:rgba(250,250,250,.45); }
  .band-dark .skeleton { background:linear-gradient(90deg, rgba(255,255,255,.07) 25%, rgba(255,255,255,.16) 50%, rgba(255,255,255,.07) 75%); background-size:200% 100%; }

  /* light bands break up the dark — same markup, flipped surface */
  .band-light { background:#FAFAFA; color:var(--ink); position:relative; z-index:1; }
  .band-lav   { background:#F4F4F5; color:var(--ink); position:relative; z-index:1; }

  /* ── colour helpers ── */
  .grad-text { color:#00A276; }
  .band-dark .grad-text { color:#00C48C; }
  .btn-grad { background:#0A0A0B; color:#fff; transition:background .25s var(--ease), transform .25s var(--ease), box-shadow .3s var(--ease); }
  .btn-grad:hover { background:#26262A; transform:translateY(-1px); }
  .btn-mint { background:#00C48C; color:#04120D; transition:background .25s var(--ease), transform .25s var(--ease); }
  .btn-mint:hover { background:#00A276; transform:translateY(-1px); }
  .btn-ghost { transition:background .25s var(--ease), border-color .25s var(--ease), transform .25s var(--ease); }
  .btn-ghost:hover { transform:translateY(-1px); }
  .ring-glow, .ring-glow-pink { box-shadow:0 0 0 1px rgba(0,196,140,.35), 0 24px 60px -34px rgba(10,10,11,.35); }

  .card-hover { transition:transform .35s var(--ease), border-color .35s var(--ease), box-shadow .35s var(--ease); will-change:transform; }
  .card-hover:hover { transform:translateY(-4px); border-color:rgba(0,196,140,.55); box-shadow:0 22px 44px -28px rgba(10,10,11,.3); }
  .band-dark .card-hover:hover { box-shadow:0 22px 44px -28px rgba(0,0,0,.8); }

  .pulse-dot { position:relative; }
  .pulse-dot::after { content:''; position:absolute; inset:-4px; border-radius:50%; border:1px solid currentColor; opacity:.5; animation:pulseRing 1.8s ease-out infinite; }
  @keyframes pulseRing { 0%{transform:scale(.7);opacity:.7} 100%{transform:scale(1.5);opacity:0} }

  .marquee { display:flex; gap:3rem; width:max-content; animation:slide 34s linear infinite; }
  .marquee:hover { animation-play-state:paused; }
  @keyframes slide { from{transform:translateX(0)} to{transform:translateX(-50%)} }

  /* ── scroll reveal, staggered ── */
  .reveal { opacity:0; transform:translateY(22px); transition:opacity .7s var(--ease), transform .7s var(--ease); }
  .reveal.in { opacity:1; transform:none; }
  .reveal-l { opacity:0; transform:translateX(-24px); transition:opacity .7s var(--ease), transform .7s var(--ease); }
  .reveal-l.in { opacity:1; transform:none; }
  .reveal-s { opacity:0; transform:scale(.94); transition:opacity .6s var(--ease), transform .6s var(--ease); }
  .reveal-s.in { opacity:1; transform:none; }

  /* scroll progress */
  #progress { position:fixed; top:0; left:0; height:2px; width:0; z-index:60; background:#00C48C; transition:width .12s linear; }

  /* ── form controls ── */
  input, textarea, select { font-family:Inter, sans-serif; }
  .field { width:100%; background:#fff; border:1px solid rgba(10,10,11,.14); border-radius:14px; padding:0 14px; height:48px; color:#0A0A0B; font-size:14px; outline:none; transition:.25s var(--ease); }
  textarea.field { padding:12px 14px; height:auto; line-height:1.55; }
  .field:focus { border-color:#00C48C; background:#fff; box-shadow:0 0 0 4px rgba(0,196,140,.16); }
  .field::placeholder { color:#A1A1AA; }

  .label { font-size:11px; font-weight:600; letter-spacing:.12em; text-transform:uppercase; color:#71717A; display:block; margin-bottom:7px; }
  select.field option { background:#fff; color:#0A0A0B; }
  [x-cloak] { display:none !important; }

  .badge-off { background:#E8FAF3; color:#00845F; }
  .price-strike { color:#A1A1AA; }

  /* inline loading shimmer */
  .skeleton { background:linear-gradient(90deg, rgba(10,10,11,.05) 25%, rgba(10,10,11,.10) 50%, rgba(10,10,11,.05) 75%); background-size:200% 100%; animation:shimmer 1.4s linear infinite; }
  @keyframes shimmer { 0%{background-position:0% 50%} 100%{background-position:200% 50%} }

  @media (prefers-reduced-motion: reduce) {
    *, *::before, *::after { animation-duration:.01ms !important; animation-iteration-count:1 !important; transition-duration:.01ms !important; }
    .reveal, .reveal-l, .reveal-s { opacity:1 !important; transform:none !important; }
  }
</style>
@stack('styles')
</head>
<body class="min-h-screen antialiased">
<div id="progress"></div>
<div class="aurora"></div>
<div class="grid-bg"></div>

@include('partials.nav')

<main>
  @yield('content')
</main>

@include('partials.footer')

{{-- toast --}}
<div x-data="{ show:{{ session('toast') ? 'true' : 'false' }}, msg:@js(session('toast') ?? '') }"
     x-on:toast.window="msg = $event.detail; show = true; setTimeout(() => show = false, 4200)"
     x-show="show" x-cloak x-init="if(show) setTimeout(()=>show=false, 5200)"
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-200" x-transition:leave-end="opacity-0 translate-y-2"
     class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[80] glass-strong rounded-full pl-4 pr-3 py-2.5 flex items-center gap-3 text-[13px] font-medium shadow-2xl max-w-[92vw]">
  <span class="w-2 h-2 rounded-full bg-mint shrink-0 pulse-dot text-mint-deep"></span>
  <span x-text="msg" class="truncate text-white"></span>
  <button x-on:click="show=false" class="w-6 h-6 rounded-full hover:bg-tint grid place-items-center text-mut shrink-0">✕</button>
</div>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    /* scroll progress */
    const bar = document.getElementById('progress');
    const onScroll = () => {
      const h = document.documentElement;
      const pct = (h.scrollTop / Math.max(1, h.scrollHeight - h.clientHeight)) * 100;
      bar.style.width = pct + '%';
    };
    document.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    /* staggered reveals */
    const items = document.querySelectorAll('.reveal, .reveal-l, .reveal-s');
    const showAll = () => items.forEach(el => el.classList.add('in'));

    if (reduced || !('IntersectionObserver' in window)) { showAll(); }
    else {
      const io = new IntersectionObserver((entries) => {
        entries.forEach((e, i) => {
          if (!e.isIntersecting) return;
          const delay = parseInt(e.target.dataset.delay || (i * 70), 10);
          setTimeout(() => e.target.classList.add('in'), Math.min(delay, 420));
          io.unobserve(e.target);
        });
      }, { threshold: 0.1, rootMargin: '0px 0px -60px 0px' });
      items.forEach(el => io.observe(el));
      setTimeout(showAll, 3000);
    }

    /* count-up numbers: <span data-count="18400" data-suffix="+"> */
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length && 'IntersectionObserver' in window && !reduced) {
      const co = new IntersectionObserver((entries) => {
        entries.forEach(e => {
          if (!e.isIntersecting) return;
          const el = e.target, target = parseFloat(el.dataset.count), dec = parseInt(el.dataset.decimals || 0, 10);
          const suffix = el.dataset.suffix || '', prefix = el.dataset.prefix || '';
          const start = performance.now(), dur = 1400;
          const tick = (now) => {
            const p = Math.min(1, (now - start) / dur);
            const eased = 1 - Math.pow(1 - p, 3);
            const v = target * eased;
            el.textContent = prefix + (dec ? v.toFixed(dec) : Math.round(v).toLocaleString('en-IN')) + suffix;
            if (p < 1) requestAnimationFrame(tick);
          };
          requestAnimationFrame(tick);
          co.unobserve(el);
        });
      }, { threshold: 0.4 });
      counters.forEach(el => co.observe(el));
    } else {
      counters.forEach(el => {
        const dec = parseInt(el.dataset.decimals || 0, 10);
        el.textContent = (el.dataset.prefix || '') + (dec ? parseFloat(el.dataset.count).toFixed(dec) : parseFloat(el.dataset.count).toLocaleString('en-IN')) + (el.dataset.suffix || '');
      });
    }
  });

  /* tiny helper used by the inline (no-reload) forms */
  window.qg = {
    async post(url, data) {
      const res = await fetch(url, {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
          'X-Requested-With': 'XMLHttpRequest',
          'Accept': 'application/json',
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
      });
      if (!res.ok) throw new Error('HTTP ' + res.status);
      return res.json();
    },
    toast(msg) { window.dispatchEvent(new CustomEvent('toast', { detail: msg })); },
    scrollTo(el) { el?.scrollIntoView({ behavior: 'smooth', block: 'start' }); },
  };
</script>
@stack('scripts')
</body>
</html>
