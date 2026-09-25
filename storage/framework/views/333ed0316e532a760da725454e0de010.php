<div style="padding:10px 16px;background:var(--card);border-bottom:1px solid var(--line2);flex-shrink:0">
  <div style="display:flex;background:var(--bg);border:1px solid var(--line);border-radius:12px;padding:3px;gap:3px;max-width:360px;margin:0 auto">
    <a href="<?php echo e(url('/')); ?>" style="flex:1;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;gap:6px;font-size:12.5px;font-weight:650;text-decoration:none;<?php echo e(!request()->is('creator*') ? 'background:var(--text);color:white;box-shadow:0 2px 8px rgba(0,0,0,0.12)' : 'color:var(--muted)'); ?>">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a4 4 0 0 1 8 0v2"/></svg>
      For Business
    </a>
    <a href="<?php echo e(route('creator.dashboard')); ?>" style="flex:1;height:34px;border-radius:9px;display:flex;align-items:center;justify-content:center;gap:6px;font-size:12.5px;font-weight:650;text-decoration:none;<?php echo e(request()->is('creator*') ? 'background:var(--text);color:white;box-shadow:0 2px 8px rgba(0,0,0,0.12)' : 'color:var(--muted)'); ?>">
      <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><circle cx="12" cy="8" r="4"/><path d="M4 20a8 8 0 0 1 16 0"/></svg>
      For Creators
    </a>
  </div>
</div>
<?php /**PATH C:\laragon\www\quick-content-laravel\resources\views/components/mode-switch.blade.php ENDPATH**/ ?>