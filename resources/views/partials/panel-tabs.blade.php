@php
  $tabs = [
    ['label' => 'Overview',    'route' => 'business.home',    'icon' => 'M4 13h6V4H4v9zm0 7h6v-5H4v5zm10 0h6V11h-6v9zm0-16v5h6V4h-6z'],
    ['label' => 'Task board',  'route' => 'board',            'icon' => 'M4 5h4v14H4zM10 5h4v9h-4zM16 5h4v6h-4z'],
    ['label' => 'Marketplace', 'route' => 'marketplace',      'icon' => 'M3 9l1.5-5h15L21 9M3 9h18M3 9v10a1 1 0 0 0 1 1h16a1 1 0 0 0 1-1V9'],
    ['label' => 'Settings',    'route' => 'business.profile', 'icon' => 'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1a2 2 0 1 1-2.8 2.8l-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.5V21a2 2 0 1 1-4 0v-.1A1.7 1.7 0 0 0 9 19.4a1.7 1.7 0 0 0-1.9.3l-.1.1a2 2 0 1 1-2.8-2.8l.1-.1a1.7 1.7 0 0 0 .3-1.9 1.7 1.7 0 0 0-1.5-1H3a2 2 0 1 1 0-4h.1A1.7 1.7 0 0 0 4.6 9a1.7 1.7 0 0 0-.3-1.9l-.1-.1a2 2 0 1 1 2.8-2.8l.1.1a1.7 1.7 0 0 0 1.9.3H9a1.7 1.7 0 0 0 1-1.5V3a2 2 0 1 1 4 0v.1a1.7 1.7 0 0 0 1 1.5 1.7 1.7 0 0 0 1.9-.3l.1-.1a2 2 0 1 1 2.8 2.8l-.1.1a1.7 1.7 0 0 0-.3 1.9V9a1.7 1.7 0 0 0 1.5 1H21a2 2 0 1 1 0 4h-.1a1.7 1.7 0 0 0-1.5 1z'],
  ];
  $currentRoute = request()->route()?->getName();
@endphp

<div class="flex items-center gap-1.5 overflow-x-auto pb-1 -mx-1 px-1">
  @foreach($tabs as $tab)
    @php $active = $currentRoute === $tab['route']; @endphp
    <a href="{{ route($tab['route']) }}"
       class="shrink-0 h-10 px-4 rounded-xl inline-flex items-center gap-2 text-[13.5px] font-medium transition-all duration-300
              {{ $active ? 'btn-grad text-white shadow-lg shadow-pink/20' : 'glass btn-ghost text-mut hover:text-white hover:border-white/25' }}">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="{{ $tab['icon'] }}"/></svg>
      {{ $tab['label'] }}
    </a>
  @endforeach
</div>
