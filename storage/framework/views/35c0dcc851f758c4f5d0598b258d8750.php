<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Login — QuickContent</title>
<script src="https://cdn.tailwindcss.com"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>*{font-family:Inter,sans-serif}</style>
</head>
<body class="min-h-screen bg-[#F8F8F7] flex items-center justify-center p-4">
<div class="w-full max-w-[420px] bg-white border border-[#E8E8E6] rounded-2xl p-6 sm:p-7 shadow-sm">
  <a href="<?php echo e(route('landing')); ?>" class="flex items-center gap-2.5">
    <div class="w-9 h-9 rounded-xl bg-[#0F0F0F] text-white grid place-items-center font-black text-[13px]">QC</div>
    <div><div class="font-extrabold text-[14px] leading-none">QuickContent</div><div class="text-[11px] font-semibold text-[#7A7A78]">India's First Quick Delivery</div></div>
  </a>
  <h1 class="mt-5 text-[20px] font-black tracking-tight">Welcome back</h1>
  <p class="text-[13px] font-medium text-[#7A7A78]">Login to manage orders. Hostinger-safe — file session, no Redis.</p>

  <?php if($errors->any()): ?>
    <div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-3 py-2.5 text-[13px] font-semibold"><?php echo e($errors->first()); ?></div>
  <?php endif; ?>
  <?php if(session('error')): ?>
    <div class="mt-4 bg-red-50 border border-red-200 text-red-700 rounded-xl px-3 py-2.5 text-[13px] font-semibold"><?php echo e(session('error')); ?></div>
  <?php endif; ?>

  <form method="POST" action="<?php echo e(route('login.post')); ?>" class="mt-5 space-y-3">
    <?php echo csrf_field(); ?>
    <div>
      <label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Email</label>
      <input name="email" type="email" value="<?php echo e(old('email','admin@quickcontent.in')); ?>" required class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px] font-medium outline-none focus:bg-white focus:border-[#0F0F0F]">
    </div>
    <div>
      <label class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Password</label>
      <input name="password" type="password" value="Admin@12345" required class="mt-1 w-full h-11 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[14px] font-medium outline-none focus:bg-white focus:border-[#0F0F0F]">
    </div>
    <label class="flex items-center gap-2 text-[13px] font-semibold"><input type="checkbox" name="remember" value="1" class="rounded"> Remember me</label>
    <button class="w-full h-11 rounded-full bg-[#0F0F0F] text-white font-extrabold text-[14px] hover:bg-black">Login →</button>
  </form>

  <div class="mt-5 bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3">
    <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Demo logins (change after first login)</div>
    <div class="mt-2 grid grid-cols-1 gap-1.5 text-[12px] font-mono">
      <div class="flex justify-between bg-white border border-[#E8E8E6] rounded-lg px-2.5 py-1.5"><span>admin@quickcontent.in / Admin@12345</span><span class="font-bold">super_admin</span></div>
      <div class="flex justify-between bg-white border border-[#E8E8E6] rounded-lg px-2.5 py-1.5"><span>manager@quickcontent.in / Manager@123</span><span class="font-bold">manager</span></div>
      <div class="flex justify-between bg-white border border-[#E8E8E6] rounded-lg px-2.5 py-1.5"><span>support@quickcontent.in / Support@123</span><span class="font-bold">support</span></div>
    </div>
  </div>

  <div class="mt-4 text-center text-[12px] font-semibold text-[#7A7A78]">Go to <a href="<?php echo e(route('landing')); ?>" class="text-[#0F0F0F] underline">Landing</a> • <a href="<?php echo e(route('business.home')); ?>" class="text-[#0F0F0F] underline">Marketplace</a></div>
</div>
</body>
</html>
<?php /**PATH C:\laragon\www\quick-content-laravel\resources\views/auth/login.blade.php ENDPATH**/ ?>