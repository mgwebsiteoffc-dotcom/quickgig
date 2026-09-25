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
        ink:    '#0B0B12',
        ink2:   '#13131C',
        ink3:   '#1C1C27',
        cream:  '#FAFAFB',
        lav:    '#F3F3F6',
        deep:   '#12121A',
        txt:    '#F4F4F7',
        mut:    '#9A9AAB',
        /* single brand accent */
        violet: { DEFAULT:'#5B3DF5', soft:'#A79BFF', deep:'#4A2EE0' },
        /* status only — never decoration */
        lime:   { DEFAULT:'#22C55E' },
        amber:  { DEFAULT:'#F59E0B', soft:'#FBBF24' },
        pink:   { DEFAULT:'#E11D48', soft:'#FB7185' },
        teal:   { DEFAULT:'#5B3DF5' },
        cyan:   { DEFAULT:'#5B3DF5' },
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
    color-scheme: dark;
    --ink:#0C0A1E; --cream:#FFF7F0; --deep:#191233;
    --ease: cubic-bezier(.22,.8,.26,1);
  }
  html { scroll-behavior:smooth; }
  body { font-family: Inter, system-ui, sans-serif; background:var(--ink); color:#F6F4FF; -webkit-font-smoothing:antialiased; overflow-x:hidden; }
  h1,h2,h3,h4,.font-display { font-family:"Space Grotesk", system-ui, sans-serif; letter-spacing:-0.02em; }
  ::selection { background:#5B3DF5; color:#fff; }

  /* page enters softly */
  body { animation: pageIn .5s var(--ease) both; }
  @keyframes pageIn { from { opacity:0; transform:translateY(6px) } to { opacity:1; transform:none } }

  /* ── ambient colour field ── */
  .aurora { position:fixed; inset:0; z-index:-2; overflow:hidden; pointer-events:none; }
  .aurora span { position:absolute; border-radius:50%; filter:blur(120px); animation:drift 26s ease-in-out infinite; }
  .aurora .a1 { width:46rem; height:46rem; background:radial-gradient(circle,#5B3DF5 0%,transparent 68%); top:-18rem; left:-12rem; opacity:.20; }
  .aurora .a2 { width:34rem; height:34rem; background:radial-gradient(circle,#5B3DF5 0%,transparent 68%); top:-6rem; right:-14rem; opacity:.10; animation-delay:-9s; }
  .aurora .a3, .aurora .a4 { display:none; }
  @keyframes drift { 0%,100%{ transform:translate3d(0,0,0) scale(1) } 50%{ transform:translate3d(3%,-4%,0) scale(1.08) } }

  .grid-bg { position:fixed; inset:0; z-index:-1; pointer-events:none;
    background-image:linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
    background-size:64px 64px; mask-image:radial-gradient(ellipse 100% 55% at 50% 0%, #000 40%, transparent 100%); }

  /* ── surfaces ── */
  .glass { background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.10); backdrop-filter:blur(14px); }
  .glass-strong { background:rgba(21,18,46,0.86); border:1px solid rgba(255,255,255,0.12); backdrop-filter:blur(18px); }

  /* light bands break up the dark — same markup, flipped surface */
  .band-light { background:var(--cream); color:var(--deep); position:relative; z-index:1; }
  .band-lav   { background:#F2EDFF; color:var(--deep); position:relative; z-index:1; }
  .band-light .glass, .band-lav .glass { background:#fff !important; border-color:rgba(25,18,51,.09) !important; backdrop-filter:none; box-shadow:0 18px 50px -34px rgba(25,18,51,.5); }
  .band-light .glass-strong, .band-lav .glass-strong { background:#fff !important; border-color:rgba(25,18,51,.12) !important; backdrop-filter:none; box-shadow:0 26px 70px -40px rgba(91,61,245,.55); }
  .band-light .text-mut, .band-lav .text-mut { color:#655C86 !important; }
  .band-light .text-white\/45, .band-lav .text-white\/45,
  .band-light .text-white\/40, .band-lav .text-white\/40 { color:#8A82A8 !important; }
  .band-light .text-white\/70, .band-lav .text-white\/70,
  .band-light .text-white\/80, .band-lav .text-white\/80,
  .band-light .text-white\/85, .band-lav .text-white\/85 { color:#3B3260 !important; }
  .band-light .border-white\/8, .band-lav .border-white\/8,
  .band-light .border-white\/10, .band-lav .border-white\/10 { border-color:rgba(25,18,51,.10) !important; }
  .band-light .bg-white\/3, .band-lav .bg-white\/3,
  .band-light .bg-white\/6, .band-lav .bg-white\/6 { background:rgba(25,18,51,.035) !important; }
  .band-light td.text-white, .band-lav td.text-white,
  .band-light th.text-white, .band-lav th.text-white,
  .band-light blockquote, .band-lav blockquote { color:var(--deep) !important; }
  .band-light .bg-white\/3, .band-lav .bg-white\/3 { background:rgba(25,18,51,.03) !important; }
  .band-light .grad-text, .band-lav .grad-text { color:#4A2EE0; }

  /* ── colour helpers ── */
  .grad-text { color:#A79BFF; }
  .btn-grad { background:#5B3DF5; color:#fff; transition:background .25s var(--ease), transform .25s var(--ease), box-shadow .3s var(--ease); }
  .btn-grad:hover { background:#4A2EE0; transform:translateY(-1px); }
  .band-light .btn-grad, .band-lav .btn-grad { color:#fff; }
  .btn-ghost { transition:background .25s var(--ease), border-color .25s var(--ease), transform .25s var(--ease); }
  .btn-ghost:hover { transform:translateY(-1px); }
  .ring-glow { box-shadow:0 0 0 1px rgba(91,61,245,.28), 0 22px 60px -30px rgba(91,61,245,.45); }
  .ring-glow-pink { box-shadow:0 0 0 1px rgba(91,61,245,.28), 0 22px 60px -30px rgba(91,61,245,.45); }

  .card-hover { transition:transform .35s var(--ease), border-color .35s var(--ease), box-shadow .35s var(--ease); will-change:transform; }
  .card-hover:hover { transform:translateY(-4px); border-color:rgba(91,61,245,.40); box-shadow:0 24px 50px -30px rgba(0,0,0,.6); }
  .band-light .card-hover:hover, .band-lav .card-hover:hover { box-shadow:0 20px 40px -26px rgba(18,18,26,.28); border-color:rgba(91,61,245,.35) !important; }

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
  #progress { position:fixed; top:0; left:0; height:2px; width:0; z-index:60; background:#5B3DF5; transition:width .12s linear; }

  /* ── form controls ── */
  input, textarea, select { font-family:Inter, sans-serif; }
  .field { width:100%; background:rgba(255,255,255,0.05); border:1px solid rgba(255,255,255,0.12); border-radius:14px; padding:0 14px; height:48px; color:#F6F4FF; font-size:14px; outline:none; transition:.25s var(--ease); }
  textarea.field { padding:12px 14px; height:auto; line-height:1.55; }
  .field:focus { border-color:#5B3DF5; background:rgba(91,61,245,0.07); box-shadow:0 0 0 4px rgba(91,61,245,.14); }
  .field::placeholder { color:#7A7398; }
  .band-light .field, .band-lav .field { background:#fff !important; border-color:rgba(25,18,51,.14) !important; color:var(--deep); }
  .band-light .field::placeholder, .band-lav .field::placeholder { color:#9C94B8; }
  .label { font-size:11px; font-weight:600; letter-spacing:.12em; text-transform:uppercase; color:#A9A3C7; display:block; margin-bottom:7px; }
  .band-light .label, .band-lav .label { color:#7A7198; }
  select.field option { background:#15122E; color:#F6F4FF; }
  .band-light select.field option, .band-lav select.field option { background:#fff; color:var(--deep); }
  [x-cloak] { display:none !important; }

  .badge-off { background:#DCFCE7; color:#15803D; }
  .band-light .price-strike, .band-lav .price-strike { color:#9A9AAB; }

  /* inline loading shimmer */
  .skeleton { background:linear-gradient(90deg, rgba(255,255,255,.06) 25%, rgba(255,255,255,.14) 50%, rgba(255,255,255,.06) 75%); background-size:200% 100%; animation:shimmer 1.4s linear infinite; }
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
<div class="aurora"><span class="a1"></span><span class="a2"></span><span class="a3"></span><span class="a4"></span></div>
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
  <span class="w-2 h-2 rounded-full bg-lime shrink-0 pulse-dot text-lime"></span>
  <span x-text="msg" class="truncate text-white"></span>
  <button x-on:click="show=false" class="w-6 h-6 rounded-full hover:bg-white/10 grid place-items-center text-mut shrink-0">✕</button>
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
