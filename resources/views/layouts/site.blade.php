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
        ink:   '#06060B',
        ink2:  '#0B0B14',
        line:  'rgba(255,255,255,0.09)',
        soft:  'rgba(255,255,255,0.045)',
        txt:   '#F2F3F8',
        mut:   '#9AA1B8',
        violet:{ DEFAULT:'#7C5CFF', soft:'#A78BFA' },
        cyan:  { DEFAULT:'#22D3EE' },
        lime:  { DEFAULT:'#A3E635' },
      },
      fontFamily: {
        display: ['"Space Grotesk"','system-ui','sans-serif'],
        sans: ['Inter','system-ui','sans-serif'],
      },
      borderRadius: { '4xl':'2rem' },
      maxWidth: { shell: '1180px' },
    }
  }
}
</script>
<style>
  :root { color-scheme: dark; }
  body { font-family: Inter, system-ui, sans-serif; background:#06060B; color:#F2F3F8; -webkit-font-smoothing:antialiased; }
  h1,h2,h3,h4,.font-display { font-family:"Space Grotesk", system-ui, sans-serif; letter-spacing:-0.02em; }
  ::selection { background:#7C5CFF; color:#fff; }

  /* ambient background */
  .aurora { position:fixed; inset:0; z-index:-2; overflow:hidden; pointer-events:none; }
  .aurora::before, .aurora::after { content:''; position:absolute; width:52rem; height:52rem; border-radius:50%; filter:blur(120px); opacity:.30; }
  .aurora::before { background:radial-gradient(circle, #7C5CFF 0%, transparent 65%); top:-20rem; left:-12rem; }
  .aurora::after  { background:radial-gradient(circle, #22D3EE 0%, transparent 65%); top:-8rem; right:-18rem; opacity:.20; }
  .grid-bg { position:fixed; inset:0; z-index:-1; pointer-events:none;
    background-image:linear-gradient(rgba(255,255,255,.035) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.035) 1px, transparent 1px);
    background-size:64px 64px; mask-image:radial-gradient(ellipse 100% 60% at 50% 0%, #000 40%, transparent 100%); }

  .glass { background:rgba(255,255,255,0.045); border:1px solid rgba(255,255,255,0.09); backdrop-filter:blur(14px); }
  .glass-strong { background:rgba(13,13,22,0.82); border:1px solid rgba(255,255,255,0.10); backdrop-filter:blur(18px); }
  .grad-text { background:linear-gradient(100deg,#fff 10%,#A78BFA 45%,#22D3EE 90%); -webkit-background-clip:text; background-clip:text; color:transparent; }
  .btn-grad { background:linear-gradient(100deg,#7C5CFF,#22D3EE); color:#06060B; }
  .btn-grad:hover { filter:brightness(1.08); }
  .ring-glow { box-shadow:0 0 0 1px rgba(124,92,255,.35), 0 18px 60px -18px rgba(124,92,255,.55); }
  .card-hover { transition:transform .25s ease, border-color .25s ease, box-shadow .25s ease; }
  .card-hover:hover { transform:translateY(-4px); border-color:rgba(124,92,255,.45); box-shadow:0 24px 60px -30px rgba(124,92,255,.65); }

  .pulse-dot { position:relative; }
  .pulse-dot::after { content:''; position:absolute; inset:-4px; border-radius:50%; border:1px solid currentColor; opacity:.5; animation:pulseRing 1.8s ease-out infinite; }
  @keyframes pulseRing { 0%{transform:scale(.7);opacity:.7} 100%{transform:scale(1.5);opacity:0} }

  .marquee { display:flex; gap:3rem; width:max-content; animation:slide 32s linear infinite; }
  @keyframes slide { from{transform:translateX(0)} to{transform:translateX(-50%)} }

  .reveal { opacity:0; transform:translateY(18px); transition:opacity .7s cubic-bezier(.2,.7,.3,1), transform .7s cubic-bezier(.2,.7,.3,1); }
  .reveal.in { opacity:1; transform:none; }

  input, textarea, select { font-family:Inter, sans-serif; }
  .field { width:100%; background:rgba(255,255,255,0.04); border:1px solid rgba(255,255,255,0.10); border-radius:14px; padding:0 14px; height:48px; color:#F2F3F8; font-size:14px; outline:none; transition:.2s; }
  textarea.field { padding:12px 14px; height:auto; line-height:1.5; }
  .field:focus { border-color:#7C5CFF; background:rgba(124,92,255,0.07); box-shadow:0 0 0 4px rgba(124,92,255,.14); }
  .field::placeholder { color:#6B7285; }
  .label { font-size:11px; font-weight:600; letter-spacing:.12em; text-transform:uppercase; color:#9AA1B8; display:block; margin-bottom:7px; }
  select.field option { background:#0B0B14; }
  [x-cloak] { display:none !important; }
</style>
@stack('styles')
</head>
<body class="min-h-screen antialiased">
<div class="aurora"></div><div class="grid-bg"></div>

@include('partials.nav')

<main>
  @yield('content')
</main>

@include('partials.footer')

{{-- toast --}}
<div x-data="{ show:{{ session('toast') ? 'true' : 'false' }}, msg:@js(session('toast') ?? '') }" x-show="show" x-cloak x-init="if(show) setTimeout(()=>show=false, 5200)"
     x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-3" x-transition:enter-end="opacity-100"
     class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[80] glass-strong rounded-full pl-4 pr-3 py-2.5 flex items-center gap-3 text-[13px] font-medium shadow-2xl max-w-[92vw]">
  <span class="w-2 h-2 rounded-full bg-lime shrink-0"></span>
  <span x-text="msg" class="truncate"></span>
  <button x-on:click="show=false" class="w-6 h-6 rounded-full hover:bg-white/10 grid place-items-center text-mut shrink-0">✕</button>
</div>

<script>
  // scroll reveal (with a safe fallback so content can never stay hidden)
  document.addEventListener('DOMContentLoaded', () => {
    const items = document.querySelectorAll('.reveal');
    const showAll = () => items.forEach(el => el.classList.add('in'));
    const reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (reduced || !('IntersectionObserver' in window)) { showAll(); return; }

    const io = new IntersectionObserver((entries) => {
      entries.forEach(e => { if (e.isIntersecting) { e.target.classList.add('in'); io.unobserve(e.target); } });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    items.forEach(el => io.observe(el));
    setTimeout(showAll, 2500);
  });
</script>
@stack('scripts')
</body>
</html>
