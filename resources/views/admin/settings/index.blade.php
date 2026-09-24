@extends('admin.layout')
@section('title','Settings')
@section('breadcrumb','Admin • Settings')
@section('content')
<div class="max-w-[720px] bg-white border border-[#E8E8E6] rounded-2xl p-6">
  <div class="font-black text-[16px]">Platform Settings</div>
  <div class="text-[13px] font-medium text-[#7A7A78]">Only Super Admin can edit. Saved to <code class="bg-[#F8F8F7] border border-[#E8E8E6] px-1 py-0.5 rounded">settings</code> table or <code class="bg-[#F8F8F7] border border-[#E8E8E6] px-1 py-0.5 rounded">.env</code> — file cache is cleared on save.</div>

  <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-6 space-y-4">@csrf
    <div class="grid sm:grid-cols-3 gap-4">
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Platform Fee %</label><input name="platform_fee" value="{{ $settings['platform_fee'] }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold"></div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Creator Fee %</label><input name="creator_fee" value="{{ $settings['creator_fee'] }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold"></div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Escrow Hold (hours)</label><input name="escrow_hours" value="{{ $settings['escrow_hours'] }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold"></div>
    </div>
    <div class="grid sm:grid-cols-2 gap-4">
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Support Email</label><input name="support_email" value="{{ $settings['support_email'] }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
      <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Support Phone</label><input name="support_phone" value="{{ $settings['support_phone'] }}" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"></div>
    </div>
    <div><label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Razorpay Key</label><input value="{{ $settings['razorpay_key'] }}" disabled class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F0F0EE] text-[13px] text-[#7A7A78]"><div class="text-[11px] font-medium text-[#7A7A78] mt-1">Edit in your server environment file (RAZORPAY_KEY). Never commit secrets.</div></div>
    <label class="flex items-center gap-2 text-[13px] font-bold"><input type="checkbox" name="maintenance" value="1" {{ $settings['maintenance'] ? 'checked':'' }} class="rounded"> Maintenance mode (shows landing only)</label>
    <button class="h-11 px-7 rounded-full bg-[#0F0F0F] text-white font-extrabold text-[14px]">Save Settings → clear file cache</button>
  </form>
</div>

<div class="mt-6 max-w-[720px] bg-[#0F0F0F] text-white rounded-2xl p-5">
  <div class="text-[11px] font-bold tracking-widest uppercase text-white/60">Deployment checklist</div>
  <ul class="mt-2 space-y-1.5 text-[13px] font-medium text-white/90 list-disc pl-5">
    <li>Upload Laravel outside <code class="bg-white/10 px-1 py-0.5 rounded">public_html</code>, copy <code class="bg-white/10 px-1 py-0.5 rounded">public/*</code> into <code class="bg-white/10 px-1 py-0.5 rounded">public_html</code> and fix <code class="bg-white/10 px-1 py-0.5 rounded">index.php</code> paths.</li>
    <li><code class="bg-white/10 px-1 py-0.5 rounded">composer install --no-dev --optimize-autoloader</code> via SSH on the server.</li>
    <li><code class="bg-white/10 px-1 py-0.5 rounded">php artisan key:generate && php artisan migrate --force && php artisan storage:link</code></li>
    <li>Permissions: <code class="bg-white/10 px-1 py-0.5 rounded">chmod -R 755 storage bootstrap/cache</code></li>
    <li>Cron: queue:work & schedule:run (see Dashboard).</li>
  </ul>
</div>
@endsection
