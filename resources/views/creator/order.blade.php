@extends('layouts.app')
@section('content')
<div style="padding:16px;max-width:820px;margin:0 auto">

  <a href="{{ route('creator.dashboard') }}" style="font-size:12px;font-weight:700;color:var(--muted)">← Back to dashboard</a>

  <div style="margin-top:12px;background:var(--card);border:1px solid var(--line);border-radius:20px;padding:18px">
    <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:12px;flex-wrap:wrap">
      <div>
        <div style="font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:var(--muted)">Order {{ $o->uid }}</div>
        <h1 style="font-size:18px;font-weight:800;margin-top:4px">{{ $o->service->title ?? 'Service' }}</h1>
        <div style="font-size:12px;font-weight:600;color:var(--muted)">For {{ $o->company->name ?? '—' }} • due {{ $o->due_at?->diffForHumans() ?? '—' }}</div>
      </div>
      <div style="text-align:right">
        <div style="font-size:20px;font-weight:800">₹{{ number_format($o->total) }}</div>
        <span style="font-size:11px;font-weight:800;padding:4px 10px;border-radius:999px;background:var(--green-bg);border:1px solid var(--green-line);color:var(--green)">{{ ucfirst($o->status) }}</span>
      </div>
    </div>

    <div style="margin-top:14px">
      <div style="font-size:11px;font-weight:800;letter-spacing:.12em;text-transform:uppercase;color:var(--muted)">Brief</div>
      <div style="margin-top:6px;background:var(--bg);border:1px solid var(--line);border-radius:12px;padding:12px;font-size:13px;line-height:22px;font-weight:500">{{ $o->brief }}</div>
    </div>

    <div style="margin-top:12px;height:8px;background:var(--line2);border-radius:999px;overflow:hidden">
      <div style="height:100%;width:{{ (int) $o->progress }}%;background:var(--green)"></div>
    </div>
    <div style="font-size:11px;font-weight:700;color:var(--muted);margin-top:4px">{{ (int) $o->progress }}% complete</div>
  </div>

  {{-- Previous deliveries --}}
  @if(isset($o->deliveries) && $o->deliveries->isNotEmpty())
  <div style="margin-top:16px;background:var(--card);border:1px solid var(--line);border-radius:20px;padding:18px">
    <div style="font-size:14px;font-weight:800">Deliveries</div>
    @foreach($o->deliveries as $d)
      <div style="margin-top:10px;border:1px solid var(--line);border-radius:12px;padding:12px;background:var(--bg)">
        <div style="font-size:12px;font-weight:700;color:var(--muted)">{{ $d->created_at?->format('d M Y, g:i A') }}</div>
        @if($d->hasFile())
          <a href="{{ $d->url() }}" style="font-size:13px;font-weight:800">{{ $d->file_name }}</a>
          <span style="font-size:11px;font-weight:600;color:var(--muted)"> • {{ $d->humanSize() }}</span>
        @elseif($d->delivery_url)
          <a href="{{ $d->delivery_url }}" style="font-size:13px;font-weight:800;word-break:break-all">{{ $d->delivery_url }}</a>
        @endif
        @if($d->note)<div style="font-size:12px;font-weight:500;margin-top:4px">{{ $d->note }}</div>@endif
      </div>
    @endforeach
  </div>
  @endif

  {{-- Deliver form --}}
  <div style="margin-top:16px;background:var(--card);border:1px solid var(--line);border-radius:20px;padding:18px">
    <div style="font-size:14px;font-weight:800">Deliver this order</div>
    <form method="POST" action="{{ route('creator.deliver', $o->uid ?? $o->id) }}" enctype="multipart/form-data" style="margin-top:12px;display:flex;flex-direction:column;gap:10px">
      @csrf

      <label style="font-size:12px;font-weight:700">Upload file <span style="color:var(--muted);font-weight:600">(mp4, mov, webm, zip, png, jpg, pdf, srt — max 50 MB)</span></label>
      <input type="file" name="file" accept=".mp4,.mov,.webm,.zip,.png,.jpg,.jpeg,.pdf,.srt" style="font-size:13px;font-weight:600">
      @error('file')<div style="font-size:12px;color:#B42318;font-weight:700">{{ $message }}</div>@enderror

      <label style="font-size:12px;font-weight:700">…or paste a link (Drive, WeTransfer, Frame.io)</label>
      <input name="delivery_url" type="url" value="{{ old('delivery_url') }}" placeholder="https://drive.google.com/..." style="height:42px;padding:0 12px;border:1px solid var(--line);border-radius:12px;background:var(--bg);font-size:13px;font-weight:500">
      @error('delivery_url')<div style="font-size:12px;color:#B42318;font-weight:700">{{ $message }}</div>@enderror

      <label style="font-size:12px;font-weight:700">Note to the client (optional)</label>
      <textarea name="note" rows="3" maxlength="500" placeholder="Added the hook at 0:02 and burned-in captions." style="padding:10px 12px;border:1px solid var(--line);border-radius:12px;background:var(--bg);font-size:13px;font-weight:500">{{ old('note') }}</textarea>

      <button style="height:46px;border-radius:999px;background:var(--text);color:#fff;font-weight:800;font-size:14px;border:0;cursor:pointer">Submit for review</button>
      <div style="font-size:11px;font-weight:600;color:var(--muted);text-align:center">Escrow is released to your UPI after the client approves.</div>
    </form>
  </div>

</div>
@endsection
