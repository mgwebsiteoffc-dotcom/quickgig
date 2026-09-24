<?php $__env->startSection('title','Companies'); ?>
<?php $__env->startSection('breadcrumb','Admin • Companies'); ?>
<?php $__env->startSection('content'); ?>
<form method="GET" class="bg-white border border-[#E8E8E6] rounded-2xl p-4 flex gap-3">
  <input name="q" value="<?php echo e($q); ?>" placeholder="Search company or person..." class="flex-1 h-10 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
  <button class="h-10 px-5 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Search</button>
</form>

<div class="mt-4 bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left text-[13px]">
      <thead class="bg-[#F8F8F7] text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]"><tr><th class="px-4 py-3">Company</th><th class="px-4 py-3">Person</th><th class="px-4 py-3">Plan</th><th class="px-4 py-3">Orders</th><th class="px-4 py-3">Spent</th><th class="px-4 py-3">Joined</th></tr></thead>
      <tbody class="divide-y divide-[#F0F0EE]">
        <?php $__currentLoopData = $companies; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <tr class="hover:bg-[#F8F8F7]/50">
          <td class="px-4 py-3 flex items-center gap-3"><div class="w-9 h-9 rounded-xl bg-[#0F0F0F] text-white grid place-items-center font-black text-[11px]"><?php echo e($c['initials']); ?></div><span class="font-bold"><?php echo e($c['company']); ?></span></td>
          <td class="px-4 py-3"><div class="font-semibold"><?php echo e($c['person']); ?></div><div class="text-[11px] text-[#7A7A78] font-medium"><?php echo e($c['email']); ?> • <?php echo e($c['phone']); ?></div></td>
          <td class="px-4 py-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold border bg-white border-[#E8E8E6]"><?php echo e($c['plan']); ?></span></td>
          <td class="px-4 py-3 font-black"><?php echo e($c['orders']); ?></td>
          <td class="px-4 py-3 font-black"><?php echo e($c['spent']); ?></td>
          <td class="px-4 py-3 text-[#7A7A78] font-semibold"><?php echo e($c['joined']); ?></td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </tbody>
    </table>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\quick-content-laravel\resources\views/admin/companies/index.blade.php ENDPATH**/ ?>