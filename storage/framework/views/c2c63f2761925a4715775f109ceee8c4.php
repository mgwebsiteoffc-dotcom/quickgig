<?php $__env->startSection('title','Services — Dynamic'); ?>
<?php $__env->startSection('breadcrumb','Admin • Services • Dynamic by Profile Type'); ?>
<?php $__env->startSection('content'); ?>
<form method="GET" class="bg-white border border-[#E8E8E6] rounded-2xl p-4 space-y-3">
  <div class="flex flex-wrap gap-2 items-center">
    <div class="flex gap-1.5 flex-wrap">
      <a href="?category=all&profile_type=<?php echo e($profile); ?>&price_type=<?php echo e($priceType); ?><?php echo e($q? '&q='.$q : ''); ?>" class="px-3 py-1.5 rounded-full text-[12px] font-bold border <?php echo e($cat=='all'?'bg-ink text-white border-ink':'bg-white border-line'); ?>">All</a>
      <?php $__currentLoopData = $categories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $c): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <a href="?category=<?php echo e($c); ?>&profile_type=<?php echo e($profile); ?>&price_type=<?php echo e($priceType); ?><?php echo e($q? '&q='.$q : ''); ?>" class="px-3 py-1.5 rounded-full text-[12px] font-bold border <?php echo e($cat==$c?'bg-ink text-white border-ink':'bg-white border-line hover:bg-bg'); ?>"><?php echo e($c); ?></a>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <div class="ml-auto flex gap-2">
      <select name="profile_type" onchange="this.form.submit()" class="h-8 px-3 rounded-full border border-line bg-bg text-[12px] font-bold">
        <option value="all" <?php echo e($profile=='all'?'selected':''); ?>>All profile types</option>
        <option value="video_editor" <?php echo e($profile=='video_editor'?'selected':''); ?>>Video Editor</option>
        <option value="ugc_creator" <?php echo e($profile=='ugc_creator'?'selected':''); ?>>UGC Creator</option>
        <option value="influencer" <?php echo e($profile=='influencer'?'selected':''); ?>>Influencer (Barter)</option>
        <option value="designer" <?php echo e($profile=='designer'?'selected':''); ?>>Designer</option>
        <option value="hybrid" <?php echo e($profile=='hybrid'?'selected':''); ?>>Hybrid</option>
      </select>
      <select name="price_type" onchange="this.form.submit()" class="h-8 px-3 rounded-full border border-line bg-bg text-[12px] font-bold">
        <option value="all" <?php echo e($priceType=='all'?'selected':''); ?>>All pricing</option>
        <option value="paid" <?php echo e($priceType=='paid'?'selected':''); ?>>Paid</option>
        <option value="barter" <?php echo e($priceType=='barter'?'selected':''); ?>>Barter</option>
        <option value="hybrid" <?php echo e($priceType=='hybrid'?'selected':''); ?>>Hybrid</option>
      </select>
    </div>
  </div>
  <div class="flex gap-2">
    <input name="q" value="<?php echo e($q); ?>" placeholder="Search title..." class="flex-1 h-10 px-3 rounded-full border border-line bg-bg text-[13px]">
    <button class="h-10 px-5 rounded-full bg-ink text-white font-bold text-[13px]">Search</button>
    <a href="<?php echo e(route('admin.services.create')); ?>" class="h-10 px-5 rounded-full bg-brand text-white font-bold text-[13px] inline-flex items-center gap-1">+ New Service</a>
  </div>
  <div class="text-[11px] font-semibold text-muted flex flex-wrap gap-1.5">
    <span>Flow same for all: business picks service → matched by <b>profile_type</b> → tracking, easy like ordering food → approve. Barter: price 0 + product value, no escrow.</span>
  </div>
</form>

<?php if(isset($isDb) && $isDb): ?>
<div class="mt-4 bg-white border border-line rounded-2xl overflow-hidden">
  <div class="px-5 py-3 flex items-center justify-between border-b border-[#F0F0EE]"><div class="font-black text-[14px]"><?php echo e($services->total()); ?> Services</div><div class="text-[11px] font-bold bg-bg border border-line px-2.5 py-1 rounded-full">Dynamic • <?php echo e($cat); ?> • <?php echo e($profile); ?> • <?php echo e($priceType); ?></div></div>
  <div class="divide-y divide-[#F0F0EE]">
    <?php $__empty_1 = true; $__currentLoopData = $services; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $s): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
    <div class="flex gap-4 p-4 hover:bg-bg/50">
      <img src="<?php echo e(filter_var($s->cover, FILTER_VALIDATE_URL) ? $s->cover : ($s->cover ? asset('storage/'.$s->cover) : 'https://images.unsplash.com/photo-1574717025058-2f8737d2e2b7?w=200&q=80')); ?>" class="w-[84px] h-[64px] rounded-xl object-cover border border-line shrink-0">
      <div class="flex-1 min-w-0">
        <div class="text-[13px] font-black leading-tight"><?php echo e($s->title); ?> <span class="ml-1 text-[10px] font-black px-2 py-0.5 rounded-full border <?php echo e($s->is_active ? 'bg-green-50 border-green-200 text-green-700' : 'bg-bg border-line text-muted'); ?>"><?php echo e($s->is_active ? 'Active' : 'Hidden'); ?></span> <span class="text-[10px] font-bold px-2 py-0.5 rounded-full <?php echo e($s->price_type==='barter' ? 'bg-amber-50 border border-amber-200 text-amber-800' : ($s->price_type==='hybrid' ? 'bg-blue-50 border border-blue-200 text-brand' : 'bg-ink text-white')); ?>"><?php echo e(strtoupper($s->price_type)); ?></span></div>
        <div class="text-[11px] font-semibold text-muted"><?php echo e($s->category); ?> • <?php echo e($s->profile_type); ?> • <?php echo e($s->delivery_days); ?> days • for <?php echo e($s->creator->name ?? '—'); ?> (<?php echo e($s->creator->profileLabel() ?? ''); ?>)</div>
        <div class="mt-1 flex items-center gap-2"><span class="font-black text-[13px]"><?php echo e($s->displayPrice()); ?></span><?php if($s->barter_value): ?><span class="text-[11px] font-semibold text-muted">• Barter value ₹<?php echo e(number_format($s->barter_value)); ?></span><?php endif; ?><span class="text-[11px] text-muted">• ★ <?php echo e($s->rating); ?> • <?php echo e($s->sold_count); ?> sold</span></div>
        <?php if($s->deliverables): ?><div class="mt-1 text-[11px] font-medium text-muted">Deliver: <?php echo e(is_array($s->deliverables) ? implode(' • ', $s->deliverables) : $s->deliverables); ?></div><?php endif; ?>
      </div>
      <div class="flex flex-col gap-1.5 shrink-0">
        <a href="<?php echo e(route('admin.services.edit',$s->id)); ?>" class="h-8 px-3 rounded-full border border-line bg-white font-bold text-[12px] inline-flex items-center justify-center">Edit</a>
        <form method="POST" action="<?php echo e(route('admin.services.toggle',$s->id)); ?>"><?php echo csrf_field(); ?><button class="h-8 px-3 rounded-full border font-bold text-[12px] w-full <?php echo e($s->is_active ? 'bg-white border-line' : 'bg-ink text-white border-ink'); ?>"><?php echo e($s->is_active ? 'Hide' : 'Activate'); ?></button></form>
        <form method="POST" action="<?php echo e(route('admin.services.destroy',$s->id)); ?>" onsubmit="return confirm('Delete?')"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="h-7 w-7 mx-auto rounded-full border border-red-200 bg-red-50 text-red-600 grid place-items-center text-[11px]">✕</button></form>
      </div>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="p-8 text-center"><div class="font-black">No services</div><div class="text-[13px] text-muted">Create your first: UGC Video or Barter Collab — dynamic, no code.</div></div>
    <?php endif; ?>
  </div>
  <div class="p-4 border-t border-[#F0F0EE]"><?php echo e($services->links()); ?></div>
</div>
<?php else: ?>
  <div class="mt-4 bg-white border border-line rounded-2xl p-8 text-center"><div class="font-black">Demo mode</div><div class="text-[13px] text-muted">Run <code class="bg-bg border px-1 py-0.5 rounded">php artisan migrate --seed</code> to see real services with UGC & Barter.</div></div>
<?php endif; ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\quick-content-laravel\resources\views/admin/services/index.blade.php ENDPATH**/ ?>