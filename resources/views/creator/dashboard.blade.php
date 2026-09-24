@extends('layouts.app')
@section('content')
<div style="padding:16px;background:var(--card);border-bottom:1px solid var(--line2)">
  <div style="display:flex;align-items:center;justify-content:space-between;background:var(--bg);border:1px solid var(--line);border-radius:12px;padding:12px">
    <div style="display:flex;align-items:center;gap:10px"><span style="width:10px;height:10px;border-radius:50%;background:var(--green);box-shadow:0 0 0 6px var(--green-bg);display:inline-block"></span><div><div style="font-size:13px;font-weight:700">Available for work</div><div style="font-size:11px;color:var(--muted)">Accepting new orders • Avg reply 6 min</div></div></div>
    <div style="width:44px;height:26px;border-radius:20px;background:var(--green);position:relative;cursor:pointer" onclick="showToast('Toggled availability')"><div style="position:absolute;top:3px;left:3px;width:20px;height:20px;border-radius:50%;background:white;box-shadow:0 1px 4px rgba(0,0,0,0.15)"></div></div>
  </div>
  <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;margin-top:12px">
    <div style="background:var(--bg);border:1px solid var(--line2);border-radius:12px;padding:11px;text-align:center"><div style="font-size:15px;font-weight:800">₹84,200</div><div style="font-size:10px;color:var(--muted);font-weight:650;letter-spacing:0.04em;text-transform:uppercase;margin-top:2px">Earned this mo.</div></div>
    <div style="background:var(--bg);border:1px solid var(--line2);border-radius:12px;padding:11px;text-align:center"><div style="font-size:15px;font-weight:800">4.9 ★</div><div style="font-size:10px;color:var(--muted);font-weight:650;letter-spacing:0.04em;text-transform:uppercase;margin-top:2px">1.2k reviews</div></div>
    <div style="background:var(--bg);border:1px solid var(--line2);border-radius:12px;padding:11px;text-align:center"><div style="font-size:15px;font-weight:800">6 min</div><div style="font-size:10px;color:var(--muted);font-weight:650;letter-spacing:0.04em;text-transform:uppercase;margin-top:2px">Avg reply</div></div>
  </div>
</div>
<div style="padding:16px">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:10px"><div style="font-size:13px;font-weight:700;display:flex;align-items:center;gap:6px"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/></svg> Active orders — for you</div><div style="font-size:11px;font-weight:600;background:var(--card);border:1px solid var(--line);padding:5px 9px;border-radius:20px;cursor:pointer">Filter</div></div>
  @foreach($orders as $o)
  <div style="background:var(--card);border:1px solid var(--line);border-radius:12px;padding:12px;cursor:pointer;margin-bottom:8px">
    <div style="display:flex;align-items:center;gap:10px"><div style="width:32px;height:32px;border-radius:8px;background:var(--text);color:white;display:flex;align-items:center;justify-content:center;font-weight:700;font-size:11px">{{ $o['initials'] }}</div><div><div style="font-size:12.5px;font-weight:700">{{ $o['client'] }}</div><div style="font-size:11px;color:var(--muted)">{{ $o['person'] }} • Business • For you</div></div><span style="margin-left:auto;font-size:11px;font-weight:700;padding:4px 8px;border-radius:20px;{{ $o['statusClass']=='working' ? 'background:var(--yellow)' : ($o['statusClass']=='review' ? 'background:var(--green-bg);color:var(--green);border:1px solid var(--green-line)' : 'background:var(--bg);border:1px solid var(--line)') }}">{{ $o['status'] }}</span></div>
    <div style="font-size:12.5px;font-weight:600;margin-top:8px">{{ $o['title'] }}</div>
    <div style="display:flex;gap:8px;margin-top:6px;font-size:11px;color:var(--muted);align-items:center"><svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 3"/></svg> {{ $o['time'] }} <span>•</span> {{ $o['price'] }}</div>
    <div style="display:flex;gap:8px;margin-top:10px"><button style="height:32px;padding:0 12px;border-radius:9px;border:none;background:var(--text);color:white;font-size:12px;font-weight:650;display:flex;align-items:center;gap:5px"><svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.7"><path d="M21 11.5a8.5 8.5 0 0 1-12.5 7.5L3 21l2-5.5A8.5 8.5 0 0 1 21 11.5z"/></svg> Chat</button><button style="height:32px;padding:0 12px;border-radius:9px;border:1px solid var(--line);background:var(--card);font-size:12px;font-weight:650">Brief</button><button style="height:32px;padding:0 12px;border-radius:9px;background:var(--yellow);border:1px solid var(--yellow);font-size:12px;font-weight:650">Deliver</button></div>
  </div>
  @endforeach
</div>
@endsection
