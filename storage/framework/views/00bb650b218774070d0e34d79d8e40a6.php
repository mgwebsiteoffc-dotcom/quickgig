<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
<title><?php echo e($title ?? "QuickContent — India's First Quick Content Delivery"); ?></title>
<script src="https://cdn.tailwindcss.com"></script>
<script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{font-family:Inter,system-ui,Helvetica,Arial,sans-serif}
:root{--bg:#F8F8F7;--card:#fff;--line:#E8E8E6;--line2:#F0F0EE;--text:#0F0F0F;--muted:#7A7A78;--yellow:#FFC300;--green:#0E8A4B;--green-bg:#EAF6EF;--green-line:#CDE9D6;--blue:#1D9BF0}
body{background:#ECECE8}
.app{max-width:400px;margin:0 auto;background:var(--bg);min-height:100vh;display:flex;flex-direction:column}
@media(min-width:900px){body{background:#F0F0EE;padding:0}.app{max-width:none;margin:0;min-height:100vh}}
.topbar{height:56px;padding:0 16px;background:var(--card);border-bottom:1px solid var(--line2);display:flex;align-items:center;justify-content:space-between;flex-shrink:0;position:sticky;top:0;z-index:20}
@media(min-width:900px){.topbar{height:64px;padding:0 24px}}
.footer{flex-shrink:0;background:var(--card);border-top:1px solid var(--line2);display:flex;padding:6px 0 calc(6px + env(safe-area-inset-bottom))}
@media(min-width:900px){.footer{display:none !important}}
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(10px);background:var(--text);color:white;padding:10px 16px;border-radius:20px;font-size:12px;font-weight:600;opacity:0;transition:0.22s;pointer-events:none;z-index:70}
.toast.show{opacity:1;transform:translateX(-50%)}
</style>
<?php echo $__env->yieldPushContent('styles'); ?>
</head>
<body>
<div class="app" id="app">
  <?php echo $__env->make('components.topbar', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('components.mode-switch', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <div class="layout" style="flex:1;display:flex;flex-direction:column;min-height:0">
    <?php if(request()->is('creator*')): ?>
      <?php echo $__env->make('components.sidebar-creator', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php else: ?>
      <?php echo $__env->make('components.sidebar-business', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
    <main style="flex:1;overflow-y:auto;scrollbar-width:none" id="main">
      <?php echo $__env->yieldContent('content'); ?>
    </main>
    <?php if(!request()->is('creator*')): ?>
      <?php echo $__env->make('components.rightbar-business', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
    <?php endif; ?>
  </div>
  <?php echo $__env->make('components.footer', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
  <?php echo $__env->make('components.sheets', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
</div>
<div class="toast" id="toast"></div>
<script>
function showToast(m){const t=document.getElementById('toast');t.textContent=m;t.classList.add('show');setTimeout(()=>t.classList.remove('show'),2200)}
</script>
<?php echo $__env->yieldPushContent('scripts'); ?>
</body>
</html>
<?php /**PATH C:\laragon\www\quick-content-laravel\resources\views/layouts/app.blade.php ENDPATH**/ ?>