<?php $__env->startSection('title','Payouts'); ?>
<?php $__env->startSection('breadcrumb','Admin • Finance • Payouts'); ?>
<?php $__env->startSection('content'); ?>
<div class="grid sm:grid-cols-3 gap-4">
  <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4"><div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Held (escrow)</div><div class="text-[18px] font-black"><?php echo e($stats['hold']); ?></div></div>
  <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4"><div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Ready to pay</div><div class="text-[18px] font-black"><?php echo e($stats['ready']); ?></div></div>
  <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4"><div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Paid this month</div><div class="text-[18px] font-black"><?php echo e($stats['paid']); ?></div></div>
</div>

<div class="mt-6 bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
  <div class="px-5 py-4 border-b border-[#F0F0EE] flex items-center justify-between"><div class="font-black text-[14px]">Payout Queue</div><div class="text-[12px] font-semibold text-[#7A7A78]">RazorpayX • UPI • Cron moves held → ready after <?php echo e(48); ?>h</div></div>
  <div class="divide-y divide-[#F0F0EE]">
    <?php $__currentLoopData = $queue; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $q): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="flex flex-wrap items-center gap-4 px-5 py-4 hover:bg-[#F8F8F7]/50">
      <img src="https://i.pravatar.cc/100?img=<?php echo e($q['id'] + 4); ?>" class="w-10 h-10 rounded-full object-cover border border-[#E8E8E6]">
      <div class="flex-1 min-w-[160px]"><div class="text-[13px] font-black"><?php echo e($q['creator']); ?> <span class="text-[#7A7A78] font-semibold"><?php echo e($q['handle']); ?></span></div><div class="text-[11px] font-semibold text-[#7A7A78]">UPI: <?php echo e($q['upi']); ?> • <?php echo e($q['orders']); ?> orders • Hold till <?php echo e($q['hold_until']); ?></div></div>
      <div class="text-right"><div class="text-[14px] font-black">₹<?php echo e(number_format($q['amount'])); ?></div><span class="text-[11px] font-bold px-2.5 py-1 rounded-full border
        <?php if($q['status']=='hold'): ?> bg-amber-50 border-amber-200 text-amber-700
        <?php elseif($q['status']=='ready'): ?> bg-green-50 border-green-200 text-green-700
        <?php else: ?> bg-[#F8F8F7] border-[#E8E8E6] text-[#7A7A78] <?php endif; ?>
      "><?php echo e(ucfirst($q['status'])); ?></span></div>
      <div class="flex gap-2">
        <?php if($q['status']=='ready'): ?><form method="POST" action="<?php echo e(route('admin.payouts.paid',$q['id'])); ?>"><?php echo csrf_field(); ?><button class="h-9 px-5 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Mark Paid</button></form><?php endif; ?>
        <?php if($q['status']!='paid'): ?><form method="POST" action="<?php echo e(route('admin.payouts.hold',$q['id'])); ?>"><?php echo csrf_field(); ?><button class="h-9 px-4 rounded-full border border-[#E8E8E6] bg-white font-bold text-[13px]">Hold</button></form><?php endif; ?>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
</div>

<div class="mt-4 bg-blue-50 border border-blue-200 rounded-2xl p-4 text-[13px] leading-5"><b>How it works on Hostinger (no Redis):</b> Payments are held in Razorpay escrow. A daily cron runs <code class="bg-white border border-blue-200 px-1 py-0.5 rounded font-mono text-[11px]">php artisan payouts:release</code> (you create this command) to move eligible holds to ready. Finance clicks Mark Paid → calls RazorpayX payout API → creator gets UPI. Support cron logs to <code class="bg-white border border-blue-200 px-1 py-0.5 rounded">storage/logs/payouts.log</code>.</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\quick-content-laravel\resources\views/admin/payouts/index.blade.php ENDPATH**/ ?>