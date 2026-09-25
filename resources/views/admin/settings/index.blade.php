@extends('admin.layout')
@section('title','Settings')

@section('content')
<div x-data="settingsPage()">
  <div class="flex flex-wrap items-center justify-between gap-3">
    <div>
      <h1 class="text-[22px] font-black tracking-tight">Settings</h1>
      <p class="text-[13px] font-medium text-[#7A7A78]">
        Payment and AI credentials are encrypted before they are stored and never shown in full again.
      </p>
    </div>
    <div class="flex items-center gap-2 text-[12px] font-bold">
      <span class="px-3 py-1.5 rounded-full {{ $razorpay['enabled'] ? 'bg-green-100 text-green-700' : 'bg-[#F8F8F7] border border-[#E8E8E6] text-[#7A7A78]' }}">
        Payments: {{ $razorpay['enabled'] ? 'Razorpay ' . $razorpay['mode'] : 'demo mode' }}
      </span>
      <span class="px-3 py-1.5 rounded-full bg-[#F8F8F7] border border-[#E8E8E6] text-[#7A7A78]">AI: {{ $aiActive }}</span>
    </div>
  </div>

  @if($errors->any())
    <div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-2.5 text-[13px] font-semibold">{{ $errors->first() }}</div>
  @endif

  <form method="POST" action="{{ route('admin.settings.update') }}" class="mt-5 space-y-5">
    @csrf

    {{-- ── platform ── --}}
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Platform</div>
      <div class="mt-4 grid sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <label class="block">
          <span class="text-[12px] font-bold text-[#7A7A78]">Platform fee %</span>
          <input name="platform[fee_percent]" type="number" step="0.5" min="0" max="30" value="{{ $values['platform.fee_percent'] }}"
                 class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold">
        </label>
        <label class="block">
          <span class="text-[12px] font-bold text-[#7A7A78]">Escrow hold (hours)</span>
          <input name="platform[escrow_hours]" type="number" min="1" max="720" value="{{ $values['platform.escrow_hours'] }}"
                 class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold">
        </label>
        <label class="block">
          <span class="text-[12px] font-bold text-[#7A7A78]">Support email</span>
          <input name="platform[support_email]" type="email" value="{{ $values['platform.support_email'] }}"
                 class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
        </label>
        <label class="block">
          <span class="text-[12px] font-bold text-[#7A7A78]">Support phone</span>
          <input name="platform[support_phone]" value="{{ $values['platform.support_phone'] }}"
                 class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
        </label>
      </div>
      <label class="mt-4 flex items-center gap-2 text-[13px] font-semibold">
        <input type="checkbox" name="platform[maintenance]" value="1" @checked($values['platform.maintenance'])>
        Maintenance banner
      </label>
    </div>

    {{-- ── payments ── --}}
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="flex flex-wrap items-center justify-between gap-2">
        <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Payments · Razorpay</div>
        <button type="button" x-on:click="test('razorpay')"
                class="h-8 px-3 rounded-lg border border-[#E8E8E6] text-[12px] font-bold hover:bg-[#F8F8F7]">Test connection</button>
      </div>

      <div class="mt-4 grid sm:grid-cols-3 gap-4">
        <label class="block">
          <span class="text-[12px] font-bold text-[#7A7A78]">Key ID</span>
          <input name="payments[razorpay][key_id]" value="{{ $values['payments.razorpay.key_id'] }}" placeholder="rzp_test_xxxxxxxx"
                 class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-mono">
        </label>
        @foreach([
          ['payments[razorpay][key_secret]', 'Key secret', $masked['payments.razorpay.key_secret'], 'payments.razorpay.key_secret'],
          ['payments[razorpay][webhook_secret]', 'Webhook secret', $masked['payments.razorpay.webhook_secret'], 'payments.razorpay.webhook_secret'],
        ] as [$field, $label, $mask, $key])
          <label class="block">
            <span class="text-[12px] font-bold text-[#7A7A78]">{{ $label }}</span>
            <input name="{{ $field }}" type="password" autocomplete="new-password"
                   placeholder="{{ $mask ?: 'not set' }}"
                   class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-mono">
            @if($mask)
              <span class="mt-1 flex items-center gap-2 text-[11px] text-[#7A7A78]">
                saved · {{ $mask }}
                <button type="button" x-on:click="clearKey('{{ $key }}')" class="text-red-600 font-semibold">remove</button>
              </span>
            @endif
          </label>
        @endforeach
      </div>

      <label class="mt-4 block max-w-sm">
        <span class="text-[12px] font-bold text-[#7A7A78]">RazorpayX account number <span class="font-medium">(enables automatic payouts)</span></span>
        <input name="payments[razorpayx][account_number]" value="{{ $values['payments.razorpayx.account_number'] }}" placeholder="2323230000000000"
               class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-mono">
        <span class="mt-1 block text-[11px] text-[#7A7A78]">
          Empty means payouts stay manual: the queue still tracks what is owed, finance marks each transfer paid with a UTR.
        </span>
      </label>

      <div class="mt-4 bg-blue-50 border border-blue-200 rounded-xl p-3.5 text-[12.5px] leading-5">
        <b>Webhook URL:</b> <code class="bg-white border border-blue-200 px-1.5 py-0.5 rounded font-mono text-[11.5px]">{{ $razorpay['webhook'] }}</code><br>
        Subscribe to <code>payment.captured</code>, <code>payment.failed</code> and <code>refund.processed</code> in the Razorpay dashboard,
        then paste the signing secret above. With no keys saved the platform runs in demo mode: orders are marked held without money moving.
      </div>
    </div>

    {{-- ── ai ── --}}
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">AI providers</div>

      <label class="mt-4 block max-w-xs">
        <span class="text-[12px] font-bold text-[#7A7A78]">Preferred provider</span>
        <select name="ai[default_provider]" class="mt-1 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold">
          @foreach(['auto' => 'Auto — first configured', 'openrouter' => 'OpenRouter', 'openai' => 'OpenAI', 'gemini' => 'Google Gemini', 'none' => 'Off — deterministic engines only'] as $val => $label)
            <option value="{{ $val }}" @selected($values['ai.default_provider'] === $val)>{{ $label }}</option>
          @endforeach
        </select>
      </label>

      <div class="mt-4 grid lg:grid-cols-3 gap-4">
        @foreach([
          ['openrouter', 'OpenRouter', 'ai[openrouter][key]', 'ai[openrouter][model]', $masked['ai.openrouter.key'], $values['ai.openrouter.model'], 'ai.openrouter.key', 'sk-or-v1-…'],
          ['openai', 'OpenAI', 'ai[openai][key]', 'ai[openai][model]', $masked['ai.openai.key'], $values['ai.openai.model'], 'ai.openai.key', 'sk-…'],
          ['gemini', 'Google Gemini', 'ai[gemini][key]', 'ai[gemini][model]', $masked['ai.gemini.key'], $values['ai.gemini.model'], 'ai.gemini.key', 'AIza…'],
        ] as [$slug, $label, $keyField, $modelField, $mask, $model, $settingKey, $placeholder])
          @php $status = collect($ai)->firstWhere('key', $slug); @endphp
          <div class="border border-[#E8E8E6] rounded-xl p-4">
            <div class="flex items-center justify-between">
              <span class="text-[13px] font-black">{{ $label }}</span>
              <span class="text-[10.5px] font-bold px-2 py-0.5 rounded-full
                {{ ($status['active'] ?? false) ? 'bg-green-100 text-green-700' : (($status['enabled'] ?? false) ? 'bg-[#F8F8F7] border border-[#E8E8E6] text-[#7A7A78]' : 'bg-[#F8F8F7] text-[#B4B4B2]') }}">
                {{ ($status['active'] ?? false) ? 'in use' : (($status['enabled'] ?? false) ? 'ready' : 'no key') }}
              </span>
            </div>

            <input name="{{ $keyField }}" type="password" autocomplete="new-password" placeholder="{{ $mask ?: $placeholder }}"
                   class="mt-3 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-mono">
            @if($mask)
              <span class="mt-1 flex items-center gap-2 text-[11px] text-[#7A7A78]">
                saved · {{ $mask }}
                <button type="button" x-on:click="clearKey('{{ $settingKey }}')" class="text-red-600 font-semibold">remove</button>
              </span>
            @endif

            <input name="{{ $modelField }}" value="{{ $model }}" placeholder="model"
                   class="mt-2 w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[12.5px] font-mono">

            <button type="button" x-on:click="test('{{ $slug }}')"
                    class="mt-2 w-full h-9 rounded-lg border border-[#E8E8E6] text-[12px] font-bold hover:bg-[#F8F8F7]">Test</button>
          </div>
        @endforeach
      </div>

      <div class="mt-4 bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3.5 text-[12.5px] leading-5">
        Keys are optional. With none saved, the brief writer, task parser and matching all run on the
        deterministic engines — instant, free and offline. Add a key to hand the writing work to a model;
        output is validated against the same schema either way.
      </div>
    </div>

    <div class="flex items-center gap-3">
      <button class="h-11 px-6 rounded-xl bg-[#0F0F0F] text-white font-black text-[13.5px]">Save settings</button>
      <span x-show="result" x-cloak class="text-[13px] font-semibold"
            :class="ok ? 'text-green-700' : 'text-red-600'" x-text="result"></span>
    </div>
  </form>

  <form id="clear-key-form" method="POST" action="{{ route('admin.settings.clear') }}" class="hidden">
    @csrf<input type="hidden" name="key" id="clear-key-input">
  </form>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('alpine:init', () => {
  Alpine.data('settingsPage', () => ({
    result: '', ok: false,
    async test(target) {
      this.result = 'Testing ' + target + '…'; this.ok = false;
      try {
        const res = await fetch('{{ route('admin.settings.test') }}', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}',
            'Content-Type': 'application/json', 'Accept': 'application/json',
          },
          body: JSON.stringify({ target }),
        });
        const data = await res.json();
        this.ok = !!data.ok; this.result = data.message;
      } catch (e) { this.ok = false; this.result = 'Test failed to run.'; }
    },
    clearKey(key) {
      if (!confirm('Remove this saved key?')) return;
      document.getElementById('clear-key-input').value = key;
      document.getElementById('clear-key-form').submit();
    },
  }));
});
</script>
@endpush
