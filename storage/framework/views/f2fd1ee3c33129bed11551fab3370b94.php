<?php $__env->startSection('title','Creators'); ?>
<?php $__env->startSection('breadcrumb','Admin • Creators • Verify & Manage'); ?>
<?php $__env->startSection('content'); ?>

<form method="GET" class="bg-white border border-[#E8E8E6] rounded-2xl p-4 space-y-3">
  <div class="flex flex-wrap gap-2 items-center">
    <div class="flex gap-1.5 flex-wrap">
      <?php $__currentLoopData = ['all'=>'All','available'=>'Available','pending'=>'Pending Verify','verified'=>'Verified','barter'=>'Barter']; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $k=>$v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="?filter=<?php echo e($k); ?>&profile_type=<?php echo e($profile ?? 'all'); ?><?php echo e($q ? '&q='.$q : ''); ?>" class="px-3.5 py-1.5 rounded-full text-[13px] font-bold border <?php echo e(($filter ?? 'all')==$k ? 'bg-[#0F0F0F] text-white border-[#0F0F0F]' : 'bg-white border-[#E8E8E6] hover:bg-[#F8F8F7]'); ?>"><?php echo e($v); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="ml-auto flex gap-2">
      <select name="profile_type" onchange="this.form.submit()" class="h-9 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[12px] font-bold">
        <option value="all" <?php echo e(($profile ?? 'all')=='all' ? 'selected':''); ?>>All types</option>
        <?php $__currentLoopData = $profileTypes ?? []; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $rk=>$rv): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
          <option value="<?php echo e($rk); ?>" <?php echo e(($profile ?? '')==$rk ? 'selected':''); ?>><?php echo e($rv); ?></option>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
      </select>
    </div>
  </div>
  <div class="flex gap-2">
    <input name="q" value="<?php echo e($q); ?>" placeholder="Search name, handle, email, skill..." class="flex-1 h-10 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]">
    <button class="h-10 px-5 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Search</button>
    <?php if($q || ($filter??'all')!='all' || ($profile??'all')!='all'): ?><a href="<?php echo e(route('admin.creators.index')); ?>" class="h-10 px-4 rounded-full border border-[#E8E8E6] bg-white font-bold text-[13px] inline-flex items-center">Clear</a><?php endif; ?>
  </div>
  <div class="text-[11px] font-semibold text-[#7A7A78] flex flex-wrap gap-2">
    <span>Profile type decides matching:</span>
    <span class="px-2 py-1 rounded-full bg-[#F8F8F7] border border-[#E8E8E6]"><b>Video Editor</b> → Reels</span>
    <span class="px-2 py-1 rounded-full bg-blue-50 border border-blue-200 text-brand"><b>UGC Creator</b> → UGC Video</span>
    <span class="px-2 py-1 rounded-full bg-amber-50 border border-amber-200 text-amber-800"><b>Influencer</b> → Barter Collab</span>
  </div>
</form>

<?php if(isset($isDb) && $isDb): ?>
  
  <div class="mt-4 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php $__empty_1 = true; $__currentLoopData = $creators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4 hover:shadow-sm transition">
      <div class="flex gap-3">
        <div class="relative shrink-0"><img src="<?php echo e($c->avatarUrl()); ?>" class="w-14 h-14 rounded-full object-cover border border-[#E8E8E6]"><span class="absolute -bottom-1 -right-1 w-[20px] h-[20px] rounded-full <?php echo e($c->is_verified ? 'bg-[#1D9BF0]' : 'bg-amber-400'); ?> text-white grid place-items-center border-2 border-white" style="aspect-ratio:1/1"><?php if($c->is_verified): ?><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg><?php else: ?><span class="text-[10px] font-black text-[#0F0F0F]">!</span><?php endif; ?></span></div>
        <div class="min-w-0 flex-1">
          <div class="text-[14px] font-black leading-tight flex items-center gap-1.5"><?php echo e($c->name); ?> <span class="w-2 h-2 rounded-full <?php echo e($c->is_available ? 'bg-green-500' : 'bg-amber-400'); ?>"></span></div>
          <div class="text-[11px] font-semibold text-[#7A7A78]"><?php echo e($c->handle); ?> • <?php echo e($c->email); ?></div>
          <div class="mt-1 flex flex-wrap gap-1.5">
            <span class="text-[10px] font-black px-2 py-1 rounded-full border tracking-wide <?php echo e($c->profile_type==='ugc_creator' ? 'bg-blue-600 text-white border-blue-600' : ($c->profile_type==='influencer' ? 'bg-amber-400 text-[#0F0F0F] border-amber-400' : ($c->profile_type==='hybrid' ? 'bg-[#0F0F0F] text-white border-[#0F0F0F]' : 'bg-[#F8F8F7] border-[#E8E8E6]'))); ?>"><?php echo e($c->profileLabel()); ?></span>
            <?php if($c->barter_available): ?><span class="text-[10px] font-bold px-2 py-1 rounded-full bg-green-50 border border-green-200 text-green-700">● Barter</span><?php endif; ?>
            <span class="text-[10px] font-bold px-2 py-1 rounded-full <?php echo e($c->is_verified ? 'bg-green-50 border border-green-200 text-green-700' : 'bg-amber-50 border border-amber-200 text-amber-800'); ?>"><?php echo e($c->is_verified ? 'Verified' : 'Pending'); ?></span>
            <?php if($c->is_featured): ?><span class="text-[10px] font-bold px-2 py-1 rounded-full bg-amber-400 text-[#0F0F0F]">Featured</span><?php endif; ?>
          </div>
        </div>
      </div>
      <div class="mt-2 text-[11px] font-medium text-[#7A7A78] line-clamp-1"><?php echo e(is_array($c->skills) ? implode(' • ', array_slice($c->skills,0,3)) : $c->skills); ?> <?php if($c->ugc_niches && is_array($c->ugc_niches)): ?> • UGC: <?php echo e(implode(',', array_slice($c->ugc_niches,0,2))); ?> <?php endif; ?></div>
      <?php if($c->followers_count): ?><div class="text-[11px] font-bold">👥 <?php echo e(number_format($c->followers_count)); ?> followers • <?php echo e($c->collab_type); ?></div><?php endif; ?>
      <div class="mt-3 grid grid-cols-3 gap-2 text-center">
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl py-2"><div class="text-[13px] font-black"><?php echo e($c->orders_count); ?></div><div class="text-[10px] font-bold tracking-widest uppercase text-[#7A7A78]">Orders</div></div>
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl py-2"><div class="text-[13px] font-black">★ <?php echo e($c->rating); ?></div><div class="text-[10px] font-bold tracking-widest uppercase text-[#7A7A78]">Rating</div></div>
        <div class="bg-[#F8F8F7] border border-[#E8E8E6] rounded-xl py-2"><div class="text-[11px] font-bold"><?php echo e($c->created_at->format('M d')); ?></div><div class="text-[10px] font-bold tracking-widest uppercase text-[#7A7A78]">Joined</div></div>
      </div>
      <div class="mt-3 flex gap-2 flex-wrap">
        <a href="<?php echo e(route('admin.creators.show',$c->id)); ?>" class="flex-1 h-8 rounded-full bg-ink text-white font-bold text-[12px] grid place-items-center">Check details →</a>
        <form method="POST" action="<?php echo e(route('admin.creators.verify',$c->id)); ?>"><?php echo csrf_field(); ?><button class="h-8 px-3 rounded-full border font-bold text-[12px] <?php echo e($c->is_verified ? 'bg-white border-[#E8E8E6]' : 'bg-amber-400 border-amber-400 text-ink'); ?>"><?php echo e($c->is_verified ? 'Unverify' : 'Verify ✓'); ?></button></form>
      </div>
      <div class="mt-2 flex gap-1.5">
        <form method="POST" action="<?php echo e(route('admin.creators.availability',$c->id)); ?>"><?php echo csrf_field(); ?><button class="flex-1 h-7 rounded-full border border-[#E8E8E6] bg-white font-bold text-[11px]"><?php echo e($c->is_available ? 'Set Busy' : 'Set Available'); ?></button></form>
        <form method="POST" action="<?php echo e(route('admin.creators.featured',$c->id)); ?>"><?php echo csrf_field(); ?><button class="flex-1 h-7 rounded-full border font-bold text-[11px] <?php echo e($c->is_featured ? 'bg-amber-400 border-amber-400 text-ink' : 'bg-white border-[#E8E8E6]'); ?>"><?php echo e($c->is_featured ? '★ Featured' : 'Feature'); ?></button></form>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="col-span-3 text-center py-12 bg-white border border-dashed border-[#E8E8E6] rounded-2xl"><div class="font-black">No creators found</div><div class="text-[13px] text-[#7A7A78] font-medium">Try clearing filters or create via onboarding.</div></div>
    <?php endif; ?>
  </div>
  <div class="mt-4"><?php echo e($creators->links()); ?></div>
<?php else: ?>
  
  <div class="mt-4 grid md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php $__currentLoopData = $creators; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="bg-white border border-[#E8E8E6] rounded-2xl p-4">
      <div class="flex gap-3"><div class="relative shrink-0"><img src="<?php echo e($c['img']); ?>" class="w-14 h-14 rounded-full object-cover border border-[#E8E8E6]"><span class="absolute -bottom-1 -right-1 w-[20px] h-[20px] rounded-full <?php echo e($c['verified'] ? 'bg-[#1D9BF0]' : 'bg-amber-400'); ?> text-white grid place-items-center border-2 border-white"><?php if($c['verified']): ?><svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3"><path d="M20 6L9 17l-5-5"/></svg><?php else: ?><span class="text-[10px] font-black text-ink">!</span><?php endif; ?></span></div><div class="min-w-0 flex-1"><div class="text-[14px] font-black"><?php echo e($c['name']); ?></div><div class="text-[11px] font-semibold text-[#7A7A78]"><?php echo e($c['handle']); ?> • <?php echo e($c['email']); ?></div><div class="text-[11px] font-medium text-[#7A7A78]"><?php echo e($c['skills']); ?></div></div></div>
      <div class="mt-3 flex gap-2"><a href="<?php echo e(route('admin.creators.index')); ?>" class="flex-1 h-8 rounded-full bg-ink text-white font-bold text-[12px] grid place-items-center">Check details →</a></div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
  </div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\quick-content-laravel\resources\views/admin/creators/index.blade.php ENDPATH**/ ?>