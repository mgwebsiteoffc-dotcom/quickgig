<?php $__env->startSection('title','Order '.$order['id']); ?>
<?php $__env->startSection('breadcrumb','Admin • Orders • '.$order['id']); ?>
<?php $__env->startSection('content'); ?>
<div class="grid lg:grid-cols-[1.7fr_0.9fr] gap-6">
  <div class="space-y-4">
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="flex flex-wrap items-start justify-between gap-3">
        <div>
          <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Order <?php echo e($order['id']); ?> • <?php echo e($order['created']); ?></div>
          <div class="text-[18px] font-black mt-1"><?php echo e($order['service']); ?></div>
          <div class="text-[13px] font-medium text-[#7A7A78]">For <b class="text-[#0F0F0F]"><?php echo e($order['company']); ?> • <?php echo e($order['person']); ?></b> → Creator: <b class="text-[#0F0F0F]"><?php echo e($order['creator']); ?></b></div>
        </div>
        <div class="text-right"><div class="text-[20px] font-black">₹<?php echo e(number_format($order['amount'])); ?></div><div class="text-[11px] font-bold px-2.5 py-1 rounded-full inline-flex <?php echo e($order['escrow']=='held' ? 'bg-amber-400 text-[#0F0F0F]' : 'bg-green-500 text-white'); ?>">Escrow: <?php echo e(ucfirst($order['escrow'])); ?></div></div>
      </div>

      <div class="mt-5 grid grid-cols-3 gap-3 text-center">
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3"><div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Status</div><div class="font-black capitalize"><?php echo e($order['status']); ?></div></div>
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3"><div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Escrow</div><div class="font-black"><?php echo e(ucfirst($order['escrow'])); ?></div></div>
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3"><div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Delivery</div><div class="font-black">1 Day</div></div>
      </div>

      <div class="mt-5">
        <div class="text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]">Timeline (quick-delivery-style)</div>
        <div class="mt-3 space-y-3">
          <?php $__currentLoopData = $timeline; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $t): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <div class="flex gap-3">
            <div class="w-7 h-7 rounded-full grid place-items-center shrink-0 <?php echo e($t['done'] ? 'bg-green-500 text-white' : 'bg-white border-2 border-[#E8E8E6]'); ?>"><?php if($t['done']): ?><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg><?php else: ?><span class="w-2 h-2 bg-[#E8E8E6] rounded-full"></span><?php endif; ?></div>
            <div class="flex-1 pb-3 border-b border-[#F0F0EE] last:border-0"><div class="text-[13px] font-bold <?php echo e($t['done'] ? '' : 'text-[#7A7A78]'); ?>"><?php echo e($t['t']); ?></div><div class="text-[11px] font-semibold text-[#7A7A78]"><?php echo e($t['time']); ?></div></div>
          </div>
          <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
      </div>
    </div>

    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="font-black text-[14px]">Brief from Company</div>
      <div class="mt-2 text-[13px] leading-6 font-medium text-[#2b2b2b] bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3">“Need a talking-head reel for Avante Studio — 30 sec, hook in first 2 sec, captions burned-in, 9:16, brand colors navy + white. Reference: @devtalksbusiness reel style. Deliver with SRT + thumbnail option.”</div>
      <div class="mt-3 flex gap-2 text-[12px] font-bold"><span class="px-2.5 py-1 rounded-full bg-white border border-[#E8E8E6]">1 Day</span><span class="px-2.5 py-1 rounded-full bg-white border border-[#E8E8E6]">9:16</span><span class="px-2.5 py-1 rounded-full bg-white border border-[#E8E8E6]">Captions required</span></div>
    </div>
  </div>

  <div class="space-y-4">
    <?php if(auth()->user()->hasAnyRole(['super_admin','admin','manager'])): ?>
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="font-black text-[13px]">Change Status</div>
      <form method="POST" action="<?php echo e(route('admin.orders.status',$order['id'])); ?>" class="mt-3 space-y-3"><?php echo csrf_field(); ?>
        <select name="status" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold">
          <?php $__currentLoopData = ['pending','working','review','delivered','approved','cancelled']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?><option value="<?php echo e($s); ?>" <?php echo e($order['status']==$s ? 'selected':''); ?>><?php echo e(ucfirst($s)); ?></option><?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </select>
        <button class="w-full h-10 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Update Status</button>
      </form>
      <form method="POST" action="<?php echo e(route('admin.orders.assign',$order['id'])); ?>" class="mt-3 flex gap-2"><?php echo csrf_field(); ?>
        <select name="creator_id" class="flex-1 h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px] font-semibold"><option value="1">Priya Sharma (@priyaedits)</option><option value="2">Rahul Verma (@rahulcuts)</option><option value="4">Neha Jain (@nehacreates)</option></select>
        <button class="h-10 px-4 rounded-full border-2 border-[#0F0F0F] font-bold text-[13px]">Re-assign</button>
      </form>
    </div>
    <?php endif; ?>

    <?php if(auth()->user()->hasAnyRole(['super_admin','admin','finance']) && $order['escrow']=='held'): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
      <div class="font-black text-[13px]">Escrow Control</div>
      <div class="text-[12px] font-semibold text-[#7A7A78] mt-1">₹<?php echo e(number_format($order['amount'])); ?> held via Razorpay. Release only after Company approves.</div>
      <form method="POST" action="<?php echo e(route('admin.orders.release',$order['id'])); ?>" class="mt-3"><?php echo csrf_field(); ?><button class="w-full h-10 rounded-full bg-amber-400 text-[#0F0F0F] font-black text-[13px]">Release Escrow → Creator Payout</button></form>
    </div>
    <?php endif; ?>

    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5">
      <div class="font-black text-[13px]">Chat (polling — Hostinger safe)</div>
      <div class="mt-3 space-y-2 max-h-[220px] overflow-y-auto">
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl p-3"><div class="text-[11px] font-bold text-[#7A7A78]">Rohan Sharma (Avante) • 10:32 AM</div><div class="text-[13px] font-medium">Hi Priya, can we add a hook text at 0:02?</div></div>
        <div class="bg-blue-50 border border-blue-100 rounded-xl p-3"><div class="text-[11px] font-bold text-blue-700">Priya Sharma • 10:38 AM</div><div class="text-[13px] font-medium">Sure! Adding now — will share draft in 1 hour.</div></div>
      </div>
      <form method="POST" action="<?php echo e(route('orders.message',$order['id'])); ?>" class="mt-3 flex gap-2"><?php echo csrf_field(); ?><input name="message" placeholder="Type as admin..." class="flex-1 h-10 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"><button class="h-10 px-5 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Send</button></form>
      <div class="mt-2 text-[11px] text-[#7A7A78] font-medium">No WebSockets needed — page polls every 15s. Works on shared hosting.</div>
    </div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\quick-content-laravel\resources\views/admin/orders/show.blade.php ENDPATH**/ ?>