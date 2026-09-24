<?php $__env->startSection('title','Blogs'); ?>
<?php $__env->startSection('breadcrumb','Admin • Content • Blogs'); ?>
<?php $__env->startSection('content'); ?>
<div class="flex flex-wrap gap-3 items-center justify-between">
  <form method="GET" class="flex gap-2 flex-1 max-w-[420px]">
    <input name="q" value="<?php echo e($q); ?>" placeholder="Search title..." class="flex-1 h-10 px-3 rounded-full border border-[#E8E8E6] bg-white text-[13px]">
    <button class="h-10 px-5 rounded-full bg-[#0F0F0F] text-white font-bold text-[13px]">Search</button>
  </form>
  <a href="<?php echo e(route('admin.blogs.create')); ?>" class="h-10 px-6 rounded-full bg-[#2563EB] text-white font-bold text-[13px] inline-flex items-center gap-2"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 5v14M5 12h14"/></svg> New Blog Post</a>
</div>

<div class="mt-4 bg-white border border-[#E8E8E6] rounded-2xl overflow-hidden">
  <div class="overflow-x-auto">
    <table class="w-full text-left text-[13px]">
      <thead class="bg-[#F8F8F7] text-[11px] font-bold tracking-widest uppercase text-[#7A7A78]"><tr><th class="px-4 py-3">Post</th><th class="px-4 py-3">Category</th><th class="px-4 py-3">Status</th><th class="px-4 py-3">Views</th><th class="px-4 py-3">Updated</th><th class="px-4 py-3"></th></tr></thead>
      <tbody class="divide-y divide-[#F0F0EE]">
        <?php $__empty_1 = true; $__currentLoopData = $blogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $b): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <tr class="hover:bg-[#F8F8F7]/50">
          <td class="px-4 py-3 flex gap-3 items-center">
            <img src="<?php echo e(filter_var($b->cover, FILTER_VALIDATE_URL) ? $b->cover : asset('storage/'.$b->cover)); ?>" class="w-14 h-10 rounded-lg object-cover border border-[#E8E8E6] bg-[#F8F8F7]">
            <div><div class="font-bold leading-tight line-clamp-1"><?php echo e($b->title); ?></div><div class="text-[11px] text-[#7A7A78] font-mono">/<?php echo e($b->slug); ?> • <?php echo e($b->reading_minutes); ?> min</div></div>
          </td>
          <td class="px-4 py-3"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold border" style="background:<?php echo e($b->category->color ?? '#E8E8E6'); ?>15; border-color:<?php echo e($b->category->color ?? '#E8E8E6'); ?>; color:<?php echo e($b->category->color ?? '#0F0F0F'); ?>"><?php echo e($b->category->name ?? '—'); ?></span></td>
          <td class="px-4 py-3">
            <?php if($b->is_published): ?><span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-green-50 border border-green-200 text-green-700">Published</span> <?php else: ?> <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-[#F8F8F7] border border-[#E8E8E6] text-[#7A7A78]">Draft</span> <?php endif; ?>
            <?php if($b->is_featured): ?><span class="ml-1 px-2 py-1 rounded-full text-[11px] font-black bg-amber-400 text-[#0F0F0F]">Featured</span><?php endif; ?>
          </td>
          <td class="px-4 py-3 font-bold"><?php echo e($b->views); ?></td>
          <td class="px-4 py-3 text-[#7A7A78] font-semibold text-[12px]"><?php echo e($b->updated_at->diffForHumans()); ?></td>
          <td class="px-4 py-3 flex gap-1.5">
            <a href="<?php echo e(route('blog.show',$b->slug)); ?>" target="_blank" class="h-8 px-3 rounded-full border border-[#E8E8E6] bg-white font-bold text-[12px] inline-flex items-center">View</a>
            <a href="<?php echo e(route('admin.blogs.edit',$b)); ?>" class="h-8 px-3 rounded-full bg-[#0F0F0F] text-white font-bold text-[12px] inline-flex items-center">Edit</a>
          </td>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <tr><td colspan="6" class="px-4 py-12 text-center"><div class="font-black">No blogs yet</div><div class="text-[13px] text-[#7A7A78] font-medium">Create your first post — Quill editor works on Hostinger file uploads (no S3).</div></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="p-4 border-t border-[#F0F0EE]"><?php echo e($blogs->links()); ?></div>
</div>

<div class="mt-4 bg-blue-50 border border-blue-200 rounded-2xl p-4 text-[13px] leading-5"><b>SEO/AEO per post:</b> Each blog auto-generates <code class="bg-white border border-blue-200 px-1 py-0.5 rounded">JSON-LD BlogPosting</code> + OG + canonical. Add per-post FAQ in editor → extra <code class="bg-white border px-1 py-0.5 rounded">FAQPage</code> for answer engines. Sitemap at <a href="/sitemap.xml" class="underline">/sitemap.xml</a> updates automatically.</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('admin.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\laragon\www\quick-content-laravel\resources\views/admin/blogs/index.blade.php ENDPATH**/ ?>