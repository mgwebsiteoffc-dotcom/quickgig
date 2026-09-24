@php $isCreator = request()->is('creator*'); @endphp
@if(!$isCreator)
<div class="footer">
  <a href="{{ url('/') }}" class="footer-item active" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;color:var(--text);text-decoration:none;font-size:10.5px;font-weight:600;padding:4px 0">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 10L12 3l9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg> Home
  </a>
  <a href="{{ url('/') }}#search" class="footer-item" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;color:var(--muted);text-decoration:none;font-size:10.5px;font-weight:600;padding:4px 0">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="11" cy="11" r="7"/><path d="M16.5 16.5L21 21"/></svg> Search
  </a>
  <a href="{{ route('orders.show','UNJ-8841') }}" class="footer-item" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;color:var(--muted);text-decoration:none;font-size:10.5px;font-weight:600;padding:4px 0">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/></svg> Orders
  </a>
  <div class="footer-item" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;color:var(--muted);font-size:10.5px;font-weight:600;padding:4px 0">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="M8 10h8M8 14h5"/></svg> Company
  </div>
</div>
@else
<div class="footer">
  <a href="{{ route('creator.dashboard') }}" class="footer-item active" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;color:var(--text);text-decoration:none;font-size:10.5px;font-weight:600;padding:4px 0">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M3 10L12 3l9 7v10a1 1 0 0 1-1 1h-5v-6H9v6H4a1 1 0 0 1-1-1z"/></svg> Home
  </a>
  <div class="footer-item" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;color:var(--muted);font-size:10.5px;font-weight:600;padding:4px 0">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M21 11.5a8.5 8.5 0 0 1-12.5 7.5L3 21l2-5.5A8.5 8.5 0 0 1 21 11.5z"/></svg> Inbox
  </div>
  <div class="footer-item" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;color:var(--muted);font-size:10.5px;font-weight:600;padding:4px 0">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 2v4M12 18v4M4.9 4.9l2.8 2.8M16.3 16.3l2.8 2.8M2 12h4M18 12h4M4.9 19.1l2.8-2.8M16.3 7.7l2.8-2.8"/><circle cx="12" cy="12" r="5"/></svg> Earnings
  </div>
  <div class="footer-item" style="flex:1;display:flex;flex-direction:column;align-items:center;gap:3px;color:var(--muted);font-size:10.5px;font-weight:600;padding:4px 0">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/></svg> Profile
  </div>
</div>
@endif
