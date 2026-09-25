<?php
  $isCreator = request()->is('creator*');
  $company = $current ?? ['company'=>'Avante Studio','person'=>'Rohan Sharma','initials'=>'AS'];
  $creator = ['name'=>'Priya Sharma','handle'=>'@priyaedits','initials'=>'PS'];
?>
<div class="topbar" id="topbar">
  <?php if(!$isCreator): ?>
    <div style="display:flex;align-items:center;gap:10px;min-width:0">
      <div style="width:36px;height:36px;border-radius:10px;background:var(--text);color:white;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex-shrink:0"><?php echo e($company['initials']); ?></div>
      <div style="min-width:0">
        <div style="font-size:13.5px;font-weight:700;display:flex;align-items:center;gap:6px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
          <?php echo e($company['company']); ?>

          <span style="width:14px;height:14px;min-width:14px;aspect-ratio:1/1;border-radius:50%;background:var(--blue);display:inline-flex;align-items:center;justify-content:center"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5l10-10"/></svg></span>
        </div>
        <div style="font-size:11.5px;color:var(--muted);font-weight:500;display:flex;align-items:center;gap:5px">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/></svg>
          <?php echo e($company['person']); ?> • Business
        </div>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
      <form method="POST" action="<?php echo e(route('business.switch')); ?>" style="display:inline"><?php echo csrf_field(); ?><input type="hidden" name="company_id" value="<?php echo e(($company['id'] ?? 1) % 3 + 1); ?>"><button type="submit" style="font-size:11px;font-weight:700;background:var(--bg);border:1px solid var(--line);padding:6px 10px;border-radius:20px;cursor:pointer;display:flex;align-items:center;gap:5px"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M17 1l4 4-4 4M3 11V9a4 4 0 0 1 4-4h14M7 23l-4-4 4-4M21 13v2a4 4 0 0 1-4 4H3"/></svg> Switch</button></form>
      <a href="<?php echo e(route('business.profile')); ?>" style="font-size:11px;font-weight:700;background:var(--text);color:white;padding:6px 10px;border-radius:20px;display:inline-flex;align-items:center;gap:5px"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="1.8"><path d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/><path d="M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg> Profile</a>
      <a href="<?php echo e(route('orders.show','UNJ-8841')); ?>" style="width:36px;height:36px;border-radius:50%;background:var(--bg);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;cursor:pointer;color:var(--text)"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/></svg></a>
    </div>
  <?php else: ?>
    <div style="display:flex;align-items:center;gap:10px;min-width:0">
      <div style="width:36px;height:36px;border-radius:10px;background:var(--yellow);color:var(--text);display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex-shrink:0"><?php echo e($creator['initials']); ?></div>
      <div style="min-width:0">
        <div style="font-size:13.5px;font-weight:700;display:flex;align-items:center;gap:6px">
          <?php echo e($creator['name']); ?>

          <span style="width:14px;height:14px;min-width:14px;aspect-ratio:1/1;border-radius:50%;background:var(--blue);display:inline-flex;align-items:center;justify-content:center"><svg width="8" height="8" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><path d="M5 12l5 5l10-10"/></svg></span>
        </div>
        <div style="font-size:11.5px;color:var(--muted);font-weight:500;display:flex;align-items:center;gap:5px">
          <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/></svg>
          <?php echo e($creator['handle']); ?> • Creator • <span style="color:var(--green);font-weight:700">● Available</span>
        </div>
      </div>
    </div>
    <div style="display:flex;align-items:center;gap:8px;flex-shrink:0">
      <a href="<?php echo e(route('creator.profile')); ?>" style="font-size:11px;font-weight:700;background:var(--yellow);color:var(--text);padding:6px 10px;border-radius:20px;display:inline-flex;align-items:center;gap:5px;border:1px solid var(--line)"><svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M12 20a8 8 0 1 0 0-16 8 8 0 0 0 0 16z"/><path d="M12 14a4 4 0 1 0 0-8 4 4 0 0 0 0 8z"/></svg> Profile</a>
      <div style="width:36px;height:36px;border-radius:50%;background:var(--bg);border:1px solid var(--line);display:flex;align-items:center;justify-content:center;cursor:pointer"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M21 11.5a8.5 8.5 0 0 1-12.5 7.5L3 21l2-5.5A8.5 8.5 0 0 1 21 11.5z"/></svg></div>
    </div>
  <?php endif; ?>
</div>
<?php /**PATH C:\laragon\www\quick-content-laravel\resources\views/components/topbar.blade.php ENDPATH**/ ?>