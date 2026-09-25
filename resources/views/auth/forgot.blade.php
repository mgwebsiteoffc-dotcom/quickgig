<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reset password — QuickContent</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<style>*{font-family:Inter,system-ui,sans-serif}</style>
</head>
<body class="bg-[#ECECE8] min-h-screen grid place-items-center p-4">
  <div class="w-full max-w-[380px] bg-white border border-[#E8E8E6] rounded-2xl p-6">
    <div class="text-[18px] font-black">Forgot your password?</div>
    <div class="text-[12px] font-semibold text-[#7A7A78] mt-1">We'll email you a secure link to set a new one.</div>

    @if(session('toast'))<div class="mt-4 bg-green-50 border border-green-200 text-green-800 rounded-xl px-3 py-2 text-[12px] font-bold">{{ session('toast') }}</div>@endif
    @error('email')<div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-3 py-2 text-[12px] font-bold">{{ $message }}</div>@enderror

    <form method="POST" action="{{ route('password.email') }}" class="mt-4 space-y-3">@csrf
      <input name="email" type="email" required placeholder="you@company.com" value="{{ old('email') }}" class="w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px] font-medium outline-none focus:bg-white focus:border-[#0F0F0F]">
      <button class="w-full h-11 rounded-full bg-[#0F0F0F] text-white font-bold text-[14px]">Send reset link</button>
    </form>

    <a href="{{ route('login') }}" class="block mt-4 text-center text-[12px] font-bold text-[#7A7A78]">← Back to sign in</a>
  </div>
</body>
</html>
