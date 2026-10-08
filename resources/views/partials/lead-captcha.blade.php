@php
  $captchaA = random_int(2, 9);
  $captchaB = random_int(1, 9);
  session(['lead_captcha_answer' => $captchaA + $captchaB]);
@endphp
<div class="rounded-2xl border border-line bg-tint/40 p-4">
  <label class="label" for="lead_captcha">Spam check: what is {{ $captchaA }} + {{ $captchaB }}?</label>
  <input id="lead_captcha" name="captcha_answer" type="number" inputmode="numeric" required class="field" placeholder="Your answer">
</div>