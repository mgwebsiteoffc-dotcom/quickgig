<?php $__env->startSection('title','Dashboard'); ?>
<?php $__env->startSection('breadcrumb','Admin • Overview'); ?>
<?php $__env->startSection('content'); ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
  <?php $__currentLoopData = $stats; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
  <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4">
    <div class="flex items-center justify-between">
      <div class="w-9 h-9 rounded-xl bg-[#F8F8F7] border border-[#E8E8E6] grid place-items-center">
        <?php if($s['icon']=='shopping-bag'): ?><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#2563EB" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"/><line x1="3" y1="6" x2="21" y2="6"/><path d="M16 10a4 4 0 0 1-8 0"/></svg>
        <?php elseif($s['icon']=='wallet'): ?><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#0E8A4B" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><path d="M16 14a2 2 0 0 0 0-4"/></svg>
        <?php elseif($s['icon']=='users'): ?><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#7C3AED" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
        <?php else: ?><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#D97706" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg><?php endif; ?>
      </div>
      <span class="text-[11px] font-bold px-2 py-1 rounded-full bg-[#F8F8F7] border border-[#E8E8E6]"><?php echo e($s['change']); ?></span>
    </div>
    <div class="mt-3 text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]"><?php echo e($s['label']); ?></div>
    <div class="text-[22px] font-black tracking-tight"><?php echo e($s['value']); ?></div>
  </div>
  <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
</div>

<div class="mt-6 grid lg:grid-cols-[1.6fr_0.9fr] gap-6">
  <!-- Recent orders -->
  <div class="bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
    <div class="px-5 py-4 flex items-center justify-between border-b border-[#F0F0EE]">
      <div class="font-black text-[14px]">Recent Orders</div>
      <a href="<?php echo e(route('admin.orders.index')); ?>" class="h-8 px-3 rounded-full border border-[#E8E8E6] font-bold text-[12px] inline-flex items-center hover:bg-[#F8F8F7]">View all</a>
    </div>
    <div class="divide-y divide-[#F0F0EE]">
      <?php $__currentLoopData = $recentOrders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $o): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
      <a href="<?php echo e(route('admin.orders.show',$o['id'])); ?>" class="flex items-center gap-3 px-5 py-3 hover:bg-[#F8F8F7]/70">
        <div class="w-9 h-9 rounded-xl bg-[#0F0F0F] text-white grid place-items-center font-black text-[10px]"><?php echo e(substr($o['id'],0,2)); ?></div>
        <div class="flex-1 min-w-0">
          <div class="text-[13px] font-bold truncate"><?php echo e($o['id']); ?> • <?php echo e($o['service']); ?></div>
          <div class="text-[11px] font-semibold text-[#7A7A78] truncate"><?php echo e($o['company']); ?> → <?php echo e($o['creator']); ?> • <?php echo e($o['time']); ?></div>
        </div>
        <div class="text-right">
          <div class="text-[13px] font-black">₹<?php echo e(number_format($o['amount'])); ?></div>
          <span class="text-[11px] font-bold px-2 py-0.5 rounded-full border
            <?php if($o['status']=='pending'): ?> bg-amber-50 border-amber-200 text-amber-700
            <?php elseif($o['status']=='working'): ?> bg-blue-50 border-blue-200 text-blue-700
            <?php elseif($o['status']=='review'): ?> bg-violet-50 border-violet-200 text-violet-700
            <?php elseif($o['status']=='delivered'): ?> bg-green-50 border-green-200 text-green-700
            <?php else: ?> bg-[#F8F8F7] border-[#E8E8E6] <?php endif; ?>
          "><?php echo e(ucfirst($o['status'])); ?></span>
        </div>
      </a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </div>

  <!-- Payout queue + quick actions -->
  <div class="space-y-6">
    <div class="bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
      <div class="px-5 py-4 border-b border-[#F0F0EE] flex items-center justify-between">
        <div class="font-black text-[14px]">Payout Queue</div>
        <span class="text-[11px] font-bold bg-amber-400 text-[#0F0F0F] px-2 py-1 rounded-full">3 ready</span>
      </div>
      <div class="p-3 space-y-3">
        <?php $__currentLoopData = $payoutQueue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <div class="flex items-center gap-3 p-3 rounded-xl bg-[#F8F8F7] border border-[#E8E8E6]">
          <img src="https://i.pravatar.cc/100?img=<?php echo e($loop->index+5); ?>" class="w-9 h-9 rounded-full object-cover border border-white">
          <div class="flex-1 min-w-0"><div class="text-[13px] font-bold truncate"><?php echo e($p['creator']); ?></div><div class="text-[11px] font-semibold text-[#7A7A78] truncate"><?php echo e($p['handle']); ?> • <?php echo e($p['orders']); ?> orders</div></div>
          <div class="text-right"><div class="text-[13px] font-black">₹<?php echo e(number_format($p['amount'])); ?></div><div class="text-[11px] font-semibold text-[#7A7A78]"><?php echo e($p['upi']); ?></div></div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <a href="<?php echo e(route('admin.payouts.index')); ?>" class="h-9 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px] grid place-items-center">Go to Payouts</a>
      </div>
    </div>

    <div class="bg-[#0F0F0F] text-white rounded-2xl p-5">
      <div class="text-[11px] font-bold tracking-widest uppercase text-white/60">Quick Actions</div>
      <div class="mt-3 grid grid-cols-2 gap-2">
        <a href="<?php echo e(route('admin.orders.index')); ?>?status=pending" class="h-10 rounded-xl bg-white text-[#0F0F0F] font-bold text-[13px] grid place-items-center">Pending (<?php echo e(1); ?>)</a>
        <a href="<?php echo e(route('admin.orders.index')); ?>?status=review" class="h-10 rounded-xl bg-white/10 border border-white/20 font-bold text-[13px] grid place-items-center">In Review</a>
        <a href="<?php echo e(route('admin.creators.index')); ?>?filter=pending" class="h-10 rounded-xl bg-white/10 border border-white/20 font-bold text-[13px] grid place-items-center">Verify Creators</a>
        <a href="<?php echo e(route('admin.users.index')); ?>" class="h-10 rounded-xl bg-amber-400 text-[#0F0F0F] font-black text-[13px] grid place-items-center">Manage Roles</a>
      </div>
      <div class="mt-3 text-[11px] font-medium text-white/60">Role: <b class="text-white"><?php echo e($role); ?></b> • Super Admin sees everything. Manager: orders+creators. Support: orders view. Finance: payouts.</div>
    </div>
  </div>
</div>

<div class="mt-6 bg-amber-50 border border-amber-200 rounded-2xl p-4 flex gap-3">
  <div class="w-8 h-8 rounded-full bg-amber-400 text-[#0F0F0F] grid place-items-center font-black shrink-0">!</div>
  <div class="text-[13px] leading-5"><b>Hostinger cron setup (required for queue):</b> In hPanel → Cron Jobs → Add: <code class="bg-white border border-amber-200 px-1.5 py-0.5 rounded font-mono text-[11px]">* * * * * /usr/bin/php /home/u123456789/domains/yourdomain.com/artisan queue:work --stop-when-empty >> /dev/null 2>&1</code> and <code class="bg-white border border-amber-200 px-1.5 py-0.5 rounded font-mono text-[11px]">* * * * * /usr/bin/php /home/u123456789/domains/yourdomain.com/artisan schedule:run >> /dev/null 2>&1</code></div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\quick-content-laravel\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>