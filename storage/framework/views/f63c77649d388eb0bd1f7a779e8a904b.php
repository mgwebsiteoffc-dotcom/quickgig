<?php $__env->startSection('title','FAQs'); ?>
<?php $__env->startSection('breadcrumb','Admin • Content • FAQs'); ?>
<?php $__env->startSection('content'); ?>
<div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 flex gap-3">
  <div class="w-8 h-8 rounded-full bg-[#2563EB] text-white grid place-items-center font-black shrink-0">?</div>
  <div class="text-[13px] leading-5"><b>AEO — Answer Engine Optimization:</b> These FAQs auto-render on landing + per-blog FAQPage JSON-LD. Google & Perplexity pick them up. Keep answers <b>concise (40-60 words), answer-first, no fluff</b>. Order via <b>Sort</b>. Featured = landing. Hostinger: file cache auto-cleared on save.</div>
</div>

<div class="mt-4 grid lg:grid-cols-[1.6fr_0.9fr] gap-6">
  <div class="bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
    <div class="px-5 py-4 border-b border-[#F0F0EE] flex items-center justify-between">
      <div class="font-black text-[14px]">All FAQs</div>
      <form method="GET" class="flex gap-2"><input name="q" value="<?php echo e($q); ?>" placeholder="Search..." class="h-9 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]"><button class="h-9 px-4 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Search</button></form>
    </div>
    <div class="divide-y divide-[#F0F0EE]">
      <?php $__empty_1 = true; $__currentLoopData = $faqs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $f): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <div class="p-4 hover:bg-[#F8F8F7]/50">
        <form method="POST" action="<?php echo e(route('admin.faqs.update',$f)); ?>" class="space-y-2"><?php echo csrf_field(); ?> <?php echo method_field('PUT'); ?>
          <input name="question" value="<?php echo e($f->question); ?>" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-white text-[13px] font-bold">
          <textarea name="answer" rows="3" class="w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] leading-6"><?php echo e($f->answer); ?></textarea>
          <div class="flex flex-wrap gap-2">
            <select name="category" class="h-9 px-3 rounded-full border border-[#E8E8E6] bg-white text-[12px] font-bold"><option>General</option><option <?php if($f->category=='Pricing'): echo 'selected'; endif; ?>>Pricing</option><option <?php if($f->category=='Delivery'): echo 'selected'; endif; ?>>Delivery</option><option <?php if($f->category=='Creators'): echo 'selected'; endif; ?>>Creators</option></select>
            <input name="sort_order" type="number" value="<?php echo e($f->sort_order); ?>" class="w-[90px] h-9 px-3 rounded-full border border-[#E8E8E6] bg-white text-[12px] font-bold" placeholder="Sort">
            <input name="slug" value="<?php echo e($f->slug); ?>" class="flex-1 h-9 px-3 rounded-full border border-[#E8E8E6] bg-[#F8F8F7] text-[12px] font-mono" placeholder="slug">
            <label class="flex items-center gap-1 text-[12px] font-bold"><input type="checkbox" name="is_published" value="1" <?php echo e($f->is_published?'checked':''); ?>> Published</label>
            <label class="flex items-center gap-1 text-[12px] font-bold"><input type="checkbox" name="is_featured" value="1" <?php echo e($f->is_featured?'checked':''); ?>> Featured (landing)</label>
            <button class="h-9 px-4 rounded-full bg-[#0F0F0F] text-white font-bold text-[12px]">Save</button>
          </div>
        </form>
        <form method="POST" action="<?php echo e(route('admin.faqs.destroy',$f)); ?>" onsubmit="return confirm('Delete FAQ?')" class="mt-2"><?php echo csrf_field(); ?> <?php echo method_field('DELETE'); ?><button class="text-[11px] font-bold text-red-600 hover:underline">Delete</button></form>
      </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <div class="p-8 text-center text-[#7A7A78] font-medium">No FAQs yet.</div>
      <?php endif; ?>
    </div>
    <div class="p-4 border-t border-[#F0F0EE]"><?php echo e($faqs->links()); ?></div>
  </div>

  <div class="bg-white border border-[#E8E8E6] rounded-2xl p-5 h-fit">
    <div class="font-black text-[14px]">Add FAQ</div>
    <div class="text-[12px] font-medium text-[#7A7A78]">Writes to DB → landing FAQPage JSON-LD updates instantly (AEO).</div>
    <form method="POST" action="<?php echo e(route('admin.faqs.store')); ?>" class="mt-4 space-y-3"><?php echo csrf_field(); ?>
      <input name="question" required placeholder="Question — e.g. What does it cost?" class="w-full h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-bold">
      <textarea name="answer" required rows="4" placeholder="Answer — concise, 40-60 words, answer first for AEO. Mention Hostinger/escrow/pricing clearly." class="w-full px-3 py-2 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] leading-6"></textarea>
      <div class="grid grid-cols-2 gap-3">
        <select name="category" class="h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px] font-semibold"><option>General</option><option>Pricing</option><option>Delivery</option><option>Creators</option></select>
        <input name="sort_order" type="number" value="99" class="h-10 px-3 rounded-xl border border-[#E8E8E6] bg-[#F8F8F7] text-[13px]" placeholder="Sort (1 = top)">
      </div>
      <label class="flex items-center gap-2 text-[12px] font-bold"><input type="checkbox" name="is_published" value="1" checked> Published</label>
      <label class="flex items-center gap-2 text-[12px] font-bold"><input type="checkbox" name="is_featured" value="1" checked> Featured on landing</label>
      <button class="w-full h-10 rounded-full bg-[#2563EB] text-white font-bold text-[13px]">Add FAQ → live JSON-LD</button>
    </form>
    <div class="mt-4 text-[11px] font-medium text-[#7A7A78]">Preview JSON-LD at <code class="bg-[#F8F8F7] border px-1 py-0.5 rounded">view-source:https://yourdomain.com</code> → search <code class="bg-[#F8F8F7] px-1 py-0.5 rounded">FAQPage</code>.</div>
  </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\quick-content-laravel\resources\views/admin/faqs/index.blade.php ENDPATH**/ ?>