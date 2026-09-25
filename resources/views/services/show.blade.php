@extends('layouts.app')
@section('content')
<div style="padding:16px;max-width:900px;margin:0 auto">

  <a href="{{ route('business.home') }}" style="font-size:12px;font-weight:700;color:var(--muted)">← Back to marketplace</a>

  <div style="margin-top:12px;background:var(--card);border:1px solid var(--line);border-radius:20px;overflow:hidden">
    @if($service->cover)
      <img src="{{ filter_var($service->cover, FILTER_VALIDATE_URL) ? $service->cover : asset('storage/'.$service->cover) }}" alt="{{ $service->title }}" style="width:100%;height:220px;object-fit:cover">
    @endif

    <div style="padding:18px">
      <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
        <div>
          <div style="font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:var(--muted)">{{ $service->category ?: 'Service' }}</div>
          <h1 style="font-size:20px;font-weight:800;margin-top:4px">{{ $service->title }}</h1>
        </div>
        <div style="text-align:right">
          <div style="font-size:22px;font-weight:800">{{ $service->displayPrice() }}</div>
          <div style="font-size:12px;font-weight:600;color:var(--muted)">Delivery in {{ $service->delivery_days }} day{{ $service->delivery_days == 1 ? '' : 's' }}</div>
        </div>
      </div>

      @if($creator)
      <a href="{{ route('creator.public', $creator->id) }}" style="margin-top:14px;display:flex;align-items:center;gap:10px;padding:10px;border:1px solid var(--line);border-radius:14px;background:var(--bg)">
        <img src="{{ $creator->avatarUrl() }}" alt="{{ $creator->name }}" style="width:38px;height:38px;border-radius:50%;object-fit:cover">
        <div style="flex:1;min-width:0">
          <div style="font-size:13px;font-weight:800">{{ $creator->name }} @if($creator->is_verified)<span style="color:var(--blue)">✓</span>@endif</div>
          <div style="font-size:11px;font-weight:600;color:var(--muted)">{{ $creator->handle }} • {{ $creator->profileLabel() }} • {{ $creator->rating }}★</div>
        </div>
        <span style="font-size:11px;font-weight:800;padding:4px 10px;border-radius:999px;border:1px solid var(--line);background:#fff">{{ $creator->is_available ? 'Available' : 'Busy' }}</span>
      </a>
      @endif

      @if($service->description)
      <p style="margin-top:14px;font-size:13px;line-height:22px;font-weight:500">{{ $service->description }}</p>
      @endif

      @if(!empty($service->deliverables))
      <div style="margin-top:14px">
        <div style="font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:var(--muted)">What you get</div>
        <ul style="margin-top:6px;padding-left:18px;font-size:13px;font-weight:500;line-height:22px">
          @foreach((array) $service->deliverables as $d)<li>{{ $d }}</li>@endforeach
        </ul>
      </div>
      @endif
    </div>
  </div>

  {{-- Order form --}}
  <div style="margin-top:16px;background:var(--card);border:1px solid var(--line);border-radius:20px;padding:18px">
    <div style="font-size:14px;font-weight:800">Place your order</div>
    <form method="POST" action="{{ route('orders.store') }}" style="margin-top:12px;display:flex;flex-direction:column;gap:10px">
      @csrf
      <input type="hidden" name="service_id" value="{{ $service->id }}">
      @if($creator)<input type="hidden" name="creator_id" value="{{ $creator->id }}">@endif

      <label style="font-size:12px;font-weight:700">Ordering as</label>
      <select name="company_id" required style="height:42px;padding:0 12px;border:1px solid var(--line);border-radius:12px;background:var(--bg);font-size:13px;font-weight:600">
        @forelse($companies as $c)
          <option value="{{ $c->id }}" {{ $currentCompany == $c->id ? 'selected' : '' }}>{{ $c->name }}</option>
        @empty
          <option value="">No company yet — complete onboarding first</option>
        @endforelse
      </select>

      <label style="font-size:12px;font-weight:700">Your brief</label>
      <textarea name="brief" rows="4" required minlength="8" placeholder="Hook in first 2 seconds, 9:16, burned-in captions, brand colours…" style="padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:var(--bg);font-size:13px;font-weight:500">{{ old('brief') }}</textarea>

      <label style="font-size:12px;font-weight:700">Turnaround</label>
      <select name="turnaround" style="height:42px;padding:0 12px;border:1px solid var(--line);border-radius:12px;background:var(--bg);font-size:13px;font-weight:600">
        <option>1 Day</option><option>2 Day</option><option>3 Day</option>
      </select>

      @error('brief')<div style="font-size:12px;color:#B42318;font-weight:700">{{ $message }}</div>@enderror

      <button style="height:46px;border-radius:999px;background:var(--text);color:#fff;font-weight:800;font-size:14px;border:0;cursor:pointer">
        {{ $service->isBarter() ? 'Book barter collab' : 'Pay '.$service->displayPrice().' → held in escrow' }}
      </button>
      <div style="font-size:11px;font-weight:600;color:var(--muted);text-align:center">Money is held in escrow and released only after you approve the delivery.</div>
    </form>
  </div>

  @if($related->isNotEmpty())
  <div style="margin-top:16px">
    <div style="font-size:14px;font-weight:800">Similar services</div>
    <div style="margin-top:10px;display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:10px">
      @foreach($related as $r)
      <a href="{{ route('services.show', $r->slug ?: $r->id) }}" style="background:var(--card);border:1px solid var(--line);border-radius:16px;padding:12px;display:block">
        <div style="font-size:13px;font-weight:800">{{ $r->title }}</div>
        <div style="font-size:11px;font-weight:600;color:var(--muted)">{{ $r->creator->name ?? '' }} • {{ $r->displayPrice() }}</div>
      </a>
      @endforeach
    </div>
  </div>
  @endif

</div>
@endsection
