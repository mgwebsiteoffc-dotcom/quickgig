@if ($paginator->hasPages())
  <nav class="flex items-center justify-between gap-4" role="navigation">
    <div class="text-[12.5px] text-mut">
      Showing {{ $paginator->firstItem() }}–{{ $paginator->lastItem() }} of {{ $paginator->total() }}
    </div>

    <div class="flex items-center gap-1.5">
      @if ($paginator->onFirstPage())
        <span class="h-10 px-4 rounded-xl border border-line text-faint inline-flex items-center text-[13px]">Prev</span>
      @else
        <a href="{{ $paginator->previousPageUrl() }}" class="h-10 px-4 rounded-xl border border-line hover:border-mint inline-flex items-center text-[13px] transition">Prev</a>
      @endif

      @foreach ($elements as $element)
        @if (is_string($element))
          <span class="h-10 w-10 rounded-xl inline-flex items-center justify-center text-[13px] text-faint">{{ $element }}</span>
        @endif

        @if (is_array($element))
          @foreach ($element as $page => $url)
            @if ($page == $paginator->currentPage())
              <span class="h-10 w-10 rounded-xl btn-grad inline-flex items-center justify-center text-[13px] font-semibold">{{ $page }}</span>
            @else
              <a href="{{ $url }}" class="h-10 w-10 rounded-xl border border-line hover:border-mint inline-flex items-center justify-center text-[13px] transition">{{ $page }}</a>
            @endif
          @endforeach
        @endif
      @endforeach

      @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" class="h-10 px-4 rounded-xl border border-line hover:border-mint inline-flex items-center text-[13px] transition">Next</a>
      @else
        <span class="h-10 px-4 rounded-xl border border-line text-faint inline-flex items-center text-[13px]">Next</span>
      @endif
    </div>
  </nav>
@endif
