<footer class="band-dark mt-24">
  <div class="max-w-shell mx-auto px-5 lg:px-8 py-14">
    <div class="grid md:grid-cols-[1.4fr_1fr_1fr_1fr] gap-10">
      <div>
        <a href="{{ route('landing') }}" class="flex items-center gap-2.5">
          <span class="w-9 h-9 rounded-xl grid place-items-center bg-white/10">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="#00C48C"><path d="M13 2 4 14h7l-1 8 9-12h-7l1-8z"/></svg>
          </span>
          <span class="font-display text-[17px] leading-none text-white"><span class="font-medium">Quick</span><span class="font-bold text-mint"> GIGS</span></span>
        </a>
        <p class="mt-4 text-[13.5px] leading-6 text-mut max-w-[300px]">
          The autonomous gig marketplace. Post a brief, get matched to a verified pro in minutes, pay only when you approve.
        </p>
        <div class="mt-5 flex items-center gap-2.5">
          @foreach(['x' => 'M18.9 2H22l-7 8 8.2 12h-6.4l-5-7.3-5.8 7.3H2.9l7.5-9.1L2.2 2h6.6l4.5 6.6z', 'in' => 'M4.98 3.5a2.5 2.5 0 11.02 5 2.5 2.5 0 01-.02-5zM3 9h4v12H3zM10 9h3.8v1.7h.05c.53-1 1.83-2.05 3.77-2.05 4.03 0 4.78 2.6 4.78 6V21h-4v-5.3c0-1.26-.02-2.9-1.8-2.9-1.8 0-2.07 1.38-2.07 2.8V21h-4z'] as $label => $d)
            <a href="#" aria-label="{{ $label }}" class="w-9 h-9 rounded-xl glass grid place-items-center hover:border-mint transition">
              <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor" class="text-body"><path d="{{ $d }}"/></svg>
            </a>
          @endforeach
        </div>
      </div>

      @php
      $cols = [
        'Platform' => [
          ['Marketplace', route('marketplace')],
          ['How it works', route('how-it-works')],
          ['The engine', route('ai')],
          ['Brief builder', route('brief-builder')],
          ['Live demo', route('landing').'#demo'],
        ],
        'For you' => [
          ['For business', route('for-business')],
          ['Hire talent', route('register').'?type=business'],
          ['Work as a freelancer', route('for-creators')],
          ['For teams', route('enterprise')],
          ['Pricing', route('pricing')],
          ['Compare', route('compare')],
        ],
        'Company' => [
          ['About', route('about')],
          ['Insights', route('blog.index')],
          ['Contact', route('contact')],
          ['FAQ', route('faq')],
          ['Sitemap', route('sitemap')],
        ],
      ];
      @endphp
      @foreach($cols as $head => $links)
        <div>
          <div class="text-[11px] font-semibold tracking-[.14em] uppercase text-faint">{{ $head }}</div>
          <ul class="mt-4 space-y-2.5">
            @foreach($links as [$label, $href])
              <li><a href="{{ $href }}" class="text-[13.5px] text-mut hover:text-white transition">{{ $label }}</a></li>
            @endforeach
          </ul>
        </div>
      @endforeach
    </div>

    <div class="mt-12 pt-6 border-t border-line flex flex-col sm:flex-row items-center justify-between gap-3 text-[12.5px] text-faint">
      <div>© {{ date('Y') }} Quick GIGS. Built in India for the world.</div>
      <div class="flex items-center gap-5">
        <span class="inline-flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full bg-mint"></span> All systems operational</span>
        <a href="#" class="hover:text-body">Privacy</a>
        <a href="#" class="hover:text-body">Terms</a>
      </div>
    </div>
  </div>
</footer>
